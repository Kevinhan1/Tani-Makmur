<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $hjual->notajual }} Penjualan</title>
    <style>
        body { font-family: 'Poppins', sans-serif; font-size: 14px; }
        h2 { font-family: 'Poppins', sans-serif; font-style: italic; margin-bottom: 5px; color: #89E355; }
        h3 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #aaa; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        tfoot th { border-top: 2px solid #000; border-bottom: none; }
    </style>
</head>
<body>
    <h2>Tani Makmur</h2>
    <h3>Nota Penjualan: {{ $hjual->notajual }}</h3>
    <table style="width: 100%; margin-bottom: 15px;">
    <tr>
        <td style="width: 50%;">
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($hjual->tanggal)->format('d-m-Y') }}</p>
            <p><strong>Pelanggan:</strong> {{ $hjual->pelanggan->namapelanggan }}</p>
        </td>
        <td style="width: 50%; text-align: right;">
            <p><strong>No Polisi:</strong> {{ $hjual->nopol ?? '-' }}</p>
            <p><strong>Supir:</strong> {{ $hjual->supir ?? '-' }}</p>
        </td>
    </tr>
</table>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Jual</th>
                <th>Subtotal</th>
            </tr>
        </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($hjual->detail as $item)
                    @php
                        $subtotal = $item->qty * $item->hargajual;
                        $total += $subtotal;

                        $namabarang = \DB::table('tdbeli')
                            ->join('tbarang', 'tdbeli.kodebarang', '=', 'tbarang.kodebarang')
                            ->where('tdbeli.noref', $item->noref)
                            ->value('tbarang.namabarang');
                    @endphp
                    <tr>
                        <td>{{ $namabarang ?? '-' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rp {{ number_format($item->hargajual, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
