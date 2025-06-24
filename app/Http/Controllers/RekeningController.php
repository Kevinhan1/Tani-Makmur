<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use App\Models\MutasiRekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        } elseif (!$user || $user->status !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $query = Rekening::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('koderekening', 'like', '%' . $search . '%')
                    ->orWhere('namarekening', 'like', '%' . $search . '%');
            });
        }

        $rekening = $query->paginate(15);
        $nextCode = $this->getNextKodeRekening();

        return view('rekening', compact('rekening', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'namarekening' => 'required|string|max:50',
            'saldo' => 'required|numeric',
            'aktif' => 'nullable|boolean',
        ]);

        $kodeRekeningBaru = $this->getNextKodeRekening();

        // Saldo diset 0 karena akan dicatat sebagai mutasi
        $rekening = Rekening::create([
            'koderekening' => $kodeRekeningBaru,
            'namarekening' => $request->namarekening,
            'saldo' => 0,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        if ($request->saldo > 0) {
            $tanggal = Carbon::now()->format('Y-m-d');
            $bulanTahun = Carbon::now()->format('my');
            $prefix = 'MR/' . $bulanTahun . '/';

            $last = MutasiRekening::where('nogenerate', 'like', $prefix . '%')
                ->orderBy('nogenerate', 'desc')
                ->first();

            $nextNumber = $last ? ((int) substr($last->nogenerate, -4)) + 1 : 1;
            $nogenerate = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $tglKode = Carbon::now()->format('Ymd');
            $countRef = MutasiRekening::whereDate('tanggal', $tanggal)
                ->where('noreferensi', 'like', 'SM-' . $tglKode . '%')
                ->count() + 1;

            $noreferensi = 'SM-' . $tglKode . '-' . str_pad($countRef, 3, '0', STR_PAD_LEFT);

            MutasiRekening::create([
                'tanggal' => $tanggal,
                'koderekening' => $kodeRekeningBaru,
                'noreferensi' => $noreferensi,
                'nogenerate' => $nogenerate,
                'jenis' => 'Saldo Manual',
                'keterangan' => 'Saldo awal saat penambahan rekening',
                'masuk' => $request->saldo,
                'keluar' => 0,
            ]);
        }

        return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil ditambahkan');
    }

    public function update(Request $request, $koderekening)
    {
        $request->validate([
            'namarekening' => 'required|string|max:50',
            'saldo' => 'required|numeric',
            'aktif' => 'nullable|boolean',
        ]);

        $rekening = Rekening::findOrFail($koderekening);
        $saldoLama = $rekening->saldo;
        $saldoBaru = $request->saldo;

        $rekening->update([
            'namarekening' => $request->namarekening,
            'aktif' => $request->aktif ? 1 : 0,
            // HAPUS saldo jika trigger yang menangani saldo
            // 'saldo' => $saldoBaru,
        ]);

        $selisih = $saldoBaru - $saldoLama;

        if ($selisih != 0) {
            $tanggal = Carbon::now()->format('Y-m-d');
            $bulanTahun = Carbon::now()->format('my');
            $prefix = 'MR/' . $bulanTahun . '/';

            $last = MutasiRekening::where('nogenerate', 'like', $prefix . '%')
                ->orderBy('nogenerate', 'desc')
                ->first();

            $nextNumber = $last ? ((int) substr($last->nogenerate, -4)) + 1 : 1;
            $nogenerate = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $tglKode = Carbon::now()->format('Ymd');
            $countRef = MutasiRekening::whereDate('tanggal', $tanggal)
                ->where('noreferensi', 'like', 'SM-' . $tglKode . '%')
                ->count() + 1;

            $noreferensi = 'SM-' . $tglKode . '-' . str_pad($countRef, 3, '0', STR_PAD_LEFT);

            MutasiRekening::create([
                'tanggal' => $tanggal,
                'koderekening' => $rekening->koderekening,
                'noreferensi' => $noreferensi,
                'nogenerate' => $nogenerate,
                'jenis' => 'Saldo Manual',
                'keterangan' => 'Penyesuaian Saldo Manual oleh Admin',
                'masuk' => $selisih > 0 ? $selisih : 0,
                'keluar' => $selisih < 0 ? abs($selisih) : 0,
            ]);
        }

        return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil diubah');
    }

    public function destroy($koderekening)
    {
        $rekening = Rekening::findOrFail($koderekening);
        $rekening->delete();

        return response()->json(['success' => true]);
    }

    // 🔑 Fungsi mirip getNextKodeBarang()
    private function getNextKodeRekening()
    {
        $existingCodes = Rekening::pluck('koderekening')->toArray();

        $usedNumbers = array_map(function ($code) {
            return intval(substr($code, 2)); // hilangkan "R-"
        }, $existingCodes);

        for ($i = 1; $i <= 999; $i++) {
            if (!in_array($i, $usedNumbers)) {
                return 'R-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            }
        }

        throw new \Exception("Kode rekening penuh, tidak bisa menambahkan data baru.");
    }
}
