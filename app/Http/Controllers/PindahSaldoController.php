<?php

namespace App\Http\Controllers;

use App\Models\PindahSaldo;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PindahSaldoController extends Controller
{
    // Tampilkan daftar pindah saldo
    public function index()
    {
        // Ambil data pindah saldo dengan relasi rekening dan pengguna
        $pindahsaldo = PindahSaldo::with(['rekeningAsal', 'rekeningTujuan', 'pengguna'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('nopindahbuku', 'desc')
            ->get();

        // Data rekening untuk dropdown
        $rekening = Rekening::all();

        // Buat kode pindah buku berikutnya berdasarkan tanggal hari ini
        $nextCode = $this->generateKodePindahSaldo(date('Y-m-d'));

        return view('pindahsaldo', compact('pindahsaldo', 'rekening', 'nextCode'));
    }

    // Generate kode pindah buku otomatis sesuai format PB-YYYYMMDD-XXX
    protected function generateKodePindahSaldo($tanggal)
    {
        $datePart = Carbon::parse($tanggal)->format('Ymd');

        // Hitung sudah ada berapa data dengan tanggal tersebut
        $count = PindahSaldo::whereDate('tanggal', $tanggal)->count() + 1;

        // Format nomor urut 3 digit, misal 001, 002, ...
        $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "PB-{$datePart}-{$noUrut}";
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

        // Cari data pindah saldo lama
        $pindahSaldo = PindahSaldo::findOrFail($nopindahbuku);

        // Ambil data rekening asal & tujuan lama
        $rekAsalLama = Rekening::where('koderekening', $pindahSaldo->koderekeningasal)->first();
        $rekTujuanLama = Rekening::where('koderekening', $pindahSaldo->koderekeningtujuan)->first();

        // Ambil data rekening asal & tujuan baru dari input
        $rekAsalBaru = Rekening::where('koderekening', $request->rekeningasal)->first();
        $rekTujuanBaru = Rekening::where('koderekening', $request->rekeningtujuan)->first();

        $totalLama = $pindahSaldo->total;
        $totalBaru = $request->total;

        // Kembalikan saldo rekening lama ke keadaan sebelum pindah saldo:
        // rekening asal lama + total lama
        $rekAsalLama->saldo += $totalLama;
        $rekAsalLama->save();

        // rekening tujuan lama - total lama
        $rekTujuanLama->saldo -= $totalLama;
        $rekTujuanLama->save();

        // Kurangi saldo rekening asal baru dengan total baru
        $rekAsalBaru->saldo -= $totalBaru;
        $rekAsalBaru->save();

        // Tambahkan saldo rekening tujuan baru dengan total baru
        $rekTujuanBaru->saldo += $totalBaru;
        $rekTujuanBaru->save();

        // Update data pindah saldo
        $pindahSaldo->update([
            'tanggal' => $request->tanggal,
            'koderekeningasal' => $request->rekeningasal,
            'koderekeningtujuan' => $request->rekeningtujuan,
            'keterangan' => $request->keterangan,
            'total' => $totalBaru,
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
