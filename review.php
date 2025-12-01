<?php
require_once 'config.php';
check_login();

$message = '';
$message_type = '';

// INSERT
if (isset($_POST['tambah'])) {
    $id_buku = clean_input($_POST['id_buku']);
    $id_anggota = clean_input($_POST['id_anggota']);
    $rating = clean_input($_POST['rating']);
    $komentar = clean_input($_POST['komentar']);
    
    $query = "INSERT INTO review (id_buku, id_anggota, rating, komentar) VALUES ('$id_buku', '$id_anggota', '$rating', '$komentar')";
    if (mysqli_query($conn, $query)) {
        log_aktivitas($_SESSION['user_id'], 'Tambah Review', "Menambahkan review untuk buku ID: $id_buku");
        $message = 'Review berhasil ditambahkan!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menambahkan review!';
        $message_type = 'error';
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM review WHERE id_review = '$id'")) {
        log_aktivitas($_SESSION['user_id'], 'Hapus Review', "Menghapus review ID: $id");
        $message = 'Review berhasil dihapus!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menghapus review!';
        $message_type = 'error';
    }
}

$query = "SELECT r.*, b.judul, b.pengarang, a.nama_lengkap as nama_anggota 
          FROM review r 
          JOIN buku b ON r.id_buku = b.id_buku 
          JOIN anggota a ON r.id_anggota = a.id_anggota 
          ORDER BY r.tanggal_review DESC";
$result = mysqli_query($conn, $query);

$buku_query = mysqli_query($conn, "SELECT id_buku, judul, pengarang FROM buku ORDER BY judul");
$anggota_query = mysqli_query($conn, "SELECT id_anggota, nomor_anggota, nama_lengkap FROM anggota WHERE status = 'aktif' ORDER BY nama_lengkap");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Buku - Perpustakaan Digital</title>
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
        .btn-danger { background: #E57373; color: white; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #C8E6C9; color: #2E7D32; border-left: 4px solid #4CAF50; }
        .alert-error { background: #FFCDD2; color: #C62828; border-left: 4px solid #F44336; }
        .review-list { display: flex; flex-direction: column; gap: 15px; }
        .review-item { background: #FFF8DC; border-radius: 12px; padding: 20px; border-left: 4px solid #D2B48C; transition: all 0.3s; }
        .review-item:hover { transform: translateX(5px); box-shadow: 0 3px 10px rgba(139, 115, 85, 0.2); }
        .review-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; }
        .review-book { color: #6B5744; font-weight: bold; font-size: 18px; margin-bottom: 5px; }
        .review-author { color: #999; font-size: 14px; }
        .review-rating { color: #DAA520; font-size: 20px; }
        .review-body { color: #666; line-height: 1.6; margin: 15px 0; }
        .review-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #F5F1E8; }
        .reviewer-info { color: #999; font-size: 14px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 15px; padding: 30px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #F5DEB3; }
        .modal-header h2 { color: #6B5744; }
        .close-modal { font-size: 28px; cursor: pointer; color: #999; }
        .close-modal:hover { color: #6B5744; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #6B5744; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 15px; border: 2px solid #D2B48C; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #8B7355; }
        .star-rating { display: flex; gap: 5px; font-size: 32px; }
        .star-rating input { display: none; }
        .star-rating label { color: #D2B48C; cursor: pointer; transition: all 0.2s; }
        .star-rating label:hover, .star-rating label:hover ~ label, .star-rating input:checked ~ label { color: #DAA520; }
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
            <h1><i class="fas fa-star"></i> Review & Rating Buku</h1>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Tambah Review
            </button>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="review-list">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($review = mysqli_fetch_assoc($result)): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="review-book"><?php echo $review['judul']; ?></div>
                                    <div class="review-author"><?php echo $review['pengarang']; ?></div>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? '#DAA520' : '#D2B48C'; ?>;"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="review-body">
                                "<?php echo $review['komentar']; ?>"
                            </div>
                            <div class="review-footer">
                                <div class="reviewer-info">
                                    <i class="fas fa-user"></i> <?php echo $review['nama_anggota']; ?> • 
                                    <i class="fas fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($review['tanggal_review'])); ?>
                                </div>
                                <button class="btn btn-danger btn-sm" onclick="if(confirm('Yakin ingin menghapus review ini?')) window.location.href='?delete=<?php echo $review['id_review']; ?>'">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; color: #999;">
                        <i class="fas fa-comment-slash" style="font-size: 64px; margin-bottom: 15px;"></i>
                        <p>Belum ada review</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Tambah Review</h2>
                <span class="close-modal" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Buku</label>
                    <select name="id_buku" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php while ($buku = mysqli_fetch_assoc($buku_query)): ?>
                            <option value="<?php echo $buku['id_buku']; ?>">
                                <?php echo $buku['judul']; ?> - <?php echo $buku['pengarang']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
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
                    <label>Rating</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required>
                        <label for="star5"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2"><i class="fas fa-star"></i></label>
                        
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1"><i class="fas fa-star"></i></label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Komentar</label>
                    <textarea name="komentar" required placeholder="Tulis review Anda tentang buku ini..."></textarea>
                </div>
                
                <button type="submit" name="tambah" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Simpan Review
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