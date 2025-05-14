<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    * {
        font-family: 'Poppins', sans-serif;
    }
</style>
</head>
<body class="bg-[#E0E0E0] min-h-screen flex">

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg h-screen fixed flex flex-col">
    <div class="text-center text-2xl font-bold py-6 text-[#89E355] italic">ERP App</div>
    <nav class="flex-1">
        <ul class="space-y-2 px-4">

        <!-- Dashboard Active -->
        <li>
            <a href="{{ route('dashboard') }}"
            class="block px-2 py-2 rounded bg-gray-200 font-semibold text-[#333]">
            Dashboard
            </a>
        </li>

        <!-- Master Dropdown -->
        <li>
            <button class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition"
                onclick="toggleDropdown('masterMenu', 'icon-master')">
            <span>Master</span>
            <svg id="icon-master" class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <ul class="ml-4 mt-1 hidden text-sm text-gray-700 space-y-1" id="masterMenu">
            <li><a href="{{ route('barang.index') }}" class="block hover:text-[#89E355]">Barang</a></li>
            <li><a href="{{ route('pemasok.index') }}" class="block hover:text-[#89E355]">Pemasok</a></li>
            <li><a href="{{ route('pelanggan.index') }}" class="block hover:text-[#89E355]">Pelanggan</a></li>
            <li><a href="{{ route('rekening.index') }}" class="block hover:text-[#89E355]">Rekening</a></li>
        </ul>
        </li>

        <!-- Transaksi Dropdown -->
        <li>
        <button class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition"
                onclick="toggleDropdown('transaksiMenu', 'icon-transaksi')">
            <span>Transaksi</span>
            <svg id="icon-transaksi" class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
            <ul class="ml-4 mt-1 hidden text-sm text-gray-700 space-y-1" id="transaksiMenu">
            <li><a href="{{ route('biaya.index') }}" class="block hover:text-[#89E355]">Biaya</a></li>
            <li><a href="{{ route('penjualan.index') }}" class="block hover:text-[#89E355]">Penjualan</a></li>
            <li><a href="{{ route('pembelian.index') }}" class="block hover:text-[#89E355]">Pembelian</a></li>
            <li><a href="{{ route('pembayaranpembelian.index') }}" class="block hover:text-[#89E355]">Pembayaran Pembelian</a></li>
            <li><a href="{{ route('pembayaranpenjualan.index') }}" class="block hover:text-[#89E355]">Pembayaran Penjualan</a></li>
        </ul>
        </li>

        <!-- Laporan Dropdown -->
        <li>
            <button class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition"
                onclick="toggleDropdown('laporanMenu', 'icon-laporan')">
            <span>Laporan</span>
            <svg id="icon-laporan" class="w-4 h-4 transform transition-transform" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <ul class="ml-4 mt-1 hidden text-sm text-gray-700 space-y-1" id="laporanMenu">
            <li><a href="{{ route('mutasirekening.index') }}" class="block hover:text-[#89E355]">Mutasi Rekening</a></li>
            <li><a href="{{ route('mutasistok.index') }}" class="block hover:text-[#89E355]">Mutasi Stok</a></li>
            <li><a href="{{ route('kas.index') }}" class="block hover:text-[#89E355]">Kas</a></li>
            <li><a href="{{ route('piutang.index') }}" class="block hover:text-[#89E355]">Piutang</a></li>
            <li><a href="{{ route('laporanpenjualan.index') }}" class="block hover:text-[#89E355]">Laporan Penjualan</a></li>
        </ul>
        </li>
        </ul>
    </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 ml-64 flex flex-col">

    <!-- Topbar -->
    <div class="bg-white shadow h-16 flex items-center justify-between px-6">
        <h1 class="text-xl font-semibold">Dashboard</h1>
        <form action="{{ route('logout') }}" method="get">
        <button class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Logout</button>
        </form>
    </div>

    <!-- Content -->
    <main class="p-6">
        <p class="text-xl">Selamat datang, {{ session('user')->namapengguna }}!</p>
        <!-- Tambahkan konten dashboard lainnya -->
    </main>

    </div>

    <!-- Dropdown Toggle Script -->
    <script>
    function toggleDropdown(menuId, iconId) {
        const menu = document.getElementById(menuId);
        const icon = document.getElementById(iconId);
        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
    </script>

</body>
</html>
