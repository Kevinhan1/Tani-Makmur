<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hbeli;
use App\Models\Rekening;
use App\Models\Dbayarbeli;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PembayaranPembelianController extends Controller
{
    public function index(Request $request)
    {
        // Validasi tanggal
        $tanggalAwal = $request->tanggal_awal ?? now()->subDays(7)->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->toDateString();

        if ($tanggalAkhir < $tanggalAwal) {
            return back()->with('error', 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal.');
        }

        // Ambil query pencarian
        $query = Hbeli::query();

        // Filter by tanggal
        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Filter status lunas / belum
        if ($request->status == 'lunas') {
            $query->whereRaw('totalbayar >= total');
        } elseif ($request->status == 'belum') {
            $query->whereRaw('totalbayar < total');
        }

        // Filter notabeli (tidak tergantung tanggal atau status)
        if ($request->filled('notabeli')) {
            $query->where('notabeli', 'like', '%' . $request->notabeli . '%');
        }

        $notabelis = $query->orderBy('tanggal', 'desc')->get();

        $rekeningAktif = Rekening::where('aktif', 1)->get();

        return view('pembayaran-pembelian', [
            'notabelis' => $notabelis,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'rekeningAktif' => $rekeningAktif,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'notabeli' => 'required',
            'tanggal_bayar' => 'required|date',
            'koderekening' => 'required',
            'total_bayar' => 'required|numeric|min:1'
        ]);

        $tanggalFormat = Carbon::parse($request->tanggal_bayar)->format('dmy');
        $last = Dbayarbeli::where('no', 'like', $tanggalFormat . '%')
            ->orderByDesc('no')
            ->value('no');

        $urutan = $last ? (int)substr($last, -4) + 1 : 1;
        $no = $tanggalFormat . str_pad($urutan, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $user = Session::get('user');

            Dbayarbeli::create([
                'no' => $no,
                'notabeli' => $request->notabeli,
                'tanggal' => $request->tanggal_bayar,
                'koderekening' => $request->koderekening,
                'total' => $request->total_bayar,
                'kodepengguna' => $user->kodepengguna ?? 'ADM'
            ]);

            // Saldo dan totalbayar otomatis ditangani oleh TRIGGER
            DB::commit();
            return redirect()->route('pembayaran-pembelian.index')->with('success', 'Pembayaran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }
}
