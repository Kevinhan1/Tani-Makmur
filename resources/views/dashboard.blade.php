<!-- resources/views/dashboard.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-[#E0E0E0] flex items-center justify-center min-h-screen" style="font-family: 'Poppins', sans-serif;">

    <!-- Box putih -->
    <div class="bg-white w-[450px] h-[400px] p-8 rounded-lg shadow-lg flex flex-col items-center justify-center">
        
        <!-- Judul Dashboard -->
        <h1 class="text-2xl font-semibold text-center mb-6" style="color:#89E355; font-style: italic; font-size: 32px;">Dashboard</h1>

        <!-- Info Pengguna -->
        <p class="text-xl mb-4">Selamat datang, {{ session('user')->namapengguna }}!</p>

        <!-- Tombol Logout -->
        <form action="{{ route('logout') }}" method="get">
            <button 
                type="submit" 
                class="w-[350px] bg-[#89E355] text-white py-2 rounded-md hover:bg-[#7ED242] focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Logout
            </button>
        </form>
    </div>

</body>
</html>
