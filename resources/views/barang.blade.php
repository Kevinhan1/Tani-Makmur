@extends('layouts.main')

@section('title', 'Halaman Barang')
@section('page', 'Barang')

@section('content')
<div class="bg-white p-6 rounded shadow" style="min-height: 800px;">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Data Barang</h2>

        <form method="GET" action="{{ route('barang.index') }}" class="relative ml-20">
            <input type="text" name="search" placeholder="Cari Data"
                value="{{ request('search') }}"
                class="border rounded px-4 py-2 bg-gray-100 text-sm focus:outline-none w-80 text-left" />
            <button type="submit" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                <img src="{{ asset('icons\search-normal.svg') }}" alt="search" class="w-5 h-5" />
            </button>
        </form>

        
        

        <div class="flex items-center mr-5 gap-0 space-x-2 text-sm text-gray-700">
        <!-- Label Halaman -->

            <div id="actionButtons">
                <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                    Tambah +
                </button>
            </div>
        <span>
            Halaman {{ $barang->currentPage() }} dari {{ $barang->lastPage() }}
        </span>

                <!-- Panah Kiri -->
            @if ($barang->onFirstPage())
                <span class="px-2 py-1 border border-gray-400 text-gray-400 rounded font-bold cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ request()->fullUrlWithQuery(['page' => $barang->currentPage() - 1]) }}"
                    class="px-2 py-1 border border-gray-700 text-gray-800 rounded hover:bg-gray-100 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            <!-- Panah Kanan -->
            @if ($barang->hasMorePages())
                <a href="{{ request()->fullUrlWithQuery(['page' => $barang->currentPage() + 1]) }}"
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




    <form id="formBarang">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-500">
                    <th class="px-4 py-2 w-8 font-normal" ></th> <!-- Kolom kosong untuk checkbox -->
                    <th class="px-4 py-2 font-normal">Kode Barang</th>
                    <th class="px-4 py-2 font-normal">Nama Barang</th>
                    <th class="px-4 py-2 font-normal">Harga Beli / zak</th>
                    <th class="px-4 py-2 font-normal">Harga Jual / zak</th>
                    <th class="px-4 py-2 font-normal">1 zak / kg</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barang as $item)
                    <tr class="border-t {{ $item->aktif ? '' : 'text-gray-400' }}">
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" value="{{ $item->kodebarang }}" onchange="updateActionButtons()" style="width:14px; height:14px;">
                        </td>
                        <td class="px-4 py-2">{{ $item->kodebarang }}</td>
                        <td class="px-4 py-2">{{ $item->namabarang }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($item->hbeli, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">Rp {{ number_format($item->hjual, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $item->konversi }} kg</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">
                            @if(request('search'))
                                Barang dengan kata kunci "<strong>{{ request('search') }}</strong>" tidak ditemukan.
                                <br>
                            @else
                                Tidak ada data barang.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>
</div>



<!-- Modal Form Tambah/Edit -->
<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Barang</h3>
        <form id="formDataBarang" method="POST" onsubmit="return confirmSubmit();">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="original_kodebarang" id="original_kodebarang" value="">
												
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="aktif" id="aktif" value="1" class="mr-2 checked:accent-[#89E355]">
                <label for="aktif" class="text-sm">Aktif</label>
            </div>

            <div class="mb-4">
                <label for="kodebarang" class="block text-sm font-medium">Kode Barang</label>
                <input type="text" name="kodebarang" id="kodebarang" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $nextCode }}" readonly>
            </div>

            <div class="mb-4">
                <label for="namabarang" class="block text-sm font-medium">Nama Barang</label>
                <input type="text" name="namabarang" id="namabarang" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="hbeli" class="block text-sm font-medium">Harga Beli / pcs</label>
                <input type="number" step="0.01" name="hbeli" id="hbeli" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="hjual" class="block text-sm font-medium">Harga Jual / pcs</label>
                <input type="number" step="0.01" name="hjual" id="hjual" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="konversi" class="block text-sm font-medium">1 pcs / kg</label>
                <input type="number" step="0.01" name="konversi" id="konversi" class="w-full border rounded px-3 py-2" required>
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

