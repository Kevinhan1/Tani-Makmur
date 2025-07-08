<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\PenggunaController;

use App\Http\Controllers\BiayaController;
use App\Http\Controllers\PindahSaldoController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembayaranPembelianController;
use App\Http\Controllers\PembayaranPenjualanController;

use App\Http\Controllers\MutasiRekeningController;
use App\Http\Controllers\MutasiStokController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\LaporanPenjualanController;


// Landing page (halaman publik)
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute untuk halaman login dan proses login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');




// Rute untuk halaman dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

//Master
//Barang

Route::resource('barang', BarangController::class);


//Pemasok
Route::resource('pemasok', PemasokController::class);


//Pelanggan
Route::resource('pelanggan', PelangganController::class);


//Rekening
Route::resource('rekening', RekeningController::class);


//Pengguna
Route::resource('pengguna', PenggunaController::class);





//Transaksi 
// Biaya
Route::get('/biaya/pdf', [BiayaController::class, 'exportPdf'])->name('biaya.pdf');
Route::resource('biaya', BiayaController::class);


// Pindah Saldo 
Route::get('/pindahsaldo/pdf', [PindahSaldoController::class, 'exportPdf'])->name('pindahsaldo.pdf');
Route::resource('pindahsaldo', PindahSaldoController::class);


// Pembelian
Route::resource('/pembelian', PembelianController::class);
Route::get('/pembelian/{notabeli}/invoice', [PembelianController::class, 'cetakInvoice']);
Route::get('api/history-pembelian', [PembelianController::class, 'getHistory']);
Route::post('/api/history-pembelian/delete', [PembelianController::class, 'deleteHistory']);


// Pembayaran Pembelian 
Route::resource('/pembayaran-pembelian', PembayaranPembelianController::class);
Route::get('/pembayaran-pembelian/history/{notabeli}', [PembayaranPembelianController::class, 'getHistory']);
Route::get('/pembayaran-pembelian/data/{no}', [PembayaranPembelianController::class, 'show']);
Route::get('/pembayaran-pembelian/data/{no}', [PembayaranPembelianController::class, 'getData']);
Route::put('/pembayaran-pembelian/{no}', [PembayaranPembelianController::class, 'update'])->name('pembayaran-pembelian.update');
Route::delete('/pembayaran-pembelian/{no}', [PembayaranPembelianController::class, 'destroy'])->name('pembayaran-pembelian.destroy');


// Penjualan
Route::resource('/penjualan', PenjualanController::class);
Route::get('/penjualan/{notajual}/invoice', [PenjualanController::class, 'cetakInvoice'])->name('penjualan.invoice');
Route::get('/api/history-penjualan', [PenjualanController::class, 'getHistory']);
Route::post('/api/history-penjualan/delete', [PenjualanController::class, 'deleteHistory']);


// Pembayaran Penjualan
Route::resource('/pembayaran-penjualan', PembayaranPenjualanController::class);
Route::get('/pembayaran-penjualan/history/{notajual}', [PembayaranPenjualanController::class, 'getHistory']);
Route::get('/pembayaran-penjualan/data/{no}', [PembayaranPenjualanController::class, 'getData']);
Route::put('/pembayaran-penjualan/{no}', [PembayaranPenjualanController::class, 'update'])->name('pembayaran-penjualan.update');
Route::delete('/pembayaran-penjualan/{no}', [PembayaranPenjualanController::class, 'destroy'])->name('pembayaran-penjualan.destroy');





//laporan 
//Mutasi Rekening
Route::get('/mutasi-rekening', [MutasiRekeningController::class, 'index'])->name('mutasirekening.index');
Route::get('mutasirekening/pdf', [MutasiRekeningController::class, 'exportPdf'])->name('mutasirekening.pdf');


// Mutasi Stok
Route::get('/mutasi-stok', [MutasiStokController::class, 'index'])->name('mutasi-stok.index');
Route::get('/mutasi-stok/pdf', [MutasiStokController::class, 'exportPdf'])->name('mutasi-stok.pdf');


// Kas
Route::get('/kas', [BarangController::class, 'index'])->name('kas.index');
Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
Route::get('/kas/pdf', [KasController::class, 'exportPdf'])->name('kas.pdf');



// Piutang
Route::get('/piutang', [PiutangController::class, 'index'])->name('piutang.index');



// Laporan-Penjualan
Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan-penjualan.index');
Route::get('/laporan-penjualan/detail/{notajual}', [LaporanPenjualanController::class, 'detail']);