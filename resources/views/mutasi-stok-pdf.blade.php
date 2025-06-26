<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Mutasi Stok</h2>
    <p>Periode: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</p>
    <p>Jenis: {{ request('jenis') ?? 'Semua' }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nota</th>
                <th>No Ref</th>
                <th>Nama Barang</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Jenis</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mutasi as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                    <td>{{ $row['nota'] }}</td>
                    <td>{{ $row['noref'] }}</td>
                    <td>{{ $row['namabarang'] }}</td>
                    <td class="text-right">{{ $row['masuk'] > 0 ? $row['masuk'] : '-' }}</td>
					<td class="text-right">{{ $row['keluar'] > 0 ? $row['keluar'] : '-' }}</td>
                    <td>{{ $row['jenis'] }}</td>
                    <td>{{ $row['keterangan'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
