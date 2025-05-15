@extends('layouts.main')

@section('title', 'Halaman Pemasok')
@section('page', 'Pemasok')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 690px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Data Pemasok</h2>
        <div id="actionButtons">
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                Tambah +
            </button>
        </div>
    </div>

    <form id="formPemasok">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-500">
                    <th class="px-4 py-2 w-8 font-normal"></th> <!-- checkbox -->
                    <th class="px-4 py-2 font-normal">Kode Pemasok</th>
                    <th class="px-4 py-2 font-normal">Nama Pemasok</th>
                    <th class="px-4 py-2 font-normal">Alamat Pemasok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pemasok as $item)
                    <tr class="border-t">
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" value="{{ $item->kodepemasok }}" onchange="updateActionButtons()" style="width:14px; height:14px;">
                        </td>
                        <td class="px-4 py-2">{{ $item->kodepemasok }}</td>
                        <td class="px-4 py-2">{{ $item->namapemasok }}</td>
                        <td class="px-4 py-2">{{ $item->alamatpemasok }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<!-- Modal Form Tambah/Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pemasok</h3>
        <form id="formDataPemasok" method="POST" onsubmit="return confirmSubmit();">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="original_kodepemasok" id="original_kodepemasok" value="">

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="aktif" id="aktif" value="1" class="mr-2">
                <label for="aktif" class="text-sm">Aktif</label>
            </div>

            <div class="mb-4">
                <label for="kodepemasok" class="block text-sm font-medium">Kode Pemasok</label>
                <input type="text" name="kodepemasok" id="kodepemasok" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $nextCode }}" readonly>
            </div>

            <div class="mb-4">
                <label for="namapemasok" class="block text-sm font-medium">Nama Pemasok</label>
                <input type="text" name="namapemasok" id="namapemasok" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="alamatpemasok" class="block text-sm font-medium">Alamat Pemasok</label>
                <textarea name="alamatpemasok" id="alamatpemasok" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
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

<script>
function toggleModal() {
    document.getElementById('modalForm').classList.toggle('hidden');
}

function openModalForAdd() {
    document.getElementById('modalTitle').innerText = 'Tambah Pemasok';
    document.getElementById('formDataPemasok').action = "{{ route('pemasok.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('original_kodepemasok').value = '';

    document.getElementById('aktif').checked = false;
    document.getElementById('kodepemasok').value = "{{ $nextCode }}";
    document.getElementById('kodepemasok').readOnly = true;
    document.getElementById('namapemasok').value = '';
    document.getElementById('alamatpemasok').value = '';

    toggleModal();
}

const pemasokData = {
    @foreach ($pemasok as $item)
        "{{ $item->kodepemasok }}": {
            kodepemasok: "{{ $item->kodepemasok }}",
            namapemasok: {!! json_encode($item->namapemasok) !!},
            alamatpemasok: {!! json_encode($item->alamatpemasok) !!},
            aktif: "{{ $item->aktif }}"
        },
    @endforeach
};

function editPemasok(kode) {
    if (!pemasokData[kode]) {
        alert('Data pemasok tidak ditemukan!');
        return;
    }

    const data = pemasokData[kode];

    document.getElementById('modalTitle').innerText = 'Edit Pemasok';
    document.getElementById('formDataPemasok').action = "/pemasok/" + kode;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('original_kodepemasok').value = kode;

    document.getElementById('aktif').checked = data.aktif == 1;
    document.getElementById('kodepemasok').value = data.kodepemasok;
    document.getElementById('kodepemasok').readOnly = true;
    document.getElementById('namapemasok').value = data.namapemasok;
    document.getElementById('alamatpemasok').value = data.alamatpemasok;

    toggleModal();
}

function updateActionButtons() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selected = Array.from(checkboxes).filter(cb => cb.checked);

    // Update warna checkbox
    checkboxes.forEach(cb => {
        cb.classList.remove('accent-green-500', 'accent-gray-400');
        cb.classList.add(cb.checked ? 'accent-green-500' : 'accent-gray-400');
    });

    const container = document.getElementById('actionButtons');
    container.innerHTML = '';

    if (selected.length === 1) {
        container.innerHTML = `
            <button onclick="editPemasok('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
            <button onclick="hapusPemasok()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
        `;
    } else if (selected.length > 1) {
        container.innerHTML = `
            <button onclick="hapusPemasok()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
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

async function hapusPemasok() {
  const selected = Array.from(document.querySelectorAll('.item-checkbox'))
    .filter(cb => cb.checked)
    .map(cb => cb.value);

  if (selected.length === 0) {
    alert('Pilih data yang akan dihapus terlebih dahulu.');
    return;
  }

  const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
  if (!confirmed) return;

  for (const kode of selected) {
    try {
      const response = await fetch(`/pemasok/${kode}`, {
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

      // Hapus baris dari DOM
      const checkbox = document.querySelector(`.item-checkbox[value="${kode}"]`);
      if (checkbox) {
        checkbox.closest('tr').remove();
      }
    } catch (error) {
      alert(`Error hapus ${kode}: ${error.message}`);
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
