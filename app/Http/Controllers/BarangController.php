<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Tampilkan halaman data barang + form tambah dengan nextCode
    public function index()
    {
        $barang = Barang::where('aktif', 1)->get();

        $nextCode = $this->getNextKodeBarang();

        return view('barang', compact('barang', 'nextCode'));
    }

    // Simpan data barang baru
    public function store(Request $request)
    {
        $request->validate([
            'namabarang' => 'required|string|max:255',
            'hbeli' => 'required|numeric|min:0',
            'hjual' => 'required|numeric|min:0',
            'konversi' => 'required|numeric|min:0',
        ]);

        // Pastikan kodebarang unik dengan generate lagi sebelum insert
        $kodebarang = $this->getNextKodeBarang();

        Barang::create([
            'kodebarang' => $kodebarang,
            'namabarang' => $request->namabarang,
            'hbeli' => $request->hbeli,
            'hjual' => $request->hjual,
            'konversi' => $request->konversi,
            'aktif' => $request->has('aktif') ? 1 : 0,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    // Fungsi untuk generate kode barang unik dan berurutan
    private function getNextKodeBarang()
    {
        $existingCodes = Barang::pluck('kodebarang')->toArray();

        $usedNumbers = array_map(function ($code) {
            return intval(substr($code, 2));
        }, $existingCodes);

        for ($i = 1; $i <= 999; $i++) {
            if (!in_array($i, $usedNumbers)) {
                return 'B-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }

        throw new \Exception("Kode barang penuh, tidak bisa menambah data baru.");
    }

    public function update(Request $request, $kodebarang)
    {
    // Validasi input
    $request->validate([
        'namabarang' => 'required|string|max:255',
        'hbeli' => 'required|numeric|min:0',
        'hjual' => 'required|numeric|min:0',
        'konversi' => 'required|numeric|min:0',
    ]);

    // Cari data berdasarkan kodebarang
    $barang = Barang::where('kodebarang', $kodebarang)->firstOrFail();

    // Update data
    $barang->update([
        'namabarang' => $request->namabarang,
        'hbeli' => $request->hbeli,
        'hjual' => $request->hjual,
        'konversi' => $request->konversi,
        'aktif' => $request->has('aktif') ? 1 : 0,
    ]);

    return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui');
    }

    // app/Http/Controllers/BarangController.php

    public function destroy($kodebarang)
    {
        // Cari data barang berdasarkan kodebarang
        $barang = Barang::where('kodebarang', $kodebarang)->first();

        if (!$barang) {
            return response()->json(['message' => 'Data barang tidak ditemukan'], 404);
        }

        // Hapus data barang
        $barang->delete();

        return response()->json(['message' => 'Data berhasil dihapus'], 200);
    }




}
