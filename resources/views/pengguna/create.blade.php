<!-- resources/views/pengguna/create.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>

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
        <h1 class="text-2xl font-semibold text-center mb-6" style="color:#89E355; font-style: italic; font-size: 32px; position: 65px;" >Tani Makmur</h1>

        <!-- Formulir -->
        <form action="{{ route('pengguna.store') }}" method="POST" class="flex flex-col items-center space-y-4">
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

            <!-- Hidden inputs -->
            <input type="hidden" name="status" value="admin">
            <input type="hidden" name="aktif" value="1">

            <!-- Tombol Simpan -->
            <button 
                type="submit" 
                class="w-[350px] bg-[#89E355] text-white py-2 rounded-md hover:bg-[#7ED242] focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Simpan
            </button>
        </form>

        <!-- Link ke login -->
        <div class="text-center mt-4">
            <p class="text-sm">Punya akun? 
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-600">Masuk</a>
            </p>
        </div>
    </div>

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
