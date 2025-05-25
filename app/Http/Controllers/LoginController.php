<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // Buat view login.blade.php
    }

					public function login(Request $request)
		{
						$request->validate([
										'namapengguna' => 'required',
										'katakunci' => 'required',
						]);

						$pengguna = DB::table('tpengguna')
										->where('namapengguna', $request->namapengguna)
										->first();

						if ($pengguna && password_verify($request->katakunci, $pengguna->katakunci)) {
										session([
														'kodepengguna' => $pengguna->kodepengguna,
														'user' => $pengguna,
										]);
										return redirect('/dashboard')->with('success', 'Login berhasil!');
						}

						return redirect()->back()->with('error', 'Nama pengguna atau kata kunci salah');
		}


    public function logout()
    {
        session()->flush();
        return redirect('/login')->with('success', 'Anda berhasil logout');
    }
}
