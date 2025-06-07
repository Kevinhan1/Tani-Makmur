@extends('layouts.main')

@section('title', 'Halaman Penjualan')
@section('page', 'Penjualan')

@section('content')
<div class="bg-white p-6 rounded shadow min-h-[800px]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Nota Penjualan</h2>
        <div id="actionButtons">
            <button id="buka-modal" type="button" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">
                Tambah Barang +
            </button>
        </div>
    </div>

    <!-- Form Header Nota -->
    <form id="form-utama" class="grid grid-cols-1 md:grid-cols-5 mb-2 gap-3">
        <input type="text" name="nonota" placeholder="No Nota" class="w-[200px] border rounded px-3 py-2 text-sm" />
        <input type="date" name="tanggal" class="w-[200px] border rounded px-3 py-2 text-sm" />
        <select name="pelanggan_id" class="w-[200px] border rounded px-3 py-2 text-sm">
            <option value="">Nama Pelanggan</option>
            @foreach ($pelanggan as $p)
                <option value="{{ $p->kodepelanggan }}">{{ $p->kodepelanggan }} - {{ $p->namapelanggan }}</option>
            @endforeach
        </select>
        <input type="text" name="nokendaraan" placeholder="No Kendaraan" class="w-[200px] border rounded px-3 py-2 text-sm" />
        <input type="text" name="supir" placeholder="Nama Supir" class="w-[200px] border rounded px-3 py-2 text-sm" />
    </form>

    <!-- Tabel Barang -->
    <form id="formBarang">
        <table class="w-full text-left border-collapse mt-6 text-xs">
													<thead>
																	<tr class="text-gray-500 border-b">
																					<th class="px-2 py-1 w-6 font-normal"> </th>
																					<th class="px-2 py-1 font-normal">No SO</th>
																					<th class="px-2 py-1 font-normal">Kode - Nama Barang</th>
																					<th class="px-2 py-1 font-normal text-center">Qty Zak</th>
																					<th class="px-2 py-1 font-normal text-center">Kuantitas (Kg)</th>
																					<th class="px-2 py-1 font-normal text-center">Kuantitas (Ton)</th>
																					<th class="px-2 py-1 font-normal text-center">Harga/Jual Zak</th>
																					<th class="px-2 py-1 font-normal text-center">Harga/Jual Kg</th>
																					<th class="px-2 py-1 font-normal text-center">Harga/Jual Ton</th>
																					<th class="px-2 py-1 font-normal text-left">Subtotal</th>
																	</tr>
													</thead>
													<tbody id="tabel-barang" class="text-xs">
																	<!-- Baris diisi oleh JS -->
													</tbody>
													<tfoot>
														<tr class="font-semibold bg-gray-50 border-t">
															<td colspan="9" class="px-2 py-1 text-right">Total</td>
															<td class="px-2 py-1 text-left" id="total-akhir">Rp 0</td>
														</tr>
													</tfoot>
									</table>

    </form>
</div>

