<div>
<!-- Header -->
    <header>
        <nav class="container">
            <a href="#" class="logo">
                <i class="fas fa-graduation-cap"></i> MagangSMK
            </a>
            <ul class="nav-links">
                <li><a href="#beranda">Beranda</a></li>
                <li><a href="#lowongan">Lowongan</a></li>
                <li><a href="#perusahaan">Perusahaan</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
            <div class="auth-buttons">
                
                {{-- [PERBAIKAN] Logika Tombol Dinamis --}}
                @auth
                    {{-- Tampilan jika pengguna SUDAH LOGIN --}}
                    
                    {{-- Tombol Dashboard --}}
                    @if(Auth::user()->role->name == 'superadmin')
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="btn btn-outline">Dashboard</a>
                    @else
                        <a href="{{ route('user.dashboard') }}" wire:navigate class="btn btn-outline">Dashboard</a>
                    @endif

                    {{-- Tombol Logout --}}
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="btn btn-primary">Logout</a>
                    </form>
                @else
                    {{-- Tampilan jika pengguna BELUM LOGIN (Tamu) --}}
                    <a href="{{ route('login') }}" wire:navigate class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" wire:navigate class="btn btn-primary">Daftar</a>
                @endauth

            </div>
            <button class="mobile-menu">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="container">
            <h1>Temukan Tempat Magang Impianmu</h1>
            <p>Platform terpercaya untuk siswa SMK mencari pengalaman magang di perusahaan terbaik</p>
            <div class="search-container">
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Cari posisi magang, perusahaan, atau lokasi...">
                    <button class="search-btn">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">1,500+</div>
                    <div class="stat-label">Lowongan Magang</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">800+</div>
                    <div class="stat-label">Perusahaan Partner</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">Siswa Terdaftar</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Tingkat Keberhasilan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Mengapa Memilih MagangSMK?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Pencarian Mudah</h3>
                    <p>Temukan lowongan magang sesuai jurusan dan minatmu dengan sistem pencarian yang canggih dan filter yang lengkap.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Perusahaan Terpercaya</h3>
                    <p>Bermitra dengan perusahaan-perusahaan terkemuka yang memberikan pengalaman magang berkualitas tinggi.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Sertifikat Resmi</h3>
                    <p>Dapatkan sertifikat magang resmi yang diakui industri untuk memperkuat portofolio dan CV-mu.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Bimbingan Mentor</h3>
                    <p>Didampingi mentor berpengalaman yang akan membimbing selama proses magang berlangsung.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Peluang Karir</h3>
                    <p>Kesempatan untuk melanjutkan karir di perusahaan tempat magang atau mendapat rekomendasi kerja.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Akses 24/7</h3>
                    <p>Platform yang dapat diakses kapan saja dan dimana saja melalui website maupun aplikasi mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Companies Section -->
    <section class="companies" id="perusahaan">
        <div class="container">
            <h2 class="section-title">Perusahaan Partner Kami</h2>
            <div class="companies-grid">
                <div class="company-card">
                    <h4>PT. Teknologi Maju</h4>
                    <p>IT & Software Development</p>
                </div>
                <div class="company-card">
                    <h4>CV. Kreatif Digital</h4>
                    <p>Digital Marketing & Design</p>
                </div>
                <div class="company-card">
                    <h4>PT. Industri Otomotif</h4>
                    <p>Teknik Mesin & Otomotif</p>
                </div>
                <div class="company-card">
                    <h4>Hotel Bintang Lima</h4>
                    <p>Perhotelan & Pariwisata</p>
                </div>
                <div class="company-card">
                    <h4>PT. Elektronik Nusantara</h4>
                    <p>Teknik Elektronika</p>
                </div>
                <div class="company-card">
                    <h4>Rumah Sakit Modern</h4>
                    <p>Kesehatan & Keperawatan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Siap Memulai Perjalanan Magangmu?</h2>
            <p>Bergabunglah dengan ribuan siswa SMK lainnya yang telah menemukan tempat magang impian mereka</p>
            <a href="#" class="btn btn-large">Daftar Sekarang Gratis</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>MagangSMK</h3>
                    <p>Platform terpercaya untuk menghubungkan siswa SMK dengan perusahaan terbaik untuk pengalaman magang yang berkualitas.</p>
                </div>
                <div class="footer-section">
                    <h3>Layanan</h3>
                    <ul>
                        <li><a href="#">Cari Lowongan</a></li>
                        <li><a href="#">Daftar Perusahaan</a></li>
                        <li><a href="#">Konsultasi Karir</a></li>
                        <li><a href="#">Sertifikat Digital</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Dukungan</h3>
                    <ul>
                        <li><a href="#">Pusat Bantuan</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                        <li><a href="#">Panduan Pengguna</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Ikuti Kami</h3>
                    <ul>
                        <li><a href="#"><i class="fab fa-facebook"></i> Facebook</a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li><a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 MagangSMK. Semua hak dilindungi undang-undang.</p>
            </div>
        </div>
    </footer>
    </div>
