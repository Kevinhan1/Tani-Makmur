<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Home | Multi Daya Pratama</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        html {
            scroll-behavior: smooth;
        }
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8F8F8] flex flex-col min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-md py-6 px-6 sticky top-0 z-50">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <h1 class="text-xl md:text-2xl font-bold italic text-[#89E355]">Multi Daya Pratama</h1>
            <nav class="space-x-6 text-base md:text-lg" id="navbar">
                <a href="beranda" data-target="beranda"class="nav-link text-gray-700 hover:text-[#89E355]">Beranda</a>
                <a href="produk" data-target="produk"class="nav-link text-gray-700 hover:text-[#89E355]">Produk</a>
                <a href="layanan" data-target="layanan" class="nav-link text-gray-700 hover:text-[#89E355]">Layanan</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="beranda" class="scroll-mt-20 relative w-full min-h-screen flex items-center justify-center bg-black text-white">
            <img src="{{ asset('product-img\wheatfield2.jpg') }}" 
                alt="Gambar Tepung"
                class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="relative z-10 text-center px-4">
                <h2 class="text-[#89E355] text-4xl md:text-4xl  italic font-bold mb-4">CV. Multi Daya Pratama</h2>
                <p class="text-xl md:text-xl">Merupakan Distributor Tepung, CV. Multi Daya Pratama adalah pemasok tepung terigu yang menyediakan berbagai jenis tepung berkualitas.</p>
            </div>
        </section>

        <!-- Produk Section -->
        <section id="produk" class="scroll-mt-20 py-4 min-h-screen px-4 md:px-8 bg-white flex flex-col justify-center">
        <h2 class="text-4xl font-bold text-center mb-12 text-gray-800">Produk Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('product-img/TepungSegitigaBiruProteinSedang1kg900x900.jpeg') }}" alt="Segitiga Biru" class="w-full h-64 object-cover">
                <div class="p-4 text-center">
                    <h3 class="font-semibold text-lg text-blue-800">Segitiga Biru</h3>
                    <p class="text-sm text-gray-600">Tepung Terigu 1 KG</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('product-img/BogasariCakraKembarTeriguProteinTinggi1kg900x900.jpeg') }}" alt="Cakra Kembar" class="w-full h-64 object-cover">
                <div class="p-4 text-center">
                    <h3 class="font-semibold text-lg text-green-800">Cakra Kembar</h3>
                    <p class="text-sm text-gray-600">Tepung Terigu 1 KG</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ asset('product-img/Tepunglencana1kg900x900.jpg') }}" alt="Lencana Merah" class="w-full h-64 object-cover">
                <div class="p-4 text-center">
                    <h3 class="font-semibold text-lg text-red-800">Lencana Merah</h3>
                    <p class="text-sm text-gray-600">Tepung Terigu 1 KG</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Layanan / Order -->
<section id="layanan" class="scroll-mt-20 relative bg-cover bg-center bg-no-repeat min-h-screen flex flex-col justify-center items-center text-center px-6 py-4" style="background-image: url('{{ asset('product-img/bread.jpg') }}');">
    
    <!-- Overlay agar teks terbaca -->
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>

    <!-- Konten -->
    <div class="relative z-10">
        <h2 class="text-5xl font-bold mb-6 text-white">Layanan Pelanggan</h2>
        <p class="text-white text-2xl">Hubungi kami melalui email:</p>
        <p class="text-[#89E355] mt-4 text-2xl font-semibold">
            <a href="https://mail.google.com/mail/?view=cm&fs=1&to=cv.multidayapratama@gmail.com" 
            target="_blank" 
            class="hover:underline">
                cv.multidayapratama@gmail.com
            </a>
        </p>
    </div>
</section>


    <!-- Footer -->
    <footer class="bg-white shadow-inner py-20 mt-auto flex items-center justify-center">
        <div class="text-center text-lg text-gray-600">
            Copyright &copy; {{ date('Y') }} Multi Daya Pratama. All Right Reserved.
        </div>
    </footer>


    <!-- Active Nav Script -->
<script>
    const navLinks = document.querySelectorAll(".nav-link");

    // Scroll halus ke target saat link diklik
    navLinks.forEach(link => {
        link.addEventListener("click", function(e) {
            e.preventDefault();
            const targetId = this.getAttribute("data-target");
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 80,
                    behavior: 'smooth'
                });
                // Update URL tanpa reload
                window.history.replaceState({}, '', '#' + targetId);
            }
        });
    });

    // Highlight menu aktif saat scroll
    window.addEventListener("scroll", () => {
        const sections = document.querySelectorAll("section");
        let current = "";
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute("id");
            }
        });

        navLinks.forEach(link => {
            const target = link.getAttribute("data-target");
            link.classList.remove("text-[#89E355]", "border-b-2", "border-[#89E355]");
            if (target === current) {
                link.classList.add("text-[#89E355]", "border-b-2", "border-[#89E355]");
            }
        });
    });
</script>

</body>
</html>
