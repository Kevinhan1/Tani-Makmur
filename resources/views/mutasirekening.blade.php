@extends('layouts.main')

@section('title', 'Mutasi Rekening')
@section('page', 'Mutasi Rekening')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    {{-- HEADER: Judul dan Pagination sejajar --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Data Mutasi Rekening</h2>
        <div class="flex items-center gap-4 text-sm text-gray-700">
            <span>Halaman {{ $mutasi->currentPage() }} dari {{ $mutasi->lastPage() }}</span>

            {{-- Panah kiri --}}
            @if ($mutasi->onFirstPage())
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ request()->fullUrlWithQuery(['page' => $mutasi->currentPage() - 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Panah kanan --}}
            @if ($mutasi->hasMorePages())
                <a href="{{ request()->fullUrlWithQuery(['page' => $mutasi->currentPage() + 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>

    {{-- FILTER FORM: Tombol sampai ujung --}}
    <form id="filterForm" method="GET" action="{{ route('mutasirekening.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <div>
            <label class="text-sm text-gray-600">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal', date('Y-m-d', strtotime('-7 days'))) }}" class="w-full border rounded px-2 py-1">
        </div>
        <div>
            <label class="text-sm text-gray-600">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}" class="w-full border rounded px-2 py-1">
        </div>
        <div>
            <label class="text-sm text-gray-600">Rekening</label>
            <select name="rekening" class="w-full border rounded px-2 py-1">
                <option value="">Semua</option>
                @foreach ($rekeningList as $rek)
                    <option value="{{ $rek->koderekening }}" {{ request('rekening') == $rek->koderekening ? 'selected' : '' }}>
                        {{ $rek->namarekening }}
                    </option>
                @endforeach
            </select>

        </div>
        <div>
                        <label class="text-sm text-gray-600">Jenis</label>
            <select name="jenis" class="w-full border rounded px-2 py-1">
                <option value="">Semua</option>
                <option value="Pembelian" {{ request('jenis') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                <option value="Penjualan" {{ request('jenis') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                <option value="Pindah Buku" {{ request('jenis') == 'Pindah Buku' ? 'selected' : '' }}>Pindah Buku</option>
                <option value="Biaya" {{ request('jenis') == 'Biaya' ? 'selected' : '' }}>Biaya</option>
                <option value="Saldo Manual" {{ request('jenis') == 'Saldo Manual' ? 'selected' : '' }}>Saldo Manual</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded bg-gray-400 text-sm text-white px-4 py-2 hover:bg-gray-500 w-full">
                Tampilkan
            </button>
        </div>
        <div class="flex items-end">
            <a href="{{ route('mutasirekening.pdf', request()->all()) }}" 
                target="_blank" 
                class="flex gap-2 justify-center rounded border border-gray-300 bg-gray-100 text-sm text-gray px-4 py-2 hover:bg-gray-400 w-full">
                <span class="text-center">Print PDF</span>
                <img src="{{ asset('icons/printer.svg') }}" alt="Printer Icon" class="w-5 h-5">
            </a>
        </div>
    </form>



    {{-- Table Mutasi --}}
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
</div>
@endsection
