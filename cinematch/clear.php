<?php
session_start();
session_unset();
session_destroy();
echo "<h3>Session lama sukses dihapus bersih! Data film baru siap dimuat.</h3>";
echo "<p>Silakan buka kembali halaman utama di sini: <a href='index.php'>index.php</a></p>";
?>