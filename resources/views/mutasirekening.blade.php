@extends('layouts.main')

@section('title', 'Mutasi Rekening')
@section('page', 'Mutasi Rekening')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 600px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Data Mutasi Rekening</h2>
        <form method="GET" action="{{ route('mutasirekening.index') }}" class="relative ml-20">
            <input type="text" name="search" placeholder="Cari Data"
                value="{{ request('search') }}"
                class="border rounded px-4 py-2 bg-gray-100 text-sm focus:outline-none w-80 text-left" />
            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                <img src="{{ asset('icons\search-normal.svg') }}" alt="search" class="w-5 h-5" />
            </button>
        </form>

            <div class="flex items-center mr-5 gap-0 space-x-2 text-sm text-gray-700">
            <!-- Label Halaman -->
            <span>
                Halaman {{ $mutasi->currentPage() }} dari {{ $mutasi->lastPage() }}
            </span>

                <!-- Panah Kiri -->
            @if ($mutasi->onFirstPage())
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ request()->fullUrlWithQuery(['page' => $mutasi->currentPage() - 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <!-- Panah Kanan -->
            @if ($mutasi->hasMorePages())
                <a href="{{ request()->fullUrlWithQuery(['page' => $mutasi->currentPage() + 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
</div>

<div>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-gray-500">
                <th class="px-4 py-2 font-normal">No. Generate</th>
                <th class="px-4 py-2 font-normal">Tanggal</th>
                <th class="px-4 py-2 font-normal">No. Referensi</th>
                <th class="px-4 py-2 font-normal">Nama Rekening</th>
                <th class="px-4 py-2 font-normal">Masuk</th>
                <th class="px-4 py-2 font-normal">Keluar</th>
                <th class="px-4 py-2 font-normal">Jenis</th>
                <th class="px-4 py-2 font-normal">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasi as $item)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $item->nogenerate }}</td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td class="px-4 py-2">{{ $item->noreferensi }}</td>
                    <td class="px-4 py-2">{{ $item->rekening->namarekening ?? '-' }}</td>
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
</div>
@endsection
