    @extends('layouts.main')

    @section('title', 'Halaman Pindah Saldo')
    @section('page', 'Pindah Saldo')

    @section('content')
    <div class="bg-white p-6 rounded shadow" style="min-height: 690px;">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Data Pindah Saldo</h2>
            <div id="actionButtons">
                <button onclick="openModalForAdd()" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                    Tambah +
                </button>
            </div>
        </div>

        <form id="formPindahSaldo">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-500">
                        <th class="px-4 py-2 w-8 font-normal"></th>
                        <th class="px-4 py-2 font-normal">No Pindah Buku</th>
                        <th class="px-4 py-2 font-normal">Tanggal</th>
                        <th class="px-4 py-2 font-normal">Rekening Asal</th>
                        <th class="px-4 py-2 font-normal">Rekening Tujuan</th>
                        <th class="px-4 py-2 font-normal">Keterangan</th>
                        <th class="px-4 py-2 font-normal text-left">Total</th>
                        <th class="px-4 py-2 font-normal">Pengguna</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pindahsaldo as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" value="{{ $item->nopindahbuku }}" onchange="updateActionButtons()" style="width:14px; height:14px;">
                            </td>
                            <td class="px-4 py-2">{{ $item->nopindahbuku }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2">{{ $item->rekeningAsal->namarekening ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->rekeningTujuan->namarekening ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $item->keterangan }}</td>
                            <td class="px-4 py-2 text-left">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $item->pengguna->namapengguna ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
            <h3 id="modalTitle" class="text-xl font-semibold mb-4">Tambah Pindah Saldo</h3>
            <form id="formDataPindahSaldo" method="POST" onsubmit="return confirmSubmit();">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="original_nopindahbuku" id="original_nopindahbuku" value="">

                <div class="mb-4">
                    <label for="nopindahbuku" class="block text-sm font-medium">No Pindah Buku</label>
                    <input type="text" name="nopindahbuku" id="nopindahbuku" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $nextCode }}" readonly>
                </div>

                <div class="mb-4">
                    <label for="tanggal" class="block text-sm font-medium">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="w-full border rounded px-3 py-2" required value="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-4">
                    <label for="rekening_asal" class="block text-sm font-medium">Rekening Asal</label>
                    <select name="rekeningasal" id="rekening_asal" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Rekening Asal --</option>
                        @foreach ($rekening as $rek)
                            <option value="{{ $rek->koderekening }}">{{ $rek->namarekening }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="rekening_tujuan" class="block text-sm font-medium">Rekening Tujuan</label>
                    <select name="rekeningtujuan" id="rekening_tujuan" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Rekening Tujuan --</option>
                        @foreach ($rekening as $rek)
                            <option value="{{ $rek->koderekening }}">{{ $rek->namarekening }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="total" class="block text-sm font-medium">Total</label>
                    <input type="number" step="0.01" name="total" id="total" class="w-full border rounded px-3 py-2 text-left" required>
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
        document.getElementById('modalTitle').innerText = 'Tambah Pindah Saldo';
        document.getElementById('formDataPindahSaldo').action = "{{ route('pindahsaldo.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('original_nopindahbuku').value = '';

        document.getElementById('nopindahbuku').value = "{{ $nextCode }}";
        document.getElementById('tanggal').value = "{{ date('Y-m-d') }}";
        document.getElementById('rekening_asal').value = '';
        document.getElementById('rekening_tujuan').value = '';
        document.getElementById('keterangan').value = '';
        document.getElementById('total').value = '';

        toggleModal();
    }

    const pindahsaldoData = {
        @foreach ($pindahsaldo as $item)
            "{{ $item->nopindahbuku }}": {
                nopindahbuku: "{{ $item->nopindahbuku }}",
                tanggal: "{{ $item->tanggal }}",
                rekeningasal: "{{ $item->koderekeningasal }}",
                rekeningtujuan: "{{ $item->koderekeningtujuan }}",
                keterangan: {!! json_encode($item->keterangan) !!},
                total: "{{ $item->total }}"
            },
        @endforeach
    };

    function editPindahSaldo(nopindahbuku) {
        if (!pindahsaldoData[nopindahbuku]) {
            alert('Data pindah saldo tidak ditemukan!');
            return;
        }

        const data = pindahsaldoData[nopindahbuku];

        document.getElementById('modalTitle').innerText = 'Edit Pindah Saldo';
        document.getElementById('formDataPindahSaldo').action = "/pindahsaldo/" + nopindahbuku;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('original_nopindahbuku').value = nopindahbuku;

        document.getElementById('nopindahbuku').value = data.nopindahbuku;
        document.getElementById('tanggal').value = data.tanggal;
        document.getElementById('rekening_asal').value = data.rekeningasal;
        document.getElementById('rekening_tujuan').value = data.rekeningtujuan;
        document.getElementById('keterangan').value = data.keterangan;
        document.getElementById('total').value = data.total;

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
                <button onclick="editPindahSaldo('${selected[0].value}')" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
                <button onclick="hapusPindahSaldo()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 ml-2">Hapus</button>
            `;
        } else if (selected.length > 1) {
            container.innerHTML = `
                <button onclick="hapusPindahSaldo()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
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


    async function hapusPindahSaldo() {
    const selected = Array.from(document.querySelectorAll('.item-checkbox'))
        .filter(cb => cb.checked)
        .map(cb => cb.value);

    if (selected.length === 0) {
        alert('Pilih data yang akan dihapus terlebih dahulu.');
        return;
    }

    const confirmed = await showConfirmDelete(`Apakah Anda yakin ingin menghapus ${selected.length} data?`);
    if (!confirmed) return;

    for (const nopindahbuku of selected) {
        try {
        const response = await fetch(`/pindahsaldo/${nopindahbuku}`, {
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

        const checkbox = document.querySelector(`.item-checkbox[value="${nopindahbuku}"]`);
        if (checkbox) {
            checkbox.closest('tr').remove();
        }
        } catch (error) {
        alert(`Error hapus ${nopindahbuku}: ${error.message}`);
        }
    }

    updateActionButtons();
    }

    function confirmSubmit() {
        const total = parseFloat(document.getElementById('total').value);
    if (isNaN(total) || total <= 0) {
        alert("Total harus lebih dari 0.");
        return false;
    }
    return true;
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
