<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// Proses INSERT (Pinjam Buku)
if (isset($_POST['pinjam'])) {
    $id_anggota = clean_input($_POST['id_anggota']);
    $id_buku = clean_input($_POST['id_buku']);
    $tgl_pinjam = clean_input($_POST['tanggal_pinjam']);
    $tgl_kembali = clean_input($_POST['tanggal_kembali']);
    
    // Cek stok buku
    $cek_stok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM buku WHERE id_buku = '$id_buku'"));
    
    if ($cek_stok['stok'] > 0) {
        // Insert peminjaman
        $query = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, id_petugas) 
                  VALUES ('$id_anggota', '$id_buku', '$tgl_pinjam', '$tgl_kembali', '{$_SESSION['user_id']}')";
        
        if (mysqli_query($conn, $query)) {
            // Kurangi stok buku
            mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            
            log_aktivitas($_SESSION['user_id'], 'Peminjaman Buku', "Memproses peminjaman buku ID: $id_buku");
            $message = 'Peminjaman berhasil diproses!';
            $message_type = 'success';
        } else {
            $message = 'Gagal memproses peminjaman!';
            $message_type = 'error';
        }
    } else {
        $message = 'Stok buku tidak tersedia!';
        $message_type = 'error';
    }
}

// Proses UPDATE (Kembalikan Buku)
if (isset($_POST['kembalikan'])) {
    $id_peminjaman = clean_input($_POST['id_peminjaman']);
    $tgl_dikembalikan = clean_input($_POST['tanggal_dikembalikan']);
    
    // Ambil data peminjaman
    $data_pinjam = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT *, DATEDIFF('$tgl_dikembalikan', tanggal_kembali) as selisih_hari 
         FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'"));
    
    // Hitung denda (Rp 5000 per hari)
    $denda = 0;
    if ($data_pinjam['selisih_hari'] > 0) {
        $denda = $data_pinjam['selisih_hari'] * 5000;
    }
    
    // Update peminjaman
    $query = "UPDATE peminjaman SET 
              tanggal_dikembalikan = '$tgl_dikembalikan',
              denda = '$denda',
              status = 'dikembalikan'
              WHERE id_peminjaman = '$id_peminjaman'";
    
    if (mysqli_query($conn, $query)) {
        // Tambah stok buku
        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '{$data_pinjam['id_buku']}'");
        
        log_aktivitas($_SESSION['user_id'], 'Pengembalian Buku', "Memproses pengembalian ID: $id_peminjaman pada tanggal $tgl_dikembalikan");
        $message = 'Buku berhasil dikembalikan!' . ($denda > 0 ? ' Denda: ' . format_rupiah($denda) : '');
        $message_type = 'success';
    } else {
        $message = 'Gagal memproses pengembalian!';
        $message_type = 'error';
    }
}

// Proses DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    
    // Ambil data peminjaman
    $query_check = "SELECT * FROM peminjaman WHERE id_peminjaman = '$id'";
    $result_check = mysqli_query($conn, $query_check);
    
    if ($result_check && mysqli_num_rows($result_check) > 0) {
        $data = mysqli_fetch_assoc($result_check);
        
        if ($data['status'] === 'dipinjam') {
            // Kembalikan stok jika belum dikembalikan
            mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '{$data['id_buku']}'");
        }
        
        $query = "DELETE FROM peminjaman WHERE id_peminjaman = '$id'";
        if (mysqli_query($conn, $query)) {
            log_aktivitas($_SESSION['user_id'], 'Hapus Peminjaman', "Menghapus peminjaman ID: $id");
            $message = 'Data peminjaman berhasil dihapus!';
            $message_type = 'success';
            
            // Redirect untuk menghilangkan parameter delete dari URL
            echo "<script>window.location.href='peminjaman.php';</script>";
            exit();
        } else {
            $message = 'Gagal menghapus data peminjaman!';
            $message_type = 'error';
        }
    }
    // Hapus else block agar tidak menampilkan error jika data tidak ditemukan
    // Karena bisa jadi sudah dihapus sebelumnya
}