<!-- Modal Tambah Barang -->
<div id="modal-barang" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Barang</h3>
        
        <div class="mb-4">
            <label for="noso" class="block text-sm font-medium">No SO</label>
            <input type="text" id="noso" class="w-full border rounded px-3 py-2" placeholder="Masukkan No SO" />
        </div>

        <div class="mb-4">
            <label for="kodebarang" class="block text-sm font-medium">Kode - Nama Barang</label>
            <select id="kodebarang" class="w-full border rounded px-3 py-2">
                <option value="">-- Pilih Barang --</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->kodebarang }}" data-hjual="{{ $b->hjual }}" data-konversi="{{ $b->konversi }}">
                        {{ $b->kodebarang }} - {{ $b->namabarang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="qty_zak" class="block text-sm font-medium">Qty Zak</label>
            <input type="number" id="qty_zak" min="0" class="w-full border rounded px-3 py-2" placeholder="Jumlah Zak" />
        </div>

        <div class="mb-4">
            <label for="kuantitas_kg" class="block text-sm font-medium">Kuantitas (Kg)</label>
            <input type="number" id="kuantitas_kg" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="Otomatis" readonly />
        </div>

        <div class="mb-4">
            <label for="kuantitas_ton" class="block text-sm font-medium">Kuantitas (Ton)</label>
            <input type="number" id="kuantitas_ton" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="Otomatis" readonly />
        </div>

        <div class="mb-4">
            <label for="harga_jual_zak" class="block text-sm font-medium">Harga Jual / Zak</label>
            <input type="number" id="harga_jual_zak" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="Otomatis" readonly />
        </div>

        <div class="mb-4">
            <label for="harga_jual_kg" class="block text-sm font-medium">Harga Jual / Kg</label>
            <input type="number" id="harga_jual_kg" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="Otomatis" readonly />
        </div>

        <div class="mb-6">
            <label for="harga_jual_ton" class="block text-sm font-medium">Harga Jual / Ton</label>
            <input type="number" id="harga_jual_ton" class="w-full border rounded px-3 py-2 bg-gray-100" placeholder="Otomatis" readonly />
        </div>

        <div class="flex justify-end space-x-2">
            <button id="simpan-barang" type="button" class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Simpan</button>
            <button id="tutup-modal" type="button" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
        </div>
    </div>
</div>

<script>
    // Data barang dari backend
    const dataBarang = @json($barang);
    let barangList = [];

    const modal = document.getElementById('modal-barang');
    const bukaModalBtn = document.getElementById('buka-modal');
    const tutupModalBtn = document.getElementById('tutup-modal');

    bukaModalBtn.onclick = () => {
        resetForm();
        modal.classList.remove('hidden');
    };
    tutupModalBtn.onclick = () => {
        modal.classList.add('hidden');
    };

    const kodebarangSelect = document.getElementById('kodebarang');
    const qtyZakInput = document.getElementById('qty_zak');
    const kuantitasKgInput = document.getElementById('kuantitas_kg');
    const kuantitasTonInput = document.getElementById('kuantitas_ton');
    const hargaJualZakInput = document.getElementById('harga_jual_zak');
    const hargaJualKgInput = document.getElementById('harga_jual_kg');
    const hargaJualTonInput = document.getElementById('harga_jual_ton');

    let selectedBarang = null;

    // Saat pilih barang, isi harga jual dan konversi
    kodebarangSelect.addEventListener('change', () => {
        const kode = kodebarangSelect.value;
        selectedBarang = dataBarang.find(b => b.kodebarang === kode);

        if (selectedBarang) {
            hargaJualZakInput.value = selectedBarang.hjual ?? 0;
            // hitung harga per kg = harga zak / konversi
            const konversi = selectedBarang.konversi ?? 1;
            hargaJualKgInput.value = (selectedBarang.hjual / konversi).toFixed(2);
            hargaJualTonInput.value = ((selectedBarang.hjual / konversi) * 1000).toFixed(2);

            // reset qty dan kuantitas
            qtyZakInput.value = '';
            kuantitasKgInput.value = '';
            kuantitasTonInput.value = '';
        } else {
            hargaJualZakInput.value = '';
            hargaJualKgInput.value = '';
            hargaJualTonInput.value = '';
            kuantitasKgInput.value = '';
            kuantitasTonInput.value = '';
            qtyZakInput.value = '';
        }
    });

    // Saat qty zak diinput, hitung kuantitas kg dan ton otomatis
    qtyZakInput.addEventListener('input', () => {
        if (!selectedBarang) return;
        const qtyZak = parseFloat(qtyZakInput.value) || 0;
        const konversi = selectedBarang.konversi ?? 1;

        const kuantitasKg = qtyZak * konversi;
        const kuantitasTon = kuantitasKg / 1000;

        kuantitasKgInput.value = kuantitasKg.toFixed(2);
        kuantitasTonInput.value = kuantitasTon.toFixed(4);
    });

    document.getElementById('simpan-barang').addEventListener('click', () => {
        if (!selectedBarang) return alert('Pilih barang terlebih dahulu!');
        const noso = document.getElementById('noso').value.trim();
        const qtyZak = parseFloat(qtyZakInput.value);
        if (!noso) return alert('No SO harus diisi!');
        if (!qtyZak || qtyZak <= 0) return alert('Qty Zak harus diisi dan > 0!');

        const konversi = selectedBarang.konversi ?? 1;
        const hargaZak = selectedBarang.hjual ?? 0;
        const kuantitasKg = qtyZak * konversi;
        const kuantitasTon = kuantitasKg / 1000;
        const hargaKg = hargaZak / konversi;
        const hargaTon = hargaKg * 1000;
        const subtotal = qtyZak * hargaZak;

        // Tambah ke list
        barangList.push({
            noso,
            kodebarang: selectedBarang.kodebarang,
            namabarang: selectedBarang.namabarang,
            qtyZak,
            kuantitasKg,
            kuantitasTon,
            hargaZak,
            hargaKg,
            hargaTon,
            subtotal
        });

        renderTable();
        modal.classList.add('hidden');
    });

    function resetForm() {
        document.getElementById('noso').value = '';
        kodebarangSelect.value = '';
        hargaJualZakInput.value = '';
        hargaJualKgInput.value = '';
        hargaJualTonInput.value = '';
        qtyZakInput.value = '';
        kuantitasKgInput.value = '';
        kuantitasTonInput.value = '';
        selectedBarang = null;
    }

    function renderTable() {
        const tbody = document.getElementById('tabel-barang');
        tbody.innerHTML = '';
        let total = 0;

        barangList.forEach((item, index) => {
            total += item.subtotal;
            tbody.innerHTML += `
                <tr class="border-t">
                    <td class="px-4 py-2 text-center">
                        <input type="checkbox" class="item-checkbox cursor-pointer accent-gray-400" style="width:14px; height:14px;" data-index="${index}">
                    </td>
                    <td class="px-4 py-2">${item.noso}</td>
                    <td class="px-4 py-2">${item.kodebarang} - ${item.namabarang}</td>
                    <td class="px-4 py-2 text-center">${item.qtyZak.toFixed(2)}</td>
                    <td class="px-4 py-2 text-center">${item.kuantitasKg.toFixed(2)}</td>
                    <td class="px-4 py-2 text-center">${item.kuantitasTon.toFixed(4)}</td>
                    <td class="px-4 py-2 text-center">Rp ${formatRupiah(item.hargaZak)}</td>
                    <td class="px-4 py-2 text-center">Rp ${formatRupiah(item.hargaKg)}</td>
                    <td class="px-4 py-2 text-center">Rp ${formatRupiah(item.hargaTon)}</td>
                    <td class="px-4 py-2 text-left font-semibold">Rp ${formatRupiah(item.subtotal)}</td>
                </tr>
            `;
        });

        document.getElementById('total-akhir').textContent = 'Rp ' + formatRupiah(total);
    }

    function formatRupiah(number) {
        return number.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }
</script>
@endsection
