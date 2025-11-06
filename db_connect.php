<?php
// db_connect.php (Pastikan kode Anda mirip seperti ini)

$servername = "localhost";
$username = "root";
$password = ""; // Cek ini!
$dbname = "beauty-fashion";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    // Jika gagal, pastikan Anda menggunakan die()
    // Ini akan menghentikan skrip dan menampilkan error.
    die("Koneksi gagal: " . $conn->connect_error);
}

// Jika sukses, skrip berlanjut, dan variabel $conn tersedia.
?>