<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// Proses INSERT (Reservasi)
if (isset($_POST['reservasi'])) {
    $id_anggota = clean_input($_POST['id_anggota']);
    $id_buku = clean_input($_POST['id_buku']);
    
    // Cek apakah buku sedang dipinjam
    $cek_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id_buku = '$id_buku'"));
    
    if ($cek_buku['stok'] == 0) {
        // Cek apakah sudah ada reservasi yang sama
        $cek_reservasi = mysqli_query($conn, "SELECT * FROM reservasi 
                                              WHERE id_anggota = '$id_anggota' 
                                              AND id_buku = '$id_buku' 
                                              AND status = 'menunggu'");
        
        if (mysqli_num_rows($cek_reservasi) > 0) {
            $message = 'Anda sudah melakukan reservasi untuk buku ini!';
            $message_type = 'error';
        } else {
            $query = "INSERT INTO reservasi (id_anggota, id_buku) VALUES ('$id_anggota', '$id_buku')";
            
            if (mysqli_query($conn, $query)) {
                log_aktivitas($_SESSION['user_id'], 'Reservasi Buku', "Reservasi buku ID: $id_buku untuk anggota ID: $id_anggota");
                $message = 'Reservasi berhasil! Anda akan dihubungi ketika buku tersedia.';
                $message_type = 'success';
            } else {
                $message = 'Gagal melakukan reservasi!';
                $message_type = 'error';
            }
        }
    } else {
        $message = 'Buku masih tersedia! Silakan pinjam langsung.';
        $message_type = 'error';
    }
}

// Proses UPDATE (Ubah Status)
if (isset($_POST['update_status'])) {
    $id = clean_input($_POST['id_reservasi']);
    $status = clean_input($_POST['status']);
    
    $query = "UPDATE reservasi SET status = '$status' WHERE id_reservasi = '$id'";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Update Status Reservasi', "Mengubah status reservasi ID: $id menjadi $status");
        $message = 'Status reservasi berhasil diupdate!';
        $message_type = 'success';
    } else {
        $message = 'Gagal mengupdate status!';
        $message_type = 'error';
    }
}

// Proses DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    
    if (mysqli_query($conn, "DELETE FROM reservasi WHERE id_reservasi = '$id'")) {
        log_aktivitas($_SESSION['user_id'], 'Hapus Reservasi', "Menghapus reservasi ID: $id");
        $message = 'Reservasi berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus reservasi!';
        $message_type = 'error';
    }
}

// Ambil data reservasi
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

$query = "SELECT r.*, 
          b.judul, b.pengarang, b.cover_buku, b.stok,
          a.nomor_anggota, a.nama_lengkap, a.no_telepon, a.email
          FROM reservasi r
          JOIN buku b ON r.id_buku = b.id_buku
          JOIN anggota a ON r.id_anggota = a.id_anggota
          WHERE 1=1";

if (!empty($status_filter)) {
    $query .= " AND r.status = '$status_filter'";
}

if (!empty($search)) {
    $query .= " AND (b.judul LIKE '%$search%' OR a.nama_lengkap LIKE '%$search%' OR a.nomor_anggota LIKE '%$search%')";
}

$query .= " ORDER BY r.tanggal_reservasi DESC";
$result = mysqli_query($conn, $query);

