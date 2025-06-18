<?php

namespace App\Http\Controllers;

use App\Models\PindahSaldo;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PindahSaldoController extends Controller
{
    // Tampilkan daftar pindah saldo
    public function index(Request $request)
{
    // Query awal dengan relasi
    $query = PindahSaldo::with(['rekeningAsal', 'rekeningTujuan', 'pengguna'])
        ->orderBy('tanggal', 'desc')
        ->orderBy('nopindahbuku', 'desc');

    // Pencarian jika ada kata kunci
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('nopindahbuku', 'like', "%$search%")
                ->orWhere('tanggal', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%")
                ->orWhere('total', 'like', "%$search%");
        })
        ->orWhereHas('rekeningAsal', function ($q) use ($search) {
            $q->where('namarekening', 'like', "%$search%");
        })
        ->orWhereHas('rekeningTujuan', function ($q) use ($search) {
            $q->where('namarekening', 'like', "%$search%");
        })
        ->orWhereHas('pengguna', function ($q) use ($search) {
            $q->where('namapengguna', 'like', "%$search%");
        });
    }

    // Data hasil query (pagination)
    $pindahsaldo = $query->paginate(10)->withQueryString();

    // Data rekening
    $rekening = Rekening::all();

    // Kode otomatis
    $nextCode = $this->generateKodePindahSaldo(date('Y-m-d'));

    return view('pindahsaldo', compact('pindahsaldo', 'rekening', 'nextCode'));
}

    // Generate kode pindah buku otomatis sesuai format PB-YYYYMMDD-XXX
    protected function generateKodePindahSaldo($tanggal)
    {
        // Ubah tanggal ke format yyyymmdd
        $datePart = date('Ymd');

        // Ambil entri terakhir hari itu
        $last = PindahSaldo::where('nopindahbuku', 'like', "PB-{$datePart}-%")
                    ->orderBy('nopindahbuku', 'desc')
                    ->first();

        if (!$last) {
            $noUrut = 1;
        } else {
            // Ambil 3 digit terakhir dari kode terakhir (misal: PB-20250611-002 → 002)
            $lastNumber = (int) substr($last->nopindahbuku, -3);
            $noUrut = $lastNumber + 1;
        }

        // Format ke 3 digit
        $noUrutFormatted = str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        return "PB-{$datePart}-{$noUrutFormatted}";
    }


    // Simpan data pindah saldo baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'rekeningasal' => 'required|string|exists:trekening,koderekening',
            'rekeningtujuan' => 'required|string|exists:trekening,koderekening|different:rekeningasal',
            'keterangan' => 'required|string',
            'total' => 'required|numeric|min:0.01',
        ]);

        $user = session('user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kodepengguna = $user->kodepengguna;

        // Buat kode nomor pindah saldo
        $nopindahbuku = $this->generateKodePindahSaldo($request->tanggal);

        PindahSaldo::create([
            'nopindahbuku' => $nopindahbuku,
            'tanggal' => $request->tanggal,
            'koderekeningasal' => $request->rekeningasal,
            'koderekeningtujuan' => $request->rekeningtujuan,
            'keterangan' => $request->keterangan,
            'total' => $request->total,
            'kodepengguna' => $kodepengguna,
        ]);

        return redirect()->route('pindahsaldo.index')->with('success', 'Data berhasil disimpan');
    }

    // Update data pindah saldo
    public function update(Request $request, $nopindahbuku)
{
    $request->validate([
        'tanggal' => 'required|date',
        'rekeningasal' => 'required|exists:trekening,koderekening',
        'rekeningtujuan' => 'required|exists:trekening,koderekening|different:rekeningasal',
        'keterangan' => 'required|string',
        'total' => 'required|numeric|min:0.01',
    ]);

    // Ambil data pindah saldo yang lama
    $pindahSaldo = PindahSaldo::findOrFail($nopindahbuku);

    // Update hanya data, saldo akan ditangani oleh trigger dari tmutasirekening
    $pindahSaldo->update([
        'tanggal' => $request->tanggal,
        'koderekeningasal' => $request->rekeningasal,
        'koderekeningtujuan' => $request->rekeningtujuan,
        'keterangan' => $request->keterangan,
        'total' => $request->total,
    ]);

    return redirect()->route('pindahsaldo.index')->with('success', 'Data pindah saldo berhasil diupdate.');
}



    // Hapus satu atau beberapa data pindah saldo
    public function destroy($nopindahbuku)
    {
        $keys = is_array($nopindahbuku) ? $nopindahbuku : explode(',', $nopindahbuku);

        foreach ($keys as $key) {
            $pindahSaldo = PindahSaldo::find($key);
            if ($pindahSaldo) {
                $pindahSaldo->delete();
            }
        }

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
