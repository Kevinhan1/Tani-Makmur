<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
            /* Sembunyikan scrollbar hanya di sidebar (untuk Webkit-based browser) */
        .hide-scrollbar::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }
    </style>
</head>
<body class="bg-[#E0E0E0] min-h-screen flex">

<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg h-screen fixed flex flex-col">
    <div class="text-center text-2xl font-bold py-6 text-[#89E355] italic">Tani Makmur</div>
    <nav class="flex-3 overflow-y-auto h-[5000vh] hide-scrollbar">
        <ul class="space-y-2 px-4">
            <li>
                <a href="{{ route('dashboard') }}"
                class="block px-2 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
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
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="masterMenu">
            <li>
                <a href="{{ route('barang.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Barang
                </a>
            </li>
            <li>
                <a href="{{ route('pemasok.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pemasok
                </a>
            </li>
            <li>
                <a href="{{ route('pelanggan.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pelanggan
                </a>
            </li>
            <li>
                <a href="{{ route('rekening.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Rekening
                </a>
            </li>
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
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="transaksiMenu">
            <li>
                <a href="{{ route('biaya.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Biaya
                </a>
            </li>
            <li>
                <a href="{{ route('pindahsaldo.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pindah Saldo Rekening
                </a>
            </li>
            <li>
                <a href="{{ route('penjualan.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Penjualan
                </a>
            </li>
            <li>
                <a href="{{ route('pembelian.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pembelian
                </a>
            </li>
            <li>
                <a href="{{ route('pembayaranpembelian.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pembayaran Pembelian
                </a>
            </li>
            <li>
                <a href="{{ route('pembayaranpenjualan.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Pembayaran Penjualan
                </a>
            </li>
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
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="laporanMenu">
            <li>
                <a href="{{ route('mutasirekening.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Mutasi Rekening
                </a>
            </li>
            <li>
                <a href="{{ route('mutasistok.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Mutasi Stok
                </a>
            </li>
            <li>
                <a href="{{ route('kas.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Kas
                </a>
            </li>
            <li>
                <a href="{{ route('piutang.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Piutang
                </a>
            </li>
            <li>
                <a href="{{ route('laporanpenjualan.index') }}" 
                class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    Laporan Penjualan
                </a>
            </li>
        </ul>
    </li>
    </ul>
    </nav>
    </aside>
<!-- Main Content -->
<div class="flex-1 ml-64 flex flex-col">

    <!-- Topbar -->
    <div class="bg-white shadow h-16 flex items-center justify-between px-6">
        <h1 class="text-xl font-semibold">@yield('page', 'Dashboard')</h1>
        <form action="{{ route('logout') }}" method="get">
            <button class="bg-[#89E355] text-white px-4 py-2 rounded hover:bg-[#7ED242]">Logout</button>
        </form>
    </div>

    <!-- Dynamic Page Content -->
    <main class="p-6">
        @yield('content')
    </main>

</div>

<!-- Script -->
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
