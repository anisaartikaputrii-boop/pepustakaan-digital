<?php
// config.php - Konfigurasi Database
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'perpustakaan_digital';

// Koneksi ke database
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mencegah SQL Injection
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// Fungsi untuk cek login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk cek role admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Fungsi redirect jika belum login
function check_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk upload foto
function upload_foto($file, $folder = 'uploads/') {
    $target_dir = $folder;
    
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Cek apakah file adalah gambar
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return array('status' => false, 'message' => 'File bukan gambar');
    }
    
    // Batasi ukuran file (maksimal 2MB)
    if ($file["size"] > 2097152) {
        return array('status' => false, 'message' => 'Ukuran file terlalu besar (maksimal 2MB)');
    }
    
    // Hanya izinkan format tertentu
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if(!in_array($file_extension, $allowed_extensions)) {
        return array('status' => false, 'message' => 'Hanya file JPG, JPEG, PNG & GIF yang diizinkan');
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return array('status' => true, 'filename' => $new_filename, 'path' => $target_file);
    } else {
        return array('status' => false, 'message' => 'Terjadi kesalahan saat upload file');
    }
}

// Fungsi untuk hapus foto
function delete_foto($filename, $folder = 'uploads/') {
    if ($filename && file_exists($folder . $filename)) {
        unlink($folder . $filename);
    }
}

// Fungsi untuk log aktivitas
function log_aktivitas($id_user, $aktivitas, $detail = '') {
    global $conn;
    $aktivitas = clean_input($aktivitas);
    $detail = clean_input($detail);
    
    $query = "INSERT INTO log_aktivitas (id_user, aktivitas, detail) VALUES ('$id_user', '$aktivitas', '$detail')";
    mysqli_query($conn, $query);
}

// Fungsi untuk format tanggal Indonesia
function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Fungsi untuk format rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>