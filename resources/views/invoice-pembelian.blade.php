<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $notabeli }} Pembelian</title>
    <style>
        body { font-family: 'Poppins', sans-serif; font-size: 14px; }
        h2 { font-family: 'Poppins', sans-serif; font-style: italic; margin-bottom: 5px; color: #89E355;}
        h3 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th,td {  border-bottom: 1px solid #aaa; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        tfoot th { border-top: 2px solid #000; border-bottom: none;}
    </style>
</head>
<body>
    <h2>Tani Makmur</h2>
    <h3>Nota Pembelian: {{ $notabeli }}</h3>
    <p>Tanggal: {{ $tanggal }}</p>
    <p>Pemasok: {{ $namapemasok }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Beli</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['namabarang'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>Rp {{ number_format($item['hargabeli'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['qty'] * $item['hargabeli'], 0, ',', '.') }}</td>
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
