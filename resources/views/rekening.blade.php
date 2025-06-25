@extends('layouts.main')

@section('title', 'Halaman Rekening')
@section('page', 'Rekening')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Data Rekening</h2>
        <form method="GET" action="{{ route('rekening.index') }}" class="relative ml-20">
            <input type="text" name="search" placeholder="Cari Data"
                value="{{ request('search') }}"
                class="border rounded px-4 py-2 bg-gray-100 text-sm focus:outline-none w-80 text-left" />
            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                <img src="{{ asset('icons\search-normal.svg') }}" alt="search" class="w-5 h-5" />
            </button>
        </form>

        
        

        <div class="flex items-center mr-5 gap-0 space-x-2 text-sm text-gray-700">
                    <div id="actionButtons">
                        <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                            Tambah +
                        </button>
                    </div>
            <!-- Label Halaman -->
            <span>
                Halaman {{ $rekening->currentPage() }} dari {{ $rekening->lastPage() }}
            </span>

                <!-- Panah Kiri -->
            @if ($rekening->onFirstPage())
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ request()->fullUrlWithQuery(['page' => $rekening->currentPage() - 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <!-- Panah Kanan -->
            @if ($rekening->hasMorePages())
                <a href="{{ request()->fullUrlWithQuery(['page' => $rekening->currentPage() + 1]) }}"
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

    <form id="formRekening">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-500">
                    <th class="px-4 py-2 w-8 font-normal"></th>
                    <th class="px-4 py-2 font-normal">Kode Rekening</th>
                    <th class="px-4 py-2 font-normal">Nama Rekening</th>
                    <th class="px-4 py-2 font-normal">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rekening as $item)
                    <tr class="border-t {{ $item->aktif ? '' : 'text-gray-400' }}">
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" value="{{ $item->koderekening }}" onchange="updateActionButtons()" style="width:14px; height:14px;">
                        </td>
                        <td class="px-4 py-2">{{ $item->koderekening }}</td>
                        <td class="px-4 py-2">{{ $item->namarekening }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<!-- Modal Form Tambah/Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Rekening</h3>
        <form id="formDataRekening" method="POST" onsubmit="return confirmSubmit();">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="original_koderekening" id="original_koderekening" value="">

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="aktif" id="aktif" value="1" class="mr-2 checked:accent-[#89E355]">
                <label for="aktif" class="text-sm">Aktif</label>
            </div>

            <div class="mb-4">
                <label for="koderekening" class="block text-sm font-medium">Kode Rekening</label>
                <input type="text" name="koderekening" id="koderekening" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $nextCode }}" readonly>
            </div>

            <div class="mb-4">
                <label for="namarekening" class="block text-sm font-medium">Nama Rekening</label>
                <input type="text" name="namarekening" id="namarekening" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="saldo" class="block text-sm font-medium">Saldo</label>
                <input type="number" name="saldo" id="saldo" class="w-full border rounded px-3 py-2" min="0" step="0.01" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            </div>
        </form>

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

<script>
function toggleModal() {
    document.getElementById('modalForm').classList.toggle('hidden');
}

function openModalForAdd() {
    document.getElementById('modalTitle').innerText = 'Tambah Rekening';
    document.getElementById('formDataRekening').action = "{{ route('rekening.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('original_koderekening').value = '';

    document.getElementById('aktif').checked = false;
    document.getElementById('koderekening').value = "{{ $nextCode }}";
    document.getElementById('namarekening').value = '';
    document.getElementById('saldo').value = 0;

    toggleModal();
}

const rekeningData = {
    @foreach ($rekening as $item)
        "{{ $item->koderekening }}": {
            koderekening: "{{ $item->koderekening }}",
            namarekening: {!! json_encode($item->namarekening) !!},
            saldo: "{{ $item->saldo }}",
            aktif: "{{ $item->aktif }}"
        },
    @endforeach
};

function editRekening(kode) {
    const data = rekeningData[kode];
    if (!data) return showModalAlert('Data tidak ditemukan!');

    document.getElementById('modalTitle').innerText = 'Edit Rekening';
    document.getElementById('formDataRekening').action = "/rekening/" + kode;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('original_koderekening').value = kode;

    document.getElementById('aktif').checked = data.aktif == 1;
    document.getElementById('koderekening').value = data.koderekening;
    document.getElementById('namarekening').value = data.namarekening;
    document.getElementById('saldo').value = data.saldo;

    toggleModal();
}

function updateActionButtons() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selected = Array.from(checkboxes).filter(cb => cb.checked);

    checkboxes.forEach(cb => {
        cb.classList.remove('accent-green-500', 'accent-gray-400');
        cb.classList.add(cb.checked ? 'accent-green-500' : 'accent-gray-400');
    });

    const container = document.getElementById('actionButtons');
    container.innerHTML = '';

    if (selected.length === 1) {
        container.innerHTML = `
            <button onclick="editRekening('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
            <button onclick="hapusRekening()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
        `;
    } else if (selected.length > 1) {
        container.innerHTML = `
            <button onclick="hapusRekening()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
        `;
    } else {
        container.innerHTML = `
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah +</button>
        `;
    }
}

function showConfirmDelete(message) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmDeleteModal');
        document.getElementById('confirmDeleteMessage').textContent = message;

        function cleanUp() {
            document.getElementById('confirmDeleteYes').removeEventListener('click', onYes);
            document.getElementById('confirmDeleteNo').removeEventListener('click', onNo);
            modal.classList.add('hidden');
        }

        function onYes() { cleanUp(); resolve(true); }
        function onNo() { cleanUp(); resolve(false); }

        document.getElementById('confirmDeleteYes').addEventListener('click', onYes);
        document.getElementById('confirmDeleteNo').addEventListener('click', onNo);
        modal.classList.remove('hidden');
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

async function hapusRekening() {
    const selected = Array.from(document.querySelectorAll('.item-checkbox'))
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (selected.length === 0) return showModalAlert('Pilih data yang akan dihapus.');

    const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
    if (!confirmed) return;

    for (const kode of selected) {
        try {
            const response = await fetch(`/rekening/${kode}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });

            if (!response.ok) throw new Error('Gagal menghapus data');

            const checkbox = document.querySelector(`.item-checkbox[value="${kode}"]`);
            if (checkbox) checkbox.closest('tr').remove();
        } catch (error) {
            showModalAlert(`Gagal menghapus ${kode}: ${error.message}`);
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
