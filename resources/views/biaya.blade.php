@extends('layouts.main')

@section('title', 'Halaman Biaya')
@section('page', 'Biaya')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Data Biaya</h2>

        {{-- Pagination + Tombol Tambah --}}
        <div class="flex items-center gap-4 text-sm text-gray-700">

                        {{-- Tombol Tambah --}}
                <div id="actionButtons">
                    <button type="button" onclick="openModalForAdd()"
                        class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242] ml-4">
                        Tambah +
                    </button>
                </div>

            @if ($biaya instanceof \Illuminate\Pagination\LengthAwarePaginator && $biaya->count())
                <span>Halaman {{ $biaya->currentPage() }} dari {{ $biaya->lastPage() }}</span>

                {{-- Panah kiri --}}
                @if ($biaya->onFirstPage())
                    <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                        <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                @else
                    <a href="{{ request()->fullUrlWithQuery(['page' => $biaya->currentPage() - 1]) }}"
                        class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                        <svg class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                {{-- Panah kanan --}}
                @if ($biaya->hasMorePages())
                    <a href="{{ request()->fullUrlWithQuery(['page' => $biaya->currentPage() + 1]) }}"
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
            @endif
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" action="{{ route('biaya.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div>
            <label class="text-sm text-gray-600">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal', date('Y-m-d', strtotime('-7 days'))) }}"
                class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
        </div>

        <div>
            <label class="text-sm text-gray-600">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir', date('Y-m-d')) }}"
                class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
        </div>

        <div class="flex items-end">
            <button type="submit" class="rounded bg-gray-400 text-sm text-white px-6 py-2 hover:bg-gray-500 w-full">
                Tampilkan
            </button>
        </div>
        <div class="flex items-end">
            <a href="{{ route('biaya.pdf', request()->all()) }}"
                target="_blank"
                class="flex gap-2 justify-center rounded border border-gray-300 bg-gray-100 text-sm text-gray px-4 py-2 hover:bg-gray-400 w-full">
                <span class="text-center">Print PDF</span>
                <img src="{{ asset('icons/printer.svg') }}" alt="Printer Icon" class="w-5 h-5">
            </a>
        </div>
    </form>

    



    <form id="formBiaya">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-gray-500">
                <th class="px-4 py-2 w-8 font-normal"></th>
                <th class="px-4 py-2 font-normal">No Biaya</th>
                <th class="px-4 py-2 font-normal">Tanggal</th>
                <th class="px-4 py-2 font-normal">Rekening</th>
                <th class="px-4 py-2 font-normal">Keterangan</th>
                <th class="px-4 py-2 font-normal text-left">Total</th>
                <th class="px-4 py-2 font-normal">Pengguna</th>
            </tr>
        </thead>
        <tbody>
            @if ($biaya->isEmpty())
                <tr>
                    <td colspan="7" class="text-center text-gray-500 py-4">
                        Tidak ada data ditemukan.
                    </td>
                </tr>
            @else
                @foreach ($biaya as $item)
                    <tr class="border-t">
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400"
                                value="{{ $item->nobiaya }}" onchange="updateActionButtons()"
                                style="width:14px; height:14px;">
                        </td>
                        <td class="px-4 py-2">{{ $item->nobiaya }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                        <td class="px-4 py-2">{{ $item->rekening->namarekening ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->keterangan }}</td>
                        <td class="px-4 py-2 text-left">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $item->pengguna->namapengguna ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</form>


<!-- Modal Form Tambah/Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Biaya</h3>
        <form id="formDataBiaya" method="POST" onsubmit="return confirmSubmit();">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="original_nobiaya" id="original_nobiaya" value="">
            
            <div class="mb-4">
                <label for="nobiaya" class="block text-sm font-medium">No Biaya</label>
                <input type="text" name="nobiaya" id="nobiaya" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ old('nobiaya' ,$nextCode) }}" readonly>
            </div>

            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="w-full border rounded px-3 py-2" value="{{ old('tanggal', date('Y-m-d')) }}" required >
            </div>

            <div class="mb-4">
                <label for="koderekening" class="block text-sm font-medium">Rekening</label>
                <select name="koderekening" id="koderekening" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Rekening --</option>
                        @foreach ($rekening as $rek)
                            <option value="{{ $rek->koderekening }}" {{ old('koderekening') == $rek->koderekening ? 'selected' : '' }}>
                                {{ $rek->namarekening }}
                            </option>
                        @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('keterangan') }}</textarea>
            </div>


            <div class="mb-4">
                <label for="total" class="block text-sm font-medium">Total</label>
                <input type="number" step="0.01" name="total" id="total"
                    class="w-full border rounded px-3 py-2 text-left @error('total') border-red-500 @enderror"
                    value="{{ old('total') }}"
                    required>
                @error('total')
                    <div class="text-red-600 text-sm mt-1 bg-white p-2 rounded shadow">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            </div>
        </form>

        <!-- Form hapus tersembunyi -->
        <form id="formDelete" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
    <div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-60">
    <div class="bg-white p-6 rounded shadow-lg w-80 text-center">
        <p class="mb-6 text-lg" id="confirmDeleteMessage">Apakah Anda yakin ingin menghapus data?</p>
        <div class="flex justify-center space-x-4">
        <button id="confirmDeleteYes" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
        <button id="confirmDeleteNo" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Tidak</button>
        </div>
    </div>
