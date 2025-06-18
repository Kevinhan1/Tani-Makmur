@extends('layouts.main')

@section('title', 'Pembayaran Pembelian')
@section('page', 'Pembayaran Pembelian')

@section('content')
<div class="bg-white p-6 rounded shadow min-h-[800px]">

    {{-- Filter Form --}}
    <form id="filterForm" method="GET" action="{{ route('pembayaran-pembelian.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div>
            <label class="text-sm font-medium">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="w-full border rounded px-2 py-1">
        </div>
        <div>
            <label class="text-sm font-medium">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="w-full border rounded px-2 py-1">
        </div>
        <div>
            <label class="text-sm font-medium">Status</label>
            <select name="status" class="w-full border rounded px-2 py-1">
                <option value="">Semua</option>
                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium">Nota Beli</label>
            <input type="text" name="notabeli" value="{{ request('notabeli') }}" class="w-full border rounded px-2 py-1" placeholder="Opsional">
        </div>
        <div class="flex items-end">
            <button type="submit" id="submitButton" class="rounded bg-[#89E355] text-sm text-white px-4 py-2 hover:bg-[#7ED242] w-full">Tampilkan</button>
        </div>
    </form>

    {{-- Tabel Nota Beli --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="text-left text-m border-b text-gray-500">
                    <th class="px-2 py-2 text-center font-normal"></th>
                    <th class="px-3 py-2 font-normal">Nota Beli</th>
                    <th class="px-3 py-2 font-normal">Tanggal</th>
                    <th class="px-3 py-2 text-left font-normal">Total</th>
                    <th class="px-3 py-2 text-left font-normal">Total Bayar</th>
                    <th class="px-3 py-2 text-left font-normal">Sisa Bayar</th>
                    <th class="px-3 py-2 text-left font-normal"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notabelis as $item)
                    <tr class="border-b hover:bg-gray-50 text-sm">
                        <th class="px-2 py-2 text-center font-normal"></th>
                        <td class="px-3 py-2">{{ $item->notabeli }}</td>
                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-3 py-2 text-left">{{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-left">{{ number_format($item->totalbayar, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-left text-red-600 font-semibold">
                            {{ number_format($item->total - $item->totalbayar, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-left">
                            <button
                                class="border border-gray-400 text-sm text-gray-700 px-3 py-1 rounded hover:bg-gray-200"
                                onclick="openBayarModal('{{ $item->notabeli }}', '{{ $item->tanggal }}', '{{ $item->total - $item->totalbayar }}')">
                                Bayar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-sm py-6 text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Form Bayar --}}
<div id="modalBayar" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[500px] relative">
        <h2 class="text-xl font-semibold mb-4">Form Pembayaran</h2>
        <form method="POST" action="{{ route('pembayaran-pembelian.store') }}">
            @csrf
            <input type="hidden" name="notabeli" id="formNotabeli">
            <div class="mb-4">
                <label class="block text-sm font-medium">Tanggal Nota</label>
                <input type="text" id="formTanggalNota" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" class="w-full border rounded px-2 py-1" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Rekening</label>
                <select name="koderekening" class="w-full border rounded px-2 py-1" required>
                    <option value="">Pilih Rekening</option>
                    @foreach ($rekeningAktif as $rekening)
                        <option value="{{ $rekening->koderekening }}">{{ $rekening->namarekening }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Jumlah Bayar</label>
                <input type="number" step="0.01" name="total_bayar" id="formSisa" class="w-full border rounded px-2 py-1" required>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="mr-3 px-4 py-2 rounded bg-[#89E355] text-white hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="closeBayarModal()" class=" px-4 py-2 rounded border text-gray-600">Batal</button>    
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500" onclick="closeBayarModal()">✕</button>
    </div>
</div>

{{-- Modal Validasi Tanggal --}}
<div id="modalValidasi" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[400px] relative text-center">
        <h2 class="text-lg font-semibold mb-4 text-red-600">Peringatan</h2>
        <p id="validasiMessage" class="mb-4 text-sm text-gray-700"></p>
        <button onclick="closeValidasiModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm text-gray-800">Tutup</button>
    </div>
</div>

{{-- Script Modal --}}
<script>
    function openBayarModal(notabeli, tanggal, sisa) {
        document.getElementById('modalBayar').classList.remove('hidden');
        document.getElementById('formNotabeli').value = notabeli;
        document.getElementById('formTanggalNota').value = tanggal;
        document.getElementById('formSisa').value = sisa;
    }

    function closeBayarModal() {
        document.getElementById('modalBayar').classList.add('hidden');
    }

    function openValidasiModal(pesan) {
        document.getElementById('validasiMessage').innerText = pesan;
        document.getElementById('modalValidasi').classList.remove('hidden');
    }

    function closeValidasiModal() {
        document.getElementById('modalValidasi').classList.add('hidden');
    }

    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const tglAwal = document.querySelector('[name="tanggal_awal"]').value;
        const tglAkhir = document.querySelector('[name="tanggal_akhir"]').value;

        if (tglAwal && tglAkhir) {
            const awal = new Date(tglAwal);
            const akhir = new Date(tglAkhir);

            if (awal > akhir) {
                e.preventDefault();
                openValidasiModal("Tanggal awal tidak boleh melebihi tanggal akhir.");
            }
        }
    });
</script>
@endsection
