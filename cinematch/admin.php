<?php
include_once 'database.php';

// Proteksi Halaman: Pastikan hanya Admin yang bisa masuk
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, tendang ke halaman login/index
    header("Location: index.php");
    exit();
}

// PESAN STATUS
$statusMessage = "";
$statusClass = "";

// LOGIKA backend: Menangkap data dari Form lalu disimpan secara permanen ke phpMyAdmin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_film'])) {
    $judul  = mysqli_real_escape_string($conn, strip_tags(trim($_POST['judul'])));
    $genre  = mysqli_real_escape_string($conn, $_POST['genre']);
    $tahun  = intval($_POST['tahun']);
    $rating = floatval($_POST['rating']);
    $poster = mysqli_real_escape_string($conn, filter_var(trim($_POST['poster']), FILTER_SANITIZE_URL));

    if (!empty($judul) && !empty($poster)) {
        // Query SQL untuk menyimpan data langsung ke phpMyAdmin
        $queryInsert = "INSERT INTO daftar_film (judul, genre, tahun, rating, usia, poster) 
                        VALUES ('$judul', '$genre', $tahun, $rating, 'SU', '$poster')";
        
        if (mysqli_query($conn, $queryInsert)) {
            // JALUR AMAN: Biarkan database.php yang melakukan refresh array session film secara otomatis.
            // Kita tidak perlu mengutak-atik $_SESSION di sini agar $_SESSION['user'] milik admin tetap terjaga rapi!
            
            $statusMessage = "Berhasil! Film <strong>\"$judul\"</strong> telah tersimpan permanen di phpMyAdmin.";
            $statusClass = "alert-success";
            
            // Opsional: Redirect langsung ke index.php setelah 2 detik agar datanya langsung sinkron
            header("Refresh: 2; url=index.php");
        } else {
            $statusMessage = "Gagal menyimpan ke database: " . mysqli_error($conn);
            $statusClass = "alert-danger";
        }
    } else {
        $statusMessage = "Gagal! Mohon isi judul dan link poster dengan benar.";
        $statusClass = "alert-danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin Kendali - TixCinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #141414; color: white; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background-color: #000000; border-bottom: 2px solid #E50914; }
        .btn-danger-netflix { background-color: #E50914; border: none; font-weight: bold; }
        .btn-danger-netflix:hover { background-color: #b20710; }
        
        /* FIX TEKS GELAP: Mengubah warna background card & teks info agar kontras */
        .admin-card { background-color: #1c1c1c; border: 1px solid #2d2d2d; border-radius: 8px; }
        .text-muted-light { color: #b3b3b3 !important; } /* Teks info yang sebelumnya gelap gulita */
        
        /* Styling Table Katalog */
        .table-custom { color: #fff; vertical-align: middle; }
        .table-custom thead { background-color: #2b2b2b; color: #E50914; }
        .table-custom tbody tr { border-bottom: 1px solid #2d2d2d; }
        .poster-preview { width: 45px; height: 65px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand text-danger fw-bolder fs-3" href="index.php">TIXCINEMA</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    <li class="nav-item"><a class="nav-link text-secondary" href="index.php"><i class="fa-solid fa-house me-1"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link active fw-bold text-warning" href="admin.php"><i class="fa-solid fa-user-shield me-1"></i> Panel Admin</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white small fw-bold"><i class="fa-solid fa-user-gear text-danger me-1"></i> Admin (Master)</span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold px-3">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-warning fw-bold m-0"><i class="fa-solid fa-user-lock me-2"></i>Dashboard Utama Admin</h1>
                <p class="text-muted-light mt-1">Kelola katalog data film, perbarui poster, dan manipulasi sistem rekomendasi secara real-time.</p>
            </div>
            <span class="badge bg-danger px-3 py-2 fw-bold fs-6">Mode: Administrator Active</span>
        </div>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert <?= $statusClass; ?> alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> <?= $statusMessage; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-5">
                <div class="admin-card p-4 shadow-lg">
                    <h4 class="text-white fw-bold mb-4"><i class="fa-solid fa-square-plus text-danger me-2"></i>Input Film Baru</h4>
                    
                    <form method="POST" action="admin.php">
                        <input type="hidden" name="tambah_film" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">Judul Film Resmi</label>
                            <input type="text" name="judul" class="form-content form-control bg-dark text-white border-secondary" placeholder="Contoh: Cars 3" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">Kategori Genre</label>
                            <select name="genre" class="form-select bg-dark text-white border-secondary" required>
                                <option value="Action">Action</option>
                                <option value="Sci-Fi">Sci-Fi</option>
                                <option value="Horror">Horror</option>
                                <option value="Animation">Animation</option>
                                <option value="Romance">Romance</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-danger">Tahun Rilis</label>
                                <input type="number" name="tahun" class="form-control bg-dark text-white border-secondary" value="2026" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-danger">Rating IMDB</label>
                                <input type="number" step="0.1" max="10" name="rating" class="form-control bg-dark text-white border-secondary" value="8.0" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-danger">Link URL Gambar Poster Film</label>
                            <input type="url" name="poster" class="form-control bg-dark text-white border-secondary" placeholder="https://images.unsplash.com/..." required>
                            <small class="text-muted-light d-block mt-1.5" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-circle-info text-warning me-1"></i>PENTING: Gunakan tautan langsung gambar (.jpg / .png), jangan link pencarian Google.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-danger-netflix w-100 py-2.5 shadow fs-5">
                            <i class="fa-solid fa-film me-2"></i>Publish ke Bioskop
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="admin-card p-4 shadow-lg" style="max-height: 580px; overflow-y: auto;">
                    <h4 class="text-white fw-bold mb-4">
                        <i class="fa-solid fa-list-ul text-danger me-2"></i>Katalog Aktif Terdaftar (<?= count($_SESSION['daftar_film']); ?> Film)
                    </h4>
                    
                    <div class="table-responsive">
                        <table class="table table-custom table-dark table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Poster</th>
                                    <th>Judul Film</th>
                                    <th>Genre</th>
                                    <th>Tahun</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                           <tbody>
    <?php foreach ($_SESSION['daftar_film'] as $film): ?>
    <tr>
        <td>
           <img src="<?= $film['poster']; ?>" 
     class="poster-preview" 
     referrerpolicy="no-referrer" 
     onerror="this.src='https://via.placeholder.com/150?text=No+Poster'">
        </td>
        
        <td class="fw-bold text-white text-truncate" style="max-width: 180px;">
            <?= $film['judul']; ?>
        </td>
        
        <td>
            <span class="badge bg-secondary px-2 py-1 small"><?= strtoupper($film['genre']); ?></span>
        </td>
        
        <td><?= $film['tahun']; ?></td>
        
        <td class="text-warning fw-bold">
            <i class="fa-solid fa-star me-1 small"></i><?= $film['rating']; ?>
        </td>
        
        <td>
            <a href="edit_film.php?judul=<?= urlencode($film['judul']); ?>" class="btn btn-sm btn-outline-warning me-1">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <a href="delete_film.php?judul=<?= urlencode($film['judul']); ?>" class="btn btn-sm btn-outline-danger">
                <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>