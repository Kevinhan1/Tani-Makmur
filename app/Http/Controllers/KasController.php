<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiRekening;
use App\Models\Rekening;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    public function index(Request $request)
{
    if (!session()->has('user')) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    }

    $query = MutasiRekening::with('rekening')->orderBy('tanggal');
    $rekeningList = Rekening::orderBy('namarekening')->get();

    $saldoAwal = 0;
    $saldoRekening = 0;

    // Jika filter rekening dipilih
    if ($request->filled('rekening')) {
        $query->where('koderekening', $request->rekening);
    }

    // Filter tanggal
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
    }

    // Filter jenis
    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    // Hitung saldo awal: total sebelum tanggal_awal
    if ($request->filled('tanggal_awal')) {
        $saldoAwal = MutasiRekening::when($request->filled('rekening'), function ($q) use ($request) {
                return $q->where('koderekening', $request->rekening);
            })
            ->where('tanggal', '<', $request->tanggal_awal)
            ->sum(DB::raw('masuk - keluar'));
    }

    // Ambil pagination offset untuk saldo tambahan
    $page = $request->get('page', 1);
    $perPage = 15;
    $offset = ($page - 1) * $perPage;

    $saldoTambahan = (clone $query)->limit($offset)->sum(DB::raw('masuk - keluar'));

    $saldoAwal += $saldoTambahan;

    // Saldo akhir: saldo awal + semua mutasi terfilter
    $saldoRekening = $saldoAwal + (clone $query)->sum(DB::raw('masuk - keluar'));

    $kas = $query->paginate($perPage)->withQueryString();

    return view('kas', compact('kas', 'rekeningList', 'saldoAwal', 'saldoRekening'));
}



    public function exportPdf(Request $request)
{
    $tanggal_awal = $request->input('tanggal_awal') ?? date('Y-m-d', strtotime('-7 days'));
    $tanggal_akhir = $request->input('tanggal_akhir') ?? date('Y-m-d');

    // Hitung Saldo Awal
    $saldoAwal = MutasiRekening::when($request->filled('rekening'), function ($q) use ($request) {
            return $q->where('koderekening', $request->rekening);
        })
        ->where('tanggal', '<', $tanggal_awal)
        ->sum(DB::raw('masuk - keluar'));

    // Query data mutasi untuk rentang tanggal
    $query = MutasiRekening::with('rekening')
        ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
        ->orderBy('tanggal');

    if ($request->filled('rekening')) {
        $query->where('koderekening', $request->rekening);
    }

    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    $kas = $query->get();

    // Hitung Saldo Akhir: Saldo awal + total mutasi di rentang tanggal
    $saldoRekening = $saldoAwal + $kas->sum(fn($item) => $item->masuk - $item->keluar);

    $rekeningNama = 'Semua';
    if ($request->filled('rekening')) {
        $rek = Rekening::where('koderekening', $request->rekening)->first();
        $rekeningNama = $rek?->namarekening ?? 'Tidak ditemukan';
    }

    $pdf = Pdf::loadView('kas-pdf', [
        'kas' => $kas,
        'tanggalAwal' => $tanggal_awal,
        'tanggalAkhir' => $tanggal_akhir,
        'rekeningNama' => $rekeningNama,
        'saldoAwal' => $saldoAwal,
        'saldoRekening' => $saldoRekening,
        'jenis' => $request->jenis,
    ])->setPaper('A4', 'portrait');

    return $pdf->stream('kas-' . now()->format('Ymd_His') . '.pdf');
}

}
