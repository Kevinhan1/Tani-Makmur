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
    <nav class="flex-3 overflow-y-auto h-[6000vh] hide-scrollbar">
        <ul class="space-y-2 px-4">
            <li>
                <a href="{{ route('dashboard') }}"
                class="flex items-center gap-2 px-2 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
                    <img src="{{ asset('icons/dashboard.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Dashboard</span>
                </a>
            </li>

        <!-- Master Dropdown -->
        @if(session('user') && session('user')->status === 'admin')
        <li>
        <button class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition"
                onclick="toggleDropdown('masterMenu', 'icon-master')">
            <div class="flex items-center gap-2">
                <img src="{{ asset('icons\crown.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
            <span>Master</span>
            </div>
            <div>
                <svg id="icon-master" class=" ml-28 w-4 h-4 transform transition-transform " xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="masterMenu">
            <li>
                <a href="{{ route('barang.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons/box.svg') }}" alt="Box Icon" class="w-5 h-5">
                    <span>Barang</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pemasok.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons/shop.svg') }}" alt="Box Icon" class="w-5 h-5">
                    <span>Pemasok</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pelanggan.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons/people.svg') }}" alt="Box Icon" class="w-5 h-5">
                    <span>Pelanggan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('rekening.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons/card.svg') }}" alt="Box Icon" class="w-5 h-5">
                    <span>Rekening</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pengguna.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons/user.svg') }}" alt="Box Icon" class="w-5 h-5">
                    <span>Pengguna</span>
                </a>
            </li>
        </ul>
    </li>
@endif

    <!-- Transaksi Dropdown -->
    <li>
        <button class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition"
                onclick="toggleDropdown('transaksiMenu', 'icon-transaksi')">
            <div class="flex items-center gap-2">
                <img src="{{ asset('icons\receipt.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
            <span>Transaksi</span>
            <div>
                <svg id="icon-master" class="ml-[81px] w-4 h-4 transform transition-transform " xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="transaksiMenu">
            <li>
                <a href="{{ route('biaya.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\moneys.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Biaya</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pindahsaldo.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons\money-change.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Pindah Saldo Rekening</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pembelian.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons\shopping-cart.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Pembelian</span>
                </a>
            </li>
            <li>
                <a href="{{ route('penjualan.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons\bag.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Penjualan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pembayaran-pembelian.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons\money-send.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Pembayaran Pembelian</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pembayaran-penjualan.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                    <img src="{{ asset('icons\money-recive.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    <span>Pembayaran Penjualan</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- Laporan Dropdown -->
    <li>
        <button class="w-full flex items-center justify-between px-2 py-2 hover:bg-gray-100 rounded transition"
            onclick="toggleDropdown('laporanMenu', 'icon-laporan')">
            <div class="flex items-center gap-2">
                <img src="{{ asset('icons\report.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
            <span>Laporan</span>
            <div>
                <svg id="icon-laporan" class="w-4 h-4 transform transition-transform ml-[90px]" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
        <ul class="ml-4 mt-1 hidden text-sm space-y-1" id="laporanMenu">
            <li>
                <a href="{{ route('mutasirekening.index') }}" 
                class="w-full flex items-center  gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\card-mutation.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    Mutasi Rekening
                </a>
            </li>
            <li>
                <a href="{{ route('mutasi-stok.index') }}"
                class="w-full flex items-center  gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\mutasi-box.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    Mutasi Stok
                </a>
            </li>
            <li>
                <a href="{{ route('kas.index') }}" 
                class="w-full flex items-center gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\book-square.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    Kas
                </a>
            </li>
            <li>
                <a href="{{ route('piutang.index') }}" 
                class="w-full flex items-center  gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\money-stop.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
                    Piutang
                </a>
            </li>
            <li>
                <a href="{{ route('laporanpenjualan.index') }}" 
                class="w-full flex items-center  gap-2 px-2 py-2 hover:bg-gray-100 rounded transition text-black-100">
                <img src="{{ asset('icons\diagram.svg') }}" alt="Dashboard Icon" class="w-5 h-5">
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
