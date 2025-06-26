@extends('layouts.main')

@section('title', 'Halaman Pembayaran Penjualan')
@section('page', 'Pembayaran Penjualan')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 600px;">
    <div class="mb-4">
        <h2 class="text-2xl font-semibold mb-2">Data Pembayaran Penjualan</h2>

        {{-- Filter Form --}}
        <form id="filterForm" method="GET" action="{{ route('pembayaran-penjualan.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div>
                <label class="text-sm font-medium text-gray-600">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" value="{{ $tanggalAwal }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Status</label>
                <select name="status" class="w-full border rounded px-2 py-1">
                    <option value="">Semua</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum" {{ request('status') == 'belum' ? 'selected' : '' }}>Belum Lunas</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Nota Jual</label>
                <input type="text" name="notajual" value="{{ request('notajual') }}" class="w-full border rounded px-2 py-2 bg-gray-100 text-sm focus:outline-none" placeholder="Cari Nota">
            </div>
            <div class="flex items-end">
                <button type="submit" id="submitButton" class="rounded bg-gray-400 text-sm text-white px-4 py-2 hover:bg-gray-500 w-full">Tampilkan</button>
            </div>
        </form>

        {{-- Daftar Nota Jual --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="text-left text-m border-b text-gray-500">
                        <th class="px-2 py-2 text-center font-normal"></th>
                        <th class="px-3 py-2 font-normal">Nota Jual</th>
                        <th class="px-3 py-2 font-normal">Tanggal</th>
                        <th class="px-3 py-2 font-normal">Total</th>
                        <th class="px-3 py-2 font-normal">Total Bayar</th>
                        <th class="px-3 py-2 font-normal">Sisa Bayar</th>
                        <th class="px-3 py-2 font-normal"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notajuals as $item)
                        <tr class="border-b hover:bg-gray-50 text-sm">
                            <td class="px-2 py-2 text-center font-normal"></td>
                            <td class="px-3 py-2">{{ $item->notajual }}</td>
                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-3 py-2">{{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="px-3 py-2">{{ number_format($item->totalbayar, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-red-600 font-semibold">{{ number_format($item->total - $item->totalbayar, 0, ',', '.') }}</td>
                            <td class="px-3 py-2">
                                <button class="border border-gray-400 text-sm text-gray-700 px-3 py-1 rounded hover:bg-gray-200"
                                    onclick="openBayarModal('{{ $item->notajual }}', '{{ $item->tanggal }}', '{{ $item->total - $item->totalbayar }}')">
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
</div>

{{-- Modal Form Bayar --}}
<div id="modalBayar" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[500px] relative">
        <h2 class="text-xl font-semibold mb-4">Form Pembayaran</h2>
        <form id="formBayar" method="POST" action="{{ route('pembayaran-penjualan.store') }}">
            @csrf
            <button type="button" onclick="openHistoryModal()" class="w-full px-3 py-1 border-1 rounded bg-gray-200 text-black hover:bg-gray-300 mb-4">
                Lihat History Pembayaran
            </button>
            <input type="hidden" name="notajual" id="formNotajual">
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
            <div class="flex justify-end gap-2">
                <button type="button" onclick="openModalKonfirmasi()" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="closeBayarModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Batal</button>
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500" onclick="closeBayarModal()">✕</button>
    </div>
</div>

{{-- Modal History --}}
<div id="modalHistory" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[600px] relative max-h-[80vh] overflow-y-auto">
        <h2 class="text-lg font-semibold mb-4">Riwayat Pembayaran</h2>
        <div id="historyContent" class="text-sm text-gray-700 space-y-2">Memuat data...</div>
        <button onclick="closeHistoryModal()" class="absolute top-2 right-2 text-gray-600 hover:text-red-600">✕</button>
    </div>
</div>

{{-- Modal Edit Pembayaran --}}
<div id="modalEditBayar" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[500px] relative">
        <h2 class="text-xl font-semibold mb-4">Edit Pembayaran</h2>
        <form method="POST" id="formEditBayar">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium">Rekening</label>
                <select name="koderekening" id="editRekening" class="w-full border rounded px-2 py-1" required>
                    <option value="">Pilih Rekening</option>
                    @foreach ($rekeningAktif as $rekening)
                        <option value="{{ $rekening->koderekening }}">{{ $rekening->namarekening }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium">Total Bayar</label>
                <input type="number" step="0.01" name="total" id="editTotal" class="w-full border rounded px-2 py-1" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="closeModalEditBayar()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500" onclick="closeModalEditBayar()">✕</button>
    </div>
</div>

{{-- Modal Konfirmasi Simpan --}}
<div id="modalKonfirmasiSimpan" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[400px] text-center">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Konfirmasi</h2>
        <p class="mb-4 text-sm text-gray-700">Apakah Anda yakin ingin menyimpan data pembayaran ini?</p>
        <div class="flex justify-center gap-4">
            <button onclick="submitFormBayar()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Ya</button>
            <button onclick="closeModalKonfirmasi()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Tidak</button>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="modalKonfirmasiHapus" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded shadow p-6 w-[90%] md:w-[400px] text-center">
        <h2 class="text-lg font-semibold mb-4 text-red-600">Konfirmasi Hapus</h2>
        <p class="mb-4 text-sm text-gray-700">Apakah Anda yakin ingin menghapus pembayaran ini?</p>
        <div class="flex justify-center gap-4">
            <button onclick="confirmHapusBayar()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Ya, Hapus</button>
            <button onclick="closeModalHapus()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Tidak</button>
        </div>
    </div>
</div>

{{-- Script Modal --}}
<script>
    function openBayarModal(notajual, tanggal, sisa) {
        document.getElementById('modalBayar').classList.remove('hidden');
        document.getElementById('formNotajual').value = notajual;
        document.getElementById('formTanggalNota').value = tanggal;
        document.getElementById('formSisa').value = sisa;
    }

    function closeBayarModal() {
        document.getElementById('modalBayar').classList.add('hidden');
    }

    function openHistoryModal() {
        const notajual = document.getElementById('formNotajual').value;
        const modal = document.getElementById('modalHistory');
        const content = document.getElementById('historyContent');

        modal.classList.remove('hidden');
        content.innerHTML = 'Memuat data...';

        fetch(`/pembayaran-penjualan/history/${notajual}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    content.innerHTML = '<p class="text-gray-500">Belum ada pembayaran.</p>';
                } else {
                    content.innerHTML = data.map(item => `
                        <div class="border-b pb-2 bg-white-100 p-3 rounded mb-2 shadow-sm">
                            <p><strong>Tanggal:</strong> ${item.tanggal}</p>
                            <p><strong>Rekening:</strong> ${item.namarekening}</p>
                            <p><strong>Total Bayar:</strong> ${parseFloat(item.total).toLocaleString('id-ID')}</p>
                            <div class="mt-2 flex gap-2">
                                <button onclick="editBayar('${item.no}')" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Edit</button>
                                <button onclick="hapusBayar('${item.no}')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Hapus</button>
                            </div>
                        </div>
                    `).join('');
                }
            })
            .catch(() => {
                content.innerHTML = '<p class="text-red-500">Gagal memuat data.</p>';
            });
    }

    function closeHistoryModal() {
        document.getElementById('modalHistory').classList.add('hidden');
    }

    function openModalKonfirmasi() {
        document.getElementById('modalKonfirmasiSimpan').classList.remove('hidden');
    }

    function closeModalKonfirmasi() {
        document.getElementById('modalKonfirmasiSimpan').classList.add('hidden');
    }

    function submitFormBayar() {
        document.querySelector('#formBayar').submit(); // pakai ID langsung
    }

    function editBayar(no) {
        fetch(`/pembayaran-penjualan/data/${no}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editRekening').value = data.koderekening;
                document.getElementById('editTotal').value = data.total;
                document.getElementById('formEditBayar').action = `/pembayaran-penjualan/${no}`;
                document.getElementById('modalEditBayar').classList.remove('hidden');
            })
            .catch(() => alert('Gagal memuat data.'));
    }

    function closeModalEditBayar() {
        document.getElementById('modalEditBayar').classList.add('hidden');
    }

    let currentHapusNo = null;
    function hapusBayar(no) {
        currentHapusNo = no;
        document.getElementById('modalKonfirmasiHapus').classList.remove('hidden');
    }

    function closeModalHapus() {
        currentHapusNo = null;
        document.getElementById('modalKonfirmasiHapus').classList.add('hidden');
    }

    function confirmHapusBayar() {
        if (!currentHapusNo) return;
        fetch(`/pembayaran-penjualan/${currentHapusNo}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(() => location.reload())
        .catch(() => alert('Gagal menghapus'));
    }
</script>
@endsection