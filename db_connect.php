<?php
$servername = "localhost";
$username = "root"; // Ganti dengan username MySQL Anda
$password = "";     // Ganti dengan password MySQL Anda
$dbname = "beauty"; // Ganti dengan nama database yang Anda buat

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>