@extends('layouts.main')

@section('title', 'Laporan Penjualan')
@section('page', 'Laporan Penjualan')

@section('content')
<div class="bg-white p-6 rounded shadow min-h-[800px]">
    <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold">Laporan Penjualan</h2>

				

    {{-- Info halaman dan panah --}}
    <div class="flex items-center gap-4">
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

				




    {{-- Tabel Laporan --}}
    <div class="overflow-auto">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="px-3 py-2 font-normal">Nota Jual</th>
                    <th class="px-3 py-2 font-normal">Tanggal</th>
                    <th class="px-3 py-2 font-normal">Pelanggan</th>
                    <th class="px-3 py-2 text-right font-normal">Total</th>
                    <th class="px-3 py-2 text-right font-normal">Total Bayar</th>
                    <th class="px-3 py-2 font-normal">Nopol</th>
                    <th class="px-3 py-2 font-normal">Supir</th>
                    <th class="px-3 py-2 font-normal">Pengguna</th>
                    <th class="px-3 py-2 font-normal">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $item->notajual }}</td>
                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-3 py-2">{{ $item->pelanggan->namapelanggan ?? '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->totalbayar, 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ $item->nopol }}</td>
                        <td class="px-3 py-2">{{ $item->supir }}</td>
                        <td class="px-3 py-2">{{ $item->kodepengguna }}</td>
                        <td class="px-3 py-2 flex gap-2">
                            <button onclick="lihatDetail('{{ $item->notajual }}')" class=" rounded rounded-gray-500 bg-gray-300 hover:bg-gray-500 text-black px-2 py-1 rounded text-ss">Detail</button>
                            <a href="{{ route('penjualan.invoice', $item->notajual) }}" target="_blank" class="border border-gray-700 bg-gray-100 hover:bg-gray-300 text-black px-2 py-1 rounded text-ss">Invoice</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-6 text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Detail --}}
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[600px] max-h-[80vh] overflow-y-auto relative">
        <h2 class="text-xl font-semibold mb-4">Detail Penjualan</h2>
        <div id="isiDetail">Memuat data...</div>
        <button onclick="tutupModalDetail()" class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-xl">×</button>
    </div>
</div>

<script>
    function lihatDetail(notajual) {
        document.getElementById('modalDetail').classList.remove('hidden');
        document.getElementById('modalDetail').classList.add('flex');

        fetch(`/laporan-penjualan/detail/${notajual}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    document.getElementById('isiDetail').innerHTML = '<p class="text-gray-500">Tidak ada data detail.</p>';
                    return;
                }

                let html = '<table class="w-full text-sm border">';
                html += '<thead><tr class="bg-gray-100"><th class="text-left px-2 py-1 font-normal">No Ref</th><th class="text-left px-2 py-1 font-normal">Barang</th><th class="text-left px-2 py-1 font-normal">Qty</th><th class="text-left px-2 py-1 font-normal">Harga</th><th class="text-left px-2 py-1 font-normal">Subtotal</th></tr></thead><tbody>';

                data.forEach(d => {
                    html += `<tr class="border-b">
                        <td class="px-2 py-1">${d.noref}</td>
                        <td class="px-2 py-1">${d.namabarang}</td>
                        <td class="px-2 py-1 ">${d.qty}</td>
                        <td class="px-2 py-1 ">${Number(d.hargajual).toLocaleString()}</td>
                        <td class="px-2 py-1 ">${Number(d.subtotal).toLocaleString()}</td>
                    </tr>`;
                });

                html += '</tbody></table>';
                document.getElementById('isiDetail').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('isiDetail').innerHTML = '<p class="text-red-500">Gagal memuat data.</p>';
            });
    }

    function tutupModalDetail() {
        document.getElementById('modalDetail').classList.add('hidden');
    }
</script>
@endsection
