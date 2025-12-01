<?php
require_once 'config.php';
check_login();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = clean_input($_GET['id']);
    
    $query = "SELECT * FROM view_anggota_aktif WHERE id_anggota = '$id'
              UNION
              SELECT a.*, 0 as total_peminjaman, 0 as sedang_dipinjam 
              FROM anggota a 
              WHERE a.id_anggota = '$id' AND a.status = 'nonaktif'";
    
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Anggota tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID tidak valid']);
}
?>