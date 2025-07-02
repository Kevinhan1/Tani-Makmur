<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pindah Saldo</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        h2 { margin-bottom: 0; }
        p { margin: 0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Pindah Saldo</h2>
    <p>
    Periode: 
    {{ $tanggal_awal ? \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') : '-' }} 
    s/d 
    {{ $tanggal_akhir ? \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') : '-' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Referensi</th>
                <th>Tanggal</th>
                <th>Dari Rekening</th>
                <th>Ke Rekening</th>
                <th>Keterangan</th>
                <th>Total</th>
                <th>Pengguna</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nopindahbuku }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->rekeningAsal->namarekening ?? '-' }}</td>
                    <td>{{ $item->rekeningTujuan->namarekening ?? '-' }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->pengguna->namapengguna ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
