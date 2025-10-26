<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MagangSMK - Platform Pencarian Magang untuk Siswa SMK</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Custom styles to complement Tailwind */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc; /* slate-50 */
            color: #334155; /* slate-700 */
        }

        /* Custom gradient for hero and CTA */
        .hero-gradient {
            background: linear-gradient(135deg, #38bdf8 0%, #3b82f6 100%); /* sky-400 to blue-500 */
        }
        
        .cta-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); /* blue-500 to indigo-500 */
        }

        /* Animation for elements on scroll */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Custom transition delay utilities */
        .transition-delay-100 { transition-delay: 100ms; }
        .transition-delay-200 { transition-delay: 200ms; }
        .transition-delay-300 { transition-delay: 300ms; }
        .transition-delay-400 { transition-delay: 400ms; }
        .transition-delay-500 { transition-delay: 500ms; }
    </style>
</head>
<body class="antialiased">
    @php
        $homepageUrl = route('homepage');
    @endphp

    <!-- Header -->
    <header id="header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ $homepageUrl }}" class="text-white text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-graduation-cap"></i>
                <span>MagangSMK</span>
            </a>
            <ul class="hidden lg:flex items-center space-x-8 text-white font-medium">
                <li><a href="{{ $homepageUrl }}#beranda" class="hover:text-sky-200 transition-colors">Beranda</a></li>
                <li><a href="{{ $homepageUrl }}#fitur" class="hover:text-sky-200 transition-colors">Fitur</a></li>
                <li><a href="{{ $homepageUrl }}#perusahaan" class="hover:text-sky-200 transition-colors">Perusahaan</a></li>
                <li><a href="{{ $homepageUrl }}#kontak" class="hover:text-sky-200 transition-colors">Kontak</a></li>
            </ul>
            <div class="hidden lg:flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-white font-medium px-5 py-2 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Masuk</a>
                <a href="{{ route('register') }}" class="bg-white text-blue-500 font-semibold px-5 py-2.5 rounded-full hover:bg-sky-100 transition-all duration-300 shadow-lg hover:shadow-none">Daftar</a>
            </div>
            <button id="mobile-menu-button" type="button" class="lg:hidden text-white text-2xl" aria-controls="mobile-menu" aria-expanded="false" aria-label="Buka menu navigasi">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden fixed top-0 left-0 w-full h-full bg-blue-500 z-50 overflow-y-auto" aria-hidden="true">
        <div class="container mx-auto px-6 py-4 flex flex-col h-full">
             <div class="flex justify-between items-center mb-12">
                 <a href="{{ $homepageUrl }}" class="text-white text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-graduation-cap"></i>
                    <span>MagangSMK</span>
                </a>
                <button id="mobile-menu-close" type="button" class="text-white text-3xl" aria-label="Tutup menu navigasi">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <ul class="flex flex-col items-center space-y-8 text-white text-xl font-medium">
                <li><a href="{{ $homepageUrl }}#beranda" class="mobile-link">Beranda</a></li>
                <li><a href="{{ $homepageUrl }}#fitur" class="mobile-link">Fitur</a></li>
                <li><a href="{{ $homepageUrl }}#perusahaan" class="mobile-link">Perusahaan</a></li>
                <li><a href="{{ $homepageUrl }}#kontak" class="mobile-link">Kontak</a></li>
            </ul>
            <div class="mt-auto flex flex-col items-center space-y-4 pb-8">
                <a href="{{ route('login') }}" class="mobile-link w-full text-center text-white font-medium px-6 py-3 rounded-full border-2 border-white hover:bg-white hover:text-blue-500 transition-all duration-300">Masuk</a>
                <a href="{{ route('register') }}" class="mobile-link w-full text-center bg-white text-blue-500 font-semibold px-6 py-3 rounded-full hover:bg-sky-100 transition-all duration-300">Daftar</a>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="beranda" class="hero-gradient pt-32 pb-20 text-white overflow-hidden">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 reveal-on-scroll">Temukan Tempat Magang Impianmu</h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto mb-8 opacity-90 reveal-on-scroll transition-delay-100">Platform terpercaya untuk siswa SMK mendapatkan pengalaman magang di perusahaan-perusahaan terbaik Indonesia.</p>
            <div class="max-w-2xl mx-auto reveal-on-scroll transition-delay-200">
                <div class="flex flex-col md:flex-row gap-4 bg-white/20 backdrop-blur-sm p-4 rounded-full shadow-2xl">
                    <input type="text" class="w-full bg-transparent text-white placeholder-sky-100 px-6 py-3 rounded-full focus:outline-none focus:ring-2 focus:ring-white" placeholder="Cari posisi, perusahaan, atau lokasi...">
                    <button class="w-full md:w-auto bg-white text-blue-500 font-semibold px-8 py-3 rounded-full hover:bg-sky-100 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-slate-100">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="reveal-on-scroll">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">1,500+</div>
                    <div class="text-slate-500 mt-1">Lowongan Magang</div>
                </div>
                <div class="reveal-on-scroll transition-delay-100">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">800+</div>
                    <div class="text-slate-500 mt-1">Perusahaan Partner</div>
                </div>
                <div class="reveal-on-scroll transition-delay-200">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">5,000+</div>
                    <div class="text-slate-500 mt-1">Siswa Terdaftar</div>
                </div>
                <div class="reveal-on-scroll transition-delay-300">
                    <div class="text-4xl md:text-5xl font-bold text-blue-500 stat-number">95%</div>
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
            <div class="relative">
                <div class="flex gap-8 animate-marquee">
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=TechCorp" alt="TechCorp Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=InnovateInc" alt="InnovateInc Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=DigitalCreative" alt="DigitalCreative Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=OtomotifJaya" alt="OtomotifJaya Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=NusantaraHotel" alt="NusantaraHotel Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=HealthCare" alt="HealthCare Logo"></div>
                     <!-- Duplicate for seamless loop -->
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=TechCorp" alt="TechCorp Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=InnovateInc" alt="InnovateInc Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=DigitalCreative" alt="DigitalCreative Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=OtomotifJaya" alt="OtomotifJaya Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=NusantaraHotel" alt="NusantaraHotel Logo"></div>
                    <div class="flex-shrink-0 w-48 h-24 bg-white rounded-xl flex items-center justify-center shadow-md"><img src="https://placehold.co/120x40/000000/FFFFFF?text=HealthCare" alt="HealthCare Logo"></div>
                </div>
            </div>
            <style>
                @keyframes marquee { 0% { transform: translateX(0%); } 100% { transform: translateX(-50%); } }
                .animate-marquee { display: flex; animation: marquee 30s linear infinite; }
            </style>
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Header Scroll Effect ---
            const header = document.getElementById('header');
            if (header) {
                const applyHeaderState = () => {
                    if (window.scrollY > 50) {
                        header.classList.add('bg-blue-500/90', 'backdrop-blur-sm', 'shadow-lg');
                    } else {
                        header.classList.remove('bg-blue-500/90', 'backdrop-blur-sm', 'shadow-lg');
                    }
                };
                window.addEventListener('scroll', applyHeaderState);
                applyHeaderState();
            }

            // --- Mobile Menu Toggle ---
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            if (mobileMenuButton && mobileMenu && mobileMenuClose) {
                const openMenu = () => {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.setAttribute('aria-hidden', 'false');
                    mobileMenuButton.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                };
                const closeMenu = () => {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.setAttribute('aria-hidden', 'true');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                };

                mobileMenuButton.addEventListener('click', openMenu);
                mobileMenuClose.addEventListener('click', closeMenu);
                mobileLinks.forEach(link => {
                    link.addEventListener('click', closeMenu);
                });
            }

            // --- Animate on Scroll ---
            const scrollElements = document.querySelectorAll('.reveal-on-scroll');
            const elementInView = (el, dividend = 1) => {
                const elementTop = el.getBoundingClientRect().top;
                return (
                    elementTop <= (window.innerHeight || document.documentElement.clientHeight) / dividend
                );
            };

            const handleScrollAnimation = () => {
                scrollElements.forEach((el) => {
                    if (elementInView(el, 1.25)) {
                        el.classList.add('is-visible');
                    }
                });
            }
            window.addEventListener('scroll', handleScrollAnimation);
            handleScrollAnimation(); // Trigger on load

            // --- Stat Number Animation ---
            const statNumbers = document.querySelectorAll('.stat-number');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const finalValue = target.textContent;
                        const numericValue = parseInt(finalValue.replace(/\D/g, ''));
                        const suffix = finalValue.replace(/[\d,.]/g, '');
                        
                        let start = 0;
                        const duration = 2000; // 2 seconds
                        const startTime = performance.now();

                        function animate(currentTime) {
                            const elapsedTime = currentTime - startTime;
                            const progress = Math.min(elapsedTime / duration, 1);
                            const currentVal = Math.floor(progress * numericValue);
                            
                            target.textContent = currentVal.toLocaleString('id-ID') + suffix;

                            if (progress < 1) {
                                requestAnimationFrame(animate);
                            } else {
                                target.textContent = numericValue.toLocaleString('id-ID') + suffix;
                            }
                        }
                        
                        requestAnimationFrame(animate);
                        observer.unobserve(target); // Animate only once
                    }
                });
            }, { threshold: 0.5 }); // Trigger when 50% of the element is visible

            statNumbers.forEach(stat => observer.observe(stat));
        });
    </script>
</body>
</html>
