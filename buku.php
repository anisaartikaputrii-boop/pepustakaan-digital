<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// Proses INSERT
if (isset($_POST['tambah'])) {
    $isbn = clean_input($_POST['isbn']);
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $penerbit = clean_input($_POST['penerbit']);
    $tahun = clean_input($_POST['tahun_terbit']);
    $kategori = clean_input($_POST['id_kategori']);
    $halaman = clean_input($_POST['jumlah_halaman']);
    $stok = clean_input($_POST['stok']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    $cover = '';
    if (isset($_FILES['cover_buku']) && $_FILES['cover_buku']['error'] === 0) {
        $upload = upload_foto($_FILES['cover_buku'], 'uploads/covers/');
        if ($upload['status']) {
            $cover = $upload['filename'];
        } else {
            $message = $upload['message'];
            $message_type = 'error';
        }
    }
    
    if (empty($message)) {
        $query = "INSERT INTO buku (isbn, judul, pengarang, penerbit, tahun_terbit, id_kategori, jumlah_halaman, stok, cover_buku, deskripsi) 
                  VALUES ('$isbn', '$judul', '$pengarang', '$penerbit', '$tahun', '$kategori', '$halaman', '$stok', '$cover', '$deskripsi')";
        
        if (mysqli_query($conn, $query)) {
            log_aktivitas($_SESSION['user_id'], 'Tambah Buku', "Menambahkan buku: $judul");
            $message = 'Buku berhasil ditambahkan!';
            $message_type = 'success';
        } else {
            $message = 'Gagal menambahkan buku!';
            $message_type = 'error';
        }
    }
}

// Proses UPDATE
if (isset($_POST['update'])) {
    $id = clean_input($_POST['id_buku']);
    $isbn = clean_input($_POST['isbn']);
    $judul = clean_input($_POST['judul']);
    $pengarang = clean_input($_POST['pengarang']);
    $penerbit = clean_input($_POST['penerbit']);
    $tahun = clean_input($_POST['tahun_terbit']);
    $kategori = clean_input($_POST['id_kategori']);
    $halaman = clean_input($_POST['jumlah_halaman']);
    $stok = clean_input($_POST['stok']);
    $deskripsi = clean_input($_POST['deskripsi']);
    
    // Cek apakah ada upload foto baru
    $cover_update = "";
    if (isset($_FILES['cover_buku']) && $_FILES['cover_buku']['error'] === 0) {
        // Hapus foto lama
        $query_old = "SELECT cover_buku FROM buku WHERE id_buku = '$id'";
        $old_data = mysqli_fetch_assoc(mysqli_query($conn, $query_old));
        if ($old_data['cover_buku']) {
            delete_foto($old_data['cover_buku'], 'uploads/covers/');
        }
        
        $upload = upload_foto($_FILES['cover_buku'], 'uploads/covers/');
        if ($upload['status']) {
            $cover_update = ", cover_buku = '{$upload['filename']}'";
        }
    }
    
    $query = "UPDATE buku SET 
              isbn = '$isbn',
              judul = '$judul',
              pengarang = '$pengarang',
              penerbit = '$penerbit',
              tahun_terbit = '$tahun',
              id_kategori = '$kategori',
              jumlah_halaman = '$halaman',
              stok = '$stok',
              deskripsi = '$deskripsi'
              $cover_update
              WHERE id_buku = '$id'";
    
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Update Buku', "Mengupdate buku: $judul");
        $message = 'Buku berhasil diupdate!';
        $message_type = 'success';
    } else {
        $message = 'Gagal mengupdate buku!';
        $message_type = 'error';
    }
}

// Proses DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    
    // Hapus foto jika ada
    $query_foto = "SELECT cover_buku FROM buku WHERE id_buku = '$id'";
    $foto_data = mysqli_fetch_assoc(mysqli_query($conn, $query_foto));
    if ($foto_data['cover_buku']) {
        delete_foto($foto_data['cover_buku'], 'uploads/covers/');
    }
    
    $query = "DELETE FROM buku WHERE id_buku = '$id'";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Hapus Buku', "Menghapus buku ID: $id");
        $message = 'Buku berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus buku!';
        $message_type = 'error';
    }
}

