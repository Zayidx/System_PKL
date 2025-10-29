{{-- Landing page publik menampilkan hero pencarian, statistik, dan daftar mitra. --}}
<div>

    <!-- Header -->
    <header id="header" class="fixed top-0 left-0 right-0 z-40 transition-all duration-300">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-graduation-cap"></i>
                <span>MagangSMK</span>
            </a>
            <ul class="hidden lg:flex items-center space-x-8 text-white font-medium">
                <li><a href="#beranda" class="hover:text-sky-200 transition-colors">Beranda</a></li>
                <li><a href="#fitur" class="hover:text-sky-200 transition-colors">Fitur</a></li>
                <li><a href="#perusahaan" class="hover:text-sky-200 transition-colors">Perusahaan</a></li>
                <li><a href="#kontak" class="hover:text-sky-200 transition-colors">Kontak</a></li>
            </ul>
            <div class="hidden lg:flex items-center space-x-4">
                
                {{-- Tombol menyesuaikan status autentikasi pengguna --}}
                @auth
                    {{-- Tampilan jika pengguna SUDAH LOGIN --}}
                    
                    {{-- Tombol Dashboard --}}
                    @if(Auth::user()->role->name == 'superadmin')
                        <a href="{{ route('administrator.dasbor') }}" wire:navigate class="text-white font-medium px-5 py-2 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Dashboard</a>
                    @else
                        <a href="{{ route('pengguna.dasbor') }}" wire:navigate class="text-white font-medium px-5 py-2 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Dashboard</a>
                    @endif
                
                    {{-- Tombol Logout --}}
                    <form method="POST" action="{{ route('keluar') }}" style="display: inline;">
                        @csrf
                        <a
                           wire:click="logout"
                           class="bg-white text-blue-500 font-semibold px-5 py-2.5 rounded-full hover:bg-sky-100 transition-all duration-300 shadow-lg hover:shadow-none">Logout</a>
                    </form>
                @else
                     <a href="{{ route('masuk') }}" class="text-white font-medium px-5 py-2 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Masuk</a>
                <a href="{{ route('daftar') }}" class="bg-white text-blue-500 font-semibold px-5 py-2.5 rounded-full hover:bg-sky-100 transition-all duration-300 shadow-lg hover:shadow-none">Daftar</a>
                @endauth
              
            </div>
            <button id="mobile-menu-button" class="lg:hidden text-white text-2xl z-50">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden fixed top-0 left-0 w-full h-full bg-blue-500 z-50">
        <div class="container mx-auto px-6 py-4 flex flex-col h-full">
            <div class="flex justify-between items-center mb-12">
                <a href="#" class="text-white text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-graduation-cap"></i>
                    <span>MagangSMK</span>
                </a>
                <button id="mobile-menu-close" class="text-white text-3xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="flex flex-col items-center space-y-8 text-white text-xl font-medium">
                <li><a href="#beranda" class="mobile-link">Beranda</a></li>
                <li><a href="#fitur" class="mobile-link">Fitur</a></li>
                <li><a href="#perusahaan" class="mobile-link">Perusahaan</a></li>
                <li><a href="#kontak" class="mobile-link">Kontak</a></li>
            </ul>
            <div class="mt-auto flex flex-col items-center space-y-4 pb-8">
                <a href="#" class="w-full text-center text-white font-medium px-6 py-3 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Masuk</a>
                <a href="#" class="w-full text-center bg-white text-blue-500 font-semibold px-6 py-3 rounded-full hover:bg-sky-100 transition-all duration-300">Daftar</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="beranda" class="hero-gradient pt-32 pb-20 text-white overflow-hidden">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 reveal-on-scroll">Temukan Tempat Magang Impianmu</h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto mb-8 opacity-90 reveal-on-scroll transition-delay-100">Platform terpercaya untuk siswa SMK mendapatkan pengalaman magang di perusahaan-perusahaan terbaik Indonesia.</p>
            <div class="mt-10 reveal-on-scroll transition-delay-200">
                <a href="#fitur" class="bg-white text-blue-600 font-bold px-8 py-4 rounded-full text-lg hover:bg-sky-100 transition-all duration-300 transform hover:scale-105 inline-block shadow-xl">
                    Magang Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-slate-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="reveal-on-scroll">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">54</div>
                    <div class="text-slate-500 mt-1">Lowongan Magang</div>
                </div>
                <div class="reveal-on-scroll transition-delay-100">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">{{ number_format($statPerusahaan) }}</div>
                    <div class="text-slate-500 mt-1">Perusahaan Partner</div>
                </div>
                <div class="reveal-on-scroll transition-delay-200">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">270</div>
                    <div class="text-slate-500 mt-1">Siswa Terdaftar</div>
                </div>
                <div class="reveal-on-scroll transition-delay-300">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">
                        100
                    </div>
                    <div class="text-slate-500 mt-1">Tingkat Keberhasilan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-20">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4 reveal-on-scroll">Mengapa Memilih MagangSMK?</h2>
                <p class="text-slate-500 text-lg reveal-on-scroll transition-delay-100">Kami menyediakan semua yang kamu butuhkan untuk memulai karir profesionalmu.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Card 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-blue-200 hover:-translate-y-2 transition-all duration-300 reveal-on-scroll">
                    <div class="bg-sky-100 text-sky-500 text-3xl w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Pencarian Cerdas</h3>
                    <p class="text-slate-500">Temukan lowongan sesuai jurusan dan minatmu dengan sistem pencarian canggih dan filter lengkap.</p>
                </div>
                <!-- Feature Card 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-blue-200 hover:-translate-y-2 transition-all duration-300 reveal-on-scroll transition-delay-100">
                    <div class="bg-indigo-100 text-indigo-500 text-3xl w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Perusahaan Terverifikasi</h3>
                    <p class="text-slate-500">Bermitra dengan perusahaan terkemuka yang memberikan pengalaman magang berkualitas tinggi.</p>
                </div>
                <!-- Feature Card 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-blue-200 hover:-translate-y-2 transition-all duration-300 reveal-on-scroll transition-delay-200">
                    <div class="bg-emerald-100 text-emerald-500 text-3xl w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Sertifikat Resmi</h3>
                    <p class="text-slate-500">Dapatkan sertifikat magang resmi yang diakui industri untuk memperkuat portofolio dan CV-mu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Companies Section -->
    <section id="perusahaan" class="py-20 bg-slate-100">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4 reveal-on-scroll">Dipercaya oleh Perusahaan Terkemuka</h2>
            </div>
        </div>
        <div class="relative overflow-hidden">
            <div class="flex gap-8 animate-marquee">
                @foreach($perusahaanLogos as $perusahaan)
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md">
                        @if($perusahaan->logo_perusahaan)
                            <img src="{{ Storage::url($perusahaan->logo_perusahaan) }}" alt="{{ $perusahaan->nama_perusahaan }} Logo" onerror="this.style.display='none'; this.parentElement.innerText='{{ $perusahaan->nama_perusahaan }}'">
                        @else
                            <span class="text-slate-500">{{ $perusahaan->nama_perusahaan }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-gradient py-20 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 reveal-on-scroll">Siap Memulai Perjalanan Karirmu?</h2>
            <p class="text-lg md:text-xl max-w-2xl mx-auto mb-8 opacity-90 reveal-on-scroll transition-delay-100">Bergabunglah dengan ribuan siswa SMK yang telah menemukan masa depan mereka bersama kami.</p>
            <div class="reveal-on-scroll transition-delay-200">
                <a href="#" class="bg-white text-blue-600 font-bold px-8 py-4 rounded-full text-lg hover:bg-sky-100 transition-all duration-300 transform hover:scale-105 inline-block shadow-xl">Daftar Sekarang, Gratis!</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-slate-800 text-slate-300">
        <div class="container mx-auto px-6 pt-16 pb-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- About -->
                <div class="col-span-1 md:col-span-2 lg:col-span-1">
                    <h3 class="text-white text-xl font-bold flex items-center gap-2 mb-4">
                        <i class="fas fa-graduation-cap"></i> MagangSMK
                    </h3>
                    <p class="text-slate-400">Menghubungkan siswa SMK dengan perusahaan terbaik untuk pengalaman magang yang berkualitas.</p>
                </div>
                <!-- Links -->
                <div>
                    <h4 class="font-semibold text-white mb-4">Layanan</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-sky-300 transition-colors">Cari Lowongan</a></li>
                        <li><a href="#" class="hover:text-sky-300 transition-colors">Daftar Perusahaan</a></li>
                        <li><a href="#" class="hover:text-sky-300 transition-colors">Konsultasi Karir</a></li>
                    </ul>
                </div>
                <!-- Support -->
                <div>
                    <h4 class="font-semibold text-white mb-4">Dukungan</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="hover:text-sky-300 transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-sky-300 transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-sky-300 transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                <!-- Social -->
                <div>
                    <h4 class="font-semibold text-white mb-4">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-xl hover:text-sky-300 transition-colors"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-xl hover:text-sky-300 transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-xl hover:text-sky-300 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-xl hover:text-sky-300 transition-colors"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-700 pt-8 text-center text-slate-500">
                <p>&copy; 2025 MagangSMK. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>



</div>
