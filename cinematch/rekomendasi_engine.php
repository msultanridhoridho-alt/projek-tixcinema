<?php
// rekomendasi_engine.php

/**
 * FUNGSI 1: Membuat daftar genre unik secara otomatis dari database.
 * Ini adalah kunci agar genre baru seperti "Animation" atau "Family" 
 * langsung terdeteksi tanpa harus diedit manual.
 */
function dapatkanMasterGenre($databaseFilm) {
    $master = [];
    foreach ($databaseFilm as $film) {
        // Pecah string genre (misal: "Action, Sci-Fi") jadi array
        $genres = array_map('trim', explode(',', strtolower($film['genre'])));
        foreach ($genres as $g) {
            if (!empty($g) && !in_array($g, $master)) {
                $master[] = $g;
            }
        }
    }
    return $master;
}

/**
 * FUNGSI 2: Mengonversi teks genre menjadi Vektor Matriks (One-Hot Encoding).
 * Mengubah "Action, Drama" menjadi [1, 0, 1, 0, ...]
 */
function konversiKeOneHot($stringGenre, $masterGenre) {
    $vektor = array_fill(0, count($masterGenre), 0);
    $genres = array_map('trim', explode(',', strtolower($stringGenre)));

    foreach ($masterGenre as $index => $mGenre) {
        if (in_array($mGenre, $genres)) {
            $vektor[$index] = 1;
        }
    }
    return $vektor;
}

/**
 * FUNGSI 3: Menghitung skor kemiripan dengan rumus Cosine Similarity.
 * Menghasilkan nilai antara 0.0 sampai 1.0
 */
function hitungCosineSimilarity($vektorA, $vektorB) {
    $dotProduct = 0;
    $normA = 0;
    $normB = 0;

    for ($i = 0; $i < count($vektorA); $i++) {
        $dotProduct += $vektorA[$i] * $vektorB[$i];
        $normA += pow($vektorA[$i], 2);
        $normB += pow($vektorB[$i], 2);
    }

    $magnitude = sqrt($normA) * sqrt($normB);
    return ($magnitude == 0) ? 0 : ($dotProduct / $magnitude);
}

/**
 * FUNGSI UTAMA: Digunakan di index.php untuk mendapatkan daftar rekomendasi.
 */
function dapatkanRekomendasi($judulTarget, $genreTarget, $databaseFilm) {
    // 1. Ambil semua genre yang ada di database secara dinamis
    $masterGenre = dapatkanMasterGenre($databaseFilm);
    
    // 2. Ubah film yang dicari menjadi vektor target
    $vektorTarget = konversiKeOneHot($genreTarget, $masterGenre);
    $hasil = [];

    // 3. Hitung skor untuk setiap film
    foreach ($databaseFilm as $film) {
        // CATATAN: Bagian 'continue' dihapus agar film utama tetap muncul di hasil
        
        $vektorFilm = konversiKeOneHot($film['genre'], $masterGenre);
        $skor = hitungCosineSimilarity($vektorTarget, $vektorFilm);

        // Hanya masukkan film ke daftar jika ada kemiripan (skor > 0)
        if ($skor > 0) {
            $film['skor_kemiripan'] = $skor;
            $hasil[] = $film;
        }
    }

    // 4. Urutkan hasil dari skor tertinggi ke terendah (Descending)
    // Film yang dicari otomatis akan paling atas karena skornya pasti 1.0 (100%)
    usort($hasil, function($a, $b) {
        return $b['skor_kemiripan'] <=> $a['skor_kemiripan'];
    });

    return $hasil;
}