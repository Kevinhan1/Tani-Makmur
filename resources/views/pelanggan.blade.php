@extends('layouts.main')

@section('title', 'Halaman Pelanggan')
@section('page', 'Pelanggan')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<div class="bg-white p-6 rounded shadow" style="min-height: 690px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Data Pelanggan</h2>
        <div id="actionButtons">
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                Tambah +
            </button>
        </div>
    </div>

    <form id="formPelanggan">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-500">
                    <th class="px-4 py-2 font-normal"></th>
                    <th class="px-4 py-2 font-normal">Kode Pelanggan</th>
                    <th class="px-4 py-2 font-normal">Nama Pelanggan</th>
                    <th class="px-4 py-2 font-normal">Nama Kios</th>
                    <th class="px-4 py-2 font-normal">Alamat</th>
                    <th class="px-4 py-2 font-normal">Kelurahan</th>
                    <th class="px-4 py-2 font-normal">Kecamatan</th>
                    <th class="px-4 py-2 font-normal">Kota</th>
																					<th class="px-4 py-2 font-normal">KTP</th>
																					<th class="px-4 py-2 font-normal">NPWP</th>
																					<th class="px-4 py-2 font-normal">NITKU</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelanggan as $item)
                <tr class="border-t">
                    <td class="px-4 py-2 text-center">
                        <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" value="{{ $item->kodepelanggan }}" onchange="updateActionButtons()" style="width:14px; height:14px;">
                    </td>
                    <td class="px-4 py-2">{{ $item->kodepelanggan }}</td>
                    <td class="px-4 py-2">{{ $item->namapelanggan }}</td>
                    <td class="px-4 py-2">{{ $item->namakios }}</td>
                    <td class="px-4 py-2">{{ $item->alamat }}</td>
                    <td class="px-4 py-2">{{ $item->kelurahan }}</td>
                    <td class="px-4 py-2">{{ $item->kecamatan }}</td>
                    <td class="px-4 py-2">{{ $item->kota }}</td>
                    <td class="px-4 py-2">{{ $item->ktp }}</td>
                    <td class="px-4 py-2">{{ $item->npwp }}</td>
                    <td class="px-4 py-2">{{ $item->nitku }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>

<!-- Modal Form Tambah/Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-2xl">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pelanggan</h3>
        <form id="formDataPelanggan" method="POST" onsubmit="return confirmSubmit();">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="original_kodepelanggan" id="original_kodepelanggan" value="">

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="aktif" id="aktif" value="1" class="mr-2">
                <label for="aktif" class="text-sm">Aktif</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
            <!--1--> 
																<div>
                    <label for="kodepelanggan" class="block text-sm font-medium">Kode Pelanggan</label>
                    <input type="text" name="kodepelanggan" id="kodepelanggan" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $nextCode }}" readonly>
                </div>
															
												<!--2--> 
																<div>
                    <label for="kecamatan" class="block text-sm font-medium">Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatan" class="w-full border rounded px-3 py-2">
                </div>

																
												
												<!--3--> 
																<div>
                    <label for="namapelanggan" class="block text-sm font-medium">Nama Pelanggan</label>
                    <input type="text" name="namapelanggan" id="namapelanggan" class="w-full border rounded px-3 py-2" required>
                </div>
															
												<!--4-->	
																<div>
                    <label for="kota" class="block text-sm font-medium">Kota</label>
                    <input type="text" name="kota" id="kota" class="w-full border rounded px-3 py-2">
                </div>	
            <!--5--> 
															<div>
                    <label for="namakios" class="block text-sm font-medium">Nama Kios</label>
                    <input type="text" name="namakios" id="namakios" class="w-full border rounded px-3 py-2">
                </div>		
												<!--6--> 
															<div>
                   <label for="ktp" class="block text-sm font-medium">KTP</label>
                   <input type="text" name="ktp" id="ktp" class="w-full border rounded px-3 py-2">
               </div>
												<!--7--> 
																<div>
                    <label for="alamat" class="block text-sm font-medium">Alamat</label>
                    <input type="text" name="alamat" id="alamat" class="w-full border rounded px-3 py-2">
                </div>
												<!--8--> 				
																<div>
                    <label for="npwp" class="block text-sm font-medium">NPWP</label>
                    <input type="text" name="npwp" id="npwp" class="w-full border rounded px-3 py-2">
                </div>
            <!--9-->
																

            <!--10-->
																<div>
																					<label for="kelurahan" class="block text-sm font-medium">Kelurahan</label>
																					<input type="text" name="kelurahan" id="kelurahan" class="w-full border rounded px-3 py-2">
																</div>
																	

                <div>
                    <label for="nitku" class="block text-sm font-medium">NITKU</label>
                    <input type="text" name="nitku" id="nitku" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
																<button type="submit" id="submitBtn" class="px-4 py-2 bg-[#89E355] text-white rounded hover:bg-[#7ED242]">Simpan</button>
                <button type="button" onclick="toggleModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
            </div>
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

