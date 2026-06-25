<?php
// rekomendasi_engine.php

/**
 * STRUKTUR DATA: Node untuk Binary Search Tree (BST)
 * Menyimpan data film dan pointer ke anak kiri serta anak kanan.
 */
class NodeFilm {
    public $film;
    public $left = null;
    public $right = null;

    public function __construct($film) {
        $this->film = $film;
    }
}

/**
 * STRUKTUR DATA: Binary Search Tree khusus untuk mengurutkan rekomendasi
 */
class RekomendasiTree {
    public $root = null;

    public function insert($film) {
        $newNode = new NodeFilm($film);
        if ($this->root === null) {
            $this->root = $newNode;
        } else {
            $this->insertNode($this->root, $newNode);
        }
    }

    private function insertNode($current, $newNode) {
        if ($newNode->film['skor_kemiripan'] < $current->film['skor_kemiripan']) {
            if ($current->left === null) {
                $current->left = $newNode;
            } else {
                $this->insertNode($current->left, $newNode);
            }
        } else {
            if ($current->right === null) {
                $current->right = $newNode;
            } else {
                $this->insertNode($current->right, $newNode);
            }
        }
    }

    public function keArrayDescending($node, &$hasil) {
        if ($node !== null) {
            $this->keArrayDescending($node->right, $hasil);
            $hasil[] = $node->film;
            $this->keArrayDescending($node->left, $hasil);
        }
    }
}

/**
 * FUNGSI BANTUAN SDA 1: Algoritma Pemisah String Manual
 * Pengganti fungsi bawaan explode() PHP.
 */
function pisahStringManual($string, $delimiter = ',') {
    $hasil = [];
    $kataSementara = "";
    $panjang = strlen($string);
    
    for ($i = 0; $i < $panjang; $i++) {
        $char = substr($string, $i, 1); // Lebih aman dibanding $string[$i]
        if ($char === $delimiter) {
            if (trim($kataSementara) !== "") {
                $hasil[] = trim($kataSementara);
            }
            $kataSementara = ""; 
        } else {
            $kataSementara .= $char;
        }
    }
    
    if (trim($kataSementara) !== "") {
        $hasil[] = trim($kataSementara);
    }
    
    return $hasil;
}

/**
 * FUNGSI BANTUAN SDA 2: Algoritma Brute Force String Matching (VERSI PERBAIKAN)
 * Lebih kebal terhadap perbedaan konfigurasi server lokal & spasi tersembunyi.
 */
function pencarianStringManual($teks, $pola) {
    // Bersihkan spasi liar di ujung teks dan ubah ke huruf kecil semua
    $teks = strtolower(trim($teks));
    $pola = strtolower(trim($pola));

    $panjangTeks = strlen($teks);
    $panjangPola = strlen($pola);

    if ($panjangPola === 0 || $panjangPola > $panjangTeks) {
        return false;
    }

    // Melakukan pergeseran indeks (Brute Force String Matching)
    for ($i = 0; $i <= $panjangTeks - $panjangPola; $i++) {
        $cocok = true;
        for ($j = 0; $j < $panjangPola; $j++) {
            // Menggunakan substr() menjamin kecocokan karakter 100% akurat di semua versi PHP
            if (substr($teks, $i + $j, 1) !== substr($pola, $j, 1)) {
                $cocok = false;
                break; 
            }
        }
        if ($cocok) {
            return true;
        }
    }
    
    return false;
}

/**
 * FUNGSI 1: Mengambil daftar genre unik secara otomatis dari database
 */
function dapatkanMasterGenre($databaseFilm) {
    $master = [];
    foreach ($databaseFilm as $film) {
        $genres = pisahStringManual(strtolower($film['genre']));
        
        foreach ($genres as $g) {
            $sudahAda = false;
            foreach ($master as $m) {
                if ($m === $g) {
                    $sudahAda = true;
                    break;
                }
            }
            if (!$sudahAda) {
                $master[] = $g;
            }
        }
    }
    return $master;
}

/**
 * FUNGSI 2: Rekomendasi berdasarkan GENRE (Linear Search + Binary Search Tree)
 */
function dapatkanRekomendasi($genreTarget, $databaseFilm) {
    $targetGenres = pisahStringManual(strtolower($genreTarget));
    
    $treeRekomendasi = new RekomendasiTree();
    $adaData = false;

    foreach ($databaseFilm as $film) {
        $filmGenres = pisahStringManual(strtolower($film['genre']));
        $jumlahMatch = 0;

        foreach ($targetGenres as $tGenre) {
            foreach ($filmGenres as $fGenre) {
                if ($tGenre === $fGenre) {
                    $jumlahMatch++;
                    break; 
                }
            }
        }

        if ($jumlahMatch > 0) {
            $totalGenreFilm = count($filmGenres);
            $skorDesimal = $jumlahMatch / $totalGenreFilm;
            
            $film['skor_kemiripan'] = $skorDesimal; 
            $treeRekomendasi->insert($film);
            $adaData = true;
        }
    }

    $hasilUrut = [];
    if ($adaData) {
        $treeRekomendasi->keArrayDescending($treeRekomendasi->root, $hasilUrut);
    }

    return $hasilUrut;
}

/**
 * FUNGSI 3: Mencari film berdasarkan JUDUL (Linear Search + Brute Force String)
 */
function cariFilmBerdasarkanJudul($kataKunci, $databaseFilm) {
    $hasilPencarian = [];
    $kataKunci = trim($kataKunci);
    
    if ($kataKunci === "") {
        return $hasilPencarian; 
    }

    foreach ($databaseFilm as $film) {
        // Ditambahkan trim() pada judul film agar spasi di ujung database terhapus otomatis
        if (pencarianStringManual(trim($film['judul']), $kataKunci)) {
            $hasilPencarian[] = $film;
        }
    }

    return $hasilPencarian;
}
?>