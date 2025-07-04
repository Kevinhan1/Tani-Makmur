<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hbeli;
use App\Models\Rekening;
use App\Models\Dbayarbeli;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\MutasiRekening;
use Carbon\Carbon;

class PembayaranPembelianController extends Controller
{
    public function index(Request $request)
    {   
        $user = Session::get('user');

        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
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

        $notabelis = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();
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
        'koderekening' => 'required|exists:trekening,koderekening',
        'total_bayar' => 'required|numeric|min:1'
    ]);

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
    
    // Cek saldo rekening
    $rekening = Rekening::where('koderekening', $request->koderekening)->first();

    if (!$rekening) {
        return back()->withErrors(['koderekening' => 'Rekening tidak ditemukan.'])->withInput();
    }

    if ($request->total_bayar > $rekening->saldo) {
        return back()->withErrors(['total_bayar' => 'Saldo tidak cukup untuk melakukan pembayaran.'])->withInput();
    }

    // Cek total pembayaran tidak melebihi sisa bayar
    $nota = Hbeli::where('notabeli', $request->notabeli)->first();
    $sisa = $nota->total - $nota->totalbayar;

    if ($request->total_bayar > $sisa) {
        return back()->withErrors(['total_bayar' => 'Jumlah bayar melebihi sisa yang harus dibayar.'])->withInput();
    }

    // Ambil tanggal dalam format ddMMyy (bukan yyMMdd)
    $tanggal = \Carbon\Carbon::parse($request->tanggal_bayar)->format('dmy'); // contoh: 220622

    // Cari nomor terakhir yang memiliki akhiran tanggal itu (cek 2 digit pertama)
    $lastNo = Dbayarbeli::whereRaw("RIGHT(no, 6) = ?", [$tanggal])
        ->orderByDesc('no')
        ->value('no');

    // Ambil urutan 2 digit pertama (bisa INT atau substring)
    $urutan = $lastNo ? (int)substr($lastNo, 0, 2) + 1 : 1;

    // Gabungkan urutan dan tanggal → NNddMMyy
    $no = str_pad($urutan, 2, '0', STR_PAD_LEFT) . $tanggal;


    DB::beginTransaction();
    try {
        $user = Session::get('user');

        Dbayarbeli::create([
            'no' => $no,
            'notabeli' => $request->notabeli,
            'tanggal' => $request->tanggal_bayar,
            'koderekening' => $request->koderekening,
            'total' => $request->total_bayar,
            'kodepengguna' => $user->kodepengguna ?? 'ADM',
        ]);

        DB::commit();
        return redirect()->route('pembayaran-pembelian.index')->with('success', 'Pembayaran berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal insert pembayaran: ' . $e->getMessage());
        return back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
    }
}

    public function getHistory($notabeli)
    {
        $history = \App\Models\Dbayarbeli::from('tdbayarbeli as bayar')
            ->join('trekening', 'bayar.koderekening', '=', 'trekening.koderekening')
            ->where('bayar.notabeli', $notabeli)
            ->orderBy('bayar.tanggal', 'asc')
            ->get([
                'bayar.no', // ← ini penting
                'bayar.tanggal',
                'bayar.total',
                'trekening.namarekening'
            ]);

        return response()->json($history);
    }


        public function getData($no)
    {
        $bayar = Dbayarbeli::where('no', $no)->first();

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

    // Ambil data nota dan pembayaran lama
    $bayar = Dbayarbeli::where('no', $no)->firstOrFail();
    $nota = Hbeli::where('notabeli', $bayar->notabeli)->first();

    // Hitung sisa sebelum update
    $sisaSebelum = $nota->total - $nota->totalbayar + $bayar->total;

    // Validasi: jumlah baru tidak boleh lebih besar dari sisa
    if ($request->total > $sisaSebelum) {
        return back()
        ->withErrors(['total' => 'Jumlah bayar yang diubah melebihi sisa yang harus dibayar.'])
        ->withInput()
        ->with('edit_bayar_no', $no);
    }

    
    DB::beginTransaction();
    try {
        $bayar = Dbayarbeli::where('no', $no)->firstOrFail();

        // Update mutasi rekening
        $noreferensi = $bayar->notabeli . '-' . substr($bayar->no, 0, 3);

        DB::table('tmutasirekening')
            ->where('noreferensi', $noreferensi)
            ->where('jenis', 'PEMBELIAN')
            ->update([
                'koderekening' => $request->koderekening,
                'keluar' => $request->total,
            ]);

        // Update data utama
        $bayar->update([
            'koderekening' => $request->koderekening,
            'total' => $request->total,
        ]);

        DB::commit();
        return back()->with('success', 'Riwayat pembayaran berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal update pembayaran: ' . $e->getMessage());
        return back()->with('error', 'Gagal update pembayaran.');
    }
}


public function destroy($no)
{
    DB::beginTransaction();

    try {
        $bayar = Dbayarbeli::where('no', $no)->firstOrFail();

        // Ambil 3 digit terakhir dari nomor (misal: 22062501 → 220)
        $no3digit = substr($bayar->no, 0, 3);
        // Contoh: B-0625-0001-220
        $noreferensi = $bayar->notabeli . '-' . $no3digit;

        // Hapus mutasi terkait
        DB::table('tmutasirekening')
            ->where('noreferensi', $noreferensi)
            ->where('jenis', 'PEMBELIAN')
            ->delete();

        // Hapus pembayaran
        $bayar->delete();

        DB::commit();

        return back()->with('success', 'Pembayaran dan mutasi rekening berhasil dihapus.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal hapus pembayaran: ' . $e->getMessage());
        return back()->with('error', 'Gagal menghapus pembayaran.');
    }
}



}