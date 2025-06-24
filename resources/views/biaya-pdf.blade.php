<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Biaya</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Biaya</h2>
    <p>Periode: {{ $tanggalAwal ?? '-' }} s/d {{ $tanggalAkhir ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Biaya</th>
                <th>Tanggal</th>
                <th>Rekening</th>
                <th>Keterangan</th>
                <th>Total</th>
                <th>Pengguna</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($biaya as $item)
                <tr>
                    <td>{{ $item->nobiaya }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->rekening->namarekening ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->pengguna->namapengguna ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
