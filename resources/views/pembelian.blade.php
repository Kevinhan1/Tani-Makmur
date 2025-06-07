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

    <!-- Form Header Nota Pembelian -->
    <form id="form-utama" class="grid grid-cols-1 md:grid-cols-5 mb-4 gap-3" autocomplete="off">
        <input type="text" name="notabeli" id="notabeli" value="{{ $notabeli }}" placeholder="No Nota Beli" class="w-[200px] border rounded px-3 py-2 text-sm" required readonly />
        <input type="date" name="tanggal" id="tanggal" class="w-[200px] border rounded px-3 py-2 text-sm" required />
        
        <select name="kodepemasok" id="kodepemasok" class="w-[200px] border rounded px-3 py-2 text-sm" required>
            <option value="">-- Pilih Pemasok --</option>
            @foreach ($pemasok as $p)
                <option value="{{ $p->kodepemasok }}">{{ $p->namapemasok }}</option>
            @endforeach
        </select>
        <select name="koderekeing" id="koderekening" class="w-[200px] border rounded px-3 py-2 text-sm" required>
            <option value="">-- Pilih Rekening --</option>
            @foreach ($rekening as $r)
                <option value="{{ $r->koderekening }}">{{ $r->namarekening }}</option>
            @endforeach
        </select>
        <input type="number" name="totalbayar" id="totalbayar" placeholder="Total Bayar" class="w-[200px] border rounded px-3 py-2 text-sm" required />
    </form>

    <!-- Tabel Barang Pembelian -->
    <form id="formBarang">
        <table class="w-full text-left border-collapse mt-6 text-xs">
            <thead>
                <tr class="text-gray-500 border-b">
                    <th class="px-4 py-2 w-8 font-normal"></th>
                    <th class="px-2 py-1 font-normal">No Ref</th>
                    <th class="px-2 py-1 font-normal">Nama Barang</th>
                    <th class="px-2 py-1 font-normal ">No DO</th>
                    <th class="px-2 py-1 font-normal text-center">Qty</th>
                    <th class="px-2 py-1 font-normal text-center">Qty Jual</th>
                    <th class="px-2 py-1 font-normal text-right">Harga Beli</th>
                    <th class="px-2 py-1 font-normal text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="tabel-barang" class="text-xs">
                <!-- Baris akan diisi JS -->
            </tbody>
            <tfoot>
                <tr class="font-semibold bg-gray-50 border-t">
                    <td colspan="8" class="px-2 py-1 text-left">Total</td>
                    <td class="px-2 py-1 text-right" id="total-akhir">Rp 0</td>
                </tr>
                <tr class="font-semibold bg-gray-50 border-t">
                    <td colspan="8" class="px-2 py-1 text-left">Sisa Bayar</td>
                    <td class="px-2 py-1 text-right" id="sisa-bayar">Rp 0</td>
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
            <label for="kodebarang" class="block text-sm font-medium">Kode - Nama Barang</label>
            <select id="kodebarang" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Barang --</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->kodebarang }}" data-hargabeli="{{ $b->hbeli ?? 0 }}">
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
            <label for="qty" class="block text-sm font-medium">Qty</label>
            <input type="number" id="qty" min="1" class="w-full border rounded px-3 py-2" placeholder="Jumlah Qty" required />
        </div>

        <div class="mb-4">
            <label for="qtyjual" class="block text-sm font-medium">Qty Jual</label>
            <input type="number" id="qtyjual" min="0" class="w-full border rounded px-3 py-2" placeholder="Qty Jual" />
        </div>

        <div class="mb-6">
            <label for="hargabeli" class="block text-sm font-medium">Harga Beli</label>
            <input type="number" id="hargabeli" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="-" readonly />
        </div>

        <div class="flex justify-end space-x-2">
            <button id="simpan-barang" type="button" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Simpan</button>
            <button id="tutup-modal" type="button" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
        </div>
    </div>
</div>

