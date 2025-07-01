<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hbeli;
use App\Models\Dbeli;
use App\Models\Pemasok;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
class PembelianController extends Controller
{
    public function index()
    {   
        $user = Session::get('user');
        
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        $pemasok = Pemasok::where('aktif', 1)->get();
        $barang = Barang::where('aktif', 1)->get();
        $notabeli = $this->generateNoNotaBeli();

        return view('pembelian', compact('pemasok', 'barang', 'notabeli'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notabeli' => 'required',
            'tanggal' => 'required|date',
            'kodepemasok' => 'required',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.noref' => 'required',
            'items.*.kodebarang' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.hargabeli' => 'required|numeric|min:0',
        ]);

        // Ambil user dari session
        $user = Session::get('user');
        $kodepengguna = is_object($user) ? $user->kodepengguna : ($user['kodepengguna'] ?? null);

        if (!$kodepengguna) {
            return response()->json(['success' => false, 'message' => 'User belum login di session atau kodepengguna tidak tersedia']);
        }

        DB::beginTransaction();

        try {
            // Simpan header pembelian (totalbayar diisi 0)
            Hbeli::create([
                'notabeli' => $request->notabeli,
                'tanggal' => $request->tanggal,
                'kodepemasok' => $request->kodepemasok,
                'total' => $request->total,
                'totalbayar' => 0,
                'kodepengguna' => $kodepengguna
            ]);

            // Simpan detail pembelian
            foreach ($request->items as $item) {
                Dbeli::create([
                    'notabeli' => $request->notabeli,
                    'noref' => $item['noref'],
                    'kodebarang' => $item['kodebarang'],
                    'nodo' => $item['nodo'] ?? '',
                    'qty' => $item['qty'],
                    'qtyjual' => $item['qtyjual'] ?? 0,
                    'hargabeli' => $item['hargabeli'],
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data pembelian berhasil disimpan!', 'notabeli' => $request->notabeli]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function generateNoNotaBeli()
    {
        $bulanTahun = date('my');
        $prefix = 'B-' . $bulanTahun . '-';

        $lastNota = Hbeli::where('notabeli', 'like', $prefix . '%')
            ->orderBy('notabeli', 'desc')
            ->first();

        $nextNumber = $lastNota ? ((int) substr($lastNota->notabeli, -4)) + 1 : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }


    public function getHistory(Request $request)
    {
        $limit = $request->get('limit', 5); // jumlah per halaman
        $page = $request->get('page', 1);

        $data = DB::table('thbeli')
            ->join('tpemasok', 'thbeli.kodepemasok', '=', 'tpemasok.kodepemasok')
            ->select(
                'thbeli.notabeli',
                'thbeli.tanggal',
                'thbeli.total',
                'thbeli.totalbayar',
                'tpemasok.namapemasok'
            )
            ->orderByDesc('thbeli.tanggal')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json($data);
    }


    public function deleteHistory(Request $request)
    {
        $request->validate([
            'notabeli' => 'required',
        ]);

        $notabeli = $request->notabeli;

        // Cek apakah nota ada dan belum dibayar
        $hbeli = DB::table('thbeli')->where('notabeli', $notabeli)->first();
        if (!$hbeli) {
            return response()->json(['success' => false, 'message' => 'Nota tidak ditemukan.']);
        }

        if ($hbeli->totalbayar > 0) {
            return response()->json(['success' => false, 'message' => 'Nota sudah memiliki pembayaran.']);
        }

        // Cek semua detail, pastikan qtyjual = 0 semua
        $adaJual = DB::table('tdbeli')
            ->where('notabeli', $notabeli)
            ->where('qtyjual', '>', 0)
            ->exists();

        if ($adaJual) {
            return response()->json(['success' => false, 'message' => 'Data sudah dijual, tidak dapat dihapus.']);
        }

        DB::beginTransaction();
        try {
            DB::table('tdbeli')->where('notabeli', $notabeli)->delete();
            DB::table('thbeli')->where('notabeli', $notabeli)->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }



        public function cetakInvoice($notabeli)
    {
        $hbeli = Hbeli::with('pemasok')->where('notabeli', $notabeli)->first();
        if (!$hbeli) {
            return abort(404, 'Data pembelian tidak ditemukan.');
        }

        $items = Dbeli::with('barang')
            ->where('notabeli', $notabeli)
            ->get()
            ->map(function ($item) {
                return [
                    'namabarang' => $item->barang->namabarang ?? '-',
                    'qty' => $item->qty + $item->qtyjual, // << tambahkan qtyjual
                    'hargabeli' => $item->hargabeli,
                ];
            });

        $total = $items->sum(fn($item) => $item['qty'] * $item['hargabeli']);

        $pdf = Pdf::loadView('invoice-pembelian', [
            'notabeli' => $notabeli,
            'tanggal' => $hbeli->tanggal,
            'namapemasok' => $hbeli->pemasok->namapemasok ?? '-',
            'items' => $items,
            'total' => $total
        ])->setPaper('a5', 'landscape');
        return $pdf->stream("Invoice-{$notabeli}.pdf"); // Atau gunakan download() untuk mengunduh otomatis
    }

}
