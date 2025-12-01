<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// Proses INSERT
if (isset($_POST['tambah'])) {
    $nomor = clean_input($_POST['nomor_anggota']);
    $nama = clean_input($_POST['nama_lengkap']);
    $jk = clean_input($_POST['jenis_kelamin']);
    $alamat = clean_input($_POST['alamat']);
    $telp = clean_input($_POST['no_telepon']);
    $email = clean_input($_POST['email']);
    $tgl_daftar = clean_input($_POST['tanggal_daftar']);
    
    $foto = '';
    if (isset($_FILES['foto_anggota']) && $_FILES['foto_anggota']['error'] === 0) {
        $upload = upload_foto($_FILES['foto_anggota'], 'uploads/anggota/');
        if ($upload['status']) {
            $foto = $upload['filename'];
        } else {
            $message = $upload['message'];
            $message_type = 'error';
        }
    }
    
    if (empty($message)) {
        $query = "INSERT INTO anggota (nomor_anggota, nama_lengkap, jenis_kelamin, alamat, no_telepon, email, tanggal_daftar, foto_anggota) 
                  VALUES ('$nomor', '$nama', '$jk', '$alamat', '$telp', '$email', '$tgl_daftar', '$foto')";
        
        if (mysqli_query($conn, $query)) {
            log_aktivitas($_SESSION['user_id'], 'Tambah Anggota', "Menambahkan anggota: $nama");
            $message = 'Anggota berhasil ditambahkan!';
            $message_type = 'success';
        } else {
            $message = 'Gagal menambahkan anggota!';
            $message_type = 'error';
        }
    }
}

// Proses UPDATE
if (isset($_POST['update'])) {
    $id = clean_input($_POST['id_anggota']);
    $nomor = clean_input($_POST['nomor_anggota']);
    $nama = clean_input($_POST['nama_lengkap']);
    $jk = clean_input($_POST['jenis_kelamin']);
    $alamat = clean_input($_POST['alamat']);
    $telp = clean_input($_POST['no_telepon']);
    $email = clean_input($_POST['email']);
    $status = clean_input($_POST['status']);
    
    $foto_update = "";
    if (isset($_FILES['foto_anggota']) && $_FILES['foto_anggota']['error'] === 0) {
        $query_old = "SELECT foto_anggota FROM anggota WHERE id_anggota = '$id'";
        $old_data = mysqli_fetch_assoc(mysqli_query($conn, $query_old));
        if ($old_data['foto_anggota']) {
            delete_foto($old_data['foto_anggota'], 'uploads/anggota/');
        }
        
        $upload = upload_foto($_FILES['foto_anggota'], 'uploads/anggota/');
        if ($upload['status']) {
            $foto_update = ", foto_anggota = '{$upload['filename']}'";
        }
    }
    
    $query = "UPDATE anggota SET 
              nomor_anggota = '$nomor',
              nama_lengkap = '$nama',
              jenis_kelamin = '$jk',
              alamat = '$alamat',
              no_telepon = '$telp',
              email = '$email',
              status = '$status'
              $foto_update
              WHERE id_anggota = '$id'";
    
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Update Anggota', "Mengupdate anggota: $nama");
        $message = 'Anggota berhasil diupdate!';
        $message_type = 'success';
    } else {
        $message = 'Gagal mengupdate anggota!';
        $message_type = 'error';
    }
}

// Proses DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    
    $query_foto = "SELECT foto_anggota FROM anggota WHERE id_anggota = '$id'";
    $foto_data = mysqli_fetch_assoc(mysqli_query($conn, $query_foto));
    if ($foto_data['foto_anggota']) {
        delete_foto($foto_data['foto_anggota'], 'uploads/anggota/');
    }
    
    $query = "DELETE FROM anggota WHERE id_anggota = '$id'";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Hapus Anggota', "Menghapus anggota ID: $id");
        $message = 'Anggota berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus anggota!';
        $message_type = 'error';
    }
}

// Ambil data anggota
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';

$query = "SELECT * FROM anggota WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (nama_lengkap LIKE '%$search%' OR nomor_anggota LIKE '%$search%' OR email LIKE '%$search%')";
}

