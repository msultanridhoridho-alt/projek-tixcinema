<?php
// rekomendasi_engine.php

/* =================================================================
   1. MATERI: LINKED LIST
   Digunakan untuk menampung hasil pencarian secara dinamis
================================================================= */
class NodeLinkedList {
    public $data;
    public $next = null;

    public function __construct($data) {
        $this->data = $data;
    }
}

class PencarianLinkedList {
    public $head = null;

    public function tambahData($data) {
        $newNode = new NodeLinkedList($data);
        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }

    // Mengubah Linked List kembali ke Array agar bisa dibaca HTML/Frontend
    public function keArray() {
        $hasil = [];
        $current = $this->head;
        while ($current !== null) {
            $hasil[] = $current->data;
            $current = $current->next;
        }
        return $hasil;
    }
}

/* =================================================================
   2. MATERI: BINARY HEAP (MAX-HEAP) & REKURSIF
   Digunakan untuk mengurutkan (Sorting) Rekomendasi Film
================================================================= */
class MaxHeap {
    public $heap = [];

    public function insert($film) {
        $this->heap[] = $film;
        $this->heapifyUp(count($this->heap) - 1);
    }

    // Algoritma Rekursif untuk menaikkan nilai terbesar ke atas
    private function heapifyUp($index) {
        // Base case: Berhenti jika sudah mencapai root atau index tidak valid
        if ($index <= 0) return; 

        // Paksa (casting) hasil floor menjadi Integer murni untuk menghindari error tipe data
        $parentIndex = (int) floor(($index - 1) / 2);

        if ($this->heap[$parentIndex]['skor_kemiripan'] < $this->heap[$index]['skor_kemiripan']) {
            // Tukar posisi (Swap)
            $temp = $this->heap[$parentIndex];
            $this->heap[$parentIndex] = $this->heap[$index];
            $this->heap[$index] = $temp;
            
            // Panggil fungsi secara rekursif ke level yang lebih tinggi
            $this->heapifyUp($parentIndex);
        }
    }

    public function extractMax() {
        if (count($this->heap) === 0) return null;
        if (count($this->heap) === 1) return array_pop($this->heap);

        $max = $this->heap[0];
        // Pindahkan elemen terakhir ke root, lalu turunkan
        $this->heap[0] = array_pop($this->heap);
        $this->heapifyDown(0);

        return $max;
    }

    // Algoritma Rekursif untuk menata ulang heap setelah Root diambil
    private function heapifyDown($index) {
        $index = (int) $index;
        $left = 2 * $index + 1;
        $right = 2 * $index + 2;
        $largest = $index;
        $panjang = count($this->heap);

        if ($left < $panjang && $this->heap[$left]['skor_kemiripan'] > $this->heap[$largest]['skor_kemiripan']) {
            $largest = $left;
        }
        if ($right < $panjang && $this->heap[$right]['skor_kemiripan'] > $this->heap[$largest]['skor_kemiripan']) {
            $largest = $right;
        }

        if ($largest !== $index) {
            // Tukar posisi (Swap)
            $temp = $this->heap[$index];
            $this->heap[$index] = $this->heap[$largest];
            $this->heap[$largest] = $temp;
            
            // Panggil fungsi secara rekursif ke bawah
            $this->heapifyDown($largest);
        }
    }
}

/* =================================================================
   3. MATERI: HASH TABLE
   Digunakan untuk menyimpan genre unik tanpa perlu perulangan Linear
================================================================= */
class GenreHashTable {
    private $table = [];
    private $ukuran = 20; // Ukuran bucket hash

    // Fungsi Hashing sederhana (Modulo dari nilai ASCII)
    private function fungsiHash($string) {
        $hash = 0;
        $panjang = strlen($string);
        for ($i = 0; $i < $panjang; $i++) {
            $hash += ord(substr($string, $i, 1));
        }
        return $hash % $this->ukuran;
    }

    public function tambahGenre($genre) {
        $index = $this->fungsiHash($genre);
        
        // Buat bucket (array) jika belum ada (menangani Collision/Tabrakan)
        if (!isset($this->table[$index])) {
            $this->table[$index] = [];
        }

        // Cek apakah genre sudah ada di bucket tersebut
        foreach ($this->table[$index] as $g) {
            if ($g === $genre) return; // Sudah ada, tidak perlu dimasukkan
        }

        $this->table[$index][] = $genre;
    }

