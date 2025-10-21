<?php
// Pastikan db_connect.php sudah memuat koneksi $conn
if (!isset($conn)) {
    // Jika belum, ini akan memicu error. Pastikan require_once '../db_connect.php' dipanggil di file utama.
}

// 1. Ambil data semua Pesanan
// JOIN dengan tabel users dan shipping_addresses untuk detail pelanggan dan alamat
$sql_orders = "
    SELECT 
        o.id AS order_id, 
        o.order_code, 
        o.order_date, 
        o.final_amount, 
        o.payment_method, 
        o.order_status, 
        u.full_name AS customer_name,
        sa.city AS shipping_city
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN shipping_addresses sa ON o.shipping_address_id = sa.id
    ORDER BY o.order_date DESC
";

$result_orders = mysqli_query($conn, $sql_orders);

// Handle error (opsional)
if (!$result_orders) {
    // Di lingkungan produksi, ini diganti dengan logging yang aman.
    // die("Query Error: " . mysqli_error($conn)); 
}

// 2. Ambil semua status unik untuk keperluan filter dropdown
$sql_statuses = "SELECT DISTINCT order_status FROM orders ORDER BY order_status ASC";
$result_statuses = mysqli_query($conn, $sql_statuses);
?>