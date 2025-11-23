<?php
// Pastikan file ini di-include setelah db_connect.php
// $conn adalah variabel koneksi database

// Inisialisasi variabel untuk menampung data kupon
$result_coupons = null;

try {
    // Query untuk mengambil semua kupon diskon
    $sql_coupons = "SELECT * FROM coupons ORDER BY valid_until DESC, is_active DESC, created_at DESC";
    
    $result_coupons = $conn->query($sql_coupons);

    if (!$result_coupons) {
        throw new Exception("Error saat mengambil data kupon: " . $conn->error);
    }

} catch (Exception $e) {
    // Handle error (misalnya, log error dan tampilkan pesan umum)
    error_log($e->getMessage());
    // Untuk pengembangan, bisa ditampilkan:
    // echo "Terjadi kesalahan: " . $e->getMessage();
}
?>