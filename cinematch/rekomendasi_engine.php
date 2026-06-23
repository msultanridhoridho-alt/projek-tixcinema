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

    // Fungsi untuk memasukkan node baru ke dalam Tree secara rekursif
    public function insert($film) {
        $newNode = new NodeFilm($film);
        if ($this->root === null) {
            $this->root = $newNode;
        } else {
            $this->insertNode($this->root, $newNode);
        }
    }

    private function insertNode($current, $newNode) {
        // Jika skor film baru LEBIH KECIL, masukkan ke sebelah KIRI
        if ($newNode->film['skor_kemiripan'] < $current->film['skor_kemiripan']) {
            if ($current->left === null) {
                $current->left = $newNode;
            } else {
                $this->insertNode($current->left, $newNode);
            }
        } 
        // Jika skor film baru LEBIH BESAR atau SAMA DENGAN, masukkan ke sebelah KANAN
        else {
            if ($current->right === null) {
                $current->right = $newNode;
            } else {
                $this->insertNode($current->right, $newNode);
            }
        }
    }

    /**
     * REVERSE IN-ORDER TRAVERSAL (Kanan -> Akar -> Kiri)
     * Mengambil data dari Tree dari nilai terbesar ke terkecil secara rekursif
     */
    public function keArrayDescending($node, &$hasil) {
        if ($node !== null) {
            // Kunjungi cabang kanan dulu (nilai-nilai yang lebih besar)
            $this->keArrayDescending($node->right, $hasil);
            
            // Masukkan data node saat ini ke dalam array hasil
            $hasil[] = $node->film;
            
            // Kunjungi cabang kiri (nilai-nilai yang lebih kecil)
            $this->keArrayDescending($node->left, $hasil);
        }
    }
}

/**
 * FUNGSI 1: Mengambil daftar genre unik secara otomatis dari database (Linear Search).
 */
function dapatkanMasterGenre($databaseFilm) {
    $master = [];
    foreach ($databaseFilm as $film) {
        $genres = array_map('trim', explode(',', strtolower($film['genre'])));
        foreach ($genres as $g) {
            if ($g !== "") {
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
    }
    return $master;
}

/**
 * FUNGSI 2: Digunakan di index.php untuk mendapatkan daftar rekomendasi.
 * Menggunakan kombinasi Linear Search (Pencarian) dan Binary Search Tree (Pengurutan)
 */
function dapatkanRekomendasi($judulTarget, $genreTarget, $databaseFilm) {
    
    $targetGenres = array_map('trim', explode(',', strtolower($genreTarget)));
    
    // Inisialisasi Pohon Struktur Data (Tree)
    $treeRekomendasi = new RekomendasiTree();
    $adaData = false;

    // 1. LINEAR SEARCH: Hitung kecocokan genre
    foreach ($databaseFilm as $film) {
        $filmGenres = array_map('trim', explode(',', strtolower($film['genre'])));
        $jumlahMatch = 0;

        foreach ($targetGenres as $tGenre) {
            foreach ($filmGenres as $fGenre) {
                if ($tGenre === $fGenre && $tGenre !== "") {
                    $jumlahMatch++;
                    break; 
                }
            }
        }

        // 2. TREE INSERTION: Jika cocok, langsung masukkan ke dalam Tree
        if ($jumlahMatch > 0) {
            $totalGenreFilm = count($filmGenres);
            $skorDesimal = $jumlahMatch / $totalGenreFilm;
            
            $film['skor_kemiripan'] = $skorDesimal; 
            
            // Masukkan ke dalam struktur data Tree
            $treeRekomendasi->insert($film);
            $adaData = true;
        }
    }

    // 3. TREE TRAVERSAL: Ubah struktur Tree kembali menjadi Array yang berurutan secara Descending
    $hasilUrut = [];
    if ($adaData) {
        $treeRekomendasi->keArrayDescending($treeRekomendasi->root, $hasilUrut);
    }

    return $hasilUrut;
}
?>