<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('pengguna.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'namapengguna' => 'required|string|max:255',
            'katakunci' => 'required|string|min:6',
        ]);

        // Ambil data pengguna dari database berdasarkan nama pengguna
        $user = DB::table('tpengguna')->where('namapengguna', $request->namapengguna)->first();

        // Jika pengguna ditemukan dan kata kunci cocok
        if ($user && Hash::check($request->katakunci, $user->katakunci)) {
            // Menyimpan data pengguna di session (untuk login)
            Session::put('user', $user);

            // Redirect ke dashboard atau halaman utama
            return redirect()->route('dashboard');
        }

        // Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->with('error', 'Login salah, periksa nama pengguna atau kata kunci Anda.');
    }

    // Menangani logout
    public function logout()
    {
        // Menghapus session
        Session::flush();
        return redirect()->route('login');
    }
}
