<?php 
$start_of_month = date('Y-m-01 00:00:00');
$end_of_month   = date('Y-m-t 23:59:59');

// Mendapatkan tanggal hari ini untuk metrik "Pelanggan Baru (Hari Ini)"
$start_of_day = date('Y-m-d 00:00:00');
$end_of_day   = date('Y-m-d 23:59:59');

// --- 2. Pengambilan Data Metrik Utama ---

// A. Total Penjualan (Bulan Ini) - Menggunakan final_amount dari orders yang sudah completed/shipped
$sql_sales = "SELECT SUM(final_amount) AS total_sales FROM orders WHERE (order_status = 'Completed' OR order_status = 'Shipped') AND order_date BETWEEN '$start_of_month' AND '$end_of_month'";
$result_sales = $conn->query($sql_sales);
$total_sales_this_month = $result_sales->fetch_assoc()['total_sales'] ?? 0;

// B. Pesanan Baru (Pending) - Menghitung jumlah pesanan dengan status 'Pending Payment'
$sql_pending_orders = "SELECT COUNT(id) AS count_pending FROM orders WHERE order_status = 'Pending Payment'";
$result_pending_orders = $conn->query($sql_pending_orders);
$count_pending_orders = $result_pending_orders->fetch_assoc()['count_pending'] ?? 0;

// C. Total Produk Terdaftar - Menghitung total produk aktif
$sql_total_products = "SELECT COUNT(id) AS count_products FROM products WHERE is_active = 1";
$result_total_products = $conn->query($sql_total_products);
$count_total_products = $result_total_products->fetch_assoc()['count_products'] ?? 0;

// D. Pelanggan Baru (Hari Ini) - Menghitung jumlah user yang register hari ini
$sql_new_users = "SELECT COUNT(id) AS count_new_users FROM users WHERE created_at BETWEEN '$start_of_day' AND '$end_of_day'";
$result_new_users = $conn->query($sql_new_users);
$count_new_users_today = $result_new_users->fetch_assoc()['count_new_users'] ?? 0;

// --- 3. Pengambilan Data Pesanan Terbaru ---

// Mengambil 5 pesanan terbaru yang BELUM selesai atau dibatalkan, diurutkan dari yang terbaru
$sql_recent_orders = "
    SELECT 
        o.order_code, 
        o.final_amount, 
        o.order_status, 
        o.order_date, 
        u.full_name AS customer_name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.order_status NOT IN ('Completed', 'Cancelled')
    ORDER BY o.order_date DESC
    LIMIT 5";
$recent_orders = $conn->query($sql_recent_orders);
?>