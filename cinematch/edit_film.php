<?php
// Cek session tanpa memicu notice
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }

// Ambil data berdasarkan judul
$data = null;
if (isset($_GET['judul'])) {
    $judul_lama = mysqli_real_escape_string($conn, urldecode($_GET['judul']));
    $query = "SELECT * FROM daftar_film WHERE judul = '$judul_lama'";
    $data = mysqli_fetch_assoc(mysqli_query($conn, $query));
}

// Proses Update
if (isset($_POST['update_film'])) {
    $judul_lama = mysqli_real_escape_string($conn, $_POST['judul_lama']);
    $judul_baru = mysqli_real_escape_string($conn, $_POST['judul']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $tahun = intval($_POST['tahun']);
    $rating = floatval($_POST['rating']);
    $poster = mysqli_real_escape_string($conn, $_POST['poster']);

    $sql = "UPDATE daftar_film SET judul='$judul_baru', genre='$genre', tahun=$tahun, rating=$rating, poster='$poster' WHERE judul='$judul_lama'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?status=updated");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Film - TixCinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #141414; color: white; }
        .admin-card { background-color: #1c1c1c; border: 1px solid #2d2d2d; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="admin-card p-4 col-md-6 mx-auto">
        <h4 class="text-warning mb-4">Edit Film: <?= $data['judul'] ?></h4>
        <form method="POST">
            <input type="hidden" name="judul_lama" value="<?= $data['judul']; ?>">
            <div class="mb-3">
                <label>Judul Film</label>
                <input type="text" name="judul" class="form-control bg-dark text-white" value="<?= $data['judul']; ?>" required>
            </div>
            <div class="mb-3">
                <label>Genre</label>
                <input type="text" name="genre" class="form-control bg-dark text-white" value="<?= $data['genre']; ?>">
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label>Tahun</label>
                    <input type="number" name="tahun" class="form-control bg-dark text-white" value="<?= $data['tahun']; ?>">
                </div>
                <div class="col">
                    <label>Rating</label>
                    <input type="number" step="0.1" name="rating" class="form-control bg-dark text-white" value="<?= $data['rating']; ?>">
                </div>
            </div>
            <div class="mb-3">
                <label>Link Poster</label>
                <input type="url" name="poster" class="form-control bg-dark text-white" value="<?= $data['poster']; ?>">
            </div>
            <button type="submit" name="update_film" class="btn btn-danger w-100">Simpan Perubahan</button>
            <a href="admin.php" class="btn btn-secondary w-100 mt-2">Batal</a>
        </form>
    </div>
</div>
</body>
</html>