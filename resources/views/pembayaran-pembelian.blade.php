@extends('layouts.main')

@section('title', 'Halaman Pembayaran Pembelian')
@section('page', 'Pembayaran Pembelian')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 600px;">
    <div class="mb-4">
        <h2 class="text-2xl font-semibold mb-2">Data Pembayaran Pembelian</h2>

    {{-- Filter Form --}}
    <form id="filterForm" method="GET" action="{{ route('pembayaran-pembelian.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
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
            <label class="text-sm font-medium text-gray-600">Nota Beli</label>
            <input type="text" name="notabeli" value="{{ request('notabeli') }}" class="w-full border rounded px-2 py-2 bg-gray-100 text-sm focus:outline-none" placeholder="Cari Nota">
        </div>
        <div class="flex items-end">
            <button type="submit" id="submitButton" class="rounded bg-gray-400 text-sm text-white px-4 py-2 hover:bg-gray-500 w-full">Tampilkan</button>
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
            <div id="modalHistory" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded shadow p-6 w-[90%] md:w-[600px] relative max-h-[80vh] overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4">Riwayat Pembayaran</h2>
                    <div id="historyContent" class="text-sm text-gray-700 space-y-2">
                        Memuat data...
                    </div>
                    <button onclick="closeHistoryModal()" class="absolute top-2 right-2 text-gray-600 hover:text-red-600">✕</button>
                </div>
            </div>
            @csrf
            <button type="button" onclick="openHistoryModal()" class="w-full px-3 py-1 border-1 rounded bg-gray-200 text-black hover:bg-gray-300">
                Lihat History Pembayaran
            </button>
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
                @error('total_bayar')
                    <div class="text-red-600 text-sm mt-1 bg-white p-2 rounded shadow">{{ $message }}</div>
                    <script>
                        window.addEventListener('DOMContentLoaded', () => {
                            document.getElementById('modalBayar').classList.remove('hidden');
                        });
                    </script>
                @enderror
            </div>

            <div class="flex justify-end">
                <!-- Ganti tombol submit -->
                <button type="button" onclick="openModalKonfirmasi()" class="mr-3 px-4 py-2 rounded bg-[#89E355] text-white hover:bg-[#7ED242]">
                    Simpan
                </button>
                <button type="button" onclick="closeBayarModal()" class=" px-4 py-2 rounded border text-gray-600 bg-gray-300 hover:bg-gray-400">Batal</button>    
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500" onclick="closeBayarModal()">✕</button>
    </div>
</div>

{{-- Modal Edit Bayar --}}

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
                <button type="button" onclick="closeModalEditBayar()" class="px-4 py-2 rounded text-gray-700 bg-gray-300 hover:bg-gray-400">Batal</button>
            </div>
        </form>
        <button class="absolute top-2 right-2 text-gray-500" onclick="closeModalEditBayar()">✕</button>
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
    function openBayarModal(notabeli, tanggal, sisa) {
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('formNotabeli').value = notabeli;

    // Sebelumnya kamu pakai tanggal dari data pembelian:
    // document.getElementById('formTanggalNota').value = tanggal;

    // Ganti dengan tanggal sistem (hari ini)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('formTanggalNota').value = today;

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

        function openModalKonfirmasi() {
        document.getElementById('modalKonfirmasiSimpan').classList.remove('hidden');
    }

    function closeModalKonfirmasi() {
        document.getElementById('modalKonfirmasiSimpan').classList.add('hidden');
    }

    function submitFormBayar() {
        // Submit form bayar setelah konfirmasi
        document.querySelector('#modalBayar form').submit();
    }


    function openHistoryModal() {
        const notabeli = document.getElementById('formNotabeli').value;
        const modal = document.getElementById('modalHistory');
        const content = document.getElementById('historyContent');

        modal.classList.remove('hidden');
        content.innerHTML = 'Memuat data...';

        fetch(`/pembayaran-pembelian/history/${notabeli}`)
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
                            <button onclick="editBayar('${item.no}')" 
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 text-sm">
                                Edit
                            </button>
                            <button onclick="hapusBayar('${item.no}')" 
                                class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 text-sm">
                                Hapus
                            </button>
                        </div>
                    </div>
                `).join('');
                }
            })
            .catch(err => {
                content.innerHTML = '<p class="text-red-500">Gagal memuat data.</p>';
            });
    }

    function closeHistoryModal() {
        document.getElementById('modalHistory').classList.add('hidden');
    }

    function editBayar(no) {
    // Contoh: buka modal edit, isi otomatis berdasarkan data existing (bisa pakai API baru atau simpan data sebelumnya)
    alert('Edit data no: ' + no);
    // Kamu bisa fetch detail & buka form edit
}

function editBayar(no) {
    fetch(`/pembayaran-pembelian/data/${no}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('editRekening').value = data.koderekening;
            document.getElementById('editTotal').value = data.total;
            document.getElementById('formEditBayar').action = `/pembayaran-pembelian/${no}`;
            document.getElementById('modalEditBayar').classList.remove('hidden');
        })
        .catch(err => alert('Gagal memuat data.'));
}

function closeModalEditBayar() {
    document.getElementById('modalEditBayar').classList.add('hidden');
}



function hapusBayar(no) {
    if (confirm('Yakin ingin menghapus pembayaran ini?')) {
        fetch(`/pembayaran-pembelian/${no}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => location.reload())
        .catch(err => alert('Gagal menghapus'));
    }
}

let currentHapusNo = null; // simpan no yang mau dihapus

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
    fetch(`/pembayaran-pembelian/${currentHapusNo}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => location.reload())
    .catch(err => alert('Gagal menghapus'));
}

</script>
@endsection
