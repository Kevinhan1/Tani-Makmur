<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hjual;
use Illuminate\Support\Facades\Session;

class PiutangController extends Controller
{
    public function index(Request $request)
				{
								$user = Session::get('user');

								if (!session()->has('user')) {
												return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
								}

								$query = Hjual::with('pelanggan')->orderBy('tanggal', 'desc');

								// Filter tanggal
								if ($request->filled('dari') && $request->filled('sampai')) {
												$query->whereBetween('tanggal', [$request->dari, $request->sampai]);
								}

								// Filter nama pelanggan (via relasi)
								if ($request->filled('search')) {
												$query->whereHas('pelanggan', function ($q) use ($request) {
																$q->where('namapelanggan', 'like', '%' . $request->search . '%');
												});
								}

								// Paginate dan hitung sisa & status
								$data = $query->paginate(15)->through(function ($item) {
												$item->sisa = $item->total - $item->totalbayar;
												$item->status = $item->sisa <= 0 ? 'Lunas' : 'Belum Lunas';
												return $item;
								});

								return view('piutang', ['data' => $data]);
				}
}
