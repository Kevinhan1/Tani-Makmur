<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hjual;

class PiutangController extends Controller
{
    public function index(Request $request)
				{
								$data = Hjual::with('pelanggan')
												->orderBy('tanggal', 'desc')
												->paginate(15) // paginate dulu
												->through(function ($item) {
																$item->sisa = $item->total - $item->totalbayar;
																$item->status = $item->sisa <= 0 ? 'Lunas' : 'Belum Lunas';
																return $item;
												});

								return view('piutang', ['data' => $data]);
				}
}
