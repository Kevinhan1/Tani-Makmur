<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-green {
            color: green;
        }
        .text-red {
            color: red;
        }
								@page {
								margin: 1cm 0.2cm; /* atas/bawah 1cm, kanan/kiri 2cm */				
        }
    </style>
</head>
<body>
    <h2>Laporan Kas</h2>

    <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($tanggalAwal)->format('d-m-Y') }} s.d
        {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d-m-Y') }}
    </p>
				<p>Rekening: {{ $rekeningNama ?? 'Semua' }}</p>
				<p>Jenis: {{ request('jenis') ?? 'Semua' }}</p>

                <p><strong>Saldo Awal:</strong> Rp{{ number_format($saldoAwal ?? 0, 0, ',', '.') }}</p>
                <p><strong>Saldo Akhir:</strong> Rp{{ number_format($saldoRekening ?? 0, 0, ',', '.') }}</p>

				
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Generate</th>
                <th>No. Referensi</th>
                <th>Keterangan</th>
																<th>Rekening</th>
                <th>Jenis</th>
                <th>Debit (Masuk)</th>
                <th>Kredit (Keluar)</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $saldo = $saldoAwal ?? 0; @endphp
@forelse ($kas as $item)
    @php
        $saldo += $item->masuk - $item->keluar;
    @endphp
    <tr>
        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
        <td>{{ $item->nogenerate }}</td>
        <td>{{ $item->noreferensi }}</td>
        <td class="text-align-left">{{ $item->keterangan }}</td>
        <td class="text-align-left">{{ $item->rekening->namarekening ?? '-' }}</td>
        <td class="text-align-left">{{ $item->jenis }}</td>
        <td class="text-green text-align-left">Rp{{ number_format($item->masuk, 0, ',', '.') }}</td>
        <td class="text-red text-align-left">Rp{{ number_format($item->keluar, 0, ',', '.') }}</td>
        <td class="text-align-left">Rp{{ number_format($saldo, 0, ',', '.') }}</td>
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