// Ambil data peminjaman
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Update status terlambat otomatis sebelum menampilkan data
mysqli_query($conn, "UPDATE peminjaman 
                     SET status = 'terlambat' 
                     WHERE status = 'dipinjam' 
                     AND tanggal_kembali < CURDATE() 
                     AND tanggal_dikembalikan IS NULL");

$query = "SELECT * FROM view_detail_peminjaman WHERE 1=1";

if (!empty($status_filter)) {
    if ($status_filter === 'terlambat') {
        $query .= " AND status IN ('dipinjam', 'terlambat') AND hari_terlambat > 0 AND tanggal_dikembalikan IS NULL";
    } elseif ($status_filter === 'dipinjam') {
        $query .= " AND status = 'dipinjam' AND tanggal_dikembalikan IS NULL";
    } else {
        $query .= " AND status = '$status_filter'";
    }
}

if (!empty($search)) {
    $query .= " AND (nama_anggota LIKE '%$search%' OR judul_buku LIKE '%$search%' OR nomor_anggota LIKE '%$search%')";
}

$query .= " ORDER BY tanggal_pinjam DESC";
$result = mysqli_query($conn, $query);

// Ambil data untuk dropdown
$anggota_query = mysqli_query($conn, "SELECT * FROM anggota WHERE status = 'aktif' ORDER BY nama_lengkap");
$buku_query = mysqli_query($conn, "SELECT b.*, k.nama_kategori FROM buku b 
                                   LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                                   WHERE b.stok > 0 ORDER BY b.judul");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Perpustakaan Digital</title>
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
        }
        
        .navbar-brand {
            color: #FFF8DC;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .nav-links a {
            color: #FFF8DC;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            color: #6B5744;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #8B7355, #A0826D);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 115, 85, 0.4);
        }
        
        .btn-success {
            background: #7CB342;
            color: white;
        }
        
        .btn-warning {
            background: #FFB74D;
            color: white;
        }
        
        .btn-danger {
            background: #E57373;
            color: white;
        }
        
        .btn-info {
            background: #64B5F6;
            color: white;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #C8E6C9;
            color: #2E7D32;
            border-left: 4px solid #4CAF50;
        }
        
        .alert-error {
            background: #FFCDD2;
            color: #C62828;
            border-left: 4px solid #F44336;
        }
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .search-bar input,
        .search-bar select {
            padding: 12px 15px;
            border: 2px solid #D2B48C;
            border-radius: 8px;
            font-size: 14px;
            flex: 1;
            min-width: 200px;
        }
        
        .search-bar input:focus,
        .search-bar select:focus {
            outline: none;
            border-color: #8B7355;
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
            position: sticky;
            top: 0;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #F5F1E8;
        }
        
        table tr:hover {
            background: #FFF8DC;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge.dipinjam {
            background: #FFE5B4;
            color: #8B4513;
        }
        
        .badge.dikembalikan {
            background: #C8E6C9;
            color: #2E7D32;
        }
        
        .badge.terlambat {
            background: #FFD4D4;
            color: #8B0000;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #F5DEB3;
        }
        
        .modal-header h2 {
            color: #6B5744;
        }
        
        .close-modal {
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }
        
        .close-modal:hover {
            color: #6B5744;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #6B5744;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #D2B48C;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #8B7355;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .denda-info {
            background: #FFE5B4;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #FFB74D;
            margin-top: 10px;
        }
        
        .denda-info strong {
            color: #8B4513;
            font-size: 18px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 12px;
            }
            
            .btn {
                padding: 8px 16px;
                font-size: 12px;
            }
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
            <h1><i class="fas fa-exchange-alt"></i> Transaksi Peminjaman</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Pinjam Buku
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
                <input type="text" name="search" placeholder="Cari anggota, buku, atau nomor anggota..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" <?php echo $status_filter === 'dipinjam' ? 'selected' : ''; ?>>Dipinjam</option>
                    <option value="terlambat" <?php echo $status_filter === 'terlambat' ? 'selected' : ''; ?>>Terlambat</option>
                    <option value="dikembalikan" <?php echo $status_filter === 'dikembalikan' ? 'selected' : ''; ?>>Dikembalikan</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Tgl Dikembalikan</th>
                        <th>Denda</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($pinjam = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $pinjam['id_peminjaman']; ?></td>
                                <td>
                                    <strong><?php echo $pinjam['nama_anggota']; ?></strong><br>
                                    <small><?php echo $pinjam['nomor_anggota']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo $pinjam['judul_buku']; ?></strong><br>
                                    <small><?php echo $pinjam['pengarang']; ?></small>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($pinjam['tanggal_kembali'])); ?></td>
                                <td><?php echo $pinjam['tanggal_dikembalikan'] ? date('d/m/Y', strtotime($pinjam['tanggal_dikembalikan'])) : '-'; ?></td>
                                <td><?php echo $pinjam['denda'] > 0 ? format_rupiah($pinjam['denda']) : '-'; ?></td>
                                <td>
                                    <?php
                                    // Cek status berdasarkan tanggal dikembalikan
                                    if ($pinjam['tanggal_dikembalikan']) {
                                        // Sudah dikembalikan
                                        echo '<span class="badge dikembalikan">Dikembalikan</span>';
                                    } elseif ($pinjam['hari_terlambat'] > 0) {
                                        // Belum dikembalikan dan terlambat
                                        echo '<span class="badge terlambat">Terlambat ' . $pinjam['hari_terlambat'] . ' hari</span>';
                                    } else {
                                        // Belum dikembalikan dan belum terlambat
                                        echo '<span class="badge dipinjam">Dipinjam</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (!$pinjam['tanggal_dikembalikan']): ?>
                                        <button class="btn btn-success btn-sm" 
                                                onclick="returnBook(<?php echo $pinjam['id_peminjaman']; ?>, '<?php echo addslashes($pinjam['judul_buku']); ?>', <?php echo $pinjam['hari_terlambat']; ?>)">
                                            <i class="fas fa-undo"></i> Kembalikan
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="deletePinjam(<?php echo $pinjam['id_peminjaman']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px;"></i><br>
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Pinjam -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book"></i> Pinjam Buku</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST">
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
                    <label>Buku</label>
                    <select name="id_buku" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php while ($buku = mysqli_fetch_assoc($buku_query)): ?>
                            <option value="<?php echo $buku['id_buku']; ?>">
                                <?php echo $buku['judul']; ?> - <?php echo $buku['pengarang']; ?> (Stok: <?php echo $buku['stok']; ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Kembali (7 hari)</label>
                        <input type="date" name="tanggal_kembali" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                    </div>
                </div>
                
                <div class="denda-info">
                    <i class="fas fa-info-circle"></i> <strong>Informasi:</strong><br>
                    Denda keterlambatan: Rp 5.000 per hari
                </div>
                
                <button type="submit" name="pinjam" class="btn btn-success" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-check"></i> Proses Peminjaman
                </button>
            </form>
        </div>
    </div>
    
    <!-- Modal Kembalikan -->
    <div id="returnModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-undo"></i> Kembalikan Buku</h2>
                <span class="close-modal" onclick="closeModal('returnModal')">&times;</span>
            </div>
            <form method="POST" id="returnForm">
                <input type="hidden" name="id_peminjaman" id="return_id">
                <input type="hidden" id="return_tanggal_kembali">
                
                <div class="form-group">
                    <label>Buku yang dikembalikan:</label>
                    <p id="return_book_title" style="font-size: 16px; font-weight: bold; color: #6B5744;"></p>
                </div>
                
                <div class="form-group">
                    <label>Tanggal Pengembalian:</label>
                    <input type="date" name="tanggal_dikembalikan" id="return_date" 
                           value="<?php echo date('Y-m-d'); ?>" 
                           max="<?php echo date('Y-m-d'); ?>"
                           required
                           onchange="calculateDenda()"
                           style="width: 100%; padding: 12px 15px; border: 2px solid #D2B48C; border-radius: 8px; font-size: 14px;">
                </div>
                
                <div id="dendaDisplay"></div>
                
                <button type="submit" name="kembalikan" class="btn btn-success" style="width: 100%; margin-top: 20px;">
                    <i class="fas fa-check"></i> Konfirmasi Pengembalian
                </button>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function returnBook(id, title, hariTerlambat) {
            document.getElementById('return_id').value = id;
            document.getElementById('return_book_title').textContent = title;
            document.getElementById('return_tanggal_kembali').value = hariTerlambat;
            
            // Set tanggal pengembalian ke hari ini
            document.getElementById('return_date').value = '<?php echo date('Y-m-d'); ?>';
            
            // Hitung denda awal
            calculateDenda();
            
            openModal('returnModal');
        }
        
        function calculateDenda() {
            const returnDate = new Date(document.getElementById('return_date').value);
            const today = new Date('<?php echo date('Y-m-d'); ?>');
            const tanggalKembali = document.getElementById('return_tanggal_kembali').value;
            
            // Hitung selisih hari dari tanggal kembali yang dijadwalkan
            const timeDiff = returnDate - today;
            const daysDiff = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
            
            // Hitung hari terlambat berdasarkan tanggal yang dipilih
            // Misalnya tanggal kembali seharusnya 10 Nov, user pilih 15 Nov = terlambat 5 hari
            const hariTerlambatInput = parseInt(tanggalKembali) || 0;
            
            // Jika tanggal dikembalikan lebih dari hari ini + hari terlambat sebelumnya
            const totalHariTerlambat = hariTerlambatInput + daysDiff;
            
            let dendaHTML = '';
            if (totalHariTerlambat > 0) {
                const denda = totalHariTerlambat * 5000;
                dendaHTML = `
                    <div class="denda-info">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong><br>
                        Terlambat: ${totalHariTerlambat} hari<br>
                        Total Denda: <strong style="font-size: 20px;">Rp ${denda.toLocaleString('id-ID')}</strong>
                    </div>
                `;
            } else {
                dendaHTML = `
                    <div style="background: #C8E6C9; padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                        <i class="fas fa-check-circle"></i> <strong>Tepat Waktu!</strong><br>
                        Tidak ada denda
                    </div>
                `;
            }
            
            document.getElementById('dendaDisplay').innerHTML = dendaHTML;
        }
        
        function deletePinjam(id) {
            if (confirm('Yakin ingin menghapus data peminjaman ini?')) {
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