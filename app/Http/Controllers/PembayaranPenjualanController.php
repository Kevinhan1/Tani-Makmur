<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hjual;
use App\Models\Rekening;
use App\Models\Dbayarjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PembayaranPenjualanController extends Controller
{
    public function index(Request $request)
    {   
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        $tanggalAwal = $request->tanggal_awal ?? now()->subDays(7)->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->toDateString();

        if ($tanggalAkhir < $tanggalAwal) {
            return back()->with('error', 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal.');
        }

        $query = Hjual::query();
        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        if ($request->status == 'lunas') {
            $query->whereRaw('totalbayar >= total');
        } elseif ($request->status == 'belum') {
            $query->whereRaw('totalbayar < total');
        }

        if ($request->filled('notajual')) {
            $query->where('notajual', 'like', '%' . $request->notajual . '%');
        }
        $notajuals = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
        $rekeningAktif = Rekening::where('aktif', 1)->get();

        return view('pembayaran-penjualan', compact('notajuals', 'tanggalAwal', 'tanggalAkhir', 'rekeningAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notajual' => 'required',
            'tanggal_bayar' => 'required|date',
            'koderekening' => 'required|exists:trekening,koderekening',
            'total_bayar' => 'required|numeric|min:1'
        ]);

        $rekening = Rekening::where('koderekening', $request->koderekening)->first();

        $errors = [];

        // Validasi tanggal bayar
        if (!$request->tanggal_bayar) {
            $errors['tanggal_bayar'] = 'Pilih tanggal bayar.';
        }

        // Validasi rekening
        if (!$request->koderekening) {
            $errors['koderekening'] = 'Pilih rekening.';
        }

        // Validasi total bayar
        if (!$request->total_bayar || $request->total_bayar < 1) {
            $errors['total_bayar'] = 'Total bayar minimal 1.';
        }

        // Cek jika error ditemukan
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }
        
        if ($request->total_bayar > $rekening->saldo) {
            return back()->withErrors(['total_bayar' => 'Saldo tidak cukup untuk melakukan pembayaran.'])->withInput();
        }

        $nota = Hjual::where('notajual', $request->notajual)->first();
        if (!$nota) {
            return back()->withErrors(['notajual' => 'Nota penjualan tidak ditemukan.'])->withInput();
        }

        $sisa = $nota->total - $nota->totalbayar;
        if ($request->total_bayar > $sisa) {
            return back()->withErrors(['total_bayar' => 'Jumlah pembayaran melebihi sisa tagihan.'])->withInput();
        }

        $tanggal = Carbon::parse($request->tanggal_bayar)->format('dmy');
        $lastNo = Dbayarjual::whereRaw("RIGHT(no, 6) = ?", [$tanggal])
            ->orderByDesc('no')
            ->value('no');
        $urutan = $lastNo ? (int)substr($lastNo, 0, 2) + 1 : 1;
        $no = str_pad($urutan, 2, '0', STR_PAD_LEFT) . $tanggal;

        DB::beginTransaction();
        try {
            $user = Session::get('user');

            Dbayarjual::create([
                'no' => $no,
                'notajual' => $request->notajual,
                'tanggal' => $request->tanggal_bayar,
                'koderekening' => $request->koderekening,
                'total' => $request->total_bayar,
                'kodepengguna' => $user->kodepengguna ?? 'ADM',
            ]);

            DB::commit();
            return redirect()->route('pembayaran-penjualan.index')->with('success', 'Pembayaran berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal insert pembayaran penjualan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan pembayaran.');
        }
    }

    public function getHistory($notajual)
    {
        $history = Dbayarjual::from('tdbayarjual as bayar')
            ->join('trekening', 'bayar.koderekening', '=', 'trekening.koderekening')
            ->where('bayar.notajual', $notajual)
            ->orderBy('bayar.tanggal', 'asc')
            ->get([
                'bayar.no',
                'bayar.tanggal',
                'bayar.total',
                'trekening.namarekening'
            ]);

        return response()->json($history);
    }

    public function getData($no)
    {
        $bayar = Dbayarjual::where('no', $no)->first();

        if (!$bayar) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'koderekening' => $bayar->koderekening,
            'total' => $bayar->total
        ]);
    }

    public function update(Request $request, $no)
{
    $request->validate([
        'koderekening' => 'required|exists:trekening,koderekening',
        'total' => 'required|numeric|min:1'
    ]);

    $bayar = Dbayarjual::where('no', $no)->firstOrFail();
    $nota = Hjual::where('notajual', $bayar->notajual)->first();

    $sisaSebelum = $nota->total - $nota->totalbayar + $bayar->total;

    if ($request->total > $sisaSebelum) {
    return back()
        ->withErrors(['total' => 'Jumlah yang diubah melebihi sisa tagihan.'])
        ->withInput(); // penting!
    }

    DB::beginTransaction();
    try {
        $bayar = Dbayarjual::where('no', $no)->firstOrFail();
        $noreferensi = $bayar->notajual . '-' . substr($bayar->no, 0, 3);

        // Update mutasi rekening: gunakan kolom MASUK, bukan keluar
        DB::table('tmutasirekening')
            ->where('noreferensi', $noreferensi)
            ->where('jenis', 'PENJUALAN')
            ->update([
                'koderekening' => $request->koderekening,
                'masuk' => $request->total, // ✅ PERBAIKAN DI SINI
                'keluar' => 0,
            ]);

        // Update data pembayaran
        $bayar->update([
            'koderekening' => $request->koderekening,
            'total' => $request->total,
        ]);

        DB::commit();
        return back()->with('success', 'Riwayat pembayaran berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal update pembayaran penjualan: ' . $e->getMessage());
        return back()->with('error', 'Gagal update pembayaran.');
    }
}


    public function destroy($no)
    {
        DB::beginTransaction();
        try {
            $bayar = Dbayarjual::where('no', $no)->firstOrFail();
            $no3digit = substr($bayar->no, 0, 3);
            $noreferensi = $bayar->notajual . '-' . $no3digit;

            DB::table('tmutasirekening')
                ->where('noreferensi', $noreferensi)
                ->where('jenis', 'PENJUALAN')
                ->delete();

            $bayar->delete();

            DB::commit();
            return back()->with('success', 'Pembayaran dan mutasi rekening berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal hapus pembayaran penjualan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pembayaran.');
        }
    }
}