<script>
    const kodebarangSelect = document.getElementById('kodebarang');
    const nodoInput = document.getElementById('nodo');
    const qtyInput = document.getElementById('qty');
    const qtyjualInput = document.getElementById('qtyjual');
    const hargabeliInput = document.getElementById('hargabeli');

    let selectedBarang = null;
    let barangList = [];
    let norefCounter = 1;
    let selectedRefs = [];
    let editMode = false;
    let editRef = null;

    const modal = document.getElementById('modal-barang');
    const bukaModalBtn = document.getElementById('buka-modal');
    const tutupModalBtn = document.getElementById('tutup-modal');

    bukaModalBtn.onclick = () => openModal();

    tutupModalBtn.onclick = () => {
        modal.classList.add('hidden');
        editMode = false;
    };

    kodebarangSelect.addEventListener('change', () => {
        const selectedOption = kodebarangSelect.options[kodebarangSelect.selectedIndex];
        const hargaAttr = selectedOption.getAttribute('data-hargabeli');
        const harga = hargaAttr ? parseFloat(hargaAttr) : 0;
        hargabeliInput.value = harga;

        selectedBarang = {
            kodebarang: selectedOption.value,
            namabarang: selectedOption.textContent.trim(),
            hargabeli: harga
        };
    });

    document.getElementById('simpan-barang').addEventListener('click', () => {
        if (!selectedBarang) return alert('Pilih barang terlebih dahulu!');
        const noref = document.getElementById('noref').value.trim();
        const nodo = nodoInput.value.trim();
        const qty = parseFloat(qtyInput.value);
        const qtyjual = parseFloat(qtyjualInput.value) || 0;
        const hargabeli = parseFloat(hargabeliInput.value);

        if (!noref || !qty || qty <= 0 || !hargabeli || hargabeli <= 0) {
            return alert('Lengkapi data dengan benar!');
        }

        if (editMode) {
            // Update item existing
            const itemIndex = barangList.findIndex(b => b.noref === editRef);
            if (itemIndex !== -1) {
                barangList[itemIndex] = {
                    noref,
                    kodebarang: selectedBarang.kodebarang,
                    namabarang: selectedBarang.namabarang,
                    nodo,
                    qty,
                    qtyjual,
                    hargabeli,
                };
            }
            editMode = false;
            editRef = null;
        } else {
            // Insert new item
            barangList.push({
                noref,
                kodebarang: selectedBarang.kodebarang,
                namabarang: selectedBarang.namabarang,
                nodo,
                qty,
                qtyjual,
                hargabeli,
            });
            norefCounter++;
        }

        renderTable();
        modal.classList.add('hidden');
    });

    // Fungsi baru untuk hitung sisa bayar
    function hitungSisaBayar() {
        // Ambil total akhir tanpa Rp dan format
        const totalAkhirText = document.getElementById('total-akhir').textContent.replace(/[^\d]/g, '');
        const totalAkhir = parseFloat(totalAkhirText) || 0;

        // Ambil total bayar input user
        const totalBayarInput = parseFloat(document.getElementById('totalbayar').value) || 0;

        // Hitung sisa
        const sisa = totalAkhir - totalBayarInput;

        // Tampilkan sisa bayar, minimal 0 (tidak negatif)
        document.getElementById('sisa-bayar').textContent = 'Rp ' + formatRupiah(sisa >= 0 ? sisa : 0);
    }

    // Tambahkan event listener ke input totalbayar
    document.getElementById('totalbayar').addEventListener('input', hitungSisaBayar);

    function resetForm() {
        document.getElementById('noref').value = '';
        kodebarangSelect.value = '';
        nodoInput.value = '';
        qtyInput.value = '';
        qtyjualInput.value = '';
        hargabeliInput.value = '';
        selectedBarang = null;
    }

    function openModal(editItem = null) {
        modal.classList.remove('hidden');

        if (editItem) {
            // Set form isi dengan data edit
            document.getElementById('noref').value = editItem.noref;
            kodebarangSelect.value = editItem.kodebarang;
            nodoInput.value = editItem.nodo;
            qtyInput.value = editItem.qty;
            qtyjualInput.value = editItem.qtyjual;
            hargabeliInput.value = editItem.hargabeli;
            selectedBarang = {
                kodebarang: editItem.kodebarang,
                namabarang: editItem.namabarang,
                hargabeli: editItem.hargabeli,
            };
            editMode = true;
            editRef = editItem.noref;
        } else {
            // Form baru
            document.getElementById('noref').value = 'REF' + String(norefCounter).padStart(3, '0');
            resetForm();
            editMode = false;
            editRef = null;
        }
    }

    function renderTable() {
        const tbody = document.getElementById('tabel-barang');
        tbody.innerHTML = '';

        let totalAkhir = 0;

        barangList.forEach(item => {
            const subtotal = item.qty * item.hargabeli;
            totalAkhir += subtotal;

            const tr = document.createElement('tr');
            tr.classList.add('border-b', 'text-xs');

            tr.innerHTML = `
                <td class="px-2 py-1 text-center cursor-pointer hover:text-red-500" title="Hapus" data-noref="${item.noref}">&times;</td>
                <td class="px-2 py-1">${item.noref}</td>
                <td class="px-2 py-1">${item.namabarang}</td>
                <td class="px-2 py-1">${item.nodo}</td>
                <td class="px-2 py-1 text-center">${item.qty}</td>
                <td class="px-2 py-1 text-center">${item.qtyjual}</td>
                <td class="px-2 py-1 text-right">Rp ${formatRupiah(item.hargabeli)}</td>
                <td class="px-2 py-1 text-right">Rp ${formatRupiah(subtotal)}</td>
            `;

            tbody.appendChild(tr);
        });

        document.getElementById('total-akhir').textContent = 'Rp ' + formatRupiah(totalAkhir);

        // Hitung ulang sisa bayar setiap render tabel
        hitungSisaBayar();

        // Event listener hapus item
        tbody.querySelectorAll('td:first-child').forEach(td => {
            td.onclick = () => {
                const noref = td.getAttribute('data-noref');
                barangList = barangList.filter(b => b.noref !== noref);
                renderTable();
            };
            td.style.cursor = 'pointer';
        });

        // Event double click untuk edit
        tbody.querySelectorAll('tr').forEach((tr, i) => {
            tr.ondblclick = () => {
                openModal(barangList[i]);
            };
        });
    }

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }


    document.getElementById('simpan-semua').addEventListener('click', function() {
    if (barangList.length === 0) {
        alert('Belum ada barang ditambahkan!');
        return;
    }

    // Konfirmasi dulu
    if (!confirm('Apakah anda yakin semua data sudah benar dan ingin disimpan?')) {
        return;
    }

    // Ambil data form
    const data = {
        notabeli: document.getElementById('notabeli').value,
        tanggal: document.getElementById('tanggal').value,
        kodepemasok: document.getElementById('kodepemasok').value,
        totalbayar: document.getElementById('totalbayar').value,
        koderekening: document.getElementById('koderekening').value,
        total: hitungTotalBarang(),
        items: barangList
    };

    // Validasi simple
    if (!data.tanggal || !data.kodepemasok || !data.totalbayar || !data.koderekening) {
        alert('Mohon lengkapi semua form header!');
        return;
    }

    // Kirim ke controller pakai fetch API
    fetch("{{ route('pembelian.store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Data pembelian berhasil disimpan!');
            window.location.href = "{{ route('pembelian.index') }}";
        } else {
            alert('Gagal menyimpan: ' + result.message);
        }
    })
    .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan saat menyimpan data!');
    });
});

function hitungTotalBarang() {
    let total = 0;
    barangList.forEach(item => {
        total += item.qty * item.hargabeli;
    });
    return total;
}
</script>
@endsection
