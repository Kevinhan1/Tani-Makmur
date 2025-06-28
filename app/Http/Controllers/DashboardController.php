<?php

namespace App\Http\Controllers;

use App\Models\Hjual;
use App\Models\Djual;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Hari ini
        $today = Carbon::today();
        $penjualanHariIni = Hjual::whereDate('tanggal', $today);
        $totalHariIni = $penjualanHariIni->sum('total');
        $transaksiHariIni = $penjualanHariIni->count();
        $produkTerjualHariIni = Djual::whereHas('header', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })->sum('qty');

        // Bulan ini
        $bulanIni = Carbon::now()->format('Y-m');
        $penjualanBulanIni = Hjual::where('tanggal', 'like', "$bulanIni-%");
        $totalBulanIni = $penjualanBulanIni->sum('total');
        $transaksiBulanIni = $penjualanBulanIni->count();
        $produkTerjualBulanIni = Djual::whereHas('header', function ($q) use ($bulanIni) {
            $q->where('tanggal', 'like', "$bulanIni-%");
        })->sum('qty');

        // Produk terlaris top 3 bulan ini
        $topProduk = Djual::with('detailBeli.barang')
            ->whereHas('header', function ($q) use ($bulanIni) {
                $q->where('tanggal', 'like', "$bulanIni-%");
            })
            ->get()
            ->groupBy(fn($item) => optional($item->detailBeli)->kodebarang)
            ->map(function ($items, $kodebarang) {
                return [
                    'kodebarang' => $kodebarang,
                    'qty' => $items->sum('qty'),
                    'namabarang' => optional($items->first()->detailBeli->barang)->namabarang ?? $kodebarang,
                ];
            })
            ->sortByDesc('qty')
            ->take(3)
            ->values();

        $produkChart = $topProduk->map(function ($item) {
            return [
                'nama' => $item['namabarang'],
                'qty' => $item['qty'],
            ];
        });

        // Tambahkan jika < 3
        while ($produkChart->count() < 3) {
            $produkChart->push(['nama' => '-', 'qty' => 0]);
        }

        return view('dashboard', [
            'totalHariIni' => $totalHariIni,
            'transaksiHariIni' => $transaksiHariIni,
            'produkTerjualHariIni' => $produkTerjualHariIni,
            'totalBulanIni' => $totalBulanIni,
            'transaksiBulanIni' => $transaksiBulanIni,
            'produkTerjualBulanIni' => $produkTerjualBulanIni,
            'produkChart' => $produkChart,
        ]);
    }
}
