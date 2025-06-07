<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AuthController; // Pastikan untuk meng-import AuthController
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\BiayaController;
use App\Http\Controllers\PindahSaldoController;
use App\Http\Controllers\MutasiRekeningController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembelianController;


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
Route::resource('barang', BarangController::class);

//Pemasok
Route::resource('pemasok', PemasokController::class);

//Pelanggan
Route::resource('pelanggan', PelangganController::class);
//Rekening
Route::resource('rekening', RekeningController::class);


//Transaksi 
// Biaya
Route::resource('biaya', BiayaController::class);

// Pindah Buku
Route::resource('pindahsaldo', PindahSaldoController::class);

Route::resource('/penjualan', PenjualanController::class);
Route::resource('/pembelian', PembelianController::class);
Route::get('/pembayaran-pembelian', [BarangController::class, 'index'])->name('pembayaranpembelian.index');
Route::get('/pembayaran-penjualan', [BarangController::class, 'index'])->name('pembayaranpenjualan.index');

//laporan 
//Mutasi Rekening
Route::get('/mutasi-rekening', [MutasiRekeningController::class, 'index'])->name('mutasirekening.index');



Route::get('/mutasi-stok', [BarangController::class, 'index'])->name('mutasistok.index');
Route::get('/kas', [BarangController::class, 'index'])->name('kas.index');
Route::get('/piutang', [BarangController::class, 'index'])->name('piutang.index');
Route::get('/laporan-penjualan', [BarangController::class, 'index'])->name('laporanpenjualan.index');


