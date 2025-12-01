<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// INSERT
if (isset($_POST['tambah'])) {
    $nama = clean_input($_POST['nama_kategori']);
    $desk = clean_input($_POST['deskripsi']);
    
    $query = "INSERT INTO kategori (nama_kategori, deskripsi) VALUES ('$nama', '$desk')";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Tambah Kategori', "Menambahkan kategori: $nama");
        $message = 'Kategori berhasil ditambahkan!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menambahkan kategori!';
        $message_type = 'error';
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = clean_input($_POST['id_kategori']);
    $nama = clean_input($_POST['nama_kategori']);
    $desk = clean_input($_POST['deskripsi']);
    
    $query = "UPDATE kategori SET nama_kategori = '$nama', deskripsi = '$desk' WHERE id_kategori = '$id'";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Update Kategori', "Mengupdate kategori: $nama");
        $message = 'Kategori berhasil diupdate!';
        $message_type = 'success';
    } else {
        $message = 'Gagal mengupdate kategori!';
        $message_type = 'error';
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Hapus Kategori', "Menghapus kategori ID: $id");
        $message = 'Kategori berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus kategori!';
        $message_type = 'error';
    }
}

$result = mysqli_query($conn, "SELECT * FROM view_statistik_kategori ORDER BY jumlah_buku DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori - Perpustakaan Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #F5F1E8; color: #4A4A4A; }
        .navbar { background: linear-gradient(135deg, #8B7355, #A0826D); padding: 15px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: #FFF8DC; font-size: 24px; font-weight: bold; text-decoration: none; }
        .nav-links a { color: #FFF8DC; text-decoration: none; margin-left: 20px; padding: 8px 15px; border-radius: 5px; transition: all 0.3s; }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.2); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { color: #6B5744; display: flex; align-items: center; gap: 10px; }
        .btn { padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, #8B7355, #A0826D); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(139, 115, 85, 0.4); }
        .btn-success { background: #7CB342; color: white; }
        .btn-warning { background: #FFB74D; color: white; }
        .btn-danger { background: #E57373; color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #C8E6C9; color: #2E7D32; border-left: 4px solid #4CAF50; }
        .alert-error { background: #FFCDD2; color: #C62828; border-left: 4px solid #F44336; }
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .category-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); transition: all 0.3s; border-left: 4px solid #D2B48C; }
        .category-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3); }
        .category-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
        .category-name { color: #6B5744; font-size: 20px; font-weight: bold; }
        .category-stats { display: flex; gap: 20px; margin-top: 15px; }
        .stat-item { text-align: center; }
        .stat-item strong { display: block; font-size: 24px; color: #8B7355; }
        .stat-item small { color: #999; font-size: 12px; }
        .category-actions { display: flex; gap: 8px; margin-top: 15px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #F5DEB3; }
        .modal-header h2 { color: #6B5744; }
        .close-modal { font-size: 28px; cursor: pointer; color: #999; }
        .close-modal:hover { color: #6B5744; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #6B5744; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 2px solid #D2B48C; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #8B7355; }
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
            <h1><i class="fas fa-tags"></i> Kategori Buku</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="category-grid">
            <?php while ($kat = mysqli_fetch_assoc($result)): ?>
                <div class="category-card">
                    <div class="category-header">
                        <h3 class="category-name"><?php echo $kat['nama_kategori']; ?></h3>
                    </div>
                    <div class="category-stats">
                        <div class="stat-item">
                            <strong><?php echo $kat['jumlah_buku']; ?></strong>
                            <small>Judul Buku</small>
                        </div>
                        <div class="stat-item">
                            <strong><?php echo $kat['total_stok']; ?></strong>
                            <small>Total Stok</small>
                        </div>
                        <div class="stat-item">
                            <strong><?php echo number_format($kat['rata_rata_rating'], 1); ?></strong>
                            <small>Rating</small>
                        </div>
                    </div>
                    <div class="category-actions">
                        <button class="btn btn-warning btn-sm" style="flex: 1;" onclick="editCategory('<?php echo $kat['nama_kategori']; ?>', '<?php echo addslashes($kat['nama_kategori']); ?>')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Tambah Kategori</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" required>
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
    
    <script>
        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        window.onclick = function(e) { if (e.target.classList.contains('modal')) e.target.classList.remove('active'); }
    </script>
</body>
</html>