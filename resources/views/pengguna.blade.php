@extends('layouts.main')

@section('title', 'Halaman Pengguna')
@section('page', 'Pengguna')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 600px;">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Data Pengguna</h2>
    <div id="actionButtons">
      <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah +</button>
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
      @foreach ($pengguna as $item)
        <tr class="border-t {{ $item->aktif ? '' : 'text-gray-400' }}">
          <td class="px-4 py-2 text-center">
            <input type="checkbox" class="item-checkbox cursor-pointer" value="{{ $item->kodepengguna }}" onchange="updateActionButtons()" />
          </td>
          <td class="px-4 py-2">{{ $item->kodepengguna }}</td>
          <td class="px-4 py-2">{{ $item->namapengguna }}</td>
          <td class="px-4 py-2">{{ ucfirst($item->status) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Modal Tam & Edit -->
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

<script>
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
  if (!data) return alert('Data pengguna tidak ditemukan!');
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

async function hapusPengguna() {
  const sel = Array.from(document.querySelectorAll('.item-checkbox')).filter(cb => cb.checked).map(cb=>cb.value);
  if (sel.length === 0) return alert('Pilih data yang akan dihapus.');
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
