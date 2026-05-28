<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tombol paksa hapus memory data yang rusak jika ditekan
if (isset($_POST['clear_session'])) {
    unset($_SESSION['watchlist']);
    header("Location: streaming_list.php");
    exit;
}

// Inisialisasi awal watchlist jika belum ada
if (!isset($_SESSION['watchlist'])) {
    $_SESSION['watchlist'] = [];
}

// LOGIKA MENAMBAH FILM
if (isset($_POST['tambah_watchlist']) && !empty($_POST['id_film'])) {
    $judul = $_POST['id_film'];

    // Cek apakah film sudah terdaftar di watchlist
    $exists = false;
    foreach ($_SESSION['watchlist'] as $item) {
        if (is_array($item) && isset($item['judul']) && $item['judul'] === $judul) {
            $exists = true;
            break;
        }
    }

    // Hanya tambahkan jika data valid dan belum ada
    if (!$exists) {
        $_SESSION['watchlist'][] = [
            'judul'  => $judul,
            'poster' => !empty($_POST['poster_film']) ? $_POST['poster_film'] : 'https://via.placeholder.com/300x450?text=No+Poster',
            'genre'  => !empty($_POST['genre_film']) ? $_POST['genre_film'] : '-',
            'tahun'  => !empty($_POST['tahun_film']) ? $_POST['tahun_film'] : '-',
            'rating' => !empty($_POST['rating_film']) ? $_POST['rating_film'] : '-',
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Streaming List - TixCinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #141414; color: white; }
        .watchlist-card { background-color: #1c1c1c; border: 1px solid #333; border-radius: 8px; transition: 0.3s; }
        .watchlist-card:hover { border-color: #E50914; transform: translateY(-3px); }
        .poster-img { height: 340px; object-fit: cover; border-radius: 8px 8px 0 0; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark py-3 border-bottom border-secondary">
        <div class="container">
            <a class="navbar-brand text-danger fw-bolder fs-3" href="index.php">TIXCINEMA</a>
            <div class="d-flex gap-2">
                <form method="POST" class="m-0">
                    <button type="submit" name="clear_session" class="btn btn-warning btn-sm fw-bold">
                        <i class="fa-solid fa-broom me-1"></i> Bersihkan Error
                    </button>
                </form>
                <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-house me-1"></i>Home</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-white fw-bold mb-4 border-bottom border-secondary pb-3"><i class="fa-solid fa-play text-danger me-2"></i>My Streaming List</h2>

        <div class="row g-4">
            <?php 
            $valid_film_count = 0;
            if (!empty($_SESSION['watchlist'])): 
                foreach ($_SESSION['watchlist'] as $film): 
                    // Proteksi: Lewati dan jangan tampilkan jika data di session rusak/bukan array
                    if (!is_array($film) || !isset($film['judul'])) continue;
                    $valid_film_count++;
            ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card watchlist-card h-100 shadow">
                            <img src="<?= htmlspecialchars($film['poster']); ?>" class="card-img-top poster-img" alt="Poster">
                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title text-white fw-bold text-truncate mb-1"><?= htmlspecialchars($film['judul']); ?></h5>
                                <p class="text-secondary small mb-2"><?= htmlspecialchars($film['genre']); ?> | <?= htmlspecialchars($film['tahun']); ?></p>
                                <p class="text-warning fw-bold small mb-3"><i class="fa-solid fa-star me-1"></i><?= htmlspecialchars($film['rating']); ?></p>
                                
                                <a href="player.php?judul=<?= urlencode($film['judul']); ?>" class="btn btn-success mt-auto w-100 fw-bold">
                                    <i class="fa-solid fa-play me-2"></i>Play Film
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($valid_film_count === 0): ?>
                <div class="col-12 text-center mt-5 py-5 border border-secondary rounded bg-dark">
                    <h4 class="text-secondary">Daftar tontonanmu kosong atau data telah di-reset.</h4>
                    <a href="index.php" class="btn btn-danger mt-3 px-4 fw-bold">Kembali Cari Film</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>