<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
class PenggunaController extends Controller
{
    public function index(Request $request)
    {   

        $user = Session::get('user');

        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        elseif (!$user || $user->status !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
        $query = Pengguna::query();

        if ($request->has('search')) {
            $query->where('namapengguna', 'like', '%' . $request->search . '%');
        }

        $pengguna = $query->get();
        $nextCode = $this->getNextKodePengguna();

        return view('pengguna', compact('pengguna', 'nextCode'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'namapengguna' => 'required',
        'katakunci' => 'required|min:4',
        'status' => 'required|in:admin,user,developer',
    ]);

    // Cek nama pengguna duplikat (case-insensitive)
    $existing = Pengguna::whereRaw('LOWER(namapengguna) = ?', [strtolower($validated['namapengguna'])])->first();
    if ($existing) {
        return back()->withInput()->withErrors(['namapengguna' => 'Nama Pengguna sudah digunakan.']);
    }

    $validated['kodepengguna'] = $this->getNextKodePengguna();
    $validated['katakunci'] = Hash::make($validated['katakunci']);
    $validated['aktif'] = $request->has('aktif') ? 1 : 0;

    Pengguna::create($validated);

    return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
}




    public function update(Request $request, $kodepengguna)
    {
        $pengguna = Pengguna::where('kodepengguna', $kodepengguna)->firstOrFail();

        $request->validate([
            'namapengguna' => 'required|unique:tpengguna,namapengguna,' . $pengguna->kodepengguna . ',kodepengguna',
            'status' => 'required|in:admin,user,developer',
        ]);

        $data = [
            'namapengguna' => $request->namapengguna,
            'status' => $request->status,
            'aktif' => $request->has('aktif') ? 1 : 0,
        ];

        if ($request->filled('katakunci')) {
            $data['katakunci'] = Hash::make($request->katakunci);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }


    

    public function destroy($kodepengguna)
    {
        $pengguna = Pengguna::where('kodepengguna', $kodepengguna)->firstOrFail();
        $pengguna->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus.']);
    }

    private function getNextKodePengguna()
    {
        $existingCodes = Pengguna::pluck('kodepengguna')->toArray();
        $usedNumbers = array_map(function ($code) {
            return intval(preg_replace('/[^0-9]/', '', $code));
        }, $existingCodes);

        for ($i = 1; $i <= 999; $i++) {
            if (!in_array($i, $usedNumbers)) {
                return 'U-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }

        throw new \Exception("Kode pengguna penuh.");
    }
}
