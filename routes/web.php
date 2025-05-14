<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AuthController; // Pastikan untuk meng-import AuthController
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;



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
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');


Route::get('/pemasok', [BarangController::class, 'index'])->name('pemasok.index');
Route::get('/pelanggan', [BarangController::class, 'index'])->name('pelanggan.index');
Route::get('/rekening', [BarangController::class, 'index'])->name('rekening.index');

//laporan 
Route::get('/mutasirekening', [BarangController::class, 'index'])->name('mutasirekening.index');
Route::get('/mutasistok', [BarangController::class, 'index'])->name('mutasistok.index');
Route::get('/kas', [BarangController::class, 'index'])->name('kas.index');
Route::get('/piutang', [BarangController::class, 'index'])->name('piutang.index');
Route::get('/laporanpenjualan', [BarangController::class, 'index'])->name('laporanpenjualan.index');


//Transaksi 
Route::get('/biaya', [BarangController::class, 'index'])->name('biaya.index');
Route::get('/pindahsaldorekening', [BarangController::class, 'index'])->name('pindahsaldorekening.index');
Route::get('/penjualan', [BarangController::class, 'index'])->name('penjualan.index');
Route::get('/pembelian', [BarangController::class, 'index'])->name('pembelian.index');
Route::get('/pembayaranpembelian', [BarangController::class, 'index'])->name('pembayaranpembelian.index');
Route::get('/pembayaranpenjualan', [BarangController::class, 'index'])->name('pembayaranpenjualan.index');