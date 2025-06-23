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


//Pengguna
Route::resource('pengguna', PenggunaController::class);





//Transaksi 
// Biaya
Route::resource('biaya', BiayaController::class);


// Pindah Buku
Route::resource('pindahsaldo', PindahSaldoController::class);


// Pembelian
Route::resource('/pembelian', PembelianController::class);
Route::get('/pembelian/{notabeli}/invoice', [PembelianController::class, 'cetakInvoice']);

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

// Pembayaran Penjualan
Route::resource('/pembayaran-penjualan', PembayaranPenjualanController::class);
Route::get('/pembayaran-penjualan/history/{notajual}', [PembayaranPenjualanController::class, 'getHistory']);
Route::get('/pembayaran-penjualan/data/{no}', [PembayaranPenjualanController::class, 'getData']);
Route::put('/pembayaran-penjualan/{no}', [PembayaranPenjualanController::class, 'update'])->name('pembayaran-penjualan.update');
Route::delete('/pembayaran-penjualan/{no}', [PembayaranPenjualanController::class, 'destroy'])->name('pembayaran-penjualan.destroy');





//laporan 
//Mutasi Rekening
Route::get('/mutasi-rekening', [MutasiRekeningController::class, 'index'])->name('mutasirekening.index');


// Mutasi Stok
Route::get('/mutasi-stok', [MutasiStokController::class, 'index'])->name('mutasi-stok.index');
Route::get('/mutasi-stok/pdf', [MutasiStokController::class, 'exportPdf'])->name('mutasi-stok.pdf');

Route::get('/kas', [BarangController::class, 'index'])->name('kas.index');
Route::get('/piutang', [BarangController::class, 'index'])->name('piutang.index');
Route::get('/laporan-penjualan', [BarangController::class, 'index'])->name('laporanpenjualan.index');


