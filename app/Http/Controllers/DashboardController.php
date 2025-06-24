<?php

namespace App\Http\Controllers;

use App\Models\Hjual;
use App\Models\Djual;
use Illuminate\Support\Carbon;
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

    // Produk terlaris bulan ini
    $produkTerlaris = Djual::select('noref')
    ->selectRaw('SUM(qty) as total_qty')
    ->groupBy('noref')
    ->orderByDesc('total_qty')
    ->with('detailBeli.barang') // eager load sampai ke tbarang
    ->first();

				$produkNama = ($produkTerlaris && $produkTerlaris->detailBeli && $produkTerlaris->detailBeli->barang)
								? $produkTerlaris->detailBeli->barang->namabarang
								: '-';


    return view('dashboard', [
        'totalHariIni' => $totalHariIni,
        'transaksiHariIni' => $transaksiHariIni,
        'produkTerjualHariIni' => $produkTerjualHariIni,
        'totalBulanIni' => $totalBulanIni,
        'transaksiBulanIni' => $transaksiBulanIni,
        'produkTerjualBulanIni' => $produkTerjualBulanIni,
        'produkTerlaris' => $produkNama,
    ]);
}
}