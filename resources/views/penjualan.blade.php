@extends('layouts.main')

@section('title', 'Halaman Penjualan')
@section('page', 'Penjualan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="bg-white p-6 rounded shadow min-h-[800px]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Nota Penjualan</h2>
        <div id="actionButtons">
            <button id="simpan-semua" type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mr-2">
                Simpan
            </button>
            <button id="buka-modal" type="button" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                Tambah Barang +
            </button>
        </div>
    </div>

    <!-- Form Header -->
        <form id="form-utama" class="grid grid-cols-1 md:grid-cols-5 mb-4 gap-2" autocomplete="off">
            <div>
                <input type="text" name="notajual" id="notajual"
                    value="{{ $notajual }}"
                    class="w-full border rounded px-2 py-3 text-sm"
                    readonly />
            </div>
            <div>
                <input type="date" name="tanggal" id="tanggal"
                    value="{{ date('Y-m-d') }}"
                    class="w-full border rounded px-2 py-3 text-sm"
                    required />
            </div>
            <div>
                <select id="namapelanggan" class="w-full border rounded px-1 py-1 text-sm" required>
                    <option value="" selected hidden>Pilih Pelanggan</option>
                    @foreach($pelanggan as $p)
                        <option value="{{ $p->namapelanggan }}">{{ $p->namapelanggan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="text" name="nopol" id="nopol"
                    class="w-full border rounded px-1 py-3 text-sm"
                    placeholder="No Polisi" />
            </div>
            <div>
                <input type="text" name="supir" id="supir"
                    class="w-full border rounded px-1 py-3 text-sm"
                    placeholder="Supir" />
            </div>
        </form>


    <!-- Tabel Barang -->
    <form id="formBarang">
        <table class="w-full text-left border-collapse mt-6 text-xs">
                <thead>
                    <tr class="text-gray-500 border-b">
                        <th class="px-4 py-3 w-8 font-normal"></th>
                        <th class="px-4 py-3 font-normal">No Ref</th>
                        <th class="px-4 py-3 font-normal">Nama Barang</th>
                        <th class="px-4 py-3 font-normal">Kuantitas</th>
                        <th class="px-4 py-3 font-normal">Kuantitas (Ton)</th>
                        <th class="px-4 py-3 font-normal text-right">Harga Jual</th>
                        <th class="px-4 py-3 font-normal text-right">          </th>
                        <th class="px-4 py-3 font-normal text-right">Subtotal</th>
                    </tr>
                </thead>
            <tbody id="tabel-barang">
                
            </tbody>
            <tfoot>
                <tr class="font-semibold bg-gray-50 border-t">
                    <td colspan="7" class="px-2 py-1 text-left">Total</td>
                    <td class="px-4 py-2 text-right" id="total-akhir">Rp 0</td>
                </tr>
            </tfoot>
        </table>
    </form>
</div>

<!-- Modal Tambah Barang -->
<div id="modal-barang" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Barang Penjualan</h3>
        <div class="mb-4">
            <label for="noref" class="block text-sm font-medium">No Ref</label>
            <select id="noref" class="w-full border rounded px-3 py-2">
                <option value="">Pilih No Ref</option>
                @foreach($dbeli as $ref)
                    <option value="{{ $ref->noref }}" data-namabarang="{{ $ref->namabarang }}" data-hargajual="{{ $ref->hargajual }}">
                        {{ $ref->noref }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="namabarang" class="block text-sm font-medium">Nama Barang</label>
            <input type="text" id="namabarang" class="w-full border rounded px-3 py-2 bg-gray-100" readonly />
        </div>
        <div class="mb-4">
            <label for="qtyjual" class="block text-sm font-medium">Kuantitas</label>
            <input type="number" id="qtyjual" min="1" class="w-full border rounded px-3 py-2" placeholder="Qty" />
        </div>
        <div class="mb-4">
            <label for="hargajual" class="block text-sm font-medium">Harga Jual (Rp)</label>
            <input type="number" id="hargajual" class="w-full border rounded px-3 py-2 bg-gray-100" readonly />
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" id="simpan-barang" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah</button>
            <button type="button" id="tutup-modal" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
        </div>
    </div>
</div>

<!-- Modal Alert -->
<div id="custom-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
        <p id="custom-modal-message" class="text-gray-700 mb-4 text-sm"></p>
        <button id="custom-modal-ok" class="bg-[#89E355] hover:bg-[#7ED242] text-white px-4 py-2 rounded">OK</button>
    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
let barangList = [];
const modal = document.getElementById('modal-barang');
const refInput = document.getElementById('noref');

// isi nama barang & harga jual otomatis
refInput.addEventListener('input', () => {
    const list = document.getElementById('listRef').options;
    const val = refInput.value;
    let found = false;

    for (let i = 0; i < list.length; i++) {
        if (list[i].value === val) {
            const namaBarang = list[i].dataset.namabarang || '';
            const hargaJual = list[i].dataset.hargajual || '';

            document.getElementById('namabarang').value = namaBarang;
            document.getElementById('hargajual').value = hargaJual;

            // Simpan ke dataset
            refInput.dataset.namabarang = namaBarang;

            found = true;
            break;
        }
    }

    if (!found) {
        document.getElementById('namabarang').value = '';
        document.getElementById('hargajual').value = '';
        refInput.dataset.namabarang = '';
    }
});


document.getElementById('buka-modal').onclick = () => modal.classList.remove('hidden');
document.getElementById('tutup-modal').onclick = () => modal.classList.add('hidden');

document.getElementById('simpan-barang').onclick = () => {
    const noref = document.getElementById('noref').value.trim();
    const namabarang = refInput.dataset.namabarang || '';
    const qty = parseFloat(document.getElementById('qtyjual').value);
    const hargajual = parseFloat(document.getElementById('hargajual').value);

    if (!noref || !qty || !hargajual || !namabarang) {
        return alert("Isi semua data dengan benar!");
    }

    const qtyton = qty / 1000;
    const subtotal = qty * hargajual;

    barangList.push({ noref, namabarang, qty, qtyton, hargajual, subtotal });
    renderTable();
    modal.classList.add('hidden');

    // Reset
    document.getElementById('noref').value = '';
    document.getElementById('qtyjual').value = '';
    document.getElementById('hargajual').value = '';
};

function renderTable() {
    const tbody = document.getElementById('tabel-barang');
    tbody.innerHTML = '';
    let total = 0;

    barangList.forEach((item, index) => {
        total += item.subtotal;
        const row = `
        <tr class="border-b">
            <td class="px-2 py-1 text-center text-red-500 cursor-pointer" onclick="hapus(${index})">&times;</td>
            <td class="px-2 py-1">${item.noref}</td>
            <td class="px-2 py-1">${item.namabarang}</td>
            <td class="px-2 py-1">${item.qty}</td>
            <td class="px-2 py-1">${item.qtyton.toFixed(3)}</td>
            <td class="px-2 py-1 text-right">Rp ${formatRupiah(item.hargajual)}</td>
            <th class="px-4 py-3 font-normal text-right">                     </th>
            <td class="px-2 py-1 text-right">Rp ${formatRupiah(item.subtotal)}</td>
        </tr>`;
        tbody.innerHTML += row;
    });

    document.getElementById('total-akhir').textContent = 'Rp ' + formatRupiah(total);
}

function hapus(index) {
    barangList.splice(index, 1);
    renderTable();
}

function formatRupiah(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Mapping nama ➜ kode pelanggan
const pelangganMap = {
    @foreach($pelanggan as $p)
        "{{ $p->namapelanggan }}": "{{ $p->kodepelanggan }}",
    @endforeach
};

function getKodePelanggan(nama) {
    return pelangganMap[nama] || '';
}

document.getElementById('simpan-semua').addEventListener('click', () => {
    const notajual = document.getElementById('notajual').value;
    const tanggal = document.getElementById('tanggal').value;
    const namapelanggan = document.getElementById('namapelanggan').value;
    const kodepelanggan = getKodePelanggan(namapelanggan);
    const nopol = document.getElementById('nopol').value.toUpperCase();
    const supir = document.getElementById('supir').value;

    if (!notajual || !tanggal || !kodepelanggan || barangList.length === 0) {
        return showModal("Lengkapi semua data sebelum menyimpan.");
    }

    fetch("{{ route('penjualan.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content
        },
        body: JSON.stringify({
            notajual,
            tanggal,
            kodepelanggan,
            nopol,
            supir,
            items: barangList
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // ✅ Buka invoice penjualan di tab baru
            const invoiceUrl = `{{ route('penjualan.invoice', ':notajual') }}`.replace(':notajual', notajual);
            window.open(invoiceUrl, '_blank');

            // ✅ Tampilkan modal sukses
            showModal(`Data penjualan berhasil disimpan!`, () => {
                window.location.href = "{{ route('penjualan.index') }}";
            });
        } else {
            showModal(`Gagal menyimpan: ${result.message}`);
        }
    })
    .catch(err => {
        console.error(err);
        showModal('Terjadi kesalahan saat menyimpan data!');
    });
});


function showModal(msg) {
    document.getElementById('custom-modal-message').textContent = msg;
    document.getElementById('custom-modal').classList.remove('hidden');
}
document.getElementById('custom-modal-ok').addEventListener('click', () => {
    document.getElementById('custom-modal').classList.add('hidden');
});



new TomSelect('#namapelanggan', {
    create: false,
    maxItems: 1,
    placeholder: "Pilih pelanggan...",
    allowEmptyOption: true
});

new TomSelect('#noref', {
    create: false,
    maxItems: 1,
    placeholder: "Pilih No Ref...",
    allowEmptyOption: true,
    onChange(value) {
        const option = document.querySelector(`#noref option[value="${value}"]`);
        if (option) {
            const namaBarang = option.dataset.namabarang || '';
            const hargaJual = option.dataset.hargajual || '';

            document.getElementById('namabarang').value = namaBarang;
            document.getElementById('hargajual').value = hargaJual;

            // Simpan juga ke dataset untuk proses simpan
            document.getElementById('noref').dataset.namabarang = namaBarang;
        } else {
            document.getElementById('namabarang').value = '';
            document.getElementById('hargajual').value = '';
            document.getElementById('noref').dataset.namabarang = '';
        }
    }
});

</script>
@endsection
