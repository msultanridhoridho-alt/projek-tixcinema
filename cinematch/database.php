<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Koneksi Database MySQL
$host     = "localhost";
$username = "root";
$password = "";
$database = "cinematch_db";

$conn = mysqli_connect($host, $username, $password, $database);

// Cek jika koneksi database gagal
if (!$conn) {
    die("Koneksi database Cinematch gagal: " . mysqli_connect_error());
}

// Di dalam database.php
$queryFilm = "SELECT * FROM daftar_film ORDER BY id DESC";
$resultFilm = mysqli_query($conn, $queryFilm);

$_SESSION['daftar_film'] = []; // Kosongkan dulu
while ($row = mysqli_fetch_assoc($resultFilm)) {
    $_SESSION['daftar_film'][] = $row; // Isi dengan data terbaru dari MySQL
}

// Akun login tetap disimpan di session agar aman
$_SESSION['users_db'] = [
    "admin" => ["password" => "admin123", "role" => "admin", "email" => "admin@cinematch.id"],
    "user" => ["password" => "user123", "role" => "user", "email" => "pandisaputra76128@gmail.com"]
];
?>