<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class BiayaController extends Controller
{
    // Tampilkan daftar biaya
    public function index(Request $request)
    {   
            if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        
        $query = Biaya::with(['rekening', 'pengguna'])->orderBy('nobiaya', 'desc');

        // Filter berdasarkan tanggal jika diisi
        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-d', strtotime('-7 days'));
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');

        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Filter pencarian jika ada
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

        $biaya = $query->paginate(15)->withQueryString();
        $rekening = Rekening::orderBy('namarekening')->get();
        $nextCode = $this->generateKodeBiaya();

        return view('biaya', compact('biaya', 'rekening', 'nextCode', 'tanggalAwal', 'tanggalAkhir'));
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

        // Ambil data rekening terlebih dahulu
        $rekening = Rekening::where('koderekening', $request->koderekening)->first();

        if (!$rekening) {
            return back()->withInput()->withErrors(['koderekening' => 'Rekening tidak ditemukan.']);
        }

        if ($request->total > $rekening->saldo) {
            return back()->withInput()->withErrors(['total' => 'Saldo rekening tidak cukup untuk melakukan transaksi.']);
        }

        // Ambil data user dari session
        $user = Session::get('user');

        $data = $request->all();
        $data['kodepengguna'] = $user->kodepengguna;

        // Simpan data biaya setelah lolos validasi saldo
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

    public function exportPdf(Request $request)
    {
        // Ambil data dengan filter yang sama seperti index()
        $query = Biaya::with(['rekening', 'pengguna'])->orderBy('nobiaya', 'desc');

        // Filter tanggal
        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-d', strtotime('-7 days'));
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');
        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Filter pencarian
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

        $biaya = $query->get(); // ambil semua tanpa pagination

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('biaya-pdf', [
            'biaya' => $biaya,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'search' => $request->search,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('biaya-' . now()->format('Ymd_His') . '.pdf');
    }


}