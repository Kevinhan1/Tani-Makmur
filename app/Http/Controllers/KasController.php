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
        $saldoRekening = null;

        // Jika filter rekening dipilih
        if ($request->filled('rekening')) {
            $query->where('koderekening', $request->rekening);
        }

        // Hitung saldo awal untuk semua rekening atau per rekening
        if ($request->filled('tanggal_awal')) {
            $saldoAwal = MutasiRekening::when($request->filled('rekening'), function ($q) use ($request) {
                    $q->where('koderekening', $request->rekening);
                })
                ->where('tanggal', '<', $request->tanggal_awal)
                ->sum(DB::raw('masuk - keluar'));
        }

        // Saldo akhir ambil dari rekening tertentu, atau total saldo semua rekening
        if ($request->filled('rekening')) {
            $rekening = Rekening::where('koderekening', $request->rekening)->first();
            $saldoRekening = $rekening?->saldo ?? null;
        } else {
            $saldoRekening = Rekening::sum('saldo');
        }


        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $kas = $query->paginate(15)->withQueryString();

        return view('kas', compact('kas', 'rekeningList', 'saldoAwal', 'saldoRekening'));
    }


    public function exportPdf(Request $request)
    {   
        $query = MutasiRekening::with('rekening')->orderBy('tanggal');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->filled('rekening')) {
            $query->where('koderekening', $request->rekening);
        }

        if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
        }
        
        $rekeningNama = 'Semua';
            if ($request->filled('rekening')) {
                $rek = Rekening::where('koderekening', $request->rekening)->first();
                $rekeningNama = $rek?->namarekening ?? 'Tidak ditemukan';
            }

        $kas = $query->get();

        $pdf = Pdf::loadView('kas-pdf', [
            'kas' => $kas,
            'tanggalAwal' => $request->tanggal_awal,
            'tanggalAkhir' => $request->tanggal_akhir,
            'rekeningNama' => $rekeningNama,
            'jenis' => $request->jenis,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('kas-' . now()->format('Ymd_His') . '.pdf');
    }
}
