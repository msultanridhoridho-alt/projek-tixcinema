<?php
session_start();
include_once 'database.php';

// Ambil judul dari URL
$judulFilm = $_GET['judul'] ?? '';

// Cari data film di database berdasarkan judul
$filmDitemukan = null;
foreach ($_SESSION['daftar_film'] as $film) {
    if ($film['judul'] === $judulFilm) {
        $filmDitemukan = $film;
        break;
    }
}

if (!$filmDitemukan) {
    echo "Film tidak ditemukan!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Playing: <?= htmlspecialchars($filmDitemukan['judul']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #000; color: white; }
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0; background: #111; border-radius: 12px;
            box-shadow: 0 0 30px rgba(229, 9, 20, 0.4);
        }
        .video-wrapper iframe {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container py-5 text-center">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="streaming_list.php" class="btn btn-outline-danger"><i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Daftar</a>
            <h2 class="fw-bold m-0 text-danger"><?= htmlspecialchars($filmDitemukan['judul']); ?></h2>
            <div style="width: 140px;"></div>
        </div>

        <div class="video-wrapper mb-4">
            <?php if (!empty($filmDitemukan['link_trailer'])): ?>
                <iframe src="<?= $filmDitemukan['link_trailer']; ?>?autoplay=1" frameborder="0" allowfullscreen></iframe>
            <?php else: ?>
                <div class="p-5">
                    <i class="fa-solid fa-circle-exclamation fa-4x text-secondary mb-3"></i>
                    <h3>Maaf, Video belum tersedia untuk film ini.</h3>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-start p-3 bg-dark rounded">
            <span class="badge bg-danger mb-2"><?= $filmDitemukan['genre']; ?></span>
            <p class="text-secondary small">Rating: <?= $filmDitemukan['rating']; ?> | Tahun: <?= $filmDitemukan['tahun']; ?></p>
        </div>
    </div>
</body>
</html>