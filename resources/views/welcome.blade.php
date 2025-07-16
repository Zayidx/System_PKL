<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MagangSMK - Platform Pencarian Magang untuk Siswa SMK</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #87ceeb 0%, #4a90e2 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: #4a90e2;
        }

        .btn-primary {
            background: white;
            color: #4a90e2;
        }

        .btn-primary:hover {
            background: #f0f8ff;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #87ceeb 0%, #4a90e2 100%);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .search-box {
            display: flex;
            background: white;
            border-radius: 50px;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
            border-radius: 40px;
            outline: none;
            color: #333;
        }

        .search-btn {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 40px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .search-btn:hover {
            background: #357abd;
        }

        /* Stats Section */
        .stats {
            background: white;
            padding: 60px 0;
            margin-top: -40px;
            position: relative;
            z-index: 10;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item {
            padding: 2rem;
            border-radius: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: transform 0.3s;
        }

        .stat-item:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4a90e2;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 1rem;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: #f8fafc;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #1e293b;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: #87ceeb;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Companies Section */
        .companies {
            padding: 80px 0;
            background: white;
        }

        .companies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .company-card {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .company-card:hover {
            background: #87ceeb;
            color: white;
            transform: translateY(-3px);
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #4a90e2 0%, #87ceeb 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            background: white;
            color: #4a90e2;
            border-radius: 30px;
        }

        .btn-large:hover {
            background: #f0f8ff;
            transform: translateY(-3px);
        }

        /* Footer */
        footer {
            background: #1e293b;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: #87ceeb;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section ul li a:hover {
            color: #87ceeb;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #334155;
            color: #94a3b8;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            background: white;
            color: #4a90e2;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu {
                display: block;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .search-box {
                flex-direction: column;
                gap: 10px;
            }

            .search-btn {
                border-radius: 25px;
            }

            .section-title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
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
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
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

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Search functionality
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchInput = document.querySelector('.search-input');
            const searchTerm = searchInput.value.trim();
            
            if (searchTerm) {
                // Simulate search action
                alert(`Mencari: "${searchTerm}"\n\nFitur pencarian akan segera tersedia!`);
            } else {
                alert('Silakan masukkan kata kunci pencarian');
            }
        });

        // Enter key search
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();
            }
        });

        // Mobile menu toggle (basic implementation)
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            if (navLinks.style.display === 'flex') {
                navLinks.style.display = 'none';
            } else {
                navLinks.style.display = 'flex';
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '100%';
                navLinks.style.left = '0';
                navLinks.style.right = '0';
                navLinks.style.background = '#4a90e2';
                navLinks.style.padding = '1rem';
            }
        });

        // Animate stats on scroll
        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const finalNumber = target.textContent;
                        const numericValue = parseInt(finalNumber.replace(/\D/g, ''));
                        const suffix = finalNumber.replace(/[\d,]/g, '');
                        
                        let current = 0;
                        const increment = numericValue / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= numericValue) {
                                current = numericValue;
                                clearInterval(timer);
                            }
                            target.textContent = Math.floor(current).toLocaleString() + suffix;
                        }, 30);
                        
                        observer.unobserve(target);
                    }
                });
            });

            statNumbers.forEach(stat => observer.observe(stat));
        }

        // Initialize animations when page loads
        window.addEventListener('load', animateStats);

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(74, 144, 226, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'linear-gradient(135deg, #87ceeb 0%, #4a90e2 100%)';
                header.style.backdropFilter = 'none';
            }
        });

        // Add hover effects to cards
        document.querySelectorAll('.feature-card, .company-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>