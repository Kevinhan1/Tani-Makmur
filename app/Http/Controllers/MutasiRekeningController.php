<?php

// app/Http/Controllers/MutasiRekeningController.php
namespace App\Http\Controllers;

use App\Models\MutasiRekening;

class MutasiRekeningController extends Controller
{
    public function index()
    {
        $mutasi = MutasiRekening::orderBy('tanggal', 'desc')->paginate(20); // atau tanpa paginate jika tidak perlu
        return view('mutasirekening', compact('mutasi'));
    }
}
