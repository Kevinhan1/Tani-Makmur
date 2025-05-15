<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AuthController; // Pastikan untuk meng-import AuthController
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\RekeningController;

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// Langsung arahkan ke halaman form tambah pengguna
Route::get('/daftar', [PenggunaController::class, 'create'])->name('pengguna.create');
Route::post('/daftar', [PenggunaController::class, 'store'])->name('pengguna.store');

// Rute untuk halaman login dan proses login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');




// Rute untuk halaman dashboard
Route::get('dashboard', function () {
    // Memastikan hanya pengguna yang sudah login yang bisa mengakses dashboard
    if (session()->has('user')) {
        return view('dashboard');
    } else {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    }
})->name('dashboard');

//Master
//Barang
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
Route::resource('barang', BarangController::class);
Route::put('/barang/{kodebarang}', [BarangController::class, 'update'])->name('barang.update');
Route::delete('/barang/{kodebarang}', [BarangController::class, 'destroy'])->name('barang.destroy');


//Pemasok
Route::get('/pemasok', [PemasokController::class, 'index'])->name('pemasok.index');
Route::post('/pemasok', [PemasokController::class, 'store'])->name('pemasok.store');
Route::resource('pemasok', PemasokController::class);
Route::put('/pemasok/{kodepemasok}', [PemasokController::class, 'update'])->name('pemasok.update');
Route::delete('/pemasok/{kodepemasok}', [PemasokController::class, 'destroy'])->name('pemasok.destroy');


//Pelanggan
Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
Route::post('/pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
Route::resource('pelanggan', PelangganController::class);
Route::put('/pelanggan/{kodepelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
Route::delete('/pelanggan/{kodepelanggan}', [PelangganController::class, 'destroy'])->name('pelanggan.destroy');

//Rekening
Route::get('/rekening', [RekeningController::class, 'index'])->name('rekening.index');
Route::post('/rekening', [RekeningController::class, 'store'])->name('rekening.store');
Route::resource('rekening', RekeningController::class);
Route::put('/rekening/{koderekening}', [RekeningController::class, 'update'])->name('rekening.update');
Route::delete('/rekening/{koderekening}', [RekeningController::class, 'destroy'])->name('rekening.destroy');




//laporan 
Route::get('/mutasi-rekening', [BarangController::class, 'index'])->name('mutasirekening.index');
Route::get('/mutasi-stok', [BarangController::class, 'index'])->name('mutasistok.index');
Route::get('/kas', [BarangController::class, 'index'])->name('kas.index');
Route::get('/piutang', [BarangController::class, 'index'])->name('piutang.index');
Route::get('/laporan-penjualan', [BarangController::class, 'index'])->name('laporanpenjualan.index');


//Transaksi 
Route::get('/biaya', [BarangController::class, 'index'])->name('biaya.index');
Route::get('/pindah-saldo-rekening', [BarangController::class, 'index'])->name('pindahsaldorekening.index');
Route::get('/penjualan', [BarangController::class, 'index'])->name('penjualan.index');
Route::get('/pembelian', [BarangController::class, 'index'])->name('pembelian.index');
Route::get('/pembayaran-pembelian', [BarangController::class, 'index'])->name('pembayaranpembelian.index');
Route::get('/pembayaran-penjualan', [BarangController::class, 'index'])->name('pembayaranpenjualan.index');