if (!empty($status_filter)) {
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY tanggal_daftar DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Perpustakaan Digital</title>
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
        
        .member-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        
        .member-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3);
        }
        
        .member-header {
            background: linear-gradient(135deg, #F5DEB3, #D2B48C);
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .member-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .member-photo i {
            font-size: 50px;
            color: #D2B48C;
        }
        
        .member-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .member-status.aktif {
            background: #C8E6C9;
            color: #2E7D32;
        }
        
        .member-status.nonaktif {
            background: #FFCDD2;
            color: #C62828;
        }
        
        .member-body {
            padding: 20px;
        }
        
        .member-name {
            color: #6B5744;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }
        
        .member-id {
            color: #999;
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .member-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .info-item i {
            width: 20px;
            color: #8B7355;
        }
        
        .member-actions {
            display: flex;
            gap: 8px;
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
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B7355;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            background: #F5DEB3;
            border: 2px dashed #D2B48C;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .file-input-label:hover {
            background: #F5F1E8;
        }
        
        .detail-grid {
            display: grid;
            gap: 15px;
        }
        
        .detail-item {
            padding: 15px;
            background: #FFF8DC;
            border-radius: 8px;
        }
        
        .detail-item strong {
            display: block;
            color: #6B5744;
            margin-bottom: 5px;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .detail-item p {
            color: #4A4A4A;
            font-size: 15px;
        }
        
        .detail-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #D2B48C, #C19A6B);
        }
        
        .detail-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .detail-photo i {
            font-size: 70px;
            color: rgba(255,255,255,0.5);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .member-grid {
                grid-template-columns: 1fr;
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
            <h1><i class="fas fa-users"></i> Data Anggota</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-user-plus"></i> Tambah Anggota
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
                <input type="text" name="search" placeholder="Cari nama, nomor anggota, atau email..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif" <?php echo $status_filter === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                    <option value="nonaktif" <?php echo $status_filter === 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="member-grid">
            <?php while ($anggota = mysqli_fetch_assoc($result)): ?>
                <div class="member-card" onclick="showDetail(<?php echo $anggota['id_anggota']; ?>)">
                    <div class="member-header">
                        <span class="member-status <?php echo $anggota['status']; ?>">
                            <?php echo ucfirst($anggota['status']); ?>
                        </span>
                        <div class="member-photo">
                            <?php if ($anggota['foto_anggota']): ?>
                                <img src="uploads/anggota/<?php echo $anggota['foto_anggota']; ?>" 
                                     alt="<?php echo $anggota['nama_lengkap']; ?>">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="member-body">
                        <h3 class="member-name"><?php echo $anggota['nama_lengkap']; ?></h3>
                        <p class="member-id"><?php echo $anggota['nomor_anggota']; ?></p>
                        
                        <div class="member-info">
                            <div class="info-item">
                                <i class="fas fa-<?php echo $anggota['jenis_kelamin'] === 'L' ? 'mars' : 'venus'; ?>"></i>
                                <span><?php echo $anggota['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo $anggota['no_telepon'] ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo $anggota['email'] ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <span>Terdaftar: <?php echo date('d/m/Y', strtotime($anggota['tanggal_daftar'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="member-actions">
                            <button class="btn btn-warning btn-sm" style="flex: 1;"
                                    onclick="event.stopPropagation(); editMember(<?php echo htmlspecialchars(json_encode($anggota)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" style="flex: 1;"
                                    onclick="event.stopPropagation(); deleteMember(<?php echo $anggota['id_anggota']; ?>, '<?php echo addslashes($anggota['nama_lengkap']); ?>')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <!-- Modal Tambah -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Tambah Anggota Baru</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor Anggota</label>
                        <input type="text" name="nomor_anggota" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telepon">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label>Foto Anggota (Max 2MB)</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="foto_anggota" accept="image/*">
                        <div class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Pilih file gambar...</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="tambah" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
    
    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Anggota</h2>
                <span class="close-modal" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_anggota" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor Anggota</label>
                        <input type="text" name="nomor_anggota" id="edit_nomor" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_jk" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="edit_nama" required>
                </div>
                
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" id="edit_alamat"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telepon" id="edit_telp">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="edit_email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Non-Aktif</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Foto Anggota Baru (Max 2MB, kosongkan jika tidak ingin mengubah)</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="foto_anggota" accept="image/*">
                        <div class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Pilih file gambar...</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="update" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>
    
    <!-- Modal Detail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Detail Anggota</h2>
                <span class="close-modal" onclick="closeModal('detailModal')">&times;</span>
            </div>
            <div id="detailContent"></div>
        </div>
    </div>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function editMember(data) {
            document.getElementById('edit_id').value = data.id_anggota;
            document.getElementById('edit_nomor').value = data.nomor_anggota;
            document.getElementById('edit_nama').value = data.nama_lengkap;
            document.getElementById('edit_jk').value = data.jenis_kelamin;
            document.getElementById('edit_alamat').value = data.alamat || '';
            document.getElementById('edit_telp').value = data.no_telepon || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_status').value = data.status;
            openModal('editModal');
        }
        
        function deleteMember(id, nama) {
            if (confirm('Yakin ingin menghapus anggota "' + nama + '"?\nSemua data peminjaman terkait juga akan terhapus!')) {
                window.location.href = '?delete=' + id;
            }
        }
        
        function showDetail(id) {
            fetch('get_anggota_detail.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    let photoImg = data.foto_anggota ? 
                        `<img src="uploads/anggota/${data.foto_anggota}">` :
                        `<i class="fas fa-user"></i>`;
                    
                    document.getElementById('detailContent').innerHTML = `
                        <div class="detail-photo">${photoImg}</div>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <strong>Nomor Anggota</strong>
                                <p>${data.nomor_anggota}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Nama Lengkap</strong>
                                <p>${data.nama_lengkap}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Jenis Kelamin</strong>
                                <p>${data.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Status</strong>
                                <p>${data.status === 'aktif' ? '✓ Aktif' : '✗ Non-Aktif'}</p>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <strong>Alamat</strong>
                                <p>${data.alamat || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>No. Telepon</strong>
                                <p>${data.no_telepon || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Email</strong>
                                <p>${data.email || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Tanggal Daftar</strong>
                                <p>${formatDate(data.tanggal_daftar)}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Total Peminjaman</strong>
                                <p>${data.total_peminjaman || 0} buku</p>
                            </div>
                            <div class="detail-item">
                                <strong>Sedang Dipinjam</strong>
                                <p>${data.sedang_dipinjam || 0} buku</p>
                            </div>
                        </div>
                    `;
                    openModal('detailModal');
                });
        }
        
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>