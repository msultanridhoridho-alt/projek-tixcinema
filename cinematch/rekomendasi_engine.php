<?php
// rekomendasi_engine.php

/**
 * FUNGSI 1: Mengambil daftar genre unik secara otomatis dari database.
 * Diperlukan oleh index.php untuk menampilkan pilihan genre/dropdown.
 */
function dapatkanMasterGenre($databaseFilm) {
    $master = [];
    foreach ($databaseFilm as $film) {
        // Pecah string genre (misal: "Action, Sci-Fi") jadi array
        $genres = array_map('trim', explode(',', strtolower($film['genre'])));
        
        foreach ($genres as $g) {
            if ($g !== "") {
                // LINEAR SEARCH MANUAL: Cek apakah genre sudah ada di array $master atau belum
                $sudahAda = false;
                foreach ($master as $m) {
                    if ($m === $g) {
                        $sudahAda = true;
                        break; // Jika ketemu yang sama, hentikan pencarian
                    }
                }
                
                // Jika setelah dicek manual memang belum ada, baru masukkan ke array master
                if (!$sudahAda) {
                    $master[] = $g;
                }
            }
        }
    }
    return $master;
}

/**
 * FUNGSI 2: Digunakan di index.php untuk mendapatkan daftar rekomendasi.
 * Menggunakan pendekatan Array Sederhana dan Pencarian Linear
 */
function dapatkanRekomendasi($judulTarget, $genreTarget, $databaseFilm) {
    
    // 1. Pecah genre film yang sedang dicari menjadi array
    $targetGenres = array_map('trim', explode(',', strtolower($genreTarget)));
    $hasil = [];

    // 2. LINEAR SEARCH: Loop setiap film di database untuk menghitung kecocokan genre
    foreach ($databaseFilm as $film) {
        $filmGenres = array_map('trim', explode(',', strtolower($film['genre'])));
        $jumlahMatch = 0;

        // Bandingkan satu per satu genre target dengan genre film di database
        foreach ($targetGenres as $tGenre) {
            foreach ($filmGenres as $fGenre) {
                if ($tGenre === $fGenre && $tGenre !== "") {
                    $jumlahMatch++;
                    break; 
                }
            }
        }

        // Hanya masukkan ke daftar rekomendasi jika ada minimal 1 genre yang sama (skor > 0)
        if ($jumlahMatch > 0) {
            // PERBAIKAN: Hitung skor desimal agar persentase match di frontend bervariasi
            $totalGenreFilm = count($filmGenres);
            $skorDesimal = $jumlahMatch / $totalGenreFilm;
            
            $film['skor_kemiripan'] = $skorDesimal; 
            $hasil[] = $film;
        }
    }

    // 3. BUBBLE SORT: Mengurutkan hasil dari kecocokan tertinggi ke terendah (Descending)
    $n = count($hasil);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            // Jika skor film saat ini lebih kecil dari film berikutnya, tukar posisi (Swap)
            if ($hasil[$j]['skor_kemiripan'] < $hasil[$j + 1]['skor_kemiripan']) {
                $temp = $hasil[$j];
                $hasil[$j] = $hasil[$j + 1];
                $hasil[$j + 1] = $temp;
            }
        }
    }

    return $hasil;
}
?>