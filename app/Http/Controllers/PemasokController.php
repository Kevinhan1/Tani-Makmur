<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PemasokController extends Controller
{
    // Menampilkan halaman dan data pemasok
    public function index(Request $request)
    {   
        $user = Session::get('user');
        
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        if (!$user || $user->status !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
    
        $query = \App\Models\Pemasok::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('kodepemasok', 'like', '%' . $search . '%')
            ->orWhere('namapemasok', 'like', '%' . $search . '%')
            ->orWhere('alamatpemasok', 'like', '%' . $search . '%');;
        });
    }

    $pemasok = $query->paginate(15);

        // Generate next kodepemasok misal S-001, S-002 dst
        $lastKode = Pemasok::orderBy('kodepemasok', 'desc')->first()?->kodepemasok;
        $nextCode = $this->generateNextKode($lastKode);

        return view('pemasok', compact('pemasok', 'nextCode'));
    }

    // Simpan pemasok baru
    public function store(Request $request)
    {
        $request->validate([
            'kodepemasok' => 'required|unique:tpemasok,kodepemasok',
            'namapemasok' => 'required|string|max:25',
            'alamatpemasok' => 'nullable|string|max:255',
            'aktif' => 'nullable|in:0,1',
        ]);

        Pemasok::create([
            'kodepemasok' => $request->kodepemasok,
            'namapemasok' => $request->namapemasok,
            'alamatpemasok' => $request->alamatpemasok,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil disimpan.');
    }

    // Update data pemasok
    public function update(Request $request, $kodepemasok)
    {
        $pemasok = Pemasok::findOrFail($kodepemasok);

        $request->validate([
            'namapemasok' => 'required|string|max:25',
            'alamatpemasok' => 'nullable|string|max:255',
            'aktif' => 'nullable|in:0,1',
        ]);

        $pemasok->update([
            'namapemasok' => $request->namapemasok,
            'alamatpemasok' => $request->alamatpemasok,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Data pemasok berhasil diperbarui.');
    }

    // Hapus data pemasok
    public function destroy($kodepemasok)
    {
        $pemasok = Pemasok::findOrFail($kodepemasok);
        $pemasok->delete();

        return response()->json(['message' => 'Data pemasok berhasil dihapus']);
    }

    // Fungsi generate next kodepemasok dengan format S-001, S-002 dst
    private function generateNextKode($lastKode)
    {
        if (!$lastKode) {
            return 'S-001';
        }

        // Pisah 'S-' dan nomor
        $number = (int) substr($lastKode, 2);
        $number++;

        return 'S-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}