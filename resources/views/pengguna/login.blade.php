<!-- resources/views/pengguna/login.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-[#E0E0E0] flex items-center justify-center min-h-screen" style="font-family: 'Poppins', sans-serif;">

    <!-- Box putih -->
    <div class="bg-white w-[450px] h-[600px] p-8 rounded-lg shadow-lg flex flex-col items-center justify-center">
        
        <!-- Judul -->
        <h1 class="text-2xl font-semibold text-center mb-6" style="color:#89E355; font-style: italic; font-size: 32px;">Tani Makmur</h1>

        <!-- Pesan error login -->
        @if (session('error'))
            <div class="bg-red-200 text-red-800 p-2 rounded-md mb-4 w-full text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- Formulir Login -->
        <form action="{{ route('login') }}" method="POST" class="flex flex-col items-center space-y-4">
            @csrf

            <!-- Nama Pengguna -->
            <input 
                type="text" 
                name="namapengguna" 
                id="namapengguna" 
                placeholder="Nama Pengguna" 
                class="w-[350px] px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                required
            >

            <!-- Kata Kunci -->
            <div class="relative w-[350px]">
                <input 
                    type="password" 
                    id="katakunci" 
                    name="katakunci" 
                    placeholder="Kata Kunci"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    required
                >
                <i class="fas fa-eye absolute right-3 top-3.5 text-gray-500 cursor-pointer" id="toggle-password"></i>
            </div>

            <!-- Tombol Masuk -->
            <button 
                type="submit" 
                class="w-[350px] bg-[#89E355] text-white py-2 rounded-md hover:bg-[#7ED242] focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Masuk
            </button>
        </form>

    <!-- Script toggle password -->
    <script>
        const toggle = document.getElementById('toggle-password');
        const password = document.getElementById('katakunci');

        toggle.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';

            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
