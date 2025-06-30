@extends('layouts.main')

@section('title', 'Halaman Pengguna')
@section('page', 'Pengguna')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Data Pengguna</h2>
    
    <form method="GET" action="{{ route('pengguna.index') }}" class="relative ml-20">
        <input type="text" name="search" placeholder="Cari Data"
            value="{{ request('search') }}"
            class="border rounded px-4 py-2 bg-gray-100 text-sm focus:outline-none w-80 text-left" />
        <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
            <img src="{{ asset('icons/search-normal.svg') }}" alt="search" class="w-5 h-5" />
        </button>
    </form>

    <div class="flex items-center mr-5 gap-0 space-x-2 text-sm text-gray-700">
        <div id="actionButtons">
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                Tambah +
            </button>
        </div>

        <span>
            Halaman {{ $pengguna->currentPage() }} dari {{ $pengguna->lastPage() }}
        </span>

        @if ($pengguna->onFirstPage())
            <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ request()->fullUrlWithQuery(['page' => $pengguna->currentPage() - 1]) }}"
                class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        @if ($pengguna->hasMorePages())
            <a href="{{ request()->fullUrlWithQuery(['page' => $pengguna->currentPage() + 1]) }}"
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

  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-gray-500 font-normal">
        <th class="px-4 py-2 w-8"></th>
        <th class="px-4 py-2 font-normal">Kode Pengguna</th>
        <th class="px-4 py-2 font-normal">Nama Pengguna</th>
        <th class="px-4 py-2 font-normal">Status</th>
      </tr>
    </thead>
   <tbody>
        @forelse ($pengguna as $item)
            <tr class="border-t {{ $item->aktif ? '' : 'text-gray-400' }}">
                <td class="px-4 py-2 text-center">
                    <input type="checkbox" class="item-checkbox cursor-pointer" value="{{ $item->kodepengguna }}" onchange="updateActionButtons()" />
                </td>
                <td class="px-4 py-2">{{ $item->kodepengguna }}</td>
                <td class="px-4 py-2">{{ $item->namapengguna }}</td>
                <td class="px-4 py-2">{{ ucfirst($item->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-gray-500 py-4">
                    @if(request('search'))
                        Data pengguna dengan kata kunci "<strong>{{ request('search') }}</strong>" tidak ditemukan.
                    @else
                        Tidak ada data pengguna.
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
  </table>
</div>

<!-- Modal Tambah & Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
  <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
    <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pengguna</h3>
    <form id="formData" method="POST" onsubmit="return confirmSubmit();">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="id" id="formId">

      <div class="mb-4 flex items-center">
        <input type="checkbox" name="aktif" id="aktif" value="1" class="mr-2 checked:accent-[#89E355]">
        <label for="aktif">Aktif</label>
      </div>

      <div class="mb-4">
        <label for="kodepengguna">Kode Pengguna</label>
        <input type="text" name="kodepengguna" id="kodepengguna" value="{{ $nextCode }}"
              class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
      </div>

      <div class="mb-4">
        <label for="namapengguna">Nama Pengguna</label>
        <input type="text" name="namapengguna" id="namapengguna" class="w-full border rounded px-3 py-2" required>
        @error('namapengguna')
          <small class="text-red-600">{{ $message }}</small>
        @enderror
      </div>

      <div class="mb-4">
        <label for="katakunci">Kata Kunci (Password)</label>
        <input type="password" name="katakunci" id="katakunci" class="w-full border rounded px-3 py-2">
        <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password saat edit.</small>
      </div>

      <div class="mb-4">
        <label for="status">Status</label>
        <select name="status" id="status" class="w-full border rounded px-3 py-2" required>
          <option value="user">Pengguna</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="flex justify-end space-x-2">
        <button type="submit" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
        <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Hapus -->
<div id="confirmDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-60">
  <div class="bg-white p-6 rounded shadow-lg w-80 text-center">
    <p id="confirmDeleteMessage" class="mb-6 text-lg"></p>
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
  window.addEventListener('DOMContentLoaded', () => {
    @if ($errors->any())
        document.getElementById('modalTitle').innerText = 'Tambah Pengguna';
        document.getElementById('formData').action = "{{ route('pengguna.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('formId').value = '';
        document.getElementById('modalForm').classList.remove('hidden');

        // Isi kembali input jika error
        document.getElementById('namapengguna').value = @json(old('namapengguna'));
        document.getElementById('katakunci').value = @json(old('katakunci'));
        document.getElementById('status').value = "{{ old('status', 'user') }}";
        document.getElementById('aktif').checked = "{{ old('aktif') }}" === "1";
    @endif
});
function toggleModal() {
  document.getElementById('modalForm').classList.toggle('hidden');
}

function openModalForAdd() {
  document.getElementById('modalTitle').innerText = 'Tambah Pengguna';
  document.getElementById('formData').action = "{{ route('pengguna.store') }}";
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('formId').value = '';
  document.getElementById('aktif').checked = false;
  document.getElementById('kodepengguna').value = "{{ $nextCode }}";
  document.getElementById('namapengguna').value = '';
  document.getElementById('katakunci').value = '';
  document.getElementById('status').value = 'user';
  toggleModal();
}

const penggunaData = {
  @foreach ($pengguna as $item)
    "{{ $item->kodepengguna }}": {
      kodepengguna: "{{ $item->kodepengguna }}",
      namapengguna: @json($item->namapengguna), 
      status: "{{ $item->status }}",
      aktif: "{{ $item->aktif }}"
    },
  @endforeach
};

function editPengguna(id) {
  const data = penggunaData[id];
  if (!data) return showModalAlert('Data pengguna tidak ditemukan!');
  document.getElementById('modalTitle').innerText = 'Edit Pengguna';
  document.getElementById('formData').action = "/pengguna/" + id;
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('formId').value = id;
  document.getElementById('aktif').checked = data.aktif == 1;
  document.getElementById('kodepengguna').value = data.kodepengguna;
  document.getElementById('namapengguna').value = data.namapengguna;
  document.getElementById('katakunci').value = '';
  document.getElementById('status').value = data.status;
  toggleModal();
}

function updateActionButtons() {
  const sel = Array.from(document.querySelectorAll('.item-checkbox')).filter(cb => cb.checked);
  const ctn = document.getElementById('actionButtons');
  ctn.innerHTML = '';
  if (sel.length === 1) {
    ctn.innerHTML = `<button onclick="editPengguna('${sel[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</button>
                      <button onclick="hapusPengguna()" class="bg-red-500 text-white px-4 py-2 rounded ml-2">Hapus</button>`;
  } else if (sel.length > 1) {
    ctn.innerHTML = `<button onclick="hapusPengguna()" class="bg-red-500 text-white px-4 py-2 rounded">Hapus</button>`;
  } else {
    ctn.innerHTML = `<button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded">Tambah +</button>`;
  }
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

async function hapusPengguna() {
  const sel = Array.from(document.querySelectorAll('.item-checkbox')).filter(cb => cb.checked).map(cb=>cb.value);
  if (sel.length === 0) return showModalAlert('Pilih data yang akan dihapus.');
  if (!await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${sel.length} data?`)) return;

  for (let id of sel) {
    await fetch(`/pengguna/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
      }
    });
    document.querySelector(`.item-checkbox[value="${id}"]`).closest('tr').remove();
  }
  updateActionButtons();
}

function showConfirmDelete(msg) {
  return new Promise(res => {
    const m = document.getElementById('confirmDeleteModal');
    document.getElementById('confirmDeleteMessage').innerText = msg;
    m.classList.remove('hidden');
    document.getElementById('confirmDeleteYes').onclick = () => { m.classList.add('hidden'); res(true); };
    document.getElementById('confirmDeleteNo').onclick = () => { m.classList.add('hidden'); res(false); };
  });
}
</script>

<style>
.item-checkbox { accent-color: #ccc; }
.item-checkbox:checked { accent-color: #89E355; }
</style>
@endsection
