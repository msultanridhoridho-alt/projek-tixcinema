<?php
// Pastikan session dimulai paling pertama sebelum kode HTML apapun
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once 'database.php';
include_once 'rekomendasi_engine.php'; // Pindahkan ke atas agar selalu siap digunakan

// Cek status login user
$showLoginAlert = false;
if (!isset($_SESSION['user'])) {
    $showLoginAlert = true;
}

$filmDipilih = "";

// Logika Filter Rekomendasi Pintar (Menggunakan Dropdown)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action_suka'])) {
    $filmDipilih = $_POST['film_disukai'];
    $_SESSION['rekomendasi'] = [];

    // 1. Cari data lengkap dari film yang dipilih (Agar bisa ditampilkan secara terpisah)
    $dataFilmUtama = null;
    $genreDipilih = "";
    foreach ($_SESSION['daftar_film'] as $f) {
        if ($f['judul'] === $filmDipilih) {
            $genreDipilih = $f['genre'];
            $dataFilmUtama = $f;
            break;
        }
    }
    
    // Simpan data film utama ke session
    $_SESSION['film_utama'] = $dataFilmUtama;

    // 2. Eksekusi fungsi utama dari rekomendasi_engine.php
    $semuaRekomendasi = dapatkanRekomendasi($filmDipilih, $genreDipilih, $_SESSION['daftar_film']);
    
    // 3. Bersihkan hasil: Pastikan film utama TIDAK ikut masuk ke daftar 'Recommended For You'
    $rekomendasiBersih = [];
    foreach($semuaRekomendasi as $rek) {
        if (strtolower(trim($rek['judul'])) !== strtolower(trim($filmDipilih))) {
            $rekomendasiBersih[] = $rek;
        }
    }

    // 4. Ambil 4 rekomendasi terbaik saja
    $_SESSION['rekomendasi'] = array_slice($rekomendasiBersih, 0, 4);

} else if (!isset($_POST['action_suka'])) {
    // Kosongkan pencarian jika halaman di-refresh tanpa mencari
    $filmDipilih = "";
    unset($_SESSION['film_utama']);
    unset($_SESSION['rekomendasi']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TixCinema Portal - Stream Smart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #141414; color: white; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .navbar-custom { background-color: #000000; border-bottom: 2px solid #E50914; }
        
        /* FIX UKURAN POSTER: Memaksa card & gambar berbentuk portrait sempurna */
        .card-movie { 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            border: none; 
            border-radius: 6px; 
            overflow: hidden; 
            background-color: #181818;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-movie:hover { transform: scale(1.04); box-shadow: 0 10px 20px rgba(229, 9, 20, 0.4); }
        
        /* Wrapper gambar untuk memastikan tidak ada border hitam kosong */
        .movie-img-container {
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        .img-recommendation { height: 260px; }
        .img-catalog { height: 340px; }
        
        .card-movie img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            display: block;
        }

        .btn-danger-netflix { background-color: #E50914; border: none; font-weight: bold; color: white; }
        .btn-danger-netflix:hover { background-color: #b20710; color: white; }
        
        /* FOOTER MODERN Style */
        footer { background-color: #0c0c0c; border-top: 1px solid #222; padding: 50px 0 30px 0; font-size: 0.85rem; color: #757575; }
        footer a { color: #757575; text-decoration: none; transition: color 0.2s; }
        footer a:hover { color: #fff; }
        .footer-brand { color: #E50914; font-weight: 900; letter-spacing: 1px; font-size: 1.4rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand text-danger fw-bolder fs-3" href="index.php" style="letter-spacing: 1px;">TIXCINEMA</a>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#rekomendasi-section">Recommendations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white fw-bold" href="streaming_list.php">
                            <i class="fa-solid fa-list me-1"></i> My List
                        </a>
                    </li>
                    <?php if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="admin.php">
                                <i class="fa-solid fa-user-shield me-1"></i> Panel Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['user'])): ?>
                        <span class="text-white small fw-bold"><i class="fa-solid fa-circle-user text-danger me-2"></i><?= ucfirst($_SESSION['user']); ?></span>
                        <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold px-3">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-danger-netflix btn-sm px-4">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <?php if ($showLoginAlert): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center py-3 shadow border-start border-danger border-4" role="alert">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2 align-middle"></i>
                Please <strong><a href="login.php" class="alert-link text-decoration-underline">Sign In</a></strong> first to unlock our Smart Recommendation features.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center mb-5">
            <div class="col-md-8 bg-dark p-4 rounded shadow border border-secondary">
                <h4 class="mb-3 text-danger fw-bold"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Smart Recommendation</h4>
                <form method="POST" action="">
                    <input type="hidden" name="action_suka" value="1">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <select class="form-select bg-secondary text-white border-0 py-2.5 fw-semibold" name="film_disukai" required>
                                <option value="" disabled selected>Select a movie you like...</option>
                                <?php foreach ($_SESSION['daftar_film'] as $f): ?>
                                    <option value="<?= $f['judul']; ?>" <?= ($filmDipilih == $f['judul']) ? 'selected' : ''; ?>>
                                        <?= $f['judul']; ?> [<?= $f['genre']; ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-danger-netflix w-100 py-2.5 shadow"><i class="fa-solid fa-magnifying-glass me-2"></i>Find Matches</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_SESSION['film_utama'])): ?>
            <div class="row justify-content-center mb-5">
                <div class="col-md-8">
                    <h5 class="fw-bold text-white mb-3"><i class="fa-solid fa-crosshairs text-danger me-2"></i>Target Movie</h5>
                    <div class="card bg-dark text-white border-secondary shadow-lg" style="flex-direction: row; padding: 15px; border-radius: 8px;">
                        <img src="<?= $_SESSION['film_utama']['poster']; ?>" style="width: 100px; height: 145px; object-fit: cover; border-radius: 5px;" alt="poster">
                        <div class="card-body py-2 px-4 d-flex flex-column justify-content-center">
                            <h4 class="card-title text-white fw-bold mb-2"><?= $_SESSION['film_utama']['judul']; ?></h4>
                            <div>
                                <span class="badge bg-danger mb-2 me-1"><?= $_SESSION['film_utama']['genre']; ?></span>
                                <span class="badge bg-success mb-2"><i class="fa-solid fa-bullseye me-1"></i>Match: 100%</span>
                            </div>
                            <div class="d-flex gap-3 small text-light mt-1 mb-2">
                                <span><i class="fa-solid fa-calendar me-1 text-secondary"></i>Year: <?= $_SESSION['film_utama']['tahun']; ?></span>
                                <span class="text-warning fw-bold"><i class="fa-solid fa-star me-1"></i><?= $_SESSION['film_utama']['rating']; ?></span>
                            </div>
                            <form method="POST" action="streaming_list.php">
                                <input type="hidden" name="id_film" value="<?= $_SESSION['film_utama']['judul']; ?>">
                                <input type="hidden" name="poster_film" value="<?= $_SESSION['film_utama']['poster']; ?>">
                                <input type="hidden" name="genre_film" value="<?= $_SESSION['film_utama']['genre']; ?>">
                                <input type="hidden" name="tahun_film" value="<?= $_SESSION['film_utama']['tahun']; ?>">
                                <input type="hidden" name="rating_film" value="<?= $_SESSION['film_utama']['rating']; ?>">
                                <button type="submit" name="tambah_watchlist" class="btn btn-danger-netflix btn-sm px-4 mt-2">
                                    <i class="fa-solid fa-play me-2"></i>Watch Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['rekomendasi'])): ?>
            <section id="rekomendasi-section" class="mb-5 bg-black p-4 rounded border border-danger shadow-lg">
                <h3 class="fw-bold text-danger mb-4"><i class="fa-solid fa-star me-2"></i>Recommended For You</h3>
                <div class="row row-cols-1 row-cols-md-4 g-4">
                    <?php foreach ($_SESSION['rekomendasi'] as $movie): ?>
                        <div class="col">
                            <div class="card card-movie shadow-sm position-relative">
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger fw-bold shadow" style="z-index: 10; font-size: 0.8rem;">
                                    <?= isset($movie['usia']) ? $movie['usia'] : 'SU'; ?>
                                </span>
                                <div class="movie-img-container img-recommendation">
                                    <img src="<?= $movie['poster']; ?>" alt="poster">
                                </div>
                                <div class="card-body p-3 d-flex flex-column">
                                    <span class="text-danger small fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem;"><?= $movie['genre']; ?></span>

                                    <?php if (isset($movie['skor_kemiripan'])): ?>
                                        <div class="mt-1 mb-1">
                                            <span class="badge bg-success" style="font-size: 0.7rem;">
                                                <i class="fa-solid fa-bullseye me-1"></i> Match: <?= round($movie['skor_kemiripan'] * 100); ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <h6 class="text-white fw-bold m-0 text-truncate mt-1" style="font-size: 1.05rem;"><?= $movie['judul']; ?></h6>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary">
                                        <span class="text-secondary small">Year: <?= $movie['tahun']; ?></span>
                                        <span class="text-warning fw-bold small"><i class="fa-solid fa-star me-1"></i><?= $movie['rating']; ?></span>
                                    </div>
                                    
                                    <form method="POST" action="streaming_list.php" class="mt-auto">
                                        <input type="hidden" name="id_film" value="<?= $movie['judul']; ?>">
                                        <input type="hidden" name="poster_film" value="<?= $movie['poster']; ?>">
                                        <input type="hidden" name="genre_film" value="<?= $movie['genre']; ?>">
                                        <input type="hidden" name="tahun_film" value="<?= $movie['tahun']; ?>">
                                        <input type="hidden" name="rating_film" value="<?= $movie['rating']; ?>">
                                        <button type="submit" name="tambah_watchlist" class="btn btn-danger-netflix btn-sm w-100 fw-bold mt-3 py-2">
                                            <i class="fa-solid fa-play me-1 small"></i> Watch Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="my-4">
            <h3 class="fw-bold text-white mb-4"><i class="fa-solid fa-film text-danger me-2"></i>Explore Movies</h3>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($_SESSION['daftar_film'] as $movie): ?>
                    <div class="col">
                        <div class="card card-movie shadow-sm position-relative">
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger fw-bold shadow" style="z-index: 10; font-size: 0.8rem; padding: 5px 8px;">
                                <?= isset($movie['usia']) ? $movie['usia'] : 'SU'; ?>
                            </span>
                            <div class="movie-img-container img-catalog">
                                <img src="<?= $movie['poster']; ?>" alt="poster">
                            </div>
                            <div class="card-body p-3 d-flex flex-column">
                                <span class="text-danger small fw-bold text-uppercase d-block mb-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    <?= $movie['genre']; ?>
                                </span>
                                <h6 class="text-white fw-bold m-0 text-truncate" style="font-size: 1.05rem;">
                                    <?= $movie['judul']; ?>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary">
                                    <span class="text-light" style="font-size: 0.85rem;">Year: <?= $movie['tahun']; ?></span>
                                    <span class="text-warning fw-bold" style="font-size: 0.9rem;">
                                        <i class="fa-solid fa-star me-1"></i><?= $movie['rating']; ?>
                                    </span>
                                </div>
                                
                                <form method="POST" action="streaming_list.php" class="mt-auto">
                                    <input type="hidden" name="id_film" value="<?= $movie['judul']; ?>">
                                    <input type="hidden" name="poster_film" value="<?= $movie['poster']; ?>">
                                    <input type="hidden" name="genre_film" value="<?= $movie['genre']; ?>">
                                    <input type="hidden" name="tahun_film" value="<?= $movie['tahun']; ?>">
                                    <input type="hidden" name="rating_film" value="<?= $movie['rating']; ?>">
                                    <button type="submit" name="tambah_watchlist" class="btn btn-danger-netflix btn-sm w-100 fw-bold mt-3 py-2">
                                        <i class="fa-solid fa-play me-1 small"></i> Watch Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>

    <footer>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <span class="footer-brand">TIXCINEMA</span>
                    <p class="mt-2 text-secondary small" style="max-width: 300px;">
                        A global movie match platform built for movie lovers. Discover your taste, find similarities, and track your watchlists seamlessly.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="text-white fw-bold small mb-3">NAVIGATION</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="index.php">Home Base</a></li>
                        <li><a href="#">Smart Search</a></li>
                        <li><a href="streaming_list.php">Streaming List</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3">
                    <h6 class="text-white fw-bold small mb-3">HELP & LEGAL</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 small">
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms of Use</a></li>
                            <li><a href="#">Technical Help</a></li>
                        </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="text-white fw-bold small mb-3">CONNECT</h6>
                    <div class="d-flex gap-3 fs-5">
                        <a href="#"><i class="fa-brands fa-whatsapp text-success"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-github"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-secondary opacity-25 my-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small">
                <span>&copy; 2026 TixCinema Engine Pro. All rights reserved.</span>
                <span class="text-secondary">System Server: <strong class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Operational</strong></span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>