<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::all();

        // Generate kode otomatis
        $last = Pelanggan::orderBy('kodepelanggan', 'desc')->first();
        $nextCode = 'P-001';
        if ($last) {
            $num = intval(substr($last->kodepelanggan, 2)) + 1;
            $nextCode = 'P-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
								
        return view('pelanggan', compact('pelanggan', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodepelanggan' => 'required|unique:tpelanggan',
            'namapelanggan' => 'required',
        ]);

        Pelanggan::create([
            ...$request->except('aktif'),
            'aktif' => $request->has('aktif') ? 1 : 0
        ]);

        return redirect()->back();
    }

    public function update(Request $request, $kode)
    {
        $data = Pelanggan::where('kodepelanggan', $kode)->firstOrFail();

        $data->update([
            ...$request->except('aktif'),
            'aktif' => $request->has('aktif') ? 1 : 0
        ]);

        return redirect()->back();
    }

    public function destroy($kode)
    {
        Pelanggan::where('kodepelanggan', $kode)->delete();
        return response()->json(['success' => true]);
    }
}
