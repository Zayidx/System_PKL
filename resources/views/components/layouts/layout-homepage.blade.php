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

        /* Prevent body scroll when mobile menu is open */
        body.menu-open {
            overflow: hidden;
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

        /* Marquee animation for logos */
        @keyframes marquee { 
            0% { transform: translateX(0%); } 
            100% { transform: translateX(-50%); } 
        }
        .animate-marquee { 
            display: flex; 
            animation: marquee 30s linear infinite; 
        }
    </style>
</head>
<body class="antialiased">

  {{ $slot }}


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Header Scroll Effect ---
            const header = document.getElementById('header');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    header.classList.add('bg-blue-500/90', 'backdrop-blur-sm', 'shadow-lg');
                } else {
                    header.classList.remove('bg-blue-500/90', 'backdrop-blur-sm', 'shadow-lg');
                }
            });

            // --- Mobile Menu Toggle (Bug-Free) ---
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileLinks = document.querySelectorAll('.mobile-link');

            const openMenu = () => {
                mobileMenu.classList.remove('hidden');
                document.body.classList.add('menu-open');
            };
            const closeMenu = () => {
                mobileMenu.classList.add('hidden');
                document.body.classList.remove('menu-open');
            };

            mobileMenuButton.addEventListener('click', openMenu);
            mobileMenuClose.addEventListener('click', closeMenu);
            mobileLinks.forEach(link => {
                link.addEventListener('click', closeMenu);
            });

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
