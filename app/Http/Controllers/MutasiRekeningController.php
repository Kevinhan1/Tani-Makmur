<?php

// app/Http/Controllers/MutasiRekeningController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MutasiRekening;
use App\Models\Rekening;
use Barryvdh\DomPDF\Facade\Pdf;

class MutasiRekeningController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $query = MutasiRekening::with('rekening')->orderBy('tanggal', 'desc');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('rekening')) {
            $query->where('koderekening', $request->rekening);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%$search%")
                ->orWhere('noreferensi', 'like', "%$search%")
                ->orWhere('masuk', 'like', "%$search%")
                ->orWhere('keluar', 'like', "%$search%")
                ->orWhere('jenis', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
            })
            ->orWhereHas('rekening', function ($q) use ($search) {
                $q->where('namarekening', 'like', "%$search%");
            });
        }

        $mutasi = $query->paginate(15)->withQueryString();

        // ✅ Ini yang penting!
        $rekeningList = \App\Models\Rekening::orderBy('namarekening')->get();

        return view('mutasirekening', compact('mutasi', 'rekeningList'));
    }

    
        public function exportPdf(Request $request)
        {
            $query = MutasiRekening::with('rekening')->orderBy('tanggal', 'desc');

            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            }

            if ($request->filled('jenis')) {
                $query->where('jenis', $request->jenis);
            }

            if ($request->filled('rekening')) {
                $query->where('koderekening', $request->rekening);
            }

            $mutasi = $query->get();

            $pdf = Pdf::loadView('mutasirekening-pdf', [
                'mutasi' => $mutasi,
                'tanggalAwal' => $request->tanggal_awal,
                'tanggalAkhir' => $request->tanggal_akhir,
                'jenis' => $request->jenis,
            ])->setPaper('A4', 'portrait');


            return $pdf->stream('mutasi-rekening-' . now()->format('Ymd_His') . '.pdf');
        }
}


