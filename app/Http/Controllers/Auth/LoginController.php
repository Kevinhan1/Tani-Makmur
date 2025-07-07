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
    public function showLoginForm(Request $request)
    {
        // $allowedIps = explode(',', env('ALLOWED_IPS'));
        // $clientIp = $request->ip();

        // if (!in_array($clientIp, $allowedIps)) {
        //     abort(404, 'This page could not be found.');
        // }

        return view('pengguna.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // $allowedIps = explode(',', env('ALLOWED_IPS'));
        // $clientIp = $request->ip();

        // if (!in_array($clientIp, $allowedIps)) {
        //     abort(403, 'Akses ditolak. IP Anda tidak diizinkan.');
        // }

        $request->validate([
            'namapengguna' => 'required|string|max:255',
            'katakunci' => 'required|string|min:6',
        ]);

        $user = DB::table('tpengguna')->where('namapengguna', $request->namapengguna)->first();

        if (!$user || !Hash::check($request->katakunci, $user->katakunci)) {
            return back()->withInput()->with('error', 'Login salah, periksa nama pengguna atau kata kunci Anda.');
        }

        if ($user->aktif != 1) {
            return back()->withInput()->with('error', 'Akun Anda tidak aktif.');
        }

        Session::put('user', $user);
        return redirect()->route('dashboard');
    }

    // Menangani logout
    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
