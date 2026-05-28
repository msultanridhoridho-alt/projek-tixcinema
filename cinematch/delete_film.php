<?php
include_once 'database.php';
session_start();

// Proteksi: Hanya Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['judul'])) {
    $judul_hapus = mysqli_real_escape_string($conn, $_GET['judul']);
    
    // Hapus data dari database
    $queryDelete = "DELETE FROM daftar_film WHERE judul = '$judul_hapus'";
    
    if (mysqli_query($conn, $queryDelete)) {
        // Berhasil, kembali ke admin.php
        header("Location: admin.php?status=deleted");
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
}
?>