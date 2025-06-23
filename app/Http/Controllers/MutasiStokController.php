<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Djual;
use App\Models\Dbeli;
use Barryvdh\DomPDF\Facade\Pdf;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->toDateString();
        $jenis = $request->jenis;

        $mutasi = $this->getMutasiStok($tanggalAwal, $tanggalAkhir, $jenis);

        return view('mutasi-stok', compact('mutasi', 'tanggalAwal', 'tanggalAkhir', 'jenis'));
    }

    public function exportPdf(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->toDateString();
        $jenis = $request->jenis;

        $mutasi = $this->getMutasiStok($tanggalAwal, $tanggalAkhir, $jenis);

        $pdf = Pdf::loadView('mutasi-stok-pdf', compact('mutasi', 'tanggalAwal', 'tanggalAkhir', 'jenis'))
            ->setPaper('A4', 'potrait');

        return $pdf->stream('mutasi-stok-' . now()->format('Ymd_His') . '.pdf');
    }

    private function getMutasiStok($tanggalAwal, $tanggalAkhir, $jenis)
{
    $mutasi = collect();

    // PENJUALAN
    if (!$jenis || strtolower($jenis) === 'penjualan') {
        $penjualan = Djual::with(['header', 'detailBeli.barang'])
            ->whereHas('header', function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            })
            ->get()
            ->map(function ($item) {
                $detailBeli = $item->detailBeli;
                return [
                    'nota' => $item->notajual,
                    'tanggal' => $item->header->tanggal ?? '-',
                    'noref' => $item->noref,
                    'namabarang' => optional($detailBeli->barang)->namabarang ?? '-',
                    'masuk' => 0,
                    'keluar' => $item->qty,
                    'jenis' => 'Penjualan',
                    'keterangan' => 'Penjualan Barang - '. $item->notajual,
                ];
            });

        $mutasi = $mutasi->merge($penjualan);
    }

    // PEMBELIAN
    if (!$jenis || strtolower($jenis) === 'pembelian') {
        $pembelian = Dbeli::with(['barang', 'header'])
            ->whereHas('header', function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'nota' => $item->notabeli,
                    'tanggal' => $item->header->tanggal ?? '-',
                    'noref' => $item->noref,
                    'namabarang' => optional($item->barang)->namabarang ?? '-',
                    'masuk' => $item->qty,
                    'keluar' => 0,
                    'jenis' => 'Pembelian',
                    'keterangan' => 'Pembelian Barang - '. $item->notabeli,
                ];
            });

        $mutasi = $mutasi->merge($pembelian);
    }

    return $mutasi->sortByDesc('tanggal');
}

}
