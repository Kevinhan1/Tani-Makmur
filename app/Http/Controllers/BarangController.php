<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        return view('barang'); // sesuai dengan nama file barang.blade.php
    }
}

