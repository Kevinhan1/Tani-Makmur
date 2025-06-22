@extends('layouts.main')

@section('title', 'Halaman Pembelian')
@section('page', 'Pembelian')

@section('content')
<div class="bg-white p-6 rounded shadow min-h-[800px]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Nota Pembelian</h2>
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
    <form id="form-utama" class="grid grid-cols-1 md:grid-cols-5 gap-4" autocomplete="off">
    <div>
        <input type="text" name="notabeli" id="notabeli" value="{{ $notabeli }}"
            placeholder="No Nota Beli"
            class="w-full border rounded px-2 py-3 text-sm" required readonly />
    </div>
    <div>
        <input type="date" name="tanggal" id="tanggal"
            class="w-full border rounded px-2 py-3 text-sm"
            value="{{ date('Y-m-d') }}" required />
    </div>
    <div>
        <select name="kodepemasok" id="kodepemasok"
            class="w-full border rounded px-2 py-2 text-sm" required>
            <option value="" selected hidden>Pilih Pemasok</option>
            @foreach ($pemasok as $p)
                <option value="{{ $p->kodepemasok }}">{{ $p->namapemasok }}</option>
            @endforeach
        </select>
    </div>
</form>


    <!-- Tabel Barang -->
    <form id="formBarang">
        <table class="w-full text-left border-collapse mt-6 text-xs">
            <thead>
                <tr class="text-gray-500 border-b">
                    <th class="px-5 py-3 w-8 font-normal"></th>
                    <th class="px-5 py-3 font-normal">No Ref</th>
                    <th class="px-4 py-3 font-normal">Nama Barang</th>
                    <th class="px-4 py-3 font-normal ">No DO</th>
                    <th class="px-4 py-3 font-normal text-center">Kuantitas</th>
                    <th class="px-4 py-3 font-normal text-center">Kuantitas (Ton)</th>
                    <th class="px-4 py-3 font-normal text-right">Harga Beli</th>
                    <th class="px-4 py-3 font-normal text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="tabel-barang"></tbody>
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
        <h3 class="text-xl font-semibold mb-4">Tambah Barang Pembelian</h3>
        <div class="mb-4">
            <label for="noref" class="block text-sm font-medium">No Ref</label>
            <input type="text" id="noref" class="w-full border rounded px-3 py-2" placeholder="No Ref"/>
        </div>
        <div class="mb-4">
            <label for="kodebarang" class="block text-sm font-medium">Nama Barang</label>
            <select id="kodebarang" class="w-full border rounded px-3 py-2" required>
                <option value="" selected hidden>Pilih Barang</option>
                @foreach ($barang as $b)
                    <option 
                        value="{{ $b->kodebarang }}" 
                        data-hargabeli="{{ $b->hbeli ?? 0 }}" 
                        data-konversi="{{ $b->konversi ?? 0 }}">
                        {{ $b->namabarang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="nodo" class="block text-sm font-medium">No DO</label>
            <input type="text" id="nodo" class="w-full border rounded px-3 py-2" placeholder="No DO" />
        </div>
        <div class="mb-4">
            <label for="qty" class="block text-sm font-medium">Kuantitas</label>
            <input type="number" id="qty" min="1" class="w-full border rounded px-3 py-2" placeholder="Jumlah Qty" required />
        </div>
        <div class="mb-6">
            <label for="hargabeli" class="block text-sm font-medium">Harga Beli</label>
            <input type="number" id="hargabeli" class="w-full border rounded px-3 py-2 bg-gray-100" readonly />
        </div>
        <div class="flex justify-end space-x-2">
            <button id="simpan-barang" type="button" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Tambah</button>
            <button id="tutup-modal" type="button" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
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

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>


<script>
let selectedBarang = null;
let barangList = [];
let norefCounter = 1;
let editMode = false;
let editRef = null;

const modal = document.getElementById('modal-barang');
const kodebarangSelect = document.getElementById('kodebarang');

document.getElementById('buka-modal').onclick = () => openModal();
document.getElementById('tutup-modal').onclick = () => modal.classList.add('hidden');

kodebarangSelect.addEventListener('change', () => {
    const option = kodebarangSelect.options[kodebarangSelect.selectedIndex];
    const harga = parseFloat(option.getAttribute('data-hargabeli') || 0);
    const konversi = parseFloat(option.getAttribute('data-konversi') || 0);
    document.getElementById('hargabeli').value = harga;
    selectedBarang = { kodebarang: option.value, namabarang: option.textContent.trim(), hargabeli: harga, konversi: konversi,};
});

document.getElementById('simpan-barang').onclick = () => {
    const noref = document.getElementById('noref').value.trim();
    const nodo = document.getElementById('nodo').value.trim();
    const qty = parseFloat(document.getElementById('qty').value);
    const konversi = selectedBarang.konversi || 0;
    const qtyTon = (qty * konversi) / 1000;

    const hargabeli = parseFloat(document.getElementById('hargabeli').value);

    if (!selectedBarang || !noref || !qty || qty <= 0 || !hargabeli) return alert('Lengkapi data dengan benar!');

    const data = { noref, kodebarang: selectedBarang.kodebarang, namabarang: selectedBarang.namabarang, nodo, qty, qtyTon: qtyTon, hargabeli };

    if (editMode) {
        const i = barangList.findIndex(b => b.noref === editRef);
        if (i !== -1) barangList[i] = data;
        editMode = false;
        editRef = null;
    } else {
        barangList.push(data);
        norefCounter++;
    }

    renderTable();
    modal.classList.add('hidden');
};

function openModal(item = null) {
    resetForm();
    modal.classList.remove('hidden');
    if (item) {
        document.getElementById('noref').value = item.noref;
        document.getElementById('kodebarang').value = item.kodebarang;
        document.getElementById('nodo').value = item.nodo;
        document.getElementById('qty').value = item.qty;
        document.getElementById('hargabeli').value = item.hargabeli;
        selectedBarang = item;
        editMode = true;
        editRef = item.noref;
    } else {
        document.getElementById('noref').value = 'REF' + String(norefCounter).padStart(3, '0');
    }
}

function resetForm() {
    document.getElementById('noref').value = '';
    kodebarangSelect.value = '';
    document.getElementById('nodo').value = '';
    document.getElementById('qty').value = '';    
    document.getElementById('hargabeli').value = '';
    selectedBarang = null;
}

function renderTable() {
    const tbody = document.getElementById('tabel-barang');
    tbody.innerHTML = '';
    let total = 0;

    barangList.forEach(item => {
        const subtotal = item.qty * item.hargabeli;
        total += subtotal;

        const tr = document.createElement('tr');
        tr.classList.add('border-b');
        tr.innerHTML = `
            <td class="px-2 py-1 text-center cursor-pointer hover:text-red-500" title="Hapus" data-noref="${item.noref}">&times;</td>
            <td class="px-2 py-1">${item.noref}</td>
            <td class="px-2 py-1">${item.namabarang}</td>
            <td class="px-2 py-1">${item.nodo}</td>
            <td class="px-2 py-1 text-center">${item.qty}</td>
            <td class="px-2 py-1 text-center">${item.qtyTon.toFixed(3)}</td>
            <td class="px-2 py-1 text-right">Rp ${formatRupiah(item.hargabeli)}</td>
            <td class="px-2 py-1 text-right">Rp ${formatRupiah(subtotal)}</td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('total-akhir').textContent = 'Rp ' + formatRupiah(total);

    tbody.querySelectorAll('td:first-child').forEach(td => {
        td.onclick = () => {
            const noref = td.getAttribute('data-noref');
            barangList = barangList.filter(b => b.noref !== noref);
            renderTable();
        };
    });

    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.ondblclick = () => openModal(barangList[i]);
    });
}

function formatRupiah(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
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


document.getElementById('simpan-semua').onclick = () => {
    if (barangList.length === 0) {
        showModalAlert('Belum ada barang ditambahkan!');
        return;
    }

    showModalAlert('Yakin simpan pembelian ini?', () => {
        const data = {
            notabeli: document.getElementById('notabeli').value,
            tanggal: document.getElementById('tanggal').value,
            kodepemasok: document.getElementById('kodepemasok').value,
            totalbayar: 0,
            total: hitungTotalBarang(),
            items: barangList,
            qtyjual: 0,
        };

        if (!data.tanggal || !data.kodepemasok) {
            showModalAlert('Lengkapi semua form header!');
            return;
        }

                fetch("{{ route('pembelian.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                // ✅ Buka invoice di tab baru
                const invoiceUrl = `/pembelian/${data.notabeli}/invoice`;
                window.open(invoiceUrl, '_blank');

                // ✅ Tampilkan modal sukses
                showModalAlert('Data pembelian berhasil disimpan!', () => {
                    window.location.href = "{{ route('pembelian.index') }}";
                });
            } else {
                showModalAlert('Gagal menyimpan: ' + result.message);
            }
        })
        .catch(err => {
            console.error(err);
            showModalAlert('Terjadi kesalahan saat menyimpan data!');
        });
    });
};


function hitungTotalBarang() {
    return barangList.reduce((sum, item) => sum + (item.qty * item.hargabeli), 0);
}


new TomSelect('#kodepemasok', {
    create: false,
    maxItems: 1,
    placeholder: "Pilih pemasok...",
    allowEmptyOption: true
});

new TomSelect('#kodebarang', {
    create: false,
    maxItems: 1,
    placeholder: "Pilih barang...",
    allowEmptyOption: true
});

</script>
@endsection
