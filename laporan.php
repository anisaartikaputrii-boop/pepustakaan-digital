<?php
require_once 'config.php';
check_login();

// Statistik Umum
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota WHERE status = 'aktif'"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman"))['total'];
$sedang_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'"))['total'];
$total_denda = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(denda) as total FROM peminjaman WHERE denda > 0"))['total'];

// Data untuk grafik kategori (Top 6)
$kategori_data = mysqli_query($conn, "SELECT * FROM view_statistik_kategori ORDER BY jumlah_buku DESC LIMIT 6");
$kategori_labels = [];
$kategori_values = [];
while ($row = mysqli_fetch_assoc($kategori_data)) {
    $kategori_labels[] = $row['nama_kategori'];
    $kategori_values[] = $row['jumlah_buku'];
}

// Data peminjaman per hari (1 bulan terakhir) untuk line chart
$peminjaman_harian = mysqli_query($conn, "SELECT DATE(tanggal_pinjam) as tanggal, COUNT(*) as total 
                                          FROM peminjaman 
                                          WHERE tanggal_pinjam >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                                          GROUP BY tanggal ORDER BY tanggal");
$hari_labels = [];
$hari_values = [];
while ($row = mysqli_fetch_assoc($peminjaman_harian)) {
    $hari_labels[] = date('d M', strtotime($row['tanggal']));
    $hari_values[] = $row['total'];
}

// Data status peminjaman untuk pie chart
$status_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_dikembalikan IS NULL AND tanggal_kembali >= CURDATE()"))['total'];
$status_dikembalikan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_dikembalikan IS NOT NULL"))['total'];
$status_terlambat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE tanggal_dikembalikan IS NULL AND tanggal_kembali < CURDATE()"))['total'];

// Buku paling banyak dipinjam
$buku_populer = mysqli_query($conn, "SELECT * FROM view_buku_populer LIMIT 10");

// Anggota paling aktif
$anggota_aktif = mysqli_query($conn, "SELECT a.nama_lengkap, COUNT(p.id_peminjaman) as total 
                                      FROM anggota a 
                                      JOIN peminjaman p ON a.id_anggota = p.id_anggota 
                                      GROUP BY a.id_anggota 
                                      ORDER BY total DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Perpustakaan Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #F5F1E8; color: #4A4A4A; }
        .navbar { background: linear-gradient(135deg, #8B7355, #A0826D); padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #FFF8DC; font-size: 24px; font-weight: bold; text-decoration: none; }
        .nav-links a { color: #FFF8DC; text-decoration: none; margin-left: 20px; padding: 8px 15px; border-radius: 5px; transition: all 0.3s; }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.2); }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { color: #6B5744; display: flex; align-items: center; gap: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .stat-info h3 { color: #6B5744; font-size: 32px; margin-bottom: 5px; }
        .stat-info p { color: #999; font-size: 14px; }
        .stat-icon { font-size: 48px; opacity: 0.2; }
        .stat-card.blue { background: linear-gradient(135deg, #E8DCC4, #F5DEB3); }
        .stat-card.blue .stat-icon { color: #8B7355; }
        .stat-card.green { background: linear-gradient(135deg, #DED4BB, #C9B896); }
        .stat-card.green .stat-icon { color: #6B5744; }
        .stat-card.orange { background: linear-gradient(135deg, #F4E4C1, #E8D5B5); }
        .stat-card.orange .stat-icon { color: #A0826D; }
        .stat-card.red { background: linear-gradient(135deg, #F5E5D3, #DCC9B0); }
        .stat-card.red .stat-icon { color: #8B6F47; }
        .stat-card.purple { background: linear-gradient(135deg, #E8E0D5, #D2C4B0); }
        .stat-card.purple .stat-icon { color: #8B7355; }
        .chart-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        .card h2 { color: #6B5744; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #F5DEB3; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #F5DEB3; color: #6B5744; padding: 12px; text-align: left; font-weight: 600; }
        table td { padding: 12px; border-bottom: 1px solid #F5F1E8; }
        table tr:hover { background: #FFF8DC; }
        @media (max-width: 768px) {
            .chart-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php" class="navbar-brand"><i class="fas fa-book-reader"></i> Perpustakaan Digital</a>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-chart-bar"></i> Laporan & Statistik</h1>
        </div>
        
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
                    <h3><?php echo $total_peminjaman; ?></h3>
                    <p>Total Peminjaman</p>
                </div>
                <i class="fas fa-exchange-alt stat-icon"></i>
            </div>
            
            <div class="stat-card red">
                <div class="stat-info">
                    <h3><?php echo $sedang_dipinjam; ?></h3>
                    <p>Sedang Dipinjam</p>
                </div>
                <i class="fas fa-hand-holding-heart stat-icon"></i>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-info">
                    <h3><?php echo format_rupiah($total_denda ?: 0); ?></h3>
                    <p>Total Denda</p>
                </div>
                <i class="fas fa-money-bill-wave stat-icon"></i>
            </div>
        </div>
        
        <div class="chart-grid">
            <div class="card">
                <h2><i class="fas fa-chart-line"></i> Trend Peminjaman (1 Bulan Terakhir)</h2>
                <canvas id="peminjamanChart"></canvas>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-chart-pie"></i> Status Peminjaman</h2>
                <canvas id="statusChart"></canvas>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-chart-bar"></i> Top Kategori Buku</h2>
                <canvas id="kategoriChart"></canvas>
            </div>
        </div>
        
        <div class="chart-grid">
            <div class="card">
                <h2><i class="fas fa-fire"></i> Top Buku Paling Populer</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Judul Buku</th>
                            <th>Dipinjam</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($buku = mysqli_fetch_assoc($buku_populer)): ?>
                            <tr>
                                <td><strong>#<?php echo $rank++; ?></strong></td>
                                <td>
                                    <strong><?php echo $buku['judul']; ?></strong><br>
                                    <small><?php echo $buku['pengarang']; ?></small>
                                </td>
                                <td><?php echo $buku['total_dipinjam']; ?>x</td>
                                <td>
                                    <span style="color: #DAA520;">
                                        <i class="fas fa-star"></i> <?php echo number_format($buku['rata_rata_rating'], 1); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-user-check"></i> Top Anggota Paling Aktif</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nama Anggota</th>
                            <th>Total Peminjaman</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($anggota = mysqli_fetch_assoc($anggota_aktif)): ?>
                            <tr>
                                <td><strong>#<?php echo $rank++; ?></strong></td>
                                <td><?php echo $anggota['nama_lengkap']; ?></td>
                                <td><strong><?php echo $anggota['total']; ?></strong> buku</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Chart 1: Line Chart - Trend Peminjaman (1 Bulan Terakhir)
        const peminjamanCtx = document.getElementById('peminjamanChart').getContext('2d');
        new Chart(peminjamanCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($hari_labels); ?>,
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: <?php echo json_encode($hari_values); ?>,
                    borderColor: '#8B7355',
                    backgroundColor: 'rgba(139, 115, 85, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#8B7355',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(107, 87, 68, 0.9)',
                        padding: 12,
                        titleColor: '#FFF8DC',
                        bodyColor: '#FFF8DC',
                        borderColor: '#D2B48C',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#6B5744'
                        },
                        grid: {
                            color: 'rgba(139, 115, 85, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6B5744'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Chart 2: Pie Chart - Status Peminjaman
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: ['Dipinjam', 'Dikembalikan', 'Terlambat'],
                datasets: [{
                    data: [
                        <?php echo $status_dipinjam; ?>,
                        <?php echo $status_dikembalikan; ?>,
                        <?php echo $status_terlambat; ?>
                    ],
                    backgroundColor: [
                        '#8B7355',
                        '#A0826D',
                        '#C19A6B'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#6B5744',
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(107, 87, 68, 0.9)',
                        padding: 12,
                        titleColor: '#FFF8DC',
                        bodyColor: '#FFF8DC',
                        borderColor: '#D2B48C',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Chart 3: Bar Chart - Top 6 Kategori Buku
        const kategoriCtx = document.getElementById('kategoriChart').getContext('2d');
        new Chart(kategoriCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($kategori_labels); ?>,
                datasets: [{
                    label: 'Jumlah Buku',
                    data: <?php echo json_encode($kategori_values); ?>,
                    backgroundColor: [
                        'rgba(139, 115, 85, 0.8)',
                        'rgba(160, 130, 109, 0.8)',
                        'rgba(193, 154, 107, 0.8)',
                        'rgba(210, 180, 140, 0.8)',
                        'rgba(232, 220, 196, 0.8)',
                        'rgba(245, 222, 179, 0.8)'
                    ],
                    borderColor: [
                        '#8B7355',
                        '#A0826D',
                        '#C19A6B',
                        '#D2B48C',
                        '#E8DCC4',
                        '#F5DEB3'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(107, 87, 68, 0.9)',
                        padding: 12,
                        titleColor: '#FFF8DC',
                        bodyColor: '#FFF8DC',
                        borderColor: '#D2B48C',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Jumlah: ' + context.parsed.y + ' buku';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#6B5744'
                        },
                        grid: {
                            color: 'rgba(139, 115, 85, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6B5744'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>