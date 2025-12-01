<?php
require_once 'config.php';
check_login();

// Ambil statistik
$query_total_buku = "SELECT COUNT(*) as total FROM buku";
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, $query_total_buku))['total'];

$query_total_anggota = "SELECT COUNT(*) as total FROM anggota WHERE status = 'aktif'";
$total_anggota = mysqli_fetch_assoc(mysqli_query($conn, $query_total_anggota))['total'];

$query_sedang_dipinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam' AND tanggal_dikembalikan IS NULL";
$sedang_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, $query_sedang_dipinjam))['total'];

$query_terlambat = "SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_dikembalikan IS NULL AND tanggal_kembali < CURDATE()";
$terlambat = mysqli_fetch_assoc(mysqli_query($conn, $query_terlambat))['total'];

// Ambil data buku populer
$query_populer = "SELECT * FROM view_buku_populer LIMIT 5";
$result_populer = mysqli_query($conn, $query_populer);

// Ambil peminjaman terbaru
$query_peminjaman = "SELECT * FROM view_detail_peminjaman ORDER BY id_peminjaman DESC LIMIT 5";
$result_peminjaman = mysqli_query($conn, $query_peminjaman);

// Ambil aktivitas terbaru
$query_aktivitas = "SELECT l.*, u.nama_lengkap FROM log_aktivitas l 
                    LEFT JOIN users u ON l.id_user = u.id_user 
                    ORDER BY l.waktu DESC LIMIT 5";