</div>

<!-- Modal Putih -->
<div id="custom-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
        <p id="custom-modal-message" class="text-gray-700 mb-4 text-sm"></p>
        <button id="custom-modal-ok" class="bg-[#89E355] hover:bg-[#7ED242] text-white px-4 py-2 rounded">
            OK
        </button>
    </div>
</div>



<script>
    const isAdmin = @json(session('user') && session('user')->status === 'admin');

function toggleModal() {
    document.getElementById('modalForm').classList.toggle('hidden');
}

function openModalForAdd() {
    document.getElementById('modalTitle').innerText = 'Tambah Biaya';
    document.getElementById('formDataBiaya').action = "{{ route('biaya.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('original_nobiaya').value = '';

    document.getElementById('nobiaya').value = "{{ $nextCode }}";
    document.getElementById('tanggal').value = "{{ date('d-m-Y') }}";
    document.getElementById('koderekening').value = '';
    document.getElementById('keterangan').value = '';
    document.getElementById('total').value = '';

    toggleModal();
}

const biayaData = {
    @foreach ($biaya as $item)
        "{{ $item->nobiaya }}": {
            nobiaya: "{{ $item->nobiaya }}",
            tanggal: "{{ $item->tanggal }}",
            koderekening: "{{ $item->koderekening }}",
            keterangan: {!! json_encode($item->keterangan) !!},
            total: "{{ $item->total }}"
        },
    @endforeach
};


function editBiaya(nobiaya) {
    if (!biayaData[nobiaya]) {
        showModalAlert('Data biaya tidak ditemukan!');
        return;
    }

    const data = biayaData[nobiaya];

    document.getElementById('modalTitle').innerText = 'Edit Biaya';
    document.getElementById('formDataBiaya').action = "/biaya/" + nobiaya;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('original_nobiaya').value = nobiaya;

    document.getElementById('nobiaya').value = data.nobiaya;
    document.getElementById('tanggal').value = data.tanggal;
    document.getElementById('koderekening').value = data.koderekening;
    document.getElementById('keterangan').value = data.keterangan;
    document.getElementById('total').value = data.total;

    toggleModal();
}




function updateActionButtons() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selected = Array.from(checkboxes).filter(cb => cb.checked);

    const container = document.getElementById('actionButtons');
    container.innerHTML = ''; // kosongkan dulu

    // 💡 Semua user bisa Tambah jika tidak ada yang dicentang
    if (selected.length === 0) {
        container.innerHTML = `
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah +</button>
        `;
        return; // selesai
    }

    // 💡 Kalau admin dan ada data terpilih, bisa edit/hapus
    if (isAdmin) {
        if (selected.length === 1) {
            container.innerHTML = `
                <button onclick="editBiaya('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
                <button onclick="hapusBiaya()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
            `;
        } else {
            container.innerHTML = `
                <button onclick="hapusBiaya()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
            `;
        }
    }
}

@if ($errors->any())
    window.addEventListener('DOMContentLoaded', () => {
        // Jika ada error validasi, buka kembali modal form Tambah/Edit
        document.getElementById('modalForm').classList.remove('hidden');
    });
@endif


function showConfirmDelete(message) {
    return new Promise((resolve) => {
    const modal = document.getElementById('confirmDeleteModal');
    const msgElem = document.getElementById('confirmDeleteMessage');
    const btnYes = document.getElementById('confirmDeleteYes');
    const btnNo = document.getElementById('confirmDeleteNo');

    msgElem.textContent = message;
    modal.classList.remove('hidden');

        function cleanUp() {
        btnYes.removeEventListener('click', onYes);
        btnNo.removeEventListener('click', onNo);
        modal.classList.add('hidden');
        }

        function onYes() {
        cleanUp();
        resolve(true);
        }

        function onNo() {
        cleanUp();
        resolve(false);
        }

        btnYes.addEventListener('click', onYes);
        btnNo.addEventListener('click', onNo);
    });
    }

    function showModalAlert(message, callbackOk = null) {
        const modal = document.getElementById('custom-modal');
        const msgContainer = document.getElementById('custom-modal-message');
        const okBtn = document.getElementById('custom-modal-ok');

        msgContainer.textContent = message;
        modal.classList.remove('hidden');

        okBtn.onclick = () => {
            modal.classList.add('hidden');
            if (typeof callbackOk === 'function') callbackOk();
        };
    }

    async function hapusBiaya() {
    const selected = Array.from(document.querySelectorAll('.item-checkbox'))
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (selected.length === 0) {
        showModalAlert('Pilih data yang akan dihapus terlebih dahulu.');
        return;
    }

    const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
    if (!confirmed) return;

    for (const nobiaya of selected) {
        try {
        const response = await fetch(`/biaya/${nobiaya}`, {
            method: 'DELETE',
            headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
            },
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Gagal menghapus data');
        }

        const checkbox = document.querySelector(`.item-checkbox[value="${nobiaya}"]`);
        if (checkbox) {
            checkbox.closest('tr').remove();
        }
        } catch (error) {
        showModalAlert(`Error hapus ${nobiaya}: ${error.message}`);
        }
    }

    updateActionButtons();
    }

    </script>

    <style>
    .item-checkbox {
    accent-color: #ccc;
    }

    .item-checkbox:checked {
    accent-color: #89E355;
    }
    </style>
    @endsection
