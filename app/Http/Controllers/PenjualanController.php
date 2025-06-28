<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Hjual;
use App\Models\Djual;
use App\Models\Dbeli;
use App\Models\Barang;
use App\Models\Pelanggan;
use Barryvdh\DomPDF\Facade\Pdf; // 
use Carbon\Carbon;

class PenjualanController extends Controller
{
    // Menampilkan halaman utama input penjualan
    public function index()
    {   
        $user = Session::get('user');

        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $barang = Barang::where('aktif', 1)->get();
        $pelanggan = Pelanggan::where('aktif', 1)->get();

        $now = Carbon::now();
        $prefix = 'J-' . $now->format('my') . '-';

        $last = Hjual::where('notajual', 'like', $prefix . '%')
                    ->orderBy('notajual', 'desc')
                    ->first();

        $lastNumber = $last ? (int)substr($last->notajual, -4) : 0;
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $notajual = $prefix . $nextNumber;

        // Ambil data Dbeli yang masih memiliki stok > 0
        $dbeli = Dbeli::join('tbarang', 'tdbeli.kodebarang', '=', 'tbarang.kodebarang')
                    ->select('tdbeli.noref', 'tbarang.namabarang', 'tbarang.hjual as hargajual', 'tdbeli.qty','tbarang.konversi')
                    ->where('tdbeli.qty', '>', 0)
                    ->orderBy('tdbeli.noref')
                    ->get();

        return view('penjualan', compact('barang', 'pelanggan', 'notajual', 'dbeli'));
    }

    // Menyimpan data penjualan
    public function store(Request $request)
    {
        $request->validate([
            'notajual' => 'required',
            'tanggal' => 'required|date',
            'kodepelanggan' => 'required',
            'nopol' => 'nullable|string|max:25',
            'supir' => 'nullable|string|max:25',
            'items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $user = Session::get('user');

            // Hitung total
            $total = collect($request->items)->sum(function ($i) {
                return $i['qty'] * $i['hargajual'];
            });

            // Simpan header
            $hjual = Hjual::create([
                'notajual'      => $request->notajual,
                'tanggal'       => $request->tanggal,
                'kodepelanggan' => $request->kodepelanggan,
                'total'         => $total,
                'totalbayar'    => 0,
                'nopol'         => $request->nopol,
                'supir'         => $request->supir,
                'kodepengguna'  => $user->kodepengguna,
            ]);

            // Simpan detail & kurangi stok
            foreach ($request->items as $index => $item) {
                $dbeli = Dbeli::where('noref', $item['noref'])->first();

                if (!$dbeli) {
                    throw new \Exception("Data pembelian dengan No Ref {$item['noref']} tidak ditemukan.");
                }

                if ($dbeli->qty < $item['qty']) {
                    throw new \Exception("Stok pada No Ref {$item['noref']} tidak mencukupi (tersedia: {$dbeli->qty}).");
                }

                // Simpan detail
                Djual::create([
                    'notajual'   => $hjual->notajual,
                    'no'         => $index + 1,
                    'noref'      => $item['noref'],
                    'qty'        => $item['qty'],
                    'hargajual'  => $item['hargajual'],
                ]);

                // Kurangi stok
                $dbeli->qty -= $item['qty'];
                $dbeli->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'redirect' => route('penjualan.invoice', $hjual->notajual)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function getHistory(Request $request)
    {
        $limit = $request->input('limit', 5);
        $keyword = $request->input('search');

        $query = DB::table('thjual')
            ->join('tpelanggan', 'thjual.kodepelanggan', '=', 'tpelanggan.kodepelanggan')
            ->select(
                'thjual.notajual',
                'thjual.tanggal',
                'thjual.total',
                'tpelanggan.namapelanggan'
            )
            ->orderByDesc('thjual.tanggal');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('thjual.notajual', 'like', "%$keyword%")
                ->orWhere('tpelanggan.namapelanggan', 'like', "%$keyword%")
                ->orWhere('thjual.tanggal', 'like', "%$keyword%");
            });
        }

        $data = $query->paginate($limit);

        return response()->json($data);
    }




public function deleteHistory(Request $request)
{
    $request->validate([
        'notajual' => 'required',
    ]);

    $notajual = $request->notajual;

    $hjual = Hjual::where('notajual', $notajual)->first();

    if (!$hjual) {
        return response()->json(['success' => false, 'message' => 'Nota tidak ditemukan.']);
    }

    if ($hjual->totalbayar > 0) {
        return response()->json(['success' => false, 'message' => 'Sudah ada pembayaran. Tidak bisa dihapus.']);
    }

    DB::beginTransaction();
    try {
        $details = Djual::where('notajual', $notajual)->get();
        foreach ($details as $d) {
            $dbeli = Dbeli::where('noref', $d->noref)->first();
            if ($dbeli) {
                $dbeli->qty += $d->qty;
                $dbeli->save();
            }
        }

        Djual::where('notajual', $notajual)->delete();
        $hjual->delete();

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
    }
}

    public function cetakInvoice($notajual)
    {
        $hjual = Hjual::with(['pelanggan', 'detail'])->where('notajual', $notajual)->first();

        if (!$hjual) {
            return abort(404, 'Data penjualan tidak ditemukan.');
        }

        // Ambil detail lalu join manual tdbeli + tbarang berdasarkan noref
        $items = $hjual->detail->map(function ($item) {
            $namabarang = DB::table('tdbeli')
                ->join('tbarang', 'tdbeli.kodebarang', '=', 'tbarang.kodebarang')
                ->where('tdbeli.noref', $item->noref)
                ->value('tbarang.namabarang');

            return [
                'namabarang' => $namabarang ?? '-',
                'qty'        => $item->qty,
                'hargajual'  => $item->hargajual,
            ];
        });

        $total = $items->sum(fn($item) => $item['qty'] * $item['hargajual']);

        $pdf = Pdf::loadView('invoice-penjualan', [
            'hjual'         => $hjual,
            'notajual'      => $notajual,
            'tanggal'       => $hjual->tanggal,
            'namapelanggan' => $hjual->pelanggan->namapelanggan ?? '-',
            'items'         => $items,
            'total'         => $total
        ])->setPaper('a5', 'landscape');

        return $pdf->stream("Invoice-{$notajual}.pdf");
    }

}