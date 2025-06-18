<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BiayaController extends Controller
{
    // Tampilkan daftar biaya
public function index(Request $request)
{
    $query = Biaya::with(['rekening', 'pengguna'])->orderBy('nobiaya', 'desc');

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('nobiaya', 'like', "%$search%")
                ->orWhere('tanggal', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%")
                ->orWhere('total', 'like', "%$search%");
            })
        ->orWhereHas('rekening', function ($q) use ($search) {
            $q->where('namarekening', 'like', "%$search%");
        })
        ->orWhereHas('pengguna', function ($q) use ($search) {
            $q->where('namapengguna', 'like', "%$search%");
        });
    }

    $biaya = $query->paginate(10)->withQueryString(); // ← tetap bawa keyword saat pindah halaman
    $rekening = Rekening::orderBy('namarekening')->get();
    $nextCode = $this->generateKodeBiaya();

    return view('biaya', compact('biaya', 'rekening', 'nextCode'));
}

    // Generate nobiaya otomatis: BY-yyyymmdd-001 (pakai tanda - bukan /)
    private function generateKodeBiaya()
    {
        $datePart = date('Ymd');

        // Cari kode nobiaya terakhir hari ini
        $lastBiaya = Biaya::where('nobiaya', 'like', "BY-{$datePart}-%")
            ->orderBy('nobiaya', 'desc')
            ->first();

        if (!$lastBiaya) {
            $number = 1;
        } else {
            // Ambil 3 digit terakhir angka urut, misal BY-20250524-001 => 001
            $lastNumber = (int) substr($lastBiaya->nobiaya, -3);
            $number = $lastNumber + 1;
        }

        return 'BY-' . $datePart . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // Simpan data biaya baru
    public function store(Request $request)
    {
        $request->validate([
            'nobiaya' => 'required|unique:tbiaya,nobiaya',
            'tanggal' => 'required|date',
            'koderekening' => 'required',
            'keterangan' => 'nullable|string|max:255',
            'total' => 'required|numeric',
        ]);

        // Ambil data user yang login dari session
        $user = Session::get('user');

        // Gabungkan data request dengan kodepengguna dari user login
        $data = $request->all();
        $data['kodepengguna'] = $user->kodepengguna;

        Biaya::create($data);

        return redirect()->route('biaya.index')->with('success', 'Data biaya berhasil disimpan.');
    }


    // Tampilkan form edit
    public function edit($nobiaya)
    {
        $biaya = Biaya::where('nobiaya', $nobiaya)->firstOrFail();
        $rekening = Rekening::orderBy('namarekening')->get();
        return view('biaya.edit', compact('biaya', 'rekening'));
    }

    // Update data biaya
    public function update(Request $request, $nobiaya)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'koderekening' => 'required',
            'keterangan' => 'nullable|string|max:255',
            'total' => 'required|numeric',
        ]);

        $biaya = Biaya::where('nobiaya', $nobiaya)->firstOrFail();
        $biaya->update($request->except('nobiaya'));

        return redirect()->route('biaya.index')->with('success', 'Data biaya berhasil diupdate.');
    }

    // Hapus data biaya
    public function destroy($nobiaya)
    {
        $biaya = Biaya::where('nobiaya', $nobiaya)->firstOrFail();
        $biaya->delete();

        return response()->json(['message' => 'Data biaya berhasil dihapus']);
    }
}