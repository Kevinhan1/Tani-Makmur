<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hbeli;
use App\Models\Dbeli;
use App\Models\BayarBeli;
use App\Models\Pemasok;
use App\Models\Barang;
use App\Models\Rekening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PembelianController extends Controller
{
    public function index()
    {
        $pemasok = Pemasok::where('aktif', 1)->get();
        $barang = Barang::where('aktif', 1)->get();
        $rekening = Rekening::where('aktif', 1)->get();
        $notabeli = $this->generateNoNotaBeli();

        return view('pembelian', compact('pemasok', 'barang', 'notabeli', 'rekening'));
    }

    public function store(Request $request)
    {
        // Validasi minimal data utama
        $request->validate([
            'notabeli' => 'required',
            'tanggal' => 'required|date',
            'kodepemasok' => 'required',
            'total' => 'required|numeric|min:0',
            'totalbayar' => 'required|numeric|min:0',
            'koderekening' => 'required',
            'items' => 'required|array|min:1'
        ]);

        // Ambil data user yang login dari session
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User belum login di session']);
        }

        DB::beginTransaction();

        try {
            // Simpan ke table thbeli
            $hbeli = Hbeli::create([
                'notabeli' => $request->notabeli,
                'tanggal' => $request->tanggal,
                'kodepemasok' => $request->kodepemasok,
                'total' => $request->total,
                'totalbayar' => $request->totalbayar,
                'kodepengguna' => $user->kodepengguna
            ]);

            // Simpan ke table tdbeli (detail barang)
            foreach ($request->items as $item) {
                Dbeli::create([
                    'noref' => $item['noref'],
                    'notabeli' => $request->notabeli,
                    'kodebarang' => $item['kodebarang'],
                    'nodo' => $item['nodo'],
                    'qty' => $item['qty'],
                    'qtyjual' => $item['qtyjual'],
                    'hargabeli' => $item['hargabeli']
                ]);
            }

            // Ambil nilai no terakhir untuk BayarBeli
            $lastNo = BayarBeli::max('no');
            $nextNo = $lastNo ? $lastNo + 1 : 1;

            // Simpan ke table tdbayarbeli
            BayarBeli::create([
                'notabeli' => $request->notabeli,
                'tanggal' => $request->tanggal,
                'koderekening' => $request->koderekening,
                'total' => $request->totalbayar,
                'kodepengguna' => $user->kodepengguna
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data pembelian berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function generateNoNotaBeli()
    {
        $bulanTahun = date('my'); // format bulantahun -> contoh: 0625 (Juni 2025)
        $prefix = 'B-' . $bulanTahun . '-';

        $lastNota = Hbeli::where('notabeli', 'like', $prefix . '%')
            ->orderBy('notabeli', 'desc')
            ->first();

        if ($lastNota) {
            $lastNumber = (int)substr($lastNota->notabeli, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
