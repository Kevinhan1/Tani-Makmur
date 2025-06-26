<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hjual;
use App\Models\Djual;
use Illuminate\Support\Facades\DB;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
				{
								$query = Hjual::with('pelanggan');

								if ($request->filled('dari') && $request->filled('sampai')) {
												$query->whereBetween('tanggal', [$request->dari, $request->sampai]);
								}

								if ($request->filled('search')) {
												$query->whereHas('pelanggan', function ($q) use ($request) {
																$q->where('namapelanggan', 'like', '%' . $request->search . '%');
												});
								}

								$data = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

								return view('laporan-penjualan', [
												'data' => $data,
												'dari' => $request->dari,
												'sampai' => $request->sampai,
												'search' => $request->search
								]);
				}

    public function detail($notajual)
    {
        $details = Djual::where('notajual', $notajual)->get();

        $response = $details->map(function ($d) {
            $namabarang = DB::table('tdbeli')
                ->join('tbarang', 'tdbeli.kodebarang', '=', 'tbarang.kodebarang')
                ->where('tdbeli.noref', $d->noref)
                ->value('tbarang.namabarang');

            return [
                'noref' => $d->noref,
                'namabarang' => $namabarang ?? '-',
                'qty' => $d->qty,
                'hargajual' => $d->hargajual,
                'subtotal' => $d->qty * $d->hargajual,
            ];
        });

        return response()->json($response);
    }
}