    public function ambilSemuaGenre() {
        $semuaGenre = [];
        foreach ($this->table as $bucket) {
            foreach ($bucket as $genre) {
                $semuaGenre[] = $genre;
            }
        }
        return $semuaGenre;
    }
}

/* =================================================================
   4. MATERI: REKURSIF (PENCARIAN STRING)
   Pengganti Brute-Force String Matching biasa
================================================================= */

// Fungsi Bantuan 1: Mengecek kecocokan karakter secara rekursif
function cekKarakterRekursif($teks, $pola, $it, $ip) {
    if ($ip === strlen($pola)) return true;  // Base Case 1: Pola cocok semua
    if ($it === strlen($teks)) return false; // Base Case 2: Teks habis, pola belum selesai
    
    if (substr($teks, $it, 1) === substr($pola, $ip, 1)) {
        return cekKarakterRekursif($teks, $pola, $it + 1, $ip + 1);
    }
    return false;
}

// Fungsi Bantuan 2: Menggeser indeks pencarian pada teks secara rekursif
function pencarianStringRekursif($teks, $pola, $indexTeks) {
    $panjangTeks = strlen($teks);
    $panjangPola = strlen($pola);

    // Base Case 3: Indeks sudah melebihi batas kemungkinan cocok
    if ($indexTeks > $panjangTeks - $panjangPola) return false; 

    // Jika cocok dari indeks saat ini, kembalikan true
    if (cekKarakterRekursif($teks, $pola, $indexTeks, 0)) {
        return true;
    }

    // Panggil diri sendiri dengan menggeser indeks ke kanan (+1)
    return pencarianStringRekursif($teks, $pola, $indexTeks + 1);
}

/* =================================================================
   FUNGSI BANTUAN DASAR (PARSING ARRAY KATA)
================================================================= */
function pisahStringManual($string, $delimiter = ',') {
    $hasil = [];
    $kataSementara = "";
    $panjang = strlen($string);
    for ($i = 0; $i < $panjang; $i++) {
        $char = substr($string, $i, 1);
        if ($char === $delimiter) {
            if (trim($kataSementara) !== "") $hasil[] = trim($kataSementara);
            $kataSementara = ""; 
        } else {
            $kataSementara .= $char;
        }
    }
    if (trim($kataSementara) !== "") $hasil[] = trim($kataSementara);
    return $hasil;
}

/* =================================================================
   PENERAPAN ALGORITMA PADA FUNGSI UTAMA UNTUK FRONTEND
================================================================= */

// MENGGUNAKAN HASH TABLE
function dapatkanMasterGenre($databaseFilm) {
    $hashTable = new GenreHashTable();
    foreach ($databaseFilm as $film) {
        $genres = pisahStringManual(strtolower($film['genre']));
        foreach ($genres as $g) {
            $hashTable->tambahGenre($g); 
        }
    }
    return $hashTable->ambilSemuaGenre();
}

// MENGGUNAKAN BINARY MAX-HEAP
function dapatkanRekomendasi($genreTarget, $databaseFilm) {
    $targetGenres = pisahStringManual(strtolower($genreTarget));
    $maxHeap = new MaxHeap();
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
            $skorDesimal = $jumlahMatch / count($filmGenres);
            $film['skor_kemiripan'] = $skorDesimal; 
            $maxHeap->insert($film);
            $adaData = true;
        }
    }

    $hasilUrut = [];
    if ($adaData) {
        // Ambil elemen Root satu per satu agar otomatis terurut dari terbesar
        while (($film = $maxHeap->extractMax()) !== null) {
            $hasilUrut[] = $film;
        }
    }

    return $hasilUrut;
}

// MENGGUNAKAN LINKED LIST & REKURSIF PENCARIAN
function cariFilmBerdasarkanJudul($kataKunci, $databaseFilm) {
    $linkedList = new PencarianLinkedList();
    $kataKunci = strtolower(trim($kataKunci));
    
    if ($kataKunci === "") return [];

    foreach ($databaseFilm as $film) {
        $judulFilm = strtolower(trim($film['judul']));
        
        // Memanggil fungsi REKURSIF mulai dari indeks 0
        if (pencarianStringRekursif($judulFilm, $kataKunci, 0)) {
            $linkedList->tambahData($film);
        }
    }

    return $linkedList->keArray();
}
?>