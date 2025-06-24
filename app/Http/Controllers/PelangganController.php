<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Session;
class PelangganController extends Controller
{
    public function index(Request $request)
    {   
        $user = Session::get('user');
        
        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        elseif (!$user || $user->status !== 'admin') {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
        $query = \App\Models\Pelanggan::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('kodepelanggan', 'like', '%' . $search . '%')
            ->orWhere('namapelanggan', 'like', '%' . $search . '%')
            ->orWhere('namakios', 'like', '%' . $search . '%')
            ->orWhere('alamat', 'like', '%' . $search . '%')
            ->orWhere('kelurahan', 'like', '%' . $search . '%')
            ->orWhere('kecamatan', 'like', '%' . $search . '%')
            ->orWhere('kota', 'like', '%' . $search . '%')
            ->orWhere('ktp', 'like', '%' . $search . '%')
            ->orWhere('npwp', 'like', '%' . $search . '%')
            ->orWhere('nitku', 'like', '%' . $search . '%')
            ;
        });
    }

    $pelanggan = $query->paginate(15);

        // Generate kode otomatis
        $last = Pelanggan::orderBy('kodepelanggan', 'desc')->first();
        $nextCode = 'P-001';
        if ($last) {
            $num = intval(substr($last->kodepelanggan, 2)) + 1;
            $nextCode = 'P-' . str_pad($num, 3, '0', STR_PAD_LEFT);
        }
								
        return view('pelanggan', compact('pelanggan', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodepelanggan' => 'required|unique:tpelanggan',
            'namapelanggan' => 'required',
        ]);

        Pelanggan::create([
            ...$request->except('aktif'),
            'aktif' => $request->has('aktif') ? 1 : 0
        ]);

        return redirect()->back();
    }

    public function update(Request $request, $kode)
    {
        $data = Pelanggan::where('kodepelanggan', $kode)->firstOrFail();

        $data->update([
            ...$request->except('aktif'),
            'aktif' => $request->has('aktif') ? 1 : 0
        ]);

        return redirect()->back();
    }

    public function destroy($kode)
    {
        Pelanggan::where('kodepelanggan', $kode)->delete();
        return response()->json(['success' => true]);
    }
}
