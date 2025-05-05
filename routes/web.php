<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AuthController; // Pastikan untuk meng-import AuthController
use App\Http\Controllers\Auth\LoginController;


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