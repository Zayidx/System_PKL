<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InternHub - Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      AOS.init();
    });
  </script>
</head>

<body class="bg-blue-50 text-gray-800">
  
  <nav class="flex items-center justify-between px-6 py-4 bg-white shadow">
    <div class="flex items-center space-x-2">
      <img src="/img/logo.png" alt="InternHub Logo" class="h-8 w-8">
      <span class="font-bold text-xl text-blue-600">InternHub</span>
    </div>
    <ul class="hidden md:flex space-x-6 text-sm font-medium">
      <li><a href="#home" class="text-blue-600">Lowongan Magang</a></li>
      <li><a href="#aplikasi" class="hover:text-blue-600">Aplikasi Saya</a></li>
      <li><a href="#perusahaan" class="hover:text-blue-600">Perusahaan</a></li>
    </ul>
   <a href="{{ url('/dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
  Masuk ke Dashboard
</a>

  </nav>


  <section id="home" class="py-12 px-4 md:px-16" data-aos="fade-up">
    <h1 class="text-3xl md:text-4xl font-bold text-blue-900 mb-2">Selamat Datang di InternHub</h1>
    <p class="text-lg text-blue-600 mb-8">Temukan peluang magang terbaik untuk mengembangkan karir Anda</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-medium text-sm text-blue-600">Total Lowongan</h3>
        <p class="text-2xl font-bold">1,234</p>
        <span class="text-green-600 text-sm">+12%</span>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-medium text-sm text-blue-600">Pelamar Aktif</h3>
        <p class="text-2xl font-bold">5,678</p>
        <span class="text-green-600 text-sm">+8%</span>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-medium text-sm text-blue-600">Magang Berlangsung</h3>
        <p class="text-2xl font-bold">892</p>
        <span class="text-green-600 text-sm">+15%</span>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h3 class="font-medium text-sm text-blue-600">Tingkat Penempatan</h3>
        <p class="text-2xl font-bold">87%</p>
        <span class="text-green-600 text-sm">+3%</span>
      </div>
    </div>
  </section>

  <section class="py-10 px-4 md:px-16 bg-white" data-aos="fade-up">
    <h2 class="text-2xl font-bold text-blue-800 mb-6">Lowongan Magang</h2>
    <div class="space-y-6">
      <div class="p-6 bg-blue-50 rounded shadow">
        <h3 class="text-xl font-bold text-blue-800">Frontend Developer Intern</h3>
        <p class="text-blue-600">Tech Innovate</p>
        <p class="text-sm text-gray-600">Jakarta • 3 bulan • Full-time</p>
        <p class="mt-2">Bergabunglah dengan tim pengembangan frontend kami untuk membangun aplikasi web modern.</p>
      </div>
      <div class="p-6 bg-blue-50 rounded shadow">
        <h3 class="text-xl font-bold text-blue-800">UI/UX Design Intern</h3>
        <p class="text-blue-600">Creative Studio</p>
        <p class="text-sm text-gray-600">Bandung • 4 bulan • Part-time</p>
        <p class="mt-2">Kesempatan untuk belajar desain UI/UX dari para ahli di industri kreatif.</p>
      </div>
    </div>
  </section>

  <section id="perusahaan" class="py-10 px-4 md:px-16 bg-blue-50" data-aos="fade-up">
    <h2 class="text-2xl font-bold text-blue-800 mb-2">Perusahaan Partner</h2>
    <p class="mb-6 text-blue-600">Temukan perusahaan yang menawarkan program magang</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">Tech Innovate</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">Creative Studio</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">DataCorp</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">StartupHub</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">Digital Agency</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
      <div class="bg-white p-4 rounded shadow text-center">
        <h3 class="font-bold text-blue-800">Innovation Labs</h3>
        <p class="text-blue-500">Teknologi & Inovasi</p>
      </div>
    </div>
  </section>

  <footer class="bg-blue-900 text-white py-8 px-4 md:px-16 mt-10">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div>
        <h3 class="font-bold mb-2">InternHub</h3>
        <p>Platform terpercaya untuk menemukan kesempatan magang terbaik di Indonesia.</p>
      </div>
      <div>
        <h3 class="font-bold mb-2">Layanan</h3>
        <ul>
          <li>Pencarian Magang</li>
          <li>Konsultasi Karir</li>
          <li>Pelatihan</li>
        </ul>
      </div>
      <div>
        <h3 class="font-bold mb-2">Perusahaan</h3>
        <ul>
          <li>Tentang Kami</li>
          <li>Karir</li>
          <li>Kontak</li>
        </ul>
      </div>
      <div>
        <h3 class="font-bold mb-2">Dukungan</h3>
        <ul>
          <li>FAQ</li>
          <li>Bantuan</li>
          <li>Kebijakan Privasi</li>
        </ul>
      </div>
    </div>
    <p class="text-center text-sm mt-6">&copy; 2024 Inovatik. Semua hak dilindungi.</p>
  </footer>
</body>
</html>