$result_aktivitas = mysqli_query($conn, $query_aktivitas);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Perpustakaan Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F5F1E8;
            color: #4A4A4A;
        }
        
        .navbar {
            background: linear-gradient(135deg, #8B7355, #A0826D);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            color: #FFF8DC;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            color: #FFF8DC;
            text-align: right;
        }
        
        .user-info strong {
            display: block;
            font-size: 16px;
        }
        
        .user-info small {
            opacity: 0.9;
        }
        
        .logout-btn {
            background: #CD853F;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: #B8733A;
            transform: translateY(-2px);
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .menu-card {
            background: white;
            padding: 30px 20px;
            border-radius: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3);
            border-color: #D2B48C;
        }
        
        .menu-card i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #8B7355;
        }
        
        .menu-card h3 {
            color: #6B5744;
            font-size: 18px;
            margin-bottom: 8px;
        }
        
        .menu-card p {
            color: #999;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.2);
        }
        
        .stat-info h3 {
            color: #6B5744;
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .stat-info p {
            color: #999;
            font-size: 14px;
        }
        
        .stat-icon {
            font-size: 48px;
            opacity: 0.2;
        }
        
        .stat-card.blue { background: linear-gradient(135deg, #E8DCC4, #F5DEB3); }
        .stat-card.blue .stat-icon { color: #8B7355; }
        
        .stat-card.green { background: linear-gradient(135deg, #DED4BB, #C9B896); }
        .stat-card.green .stat-icon { color: #6B5744; }
        
        .stat-card.orange { background: linear-gradient(135deg, #F4E4C1, #E8D5B5); }
        .stat-card.orange .stat-icon { color: #A0826D; }
        
        .stat-card.red { background: linear-gradient(135deg, #F5E5D3, #DCC9B0); }
        .stat-card.red .stat-icon { color: #8B6F47; }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .card h2 {
            color: #6B5744;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #F5DEB3;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .book-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .book-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #FFF8DC;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .book-item:hover {
            background: #F5DEB3;
            transform: translateX(5px);
        }
        
        .book-cover {
            width: 60px;
            height: 80px;
            background: #D2B48C;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .book-info {
            flex: 1;
        }
        
        .book-info h4 {
            color: #6B5744;
            margin-bottom: 5px;
            font-size: 15px;
        }
        
        .book-info p {
            color: #999;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .rating {
            color: #DAA520;
        }
        
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .activity-item {
            padding: 12px;
            background: #FFF8DC;
            border-radius: 8px;
            border-left: 4px solid #D2B48C;
        }
        
        .activity-item strong {
            color: #6B5744;
            display: block;
            margin-bottom: 5px;
        }
        
        .activity-item small {
            color: #999;
            font-size: 12px;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background: #F5DEB3;
            color: #6B5744;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #F5F1E8;
        }
        
        table tr:hover {
            background: #FFF8DC;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.success {
            background: #C9E4CA;
            color: #2D5016;
        }
        
        .badge.warning {
            background: #FFE5B4;
            color: #8B4513;
        }
        
        .badge.danger {
            background: #FFD4D4;
            color: #8B0000;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-book-reader"></i>
            Perpustakaan Digital
        </div>
        <div class="navbar-user">
            <div class="user-info">
                <strong><?php echo $_SESSION['nama_lengkap']; ?></strong>
                <small><?php echo ucfirst($_SESSION['role']); ?></small>
            </div>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="container">
        <h1 style="color: #6B5744; margin-bottom: 30px;">
            <i class="fas fa-home"></i> Dashboard
        </h1>
        
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-info">
                    <h3><?php echo $total_buku; ?></h3>
                    <p>Total Buku</p>
                </div>
                <i class="fas fa-book stat-icon"></i>
            </div>
            
            <div class="stat-card green">
                <div class="stat-info">
                    <h3><?php echo $total_anggota; ?></h3>
                    <p>Anggota Aktif</p>
                </div>
                <i class="fas fa-users stat-icon"></i>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-info">
                    <h3><?php echo $sedang_dipinjam; ?></h3>
                    <p>Sedang Dipinjam</p>
                </div>
                <i class="fas fa-hand-holding-heart stat-icon"></i>
            </div>
            
            <div class="stat-card red">
                <div class="stat-info">
                    <h3><?php echo $terlambat; ?></h3>
                    <p>Terlambat</p>
                </div>
                <i class="fas fa-exclamation-triangle stat-icon"></i>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="buku.php" class="menu-card">
                <i class="fas fa-book"></i>
                <h3>Data Buku</h3>
                <p>Kelola koleksi buku</p>
            </a>
            
            <a href="anggota.php" class="menu-card">
                <i class="fas fa-users"></i>
                <h3>Data Anggota</h3>
                <p>Kelola data anggota</p>
            </a>
            
            <a href="peminjaman.php" class="menu-card">
                <i class="fas fa-exchange-alt"></i>
                <h3>Peminjaman</h3>
                <p>Transaksi peminjaman</p>
            </a>
            
            <a href="kategori.php" class="menu-card">
                <i class="fas fa-tags"></i>
                <h3>Kategori</h3>
                <p>Kelola kategori buku</p>
            </a>
            
            <a href="review.php" class="menu-card">
                <i class="fas fa-star"></i>
                <h3>Review Buku</h3>
                <p>Rating & ulasan</p>
            </a>
            
            <a href="reservasi.php" class="menu-card">
                <i class="fas fa-bookmark"></i>
                <h3>Reservasi</h3>
                <p>Reservasi buku</p>
            </a>
            
            <a href="laporan.php" class="menu-card">
                <i class="fas fa-chart-bar"></i>
                <h3>Laporan</h3>
                <p>Statistik & laporan</p>
            </a>
        </div>
        
        <div class="content-grid">
            <div class="card">
                <h2><i class="fas fa-fire"></i> Buku Populer</h2>
                <div class="book-list">
                    <?php while ($book = mysqli_fetch_assoc($result_populer)): ?>
                        <div class="book-item">
                            <div class="book-cover">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="book-info">
                                <h4><?php echo $book['judul']; ?></h4>
                                <p><?php echo $book['pengarang']; ?> • <?php echo $book['nama_kategori']; ?></p>
                                <div class="rating">
                                    <i class="fas fa-star"></i> <?php echo number_format($book['rata_rata_rating'], 1); ?>
                                    • <?php echo $book['total_dipinjam']; ?> peminjaman
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-history"></i> Aktivitas Terakhir</h2>
                <div class="activity-list">
                    <?php while ($log = mysqli_fetch_assoc($result_aktivitas)): ?>
                        <div class="activity-item">
                            <strong><?php echo $log['aktivitas']; ?></strong>
                            <p><?php echo $log['detail']; ?></p>
                            <small>
                                <i class="fas fa-user"></i> <?php echo $log['nama_lengkap'] ?: 'System'; ?> •
                                <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($log['waktu'])); ?>
                            </small>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-list"></i> Peminjaman Terbaru</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pinjam = mysqli_fetch_assoc($result_peminjaman)): ?>
                            <tr>
                                <td>#<?php echo $pinjam['id_peminjaman']; ?></td>
                                <td><?php echo $pinjam['nama_anggota']; ?></td>
                                <td><?php echo $pinjam['judul_buku']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($pinjam['tanggal_kembali'])); ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'success';
                                    if ($pinjam['status'] === 'dipinjam' && $pinjam['hari_terlambat'] > 0) {
                                        $badge_class = 'danger';
                                        $status_text = 'Terlambat ' . $pinjam['hari_terlambat'] . ' hari';
                                    } elseif ($pinjam['status'] === 'dipinjam') {
                                        $badge_class = 'warning';
                                        $status_text = 'Dipinjam';
                                    } else {
                                        $status_text = 'Dikembalikan';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>