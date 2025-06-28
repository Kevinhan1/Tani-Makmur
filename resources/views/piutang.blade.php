@extends('layouts.main')

@section('title', 'Halaman Piutang')
@section('page', 'Data Piutang')

@section('content')
<div class="bg-white p-6 rounded shadow min-h-[800px]">
					<div class="flex justify-between items-center mb-4">
					<h2 class="text-xl font-semibold mb-4">Daftar Piutang Penjualan</h2>
					
					{{-- Info halaman dan panah --}}
					<div class="flex justify-end items-center gap-4 text-sm text-gray-700 mb-4">
									<span>
													Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
									</span>

									{{-- Panah Pagination --}}
									<div class="flex gap-2 items-center">
																					@if ($data->onFirstPage())
													<span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
																	<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
																					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
																	</svg>
													</span>
									@else
													<a href="{{ request()->fullUrlWithQuery(['page' => $data->currentPage() - 1]) }}"
																	class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
																	<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
																					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
																	</svg>
													</a>
									@endif

									@if ($data->hasMorePages())
													<a href="{{ request()->fullUrlWithQuery(['page' => $data->currentPage() + 1]) }}"
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
</div>

				

    {{-- Filter Tanggal --}}
    <form method="GET" action="{{ route('laporan-penjualan.index') }}" class="flex flex-wrap gap-4 mb-6 items-end">
									<div>
													<label class="block text-sm">Dari Tanggal</label>
													<input type="date" name="dari" value="{{ request('tanggal_awal', date('Y-m-d', strtotime('-7 days'))) }}" class="border px-2 py-1 rounded">
									</div>
									<div>
													<label class="block text-sm">Sampai Tanggal</label>
													<input type="date" name="sampai"  value="{{ request('tanggal_akhir', date('Y-m-d')) }}" class="border px-2 py-1 rounded">
									</div>
									<div>
													<label class="block text-sm">Cari Pelanggan</label>
													<input type="text" name="search" value="{{ request('search') }}" placeholder="Cari"
																	class="border px-2 py-1 rounded w-64">
									</div>
									<div>
													<button type="submit" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded text-sm">Tampilkan</button>
									</div>
					</form>



    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="text-gray-600 text-sm text-left border-b">
                    <th class="px-4 py-2 font-normal">Nota Jual</th>
                    <th class="px-4 py-2 font-normal">Tanggal</th>
                    <th class="px-4 py-2 font-normal">Pelanggan</th>
                    <th class="px-4 py-2 text-left font-normal">Total</th>
                    <th class="px-4 py-2 text-left font-normal">Total Bayar</th>
                    <th class="px-4 py-2 text-left font-normal">Sisa</th>
                    <th class="px-4 py-2 font-normal text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data->filter(fn($item) => $item->sisa > 0) as $item)
                    <tr class="border-b text-sm hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->notajual }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-4 py-2">{{ $item->pelanggan->namapelanggan ?? '-' }}</td>
                        <td class="px-4 py-2 text-left">{{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-left">{{ number_format($item->totalbayar, 0, ',', '.') }}</td>
                        <td class="px-4 py-2 text-left text-red-600">
                            {{ number_format($item->sisa, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-white text-xs  
                                {{ $item->status == 'Lunas' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-6">Tidak ada data penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