// Data untuk dropdown
$anggota_query = mysqli_query($conn, "SELECT * FROM anggota WHERE status = 'aktif' ORDER BY nama_lengkap");
$buku_query = mysqli_query($conn, "SELECT b.*, k.nama_kategori FROM buku b 
                                   LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                                   WHERE b.stok = 0 ORDER BY b.judul");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Buku - Perpustakaan Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #F5F1E8; color: #4A4A4A; }
        .navbar { background: linear-gradient(135deg, #8B7355, #A0826D); padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #FFF8DC; font-size: 24px; font-weight: bold; text-decoration: none; }
        .nav-links a { color: #FFF8DC; text-decoration: none; margin-left: 20px; padding: 8px 15px; border-radius: 5px; transition: all 0.3s; }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.2); }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { color: #6B5744; display: flex; align-items: center; gap: 10px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #8B7355, #A0826D); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(139, 115, 85, 0.4); }
        .btn-success { background: #7CB342; color: white; }
        .btn-warning { background: #FFB74D; color: white; }
        .btn-danger { background: #E57373; color: white; }
        .btn-info { background: #64B5F6; color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #C8E6C9; color: #2E7D32; border-left: 4px solid #4CAF50; }
        .alert-error { background: #FFCDD2; color: #C62828; border-left: 4px solid #F44336; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-bar input, .search-bar select { padding: 12px 15px; border: 2px solid #D2B48C; border-radius: 8px; font-size: 14px; flex: 1; min-width: 200px; }
        .search-bar input:focus, .search-bar select:focus { outline: none; border-color: #8B7355; }
        .reservasi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
        .reservasi-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.1); transition: all 0.3s; }
        .reservasi-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3); }
        .reservasi-header { background: linear-gradient(135deg, #F5DEB3, #D2B48C); padding: 20px; position: relative; }
        .status-badge { position: absolute; top: 15px; right: 15px; padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-badge.menunggu { background: #FFE5B4; color: #8B4513; }
        .status-badge.diproses { background: #C8E6C9; color: #2E7D32; }
        .status-badge.dibatalkan { background: #FFD4D4; color: #8B0000; }
        .book-mini { display: flex; gap: 15px; align-items: center; }
        .book-cover-mini { width: 60px; height: 80px; background: #D2B48C; border-radius: 5px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .book-cover-mini img { width: 100%; height: 100%; object-fit: cover; }
        .book-cover-mini i { font-size: 30px; color: rgba(255,255,255,0.5); }
        .book-info-mini h4 { color: #6B5744; margin-bottom: 5px; font-size: 16px; }
        .book-info-mini p { color: #999; font-size: 13px; }
        .reservasi-body { padding: 20px; }
        .member-info { display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px; }
        .info-row { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; }
        .info-row i { width: 20px; color: #8B7355; }
        .date-info { background: #FFF8DC; padding: 12px; border-radius: 8px; text-align: center; margin-bottom: 15px; }
        .date-info strong { display: block; color: #6B5744; font-size: 16px; }
        .date-info small { color: #999; }
        .reservasi-actions { display: flex; gap: 8px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 15px; padding: 30px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #F5DEB3; }
        .modal-header h2 { color: #6B5744; }
        .close-modal { font-size: 28px; cursor: pointer; color: #999; }
        .close-modal:hover { color: #6B5744; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #6B5744; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 2px solid #D2B48C; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #8B7355; }
        .info-box { background: #F5DEB3; padding: 15px; border-radius: 8px; border-left: 4px solid #D2B48C; margin-bottom: 20px; }
        .info-box i { color: #8B7355; margin-right: 10px; }
        @media (max-width: 768px) {
            .reservasi-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php" class="navbar-brand">
            <i class="fas fa-book-reader"></i> Perpustakaan Digital
        </a>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-bookmark"></i> Reservasi Buku</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Reservasi Buku
            </button>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <form method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Cari buku atau anggota..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="menunggu" <?php echo $status_filter === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                    <option value="diproses" <?php echo $status_filter === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                    <option value="dibatalkan" <?php echo $status_filter === 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="reservasi-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($reservasi = mysqli_fetch_assoc($result)): ?>
                    <div class="reservasi-card">
                        <div class="reservasi-header">
                            <span class="status-badge <?php echo $reservasi['status']; ?>">
                                <?php echo ucfirst($reservasi['status']); ?>
                            </span>
                            <div class="book-mini">
                                <div class="book-cover-mini">
                                    <?php if ($reservasi['cover_buku']): ?>
                                        <img src="uploads/covers/<?php echo $reservasi['cover_buku']; ?>" alt="">
                                    <?php else: ?>
                                        <i class="fas fa-book"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="book-info-mini">
                                    <h4><?php echo $reservasi['judul']; ?></h4>
                                    <p><?php echo $reservasi['pengarang']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="reservasi-body">
                            <div class="date-info">
                                <strong><?php echo date('d F Y', strtotime($reservasi['tanggal_reservasi'])); ?></strong>
                                <small>Tanggal Reservasi</small>
                            </div>
                            
                            <div class="member-info">
                                <div class="info-row">
                                    <i class="fas fa-user"></i>
                                    <span><?php echo $reservasi['nama_lengkap']; ?></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-id-card"></i>
                                    <span><?php echo $reservasi['nomor_anggota']; ?></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo $reservasi['no_telepon'] ?: '-'; ?></span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo $reservasi['email'] ?: '-'; ?></span>
                                </div>
                                <?php if ($reservasi['stok'] > 0): ?>
                                    <div class="info-row" style="color: #2E7D32;">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Buku sudah tersedia! (Stok: <?php echo $reservasi['stok']; ?>)</strong>
                                    </div>
                                <?php else: ?>
                                    <div class="info-row" style="color: #C62828;">
                                        <i class="fas fa-times-circle"></i>
                                        <span>Buku masih dipinjam</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="reservasi-actions">
                                <?php if ($reservasi['status'] === 'menunggu'): ?>
                                    <button class="btn btn-success btn-sm" style="flex: 1;" 
                                            onclick="updateStatus(<?php echo $reservasi['id_reservasi']; ?>, 'diproses')">
                                        <i class="fas fa-check"></i> Proses
                                    </button>
                                    <button class="btn btn-warning btn-sm" style="flex: 1;"
                                            onclick="updateStatus(<?php echo $reservasi['id_reservasi']; ?>, 'dibatalkan')">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-danger btn-sm" 
                                        onclick="deleteReservasi(<?php echo $reservasi['id_reservasi']; ?>, '<?php echo addslashes($reservasi['judul']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #999;">
                    <i class="fas fa-bookmark" style="font-size: 64px; margin-bottom: 15px;"></i>
                    <p>Belum ada reservasi</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal Tambah Reservasi -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-bookmark"></i> Reservasi Buku</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST">
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Informasi:</strong> Reservasi hanya untuk buku yang sedang dipinjam (stok = 0)
                </div>
                
                <div class="form-group">
                    <label>Anggota</label>
                    <select name="id_anggota" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php while ($anggota = mysqli_fetch_assoc($anggota_query)): ?>
                            <option value="<?php echo $anggota['id_anggota']; ?>">
                                <?php echo $anggota['nomor_anggota']; ?> - <?php echo $anggota['nama_lengkap']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Buku yang Sedang Dipinjam</label>
                    <select name="id_buku" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php 
                        if (mysqli_num_rows($buku_query) > 0) {
                            while ($buku = mysqli_fetch_assoc($buku_query)): 
                        ?>
                            <option value="<?php echo $buku['id_buku']; ?>">
                                <?php echo $buku['judul']; ?> - <?php echo $buku['pengarang']; ?>
                            </option>
                        <?php 
                            endwhile;
                        } else {
                            echo '<option value="">Semua buku tersedia</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="reservasi" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-bookmark"></i> Buat Reservasi
                </button>
            </form>
        </div>
    </div>
    
    <!-- Form Update Status (Hidden) -->
    <form id="updateStatusForm" method="POST" style="display: none;">
        <input type="hidden" name="id_reservasi" id="update_id">
        <input type="hidden" name="status" id="update_status">
        <input type="hidden" name="update_status" value="1">
    </form>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function updateStatus(id, status) {
            const statusText = status === 'diproses' ? 'diproses' : 'dibatalkan';
            if (confirm(`Yakin ingin mengubah status menjadi "${statusText}"?`)) {
                document.getElementById('update_id').value = id;
                document.getElementById('update_status').value = status;
                document.getElementById('updateStatusForm').submit();
            }
        }
        
        function deleteReservasi(id, judul) {
            if (confirm('Yakin ingin menghapus reservasi untuk buku "' + judul + '"?')) {
                window.location.href = '?delete=' + id;
            }
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>