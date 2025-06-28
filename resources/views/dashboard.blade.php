@extends('layouts.main')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-semibold mb-3">Selamat Datang, {{ session('user')->namapengguna }}!</h2>

    <h2 class="text-2xl font-semibold mt-4 mb-4">Penjualan Hari ini</h2>
    <div class="flex gap-[15px]">
        @foreach ([
            ['label' => 'Total Penjualan', 'value' => 'Rp' . number_format($totalHariIni, 0, ',', '.')],
            ['label' => 'Transaksi', 'value' => $transaksiHariIni],
            ['label' => 'Produk terjual', 'value' => $produkTerjualHariIni],
        ] as $item)
        <div class="bg-white p-6 rounded shadow" style="height: 150px; min-width: 520px;">
            <div class="flex justify-between items-center mb-6">
                <p class="text-[20px] font-medium">{{ $item['label'] }}</p>
            </div>
            <p class="text-[24px] font-medium">{{ $item['value'] }}</p>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold mt-4 mb-4">Penjualan Bulanan ini</h2>
    <div class="flex gap-[15px]">
        @foreach ([
            ['label' => 'Total Penjualan', 'value' => 'Rp' . number_format($totalBulanIni, 0, ',', '.')],
            ['label' => 'Transaksi', 'value' => $transaksiBulanIni],
            ['label' => 'Produk terjual', 'value' => $produkTerjualBulanIni],
        ] as $item)
        <div class="bg-white p-6 rounded shadow" style="height: 150px; min-width: 520px;">
            <div class="flex justify-between items-center mb-6">
                <p class="text-[20px] font-medium">{{ $item['label'] }}</p>
            </div>
            <p class="text-[24px] font-medium">{{ $item['value'] }}</p>
        </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-semibold mt-4 mb-4">Top Produk Terlaris</h2>
    <div class="bg-white p-6 rounded shadow max-w-[1590px] w-full">
        <p class="text-xl font-medium mb-4">Top 3 Produk Terlaris Bulan Ini</p>
        <canvas id="produkChart" height="30"></canvas>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
<script>
    const produkData = @json($produkChart);
    const labels = produkData.map(item => item.nama);
    const dataQty = produkData.map(item => item.qty);

    const ctx = document.getElementById('produkChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Qty Terjual',
                data: dataQty,
                backgroundColor: '#89E355',
                borderRadius: 8,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    color: '#000',
                    font: {
                        weight: 'bold'
                    },
                    formatter: function(value) {
                        return value + ' pcs';
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    suggestedMax: Math.max(...dataQty) * 1.1
                }
            }
        },
        plugins: [ChartDataLabels] // Aktifkan plugin
    });
</script>
@endpush