function revealSensitive(id, ktp, npwp, nitku) {
    document.getElementById(`ktp-${id}`).innerText = ktp;
    document.getElementById(`npwp-${id}`).innerText = npwp;
    document.getElementById(`nitku-${id}`).innerText = nitku;
}

function toggleModal() {
    document.getElementById('modalForm').classList.toggle('hidden');
}

function openModalForAdd() {
    document.getElementById('modalTitle').innerText = 'Tambah Pelanggan';
    document.getElementById('formDataPelanggan').action = "{{ route('pelanggan.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('original_kodepelanggan').value = '';

    ['aktif', 'namapelanggan', 'namakios', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'ktp', 'npwp', 'nitku'].forEach(id => {
        if (id === 'aktif') {
            document.getElementById(id).checked = false;
        } else {
            document.getElementById(id).value = '';
        }
    });

    document.getElementById('kodepelanggan').value = "{{ $nextCode }}";
    document.getElementById('kodepelanggan').readOnly = true;

    toggleModal();
}

const pelangganData = {
    @foreach ($pelanggan as $item)
    "{{ $item->kodepelanggan }}": {!! json_encode($item) !!},
    @endforeach
};

function editPelanggan(kode) {
    const data = pelangganData[kode];
    if (!data) return alert('Data tidak ditemukan');

    document.getElementById('modalTitle').innerText = 'Edit Pelanggan';
    document.getElementById('formDataPelanggan').action = "/pelanggan/" + kode;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('original_kodepelanggan').value = kode;

    document.getElementById('aktif').checked = data.aktif == 1;
    ['kodepelanggan', 'namapelanggan', 'namakios', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'ktp', 'npwp', 'nitku'].forEach(id => {
        document.getElementById(id).value = data[id] ?? '';
    });

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
            <button onclick="editPelanggan('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
            <button onclick="hapusPelanggan()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
        `;
    } else if (selected.length > 1) {
        container.innerHTML = `
            <button onclick="hapusPelanggan()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
        `;
    } else {
        container.innerHTML = `
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah +</button>
        `;
    }
}

function showConfirmDelete(message) {
    return new Promise(resolve => {
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

async function hapusPelanggan() {
    const selected = Array.from(document.querySelectorAll('.item-checkbox'))
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (selected.length === 0) return alert('Pilih data terlebih dahulu.');

    const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
    if (!confirmed) return;

    for (const kode of selected) {
        try {
            const response = await fetch(`/pelanggan/${kode}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });

            if (!response.ok) throw new Error('Gagal menghapus data');

            const row = document.querySelector(`.item-checkbox[value="${kode}"]`)?.closest('tr');
            if (row) row.remove();
        } catch (error) {
            alert(`Gagal menghapus ${kode}: ${error.message}`);
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
