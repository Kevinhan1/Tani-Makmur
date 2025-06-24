<?php

namespace App\Http\Controllers;

use App\Models\PindahSaldo;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;

class PindahSaldoController extends Controller
{
    // Tampilkan daftar pindah saldo
    public function index(Request $request)
    {   
        $user = Session::get('user');
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $tanggalAwal = $request->tanggal_awal ?? date('Y-m-d', strtotime('-7 days'));
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d');

        $query = PindahSaldo::with(['rekeningAsal', 'rekeningTujuan', 'pengguna'])
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('nopindahbuku', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nopindahbuku', 'like', "%$search%")
                    ->orWhere('tanggal', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%")
                    ->orWhere('total', 'like', "%$search%");
            })
            ->orWhereHas('rekeningAsal', fn($q) => $q->where('namarekening', 'like', "%$search%"))
            ->orWhereHas('rekeningTujuan', fn($q) => $q->where('namarekening', 'like', "%$search%"))
            ->orWhereHas('pengguna', fn($q) => $q->where('namapengguna', 'like', "%$search%"));
        }

        $pindahsaldo = $query->paginate(15)->withQueryString();
        $rekening = Rekening::orderBy('namarekening')->get();
        $nextCode = $this->generateKodePindahSaldo($tanggalAkhir);

        return view('pindahsaldo', compact('pindahsaldo', 'rekening', 'nextCode', 'tanggalAwal', 'tanggalAkhir'));
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

        // Ambil data rekening asal
        $rekeningAsal = Rekening::where('koderekening', $request->rekeningasal)->first();

        if (!$rekeningAsal) {
            return back()->withErrors(['rekeningasal' => 'Rekening asal tidak ditemukan.'])->withInput();
        }

        // Cek apakah saldo mencukupi
        if ($request->total > $rekeningAsal->saldo) {
            return back()->withErrors(['total' => 'Saldo tidak cukup untuk melakukan pindah saldo.'])->withInput();
        }

        $user = session('user');
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kodepengguna = $user->kodepengguna;
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
        'rekeningasal' => 'required|string|exists:trekening,koderekening|max:5',
        'rekeningtujuan' => 'required|string|exists:trekening,koderekening|different:rekeningasal|max:5',
        'keterangan' => 'required|string|max:255',
        'total' => 'required|numeric|min:0.01',
    ]);

    $user = session('user');
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    $kodepengguna = $user->kodepengguna;

    // Hapus mutasi lama (biar saldo dikembalikan)
    DB::table('tmutasirekening')->where('noreferensi', $nopindahbuku)->delete();

    // Hapus data lama dari tpindahbuku (trigger AFTER DELETE akan menghapus mutasi juga)
    $old = PindahSaldo::findOrFail($nopindahbuku);
    $old->delete();

    // Simpan ulang data ke tpindahbuku (trigger AFTER INSERT akan buat mutasi baru)
    PindahSaldo::create([
        'nopindahbuku' => $nopindahbuku,
        'tanggal' => $request->tanggal,
        'koderekeningasal' => $request->rekeningasal,
        'koderekeningtujuan' => $request->rekeningtujuan,
        'keterangan' => $request->keterangan,
        'total' => $request->total,
        'kodepengguna' => $kodepengguna,
    ]);

    return redirect()->route('pindahsaldo.index')->with('success', 'Data pindah saldo berhasil diperbarui.');
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


    
    public function exportPdf(Request $request)
    {
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');

        $query = PindahSaldo::with(['rekeningAsal', 'rekeningTujuan', 'pengguna'])
            ->when($tanggal_awal, function ($q) use ($tanggal_awal) {
                $q->whereDate('tanggal', '>=', $tanggal_awal);
            })
            ->when($tanggal_akhir, function ($q) use ($tanggal_akhir) {
                $q->whereDate('tanggal', '<=', $tanggal_akhir);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        $pdf = Pdf::loadView('pindahsaldo-pdf', [
            'data' => $query,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ])->setPaper('a4', 'potrait');

        return $pdf->stream('laporan-pindah-saldo -'. now()->format('Ymd_His') . '.pdf');
    }
}
