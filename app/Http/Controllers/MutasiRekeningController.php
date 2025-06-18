<?php

// app/Http/Controllers/MutasiRekeningController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MutasiRekening;
use App\Models\Rekening;
class MutasiRekeningController extends Controller
{
    public function index(Request $request)
{
    // Mulai query dasar
    $query = \App\Models\MutasiRekening::orderBy('tanggal', 'desc');

    // Jika ada pencarian, filter berdasarkan kolom
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('tanggal', 'like', "%$search%")
                ->orWhere('noreferensi', 'like', "%$search%")         
                ->orWhere('masuk', 'like', "%$search%")
                ->orWhere('keluar', 'like', "%$search%")
                ->orWhere('jenis', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
        })
        ->orWhereHas('rekening', function ($q) use ($search) {
            $q->where('namarekening', 'like', "%$search%");
        });
    }

    // Ambil hasil paginasi
    $mutasi = $query->paginate(20)->withQueryString();

        // Kirim ke view
        return view('mutasirekening', compact('mutasi'));
    }
}


