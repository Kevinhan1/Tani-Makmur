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
        $topProduk = Djual::select('noref', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('header', function ($q) use ($bulanIni) {
                $q->where('tanggal', 'like', "$bulanIni-%");
            })
            ->groupBy('noref')
            ->orderByDesc('total_qty')
            ->with('detailBeli.barang')
            ->take(3)
            ->get();

        $produkChart = $topProduk->map(function ($item) {
            return [
                'nama' => $item->detailBeli->barang->namabarang ?? '-',
                'qty' => $item->total_qty,
            ];
        });

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
