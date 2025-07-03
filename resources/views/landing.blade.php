<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang di Tani Makmur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts & Tailwind -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#E0E0E0] flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow py-4 px-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold italic text-[#89E355]">Tani Makmur</h1>
        <div class="text-sm text-gray-600">
            Kontak: <a href="mailto:support@tanimakmur.com" class="text-[#89E355] hover:underline">support@tanimakmur.com</a>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-grow flex items-center justify-center text-center px-4">
        <div>
            <h2 class="text-4xl font-bold text-[#89E355] mb-4">Selamat Datang di Tani Makmur</h2>
            <p class="text-lg text-gray-700 mb-6">Sistem Informasi Manajemen Usaha Pertanian</p>

            {{-- Tombol login karyawan disembunyikan jika tidak perlu --}}
            {{-- <a href="/admin-login" class="bg-[#10B981] hover:bg-[#059669] text-white font-semibold py-3 px-6 rounded-lg shadow">
                Login Karyawan
            </a> --}}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t py-4 text-center text-sm text-gray-600 shadow-inner">
        <div>
            <p class="mb-1">
                Kontak: <a href="mailto:support@tanimakmur.com" class="text-[#89E355] hover:underline">support@tanimakmur.com</a> |
                WhatsApp: <a href="https://wa.me/6281234567890" target="_blank" class="text-[#89E355] hover:underline">+62 812 3456 7890</a>
            </p>
            <p>&copy; {{ date('Y') }} Tani Makmur. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