<!-- Modal Putih Alert -->
<div id="custom-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
        <p id="custom-modal-message" class="text-gray-700 mb-4 text-sm"></p>
        <button id="custom-modal-ok" class="bg-[#89E355] hover:bg-[#7ED242] text-white px-4 py-2 rounded">
            OK
        </button>
    </div>
</div>

<script>
function toggleModal() {
    document.getElementById('modalForm').classList.toggle('hidden');
}

// Buka modal untuk tambah barang
function openModalForAdd() {
    document.getElementById('modalTitle').innerText = 'Tambah Barang';
    document.getElementById('formDataBarang').action = "{{ route('barang.store', [], true) }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('original_kodebarang').value = '';

    // Reset form fields
    document.getElementById('aktif').checked = false;
    document.getElementById('kodebarang').value = "{{ $nextCode }}";
    document.getElementById('kodebarang').readOnly = true;
    document.getElementById('namabarang').value = '';
    document.getElementById('hbeli').value = '';
    document.getElementById('hjual').value = '';
    document.getElementById('konversi').value = '';

    toggleModal();
}

// Data barang dari blade ke JS, untuk edit
const barangData = {
    @foreach ($barang as $item)
        "{{ $item->kodebarang }}": {
            kodebarang: "{{ $item->kodebarang }}",
            namabarang: {!! json_encode($item->namabarang) !!},
            hbeli: "{{ $item->hbeli }}",
            hjual: "{{ $item->hjual }}",
            konversi: "{{ $item->konversi }}",
            aktif: "{{ $item->aktif }}"
        },
    @endforeach
};

// Buka modal edit dan isi data form
function editBarang(kode) {
    if (!barangData[kode]) {
        showModalAlert('Data barang tidak ditemukan!');
        return;
    }

    const data = barangData[kode];

    document.getElementById('modalTitle').innerText = 'Edit Barang';
    document.getElementById('formDataBarang').action = "/barang/" + kode; // route update
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('original_kodebarang').value = kode;

    document.getElementById('aktif').checked = data.aktif == 1;
    document.getElementById('kodebarang').value = data.kodebarang;
    document.getElementById('kodebarang').readOnly = true;
    document.getElementById('namabarang').value = data.namabarang;
    document.getElementById('hbeli').value = data.hbeli;
    document.getElementById('hjual').value = data.hjual;
    document.getElementById('konversi').value = data.konversi;

    toggleModal();
}

// Update tombol aksi berdasarkan checkbox
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
            <button onclick="editBarang('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
            <button onclick="hapusBarang()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
        `;
    } else if (selected.length > 1) {
        container.innerHTML = `
            <button onclick="hapusBarang()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
        `;
    } else {
        container.innerHTML = `
            <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah +</button>
        `;
    }
}

// Fungsi hapus (dummy)
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

async function hapusBarang() {
  const selected = Array.from(document.querySelectorAll('.item-checkbox'))
    .filter(cb => cb.checked)
    .map(cb => cb.value);

  if (selected.length === 0) {
    showModalAlert('Pilih data yang akan dihapus terlebih dahulu.');
    return;
  }

  const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
  if (!confirmed) return;

  for (const kode of selected) {
    try {
      const response = await fetch(`/barang/${kode}`, {
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
      showModalAlert(`Error hapus ${kode}: ${error.message}`);
    }
  }

  updateActionButtons();
}



</script>

<style> 
	.item-checkbox {
  accent-color: #ccc; /* Warna default checkbox */
}

.item-checkbox:checked {
  accent-color: #89E355;
}

.header-row {
  color: gray;
  font-weight: normal;
}


</style>
@endsection
