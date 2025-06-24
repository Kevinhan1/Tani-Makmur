<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Djual;
use App\Models\Dbeli;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;

class MutasiStokController extends Controller
{
    public function index(Request $request)
    {   
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

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

        $mutasi = $this->getMutasiStok($tanggalAwal, $tanggalAkhir, $jenis, false); // tanpa pagination

        $pdf = Pdf::loadView('mutasi-stok-pdf', compact('mutasi', 'tanggalAwal', 'tanggalAkhir', 'jenis'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('mutasi-stok-' . now()->format('Ymd_His') . '.pdf');
    }

        private function getMutasiStok($tanggalAwal, $tanggalAkhir, $jenis, $paginate = true)
    {
        $data = collect();

        if (!$jenis || strtolower($jenis) === 'pembelian') {
            $pembelian = Dbeli::with(['barang', 'header'])
                ->whereHas('header', function ($query) use ($tanggalAwal, $tanggalAkhir) {
                    $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            foreach ($pembelian as $item) {
                $data->push([
                    'tanggal' => $item->header->tanggal ?? '-',
                    'nota' => $item->notabeli,
                    'noref' => $item->noref,
                    'namabarang' => optional($item->barang)->namabarang ?? '-',
                    'masuk' => $item->qty + $item->qtyjual, // ✅ ambil total pembelian
                    'keluar' => 0,
                    'jenis' => 'Pembelian',
                    'keterangan' => 'Pembelian Barang - '. $item->notabeli,
                ]);
            }
        }

        if (!$jenis || strtolower($jenis) === 'penjualan') {
            $penjualan = Djual::with(['header', 'detailBeli.barang'])
                ->whereHas('header', function ($query) use ($tanggalAwal, $tanggalAkhir) {
                    $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);
                })
                ->get();

            foreach ($penjualan as $item) {
                $data->push([
                    'tanggal' => $item->header->tanggal ?? '-',
                    'nota' => $item->notajual,
                    'noref' => $item->noref,
                    'namabarang' => optional($item->detailBeli->barang)->namabarang ?? '-',
                    'masuk' => 0,
                    'keluar' => $item->qty,
                    'jenis' => 'Penjualan',
                    'keterangan' => 'Penjualan Barang - '. $item->notajual,
                ]);
            }
        }
        
        // Gabungkan dan urutkan
        $sorted = $data->map(function ($item) {
            $item['tanggal'] = \Carbon\Carbon::parse($item['tanggal']);
            return $item;
        })->sortByDesc('tanggal')->values();
        $sorted = $data->sortByDesc('tanggal')->values();

        if ($paginate) {
            $page = request('page', 1);
            $perPage = 15;
            return new LengthAwarePaginator(
                $sorted->forPage($page, $perPage),
                $sorted->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return $sorted;
        }

    }


