@extends('layouts.main')

@section('title', 'Mutasi Rekening')
@section('page', 'Mutasi Rekening')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    <h2 class="text-2xl font-semibold mb-4">Data Mutasi Rekening</h2>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-gray-500">
                <th class="px-4 py-2">No. Generate</th>
                <th class="px-4 py-2">Tanggal</th>
                <th class="px-4 py-2">No. Referensi</th>
                <th class="px-4 py-2">Kode Rekening</th>
                <th class="px-4 py-2">Masuk</th>
                <th class="px-4 py-2">Keluar</th>
                <th class="px-4 py-2">Jenis</th>
                <th class="px-4 py-2">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasi as $item)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $item->nogenerate }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">{{ $item->noreferensi }}</td>
                    <td class="px-4 py-2">{{ $item->koderekening }}</td>
                    <td class="px-4 py-2 text-green-600">Rp{{ number_format($item->masuk, 0, ',', '.') }}</td>
                    <td class="px-4 py-2 text-red-600">Rp{{ number_format($item->keluar, 0, ',', '.') }}</td>
                    <td class="px-4 py-2">{{ $item->jenis }}</td>
                    <td class="px-4 py-2">{{ $item->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-2 text-center text-gray-500">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $mutasi->links() }}
    </div>
</div>
@endsection
