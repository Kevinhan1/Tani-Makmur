<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;

class RekeningController extends Controller
{
    public function index(Request $request)
    {   
        $query = \App\Models\Rekening::query();

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('koderekening', 'like', '%' . $search . '%')
            ->orWhere('namarekening', 'like', '%' . $search . '%');
        });
    }

    $rekening = $query->paginate(10);

        // Generate next kode rekening (format R-001, R-002, ...)
        $last = Rekening::orderBy('koderekening', 'desc')->first();
        if ($last) {
            $num = (int) substr($last->koderekening, 2);
            $nextCode = 'R-' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextCode = 'R-001';
        }

        return view('rekening', compact('rekening', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'koderekening' => 'required|string|unique:trekening,koderekening',
            'namarekening' => 'required|string|max:50',
            'saldo' => 'required|numeric',
            'aktif' => 'nullable|boolean',
        ]);

        Rekening::create([
            'koderekening' => $request->koderekening,
            'namarekening' => $request->namarekening,
            'saldo' => $request->saldo,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil ditambahkan');
    }

    public function update(Request $request, $koderekening)
    {
        $request->validate([
            'namarekening' => 'required|string|max:50',
            'saldo' => 'required|numeric',
            'aktif' => 'nullable|boolean',
        ]);

        $rekening = Rekening::findOrFail($koderekening);
        $rekening->update([
            'namarekening' => $request->namarekening,
            'saldo' => $request->saldo,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return redirect()->route('rekening.index')->with('success', 'Data rekening berhasil diubah');
    }

    public function destroy($koderekening)
    {
        $rekening = Rekening::findOrFail($koderekening);
        $rekening->delete();

        return response()->json(['success' => true]);
    }
}
