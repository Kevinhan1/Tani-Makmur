<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PenjualanController extends Controller
{
            public function index()
            {
                $pelanggan = DB::table('tpelanggan')->where('aktif', 1)->get();
                $barang    = DB::table('tbarang')->where('aktif', 1)->get();

                return view('penjualan', compact('pelanggan', 'barang'));
            }
}
