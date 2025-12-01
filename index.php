<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Beranda</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B7355 0%, #A0826D 50%, #C19A6B 100%);
            min-height: 100vh;
            color: white;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(107, 87, 68, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: bold;
            color: #FFF8DC;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .navbar-brand i {
            font-size: 32px;
        }
        
        .navbar-menu {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .navbar-menu a {
            color: #FFF8DC;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 8px;
        }
        
        .navbar-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-login {
            background: #CD853F;
            padding: 10px 25px !important;
            border-radius: 25px;
            font-weight: 600;
        }
        
        .btn-login:hover {
            background: #B8733A;
            box-shadow: 0 5px 15px rgba(205, 133, 63, 0.4);
        }
        
        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 150px 50px 80px;
            max-width: 1400px;
            margin: 0 auto;
            gap: 60px;
        }
        
        .hero-content {
            flex: 1;
            max-width: 600px;
            animation: slideInLeft 1s ease;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .hero-content h1 {
            font-size: 56px;
            line-height: 1.2;
            margin-bottom: 25px;
            color: #FFF8DC;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero-content p {
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 40px;
            color: #F5F1E8;
            opacity: 0.95;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 16px 35px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        
        .btn-primary {
            background: #FFF8DC;
            color: #6B5744;
        }
        
        .btn-primary:hover {
            background: #F5F1E8;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 248, 220, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: #FFF8DC;
            border: 2px solid #FFF8DC;
        }
        
        .btn-secondary:hover {
            background: rgba(255, 248, 220, 0.2);
            transform: translateY(-3px);
        }
        
        /* Hero Image */
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            animation: slideInRight 1s ease;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .books-illustration {
            display: flex;
            gap: 20px;
            perspective: 1000px;
        }
        
        .book {
            width: 180px;
            height: 260px;
            background: linear-gradient(145deg, rgba(107, 87, 68, 0.8), rgba(139, 115, 85, 0.8));
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotateY(-15deg);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .book:nth-child(2) {
            transform: rotateY(0deg) translateY(-20px);
            background: linear-gradient(145deg, rgba(160, 130, 109, 0.8), rgba(193, 154, 107, 0.8));
        }
        
        .book:nth-child(3) {
            transform: rotateY(15deg);
            background: linear-gradient(145deg, rgba(193, 154, 107, 0.8), rgba(210, 180, 140, 0.8));
        }
        
        .book:hover {
            transform: rotateY(0deg) translateY(-10px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
        }
        
        .book i {
            font-size: 64px;
            color: rgba(255, 248, 220, 0.5);
        }
        
        .book::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 10px;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.3), transparent);
        }
        
        /* Features Section */
        .features {
            padding: 80px 50px;
            background: rgba(107, 87, 68, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .features h2 {
            text-align: center;
            font-size: 42px;
            margin-bottom: 60px;
            color: #FFF8DC;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .feature-card {
            background: rgba(255, 248, 220, 0.15);
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid rgba(255, 248, 220, 0.2);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 248, 220, 0.25);
            border-color: rgba(255, 248, 220, 0.4);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .feature-icon {
            font-size: 56px;
            margin-bottom: 20px;
            color: #F5DEB3;
        }
        
        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #FFF8DC;
        }
        
        .feature-card p {
            color: #F5F1E8;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        /* Stats Section */
        .stats {
            padding: 60px 50px;
            background: rgba(139, 115, 85, 0.3);
        }
        
        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #FFF8DC;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 18px;
            color: #F5F1E8;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            padding: 30px 50px;
            text-align: center;
            background: rgba(107, 87, 68, 0.5);
            color: #F5F1E8;
        }
        
        .footer p {
            margin-bottom: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .navbar-menu {
                gap: 15px;
            }
            
            .hero {
                flex-direction: column;
                padding: 100px 20px 50px;
                gap: 40px;
            }
            
            .hero-content h1 {
                font-size: 36px;
            }
            
            .hero-content p {
                font-size: 16px;
            }
            
            .books-illustration {
                gap: 10px;
            }
            
            .book {
                width: 100px;
                height: 150px;
            }
            
            .book i {
                font-size: 36px;
            }
            
            .features h2 {
                font-size: 32px;
            }
            
            .features, .stats {
                padding: 40px 20px;
            }
        }
        
        /* Loading Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        body {
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-book-reader"></i>
            Perpustakaan Digital
        </div>
        <div class="navbar-menu">
            <a href="#fitur">Fitur</a>
            <a href="#tentang">Tentang</a>
            <a href="#kontak">Kontak</a>
            <a href="login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di Perpustakaan Digital</h1>
            <p>Sistem manajemen perpustakaan modern dengan fitur lengkap untuk mengelola koleksi buku, anggota, dan transaksi peminjaman secara digital.</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sistem
                </a>
                <a href="#fitur" class="btn btn-secondary">
                    <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
        <div class="hero-image">
            <div class="books-illustration">
                <div class="book">
                    <i class="fas fa-book"></i>
                </div>
                <div class="book">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="book">
                    <i class="fas fa-bookmark"></i>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"))['total']; ?>+</div>
                <div class="stat-label">Koleksi Buku</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota"))['total']; ?>+</div>
                <div class="stat-label">Anggota Terdaftar</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total']; ?>+</div>
                <div class="stat-label">Kategori</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman"))['total']; ?>+</div>
                <div class="stat-label">Transaksi</div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features" id="fitur">
        <div class="features-container">
            <h2>Fitur Unggulan</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-book"></i></div>
                    <h3>Manajemen Buku</h3>
                    <p>Kelola koleksi buku dengan mudah, termasuk upload cover dan informasi lengkap</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Data Anggota</h3>
                    <p>Sistem keanggotaan lengkap dengan foto dan tracking peminjaman</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-exchange-alt"></i></div>
                    <h3>Peminjaman</h3>
                    <p>Transaksi peminjaman otomatis dengan perhitungan denda</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-star"></i></div>
                    <h3>Review & Rating</h3>
                    <p>Sistem penilaian buku oleh anggota untuk rekomendasi</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                    <h3>Laporan Visual</h3>
                    <p>Dashboard statistik dengan grafik interaktif real-time</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-bell"></i></div>
                    <h3>Reservasi Buku</h3>
                    <p>Anggota dapat memesan buku yang sedang dipinjam</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section class="features" id="tentang" style="background: rgba(139, 115, 85, 0.3);">
        <div class="features-container">
            <h2>Tentang Sistem</h2>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p style="font-size: 18px; line-height: 1.8; color: #F5F1E8; margin-bottom: 30px;">
                    Perpustakaan Digital adalah sistem manajemen perpustakaan berbasis web yang dirancang untuk memudahkan 
                    pengelolaan perpustakaan modern. Dengan interface yang user-friendly dan fitur yang lengkap, sistem ini 
                    membantu pustakawan dalam mengelola koleksi buku, keanggotaan, dan transaksi peminjaman secara efisien.
                </p>
                <p style="font-size: 18px; line-height: 1.8; color: #F5F1E8;">
                    Dilengkapi dengan dashboard statistik, sistem review dan rating, serta fitur reservasi buku, 
                    Perpustakaan Digital memberikan pengalaman terbaik bagi pustakawan dan anggota perpustakaan.
                </p>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section class="features" id="kontak" style="background: rgba(107, 87, 68, 0.3);">
        <div class="features-container">
            <h2>Hubungi Kami</h2>
            <div style="max-width: 600px; margin: 0 auto; text-align: center;">
                <div style="display: flex; flex-direction: column; gap: 20px; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 18px; color: #F5F1E8;">
                        <i class="fas fa-envelope" style="font-size: 24px; color: #F5DEB3;"></i>
                        <span>info@perpustakaandigital.com</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 18px; color: #F5F1E8;">
                        <i class="fas fa-phone" style="font-size: 24px; color: #F5DEB3;"></i>
                        <span>(021) 1234-5678</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; font-size: 18px; color: #F5F1E8;">
                        <i class="fas fa-map-marker-alt" style="font-size: 24px; color: #F5DEB3;"></i>
                        <span>Surabaya, East Java, Indonesia</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> Perpustakaan Digital. All rights reserved.</p>
        <p>Sistem Manajemen Perpustakaan Modern</p>
    </footer>
    
    <script>
        // Smooth scroll for navigation
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
        
        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(107, 87, 68, 1)';
            } else {
                navbar.style.background = 'rgba(107, 87, 68, 0.95)';
            }
        });
    </script>
</body>
</html>