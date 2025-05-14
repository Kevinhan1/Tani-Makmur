<!DOCTYPE html>
<html lang="en" x-data="{ openMaster: false, openLaporan: false, openTransaksi: false }" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="flex font-[Poppins] bg-[#F5F5F5] text-[#333]">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md h-screen p-4">
        <h1 class="text-2xl font-semibold mb-6 text-green-600">ERP App</h1>
        <nav class="space-y-2">

            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">Dashboard</a>

            <!-- Master -->
            <div>
                <button @click="openMaster = !openMaster"
                        class="w-full flex justify-between items-center px-4 py-2 hover:bg-gray-100 rounded">
                    Master
                    <span x-text="openMaster ? '▲' : '▼'"></span>
                </button>
                <div x-show="openMaster" class="pl-6 py-1 space-y-1">
                    <a href="{{ route('barang') }}" class="{{ request()->routeIs('barang') ? 'font-semibold text-green-600' : '' }}">Barang</a>
                    <a href="#">Pemasok</a>
                    <a href="#">Pelanggan</a>
                    <a href="#">Rekening</a>
                </div>
            </div>

            <!-- Laporan -->
            <div>
                <button @click="openLaporan = !openLaporan"
                        class="w-full flex justify-between items-center px-4 py-2 hover:bg-gray-100 rounded">
                    Laporan
                    <span x-text="openLaporan ? '▲' : '▼'"></span>
                </button>
                <div x-show="openLaporan" class="pl-6 py-1 space-y-1">
                    <a href="#">Mutasi Rekening</a>
                    <a href="#">Mutasi Stok</a>
                    <a href="#">Kas</a>
                    <a href="#">Piutang</a>
                    <a href="#">Laporan Penjualan</a>
                </div>
            </div>

            <!-- Transaksi -->
            <div>
                <button @click="openTransaksi = !openTransaksi"
                        class="w-full flex justify-between items-center px-4 py-2 hover:bg-gray-100 rounded">
                    Transaksi
                    <span x-text="openTransaksi ? '▲' : '▼'"></span>
                </button>
                <div x-show="openTransaksi" class="pl-6 py-1 space-y-1">
                    <a href="#">Biaya</a>
                    <a href="#">Pindah Saldo Rekening</a>
                    <a href="#">Penjualan</a>
                    <a href="#">Pembelian</a>
                    <a href="#">Pembayaran Pembelian</a>
                    <a href="#">Pembayaran Penjualan</a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 p-6">
        <!-- Topbar -->
        <div class="mb-6 text-xl font-bold text-green-700 border-b pb-2">@yield('page')</div>

        <!-- Page Content -->
        @yield('content')
    </div>
</body>
</html>
