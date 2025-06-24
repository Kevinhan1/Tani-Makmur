@extends('layouts.main')

@section('title', 'Mutasi Stok')
@section('page', 'Mutasi Stok')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">Laporan Mutasi Stok</h2>
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


        {{-- Filter Form --}}
        <form id="filterForm" method="GET" action="{{ route('mutasi-stok.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div>
                <label class="text-sm text-gray-600">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal', date('Y-m-d', strtotime('-7 days'))) }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm text-gray-600">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm text-gray-600">Jenis</label>
                <select name="jenis" class="w-full border rounded px-2 py-1">
                    <option value="">Semua</option>
                    <option value="Pembelian" {{ request('jenis') == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                    <option value="Penjualan" {{ request('jenis') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" id="submitButton" class="rounded bg-gray-400 text-sm text-white px-4 py-2 hover:bg-gray-500 w-full">Tampilkan</button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('mutasi-stok.pdf', request()->all()) }}" 
                target="_blank" 
                class="flex gap-2 justify-center rounded border border-gray-300 bg-gray-100 text-sm text-gray px-4 py-2 hover:bg-gray-400 w-full">
                    <span class="text-center" >Print PDF</span>
                    <img src="{{ asset('icons/printer.svg') }}" alt="Printer Icon" class="w-5 h-5">
                </a>
            </div>
        </form>

        {{-- Tabel Mutasi --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-normal">
                <thead class="text-left text-gray-600 border-b">
                    <tr>
						<th class="px-3 py-2 font-normal">Nota</th>
						<th class="px-3 py-2 font-normal">Tanggal</th>
                        <th class="px-3 py-2 font-normal">No Ref</th>
						<th class="px-3 py-2 font-normal">Nama Barang</th>
						<th class="px-3 py-2 text-right font-normal">Masuk</th>
						<th class="px-3 py-2 text-right font-normal">Keluar</th>
						<th class="px-3 py-2 font-normal">Jenis</th>
						<th class="px-3 py-2 font-normal">Keterangan</th>
					</tr>
							</thead>
								<tbody>
									@forelse ($mutasi as $row)
									<tr class="border-b hover:bg-gray-50">
									<td class="px-3 py-2">{{ $row['nota'] }}</td>
									<td class="px-3 py-2">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}</td>
									<td class="px-3 py-2">{{ $row['noref'] }}</td>
									<td class="px-3 py-2">{{ $row['namabarang'] }}</td>
									<td class="px-3 py-2 text-right">{{ $row['masuk'] }}</td>
									<td class="px-3 py-2 text-right">{{ $row['keluar'] }}</td>
									<td class="px-3 py-2">{{ $row['jenis'] }}</td>
									<td class="px-3 py-2">{{ $row['keterangan'] }}</td>
									</tr>
									@empty
								<tr>
							<td colspan="8" class="text-center py-6 text-gray-500">Tidak ada data mutasi stok</td>
						</tr>
					@endforelse
				</tbody>
            </table>
        </div>
    </div>
</div>
@endsection