// Ambil data buku
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? clean_input($_GET['kategori']) : '';

$query = "SELECT b.*, k.nama_kategori FROM buku b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (b.judul LIKE '%$search%' OR b.pengarang LIKE '%$search%' OR b.isbn LIKE '%$search%')";
}

if (!empty($kategori_filter)) {
    $query .= " AND b.id_kategori = '$kategori_filter'";
}

$query .= " ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);

// Ambil data kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori";
$result_kategori = mysqli_query($conn, $query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Perpustakaan Digital</title>
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
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .book-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3);
        }
        
        .book-cover-container {
            height: 300px;
            background: linear-gradient(135deg, #D2B48C, #C19A6B);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .book-cover-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-cover-container i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .book-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(139, 115, 85, 0.9);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .book-content {
            padding: 20px;
        }
        
        .book-content h3 {
            color: #6B5744;
            margin-bottom: 8px;
            font-size: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .book-author {
            color: #999;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .book-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-top: 10px;
            border-top: 1px solid #F5F1E8;
        }
        
        .book-info-item {
            text-align: center;
        }
        
        .book-info-item strong {
            display: block;
            color: #8B7355;
            font-size: 16px;
        }
        
        .book-info-item small {
            color: #999;
            font-size: 11px;
        }
        
        .book-actions {
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
        
        .detail-modal .detail-grid {
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
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .books-grid {
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
            <h1><i class="fas fa-book"></i> Data Buku</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Tambah Buku
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
                <input type="text" name="search" placeholder="Cari judul, pengarang, atau ISBN..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php 
                    mysqli_data_seek($result_kategori, 0);
                    while ($kat = mysqli_fetch_assoc($result_kategori)): 
                    ?>
                        <option value="<?php echo $kat['id_kategori']; ?>" 
                                <?php echo $kategori_filter == $kat['id_kategori'] ? 'selected' : ''; ?>>
                            <?php echo $kat['nama_kategori']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        
        <div class="books-grid">
            <?php while ($buku = mysqli_fetch_assoc($result)): ?>
                <div class="book-card" onclick="showDetail(<?php echo $buku['id_buku']; ?>)">
                    <div class="book-cover-container">
                        <?php if ($buku['cover_buku']): ?>
                            <img src="uploads/covers/<?php echo $buku['cover_buku']; ?>" 
                                 alt="<?php echo $buku['judul']; ?>">
                        <?php else: ?>
                            <i class="fas fa-book"></i>
                        <?php endif; ?>
                        <span class="book-badge"><?php echo $buku['nama_kategori']; ?></span>
                    </div>
                    <div class="book-content">
                        <h3><?php echo $buku['judul']; ?></h3>
                        <p class="book-author"><?php echo $buku['pengarang']; ?></p>
                        <div class="book-info">
                            <div class="book-info-item">
                                <strong><?php echo $buku['tahun_terbit']; ?></strong>
                                <small>Tahun</small>
                            </div>
                            <div class="book-info-item">
                                <strong><?php echo $buku['jumlah_halaman']; ?></strong>
                                <small>Halaman</small>
                            </div>
                            <div class="book-info-item">
                                <strong><?php echo $buku['stok']; ?></strong>
                                <small>Stok</small>
                            </div>
                        </div>
                        <div class="book-actions">
                            <button class="btn btn-warning btn-sm" 
                                    onclick="event.stopPropagation(); editBook(<?php echo htmlspecialchars(json_encode($buku)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" 
                                    onclick="event.stopPropagation(); deleteBook(<?php echo $buku['id_buku']; ?>, '<?php echo addslashes($buku['judul']); ?>')">
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
                <h2><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" required>
                            <?php 
                            mysqli_data_seek($result_kategori, 0);
                            while ($kat = mysqli_fetch_assoc($result_kategori)): 
                            ?>
                                <option value="<?php echo $kat['id_kategori']; ?>">
                                    <?php echo $kat['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="judul" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" name="pengarang" required>
                    </div>
                    <div class="form-group">
                        <label>Penerbit</label>
                        <input type="text" name="penerbit">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" min="1900" max="2099">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Halaman</label>
                        <input type="number" name="jumlah_halaman">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" required min="0">
                </div>
                
                <div class="form-group">
                    <label>Cover Buku (Max 2MB)</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="cover_buku" accept="image/*">
                        <div class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Pilih file gambar...</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi"></textarea>
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
                <h2><i class="fas fa-edit"></i> Edit Buku</h2>
                <span class="close-modal" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="id_buku" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" id="edit_isbn" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="edit_kategori" required>
                            <?php 
                            mysqli_data_seek($result_kategori, 0);
                            while ($kat = mysqli_fetch_assoc($result_kategori)): 
                            ?>
                                <option value="<?php echo $kat['id_kategori']; ?>">
                                    <?php echo $kat['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="judul" id="edit_judul" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" name="pengarang" id="edit_pengarang" required>
                    </div>
                    <div class="form-group">
                        <label>Penerbit</label>
                        <input type="text" name="penerbit" id="edit_penerbit">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tahun Terbit</label>
                        <input type="number" name="tahun_terbit" id="edit_tahun" min="1900" max="2099">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Halaman</label>
                        <input type="number" name="jumlah_halaman" id="edit_halaman">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" id="edit_stok" required min="0">
                </div>
                
                <div class="form-group">
                    <label>Cover Buku Baru (Max 2MB, kosongkan jika tidak ingin mengubah)</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="cover_buku" accept="image/*">
                        <div class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Pilih file gambar...</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi"></textarea>
                </div>
                
                <button type="submit" name="update" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>
    
    <!-- Modal Detail -->
    <div id="detailModal" class="modal detail-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Detail Buku</h2>
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
        
        function editBook(data) {
            document.getElementById('edit_id').value = data.id_buku;
            document.getElementById('edit_isbn').value = data.isbn;
            document.getElementById('edit_judul').value = data.judul;
            document.getElementById('edit_pengarang').value = data.pengarang;
            document.getElementById('edit_penerbit').value = data.penerbit || '';
            document.getElementById('edit_tahun').value = data.tahun_terbit || '';
            document.getElementById('edit_kategori').value = data.id_kategori || '';
            document.getElementById('edit_halaman').value = data.jumlah_halaman || '';
            document.getElementById('edit_stok').value = data.stok;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
            openModal('editModal');
        }
        
        function deleteBook(id, judul) {
            if (confirm('Yakin ingin menghapus buku "' + judul + '"?')) {
                window.location.href = '?delete=' + id;
            }
        }
        
        function showDetail(id) {
            fetch('get_buku_detail.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    let coverImg = data.cover_buku ? 
                        `<img src="uploads/covers/${data.cover_buku}" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px; margin-bottom: 20px;">` :
                        `<div style="width: 100%; height: 200px; background: linear-gradient(135deg, #D2B48C, #C19A6B); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                            <i class="fas fa-book" style="font-size: 80px; color: rgba(255,255,255,0.5);"></i>
                        </div>`;
                    
                    document.getElementById('detailContent').innerHTML = `
                        ${coverImg}
                        <div class="detail-grid">
                            <div class="detail-item">
                                <strong>Judul</strong>
                                <p>${data.judul}</p>
                            </div>
                            <div class="detail-item">
                                <strong>ISBN</strong>
                                <p>${data.isbn}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Pengarang</strong>
                                <p>${data.pengarang}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Penerbit</strong>
                                <p>${data.penerbit || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Tahun Terbit</strong>
                                <p>${data.tahun_terbit || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Kategori</strong>
                                <p>${data.nama_kategori}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Jumlah Halaman</strong>
                                <p>${data.jumlah_halaman || '-'}</p>
                            </div>
                            <div class="detail-item">
                                <strong>Stok Tersedia</strong>
                                <p>${data.stok}</p>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <strong>Deskripsi</strong>
                                <p>${data.deskripsi || 'Tidak ada deskripsi'}</p>
                            </div>
                        </div>
                    `;
                    openModal('detailModal');
                });
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>