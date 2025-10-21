<?php
// Pengaturan Koneksi Database
$host = "localhost"; // Biasanya 'localhost'
$user = "root";      // Ganti dengan username database Anda
$pass = "";          // Ganti dengan password database Anda
$db   = "beauty";    // Nama database Anda dari SQL dump

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Menampilkan pesan error dan menghentikan eksekusi jika koneksi gagal
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Fungsi untuk format mata uang Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi untuk mendapatkan badge status pesanan
function getStatusBadge($status) {
    switch ($status) {
        case 'Pending Payment':
            return '<span class="badge bg-warning">Menunggu Pembayaran</span>';
        case 'Processing':
            return '<span class="badge bg-info">Sedang Diproses</span>';
        case 'Shipped':
            return '<span class="badge bg-success">Dikirim</span>';
        case 'Completed':
            return '<span class="badge bg-primary">Selesai</span>';
        case 'Cancelled':
            return '<span class="badge bg-danger">Dibatalkan</span>';
        default:
            return '<span class="badge bg-secondary">' . $status . '</span>';
    }
}
?>