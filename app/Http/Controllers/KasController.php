<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiRekening;
use App\Models\Rekening;
use Barryvdh\DomPDF\Facade\Pdf;

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

								if ($request->filled('rekening')) {
												$rekening = Rekening::where('koderekening', $request->rekening)->first();
												$saldoAwal = $rekening?->saldo ?? 0;

												$query->where('koderekening', $request->rekening);
								}

								if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
												$query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
								}

								if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

								$kas = $query->paginate(15)->withQueryString();

								return view('kas', compact('kas', 'rekeningList', 'saldoAwal'));
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

        $kas = $query->get();

        $pdf = Pdf::loadView('kas.kas-pdf', [
            'kas' => $kas,
            'tanggalAwal' => $request->tanggal_awal,
            'tanggalAkhir' => $request->tanggal_akhir,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('kas-' . now()->format('Ymd_His') . '.pdf');
    }
}
