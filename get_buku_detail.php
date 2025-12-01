<?php
require_once 'config.php';
check_login();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = clean_input($_GET['id']);
    
    $query = "SELECT b.*, k.nama_kategori FROM buku b 
              LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
              WHERE b.id_buku = '$id'";
    
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Buku tidak ditemukan']);
    }
} else {
    echo json_encode(['error' => 'ID tidak valid']);
}
?>