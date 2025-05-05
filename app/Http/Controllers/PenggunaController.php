<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    public function index()
    {
        $penggunas = Pengguna::all();
        return view('pengguna.index', compact('penggunas'));
    }

    public function create()
    {
    return view('pengguna.create');
    }


    public function store(Request $request)
{
    $request->validate([
        'namapengguna' => 'required',
        'katakunci' => 'required',
    ]);

    // Ambil kode pengguna terakhir yang sudah ada di database
    $lastKodePengguna = DB::table('tpengguna')
        ->where('kodepengguna', 'like', 'U-%')
        ->orderBy('kodepengguna', 'desc')
        ->first();

    // Tentukan kode pengguna baru
    $newKodePengguna = 'U-' . str_pad(
        (intval(substr($lastKodePengguna->kodepengguna, 2)) + 1),  // Ambil angka setelah 'U-' dan tambah 1
        3,  // Total digit yang diinginkan (misalnya '000', '001', ...)
        '0',  // Tambahkan 0 di depan jika angka kurang dari 3 digit
        STR_PAD_LEFT
    );

    // Insert data pengguna baru
    DB::table('tpengguna')->insert([
        'kodepengguna' => $newKodePengguna,  // Kode pengguna baru
        'namapengguna' => $request->namapengguna,
        'katakunci' => bcrypt($request->katakunci),
        'status' => 'admin',  // Status tetap 'admin'
        'aktif' => $request->aktif,  // Nilai aktif
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/')->with('success', 'Pengguna berhasil ditambahkan.');
}


}
