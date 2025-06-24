<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mutasi Rekening</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 6px;
            border: 1px solid #000;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
        .title {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }
		@page {
			margin: 1cm 0.3cm; /* atas/bawah 1cm, kanan/kiri 2cm */				
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Mutasi Rekening</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</p>
    <p>Jenis: {{ request('jenis') ?? 'Semua' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal   </th>
                <th>No Generate</th>
                <th>No Referensi</th>
                <th>Nama Rekening</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Jenis</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mutasi as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $item->nogenerate }}</td>
                <td>{{ $item->noreferensi }}</td>
                <td>{{ $item->rekening->namarekening ?? '-' }}</td>
                <td>Rp{{ number_format($item->masuk, 0, ',', '.') }}</td>
                <td>Rp{{ number_format($item->keluar, 0, ',', '.') }}</td>
                <td>{{ $item->jenis }}</td>
                <td>{{ $item->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
