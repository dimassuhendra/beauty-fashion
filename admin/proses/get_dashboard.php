<?php
// admin/proses/get_dashboard.php

// Pastikan koneksi $conn sudah tersedia dari '../db_connect.php'
if (!isset($conn) || $conn->connect_error) {
    // Jika koneksi gagal, set $conn menjadi null untuk mencegah error
    $conn = null;
}

// --------------------------------------------------------------------
// 1. FILTER TANGGAL
// --------------------------------------------------------------------
$filter_sql = "";
// Tangkap parameter tanggal dari URL
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

if ($start_date && $end_date) {
    // FIX: Menggunakan kolom 'order_date' dari tabel orders
    $filter_sql = " WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date' ";
} else if ($start_date) {
    $filter_sql = " WHERE DATE(order_date) >= '$start_date' ";
} else if ($end_date) {
    $filter_sql = " WHERE DATE(order_date) <= '$end_date' ";
}


// --------------------------------------------------------------------
// 2. FUNGSI UTAMA PENGAMBILAN DATA
// --------------------------------------------------------------------

function fetchData($conn, $sql) {
    if (!$conn) return 0;
    
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_row();
        return $row[0] ?? 0;
    }
    return 0;
}

function fetchArrayData($conn, $sql) {
    if (!$conn) return [];
    
    $result = $conn->query($sql);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

// --------------------------------------------------------------------
// 3. PENGAMBILAN DATA UNTUK CARD
// --------------------------------------------------------------------
// Data non-order (tidak terpengaruh filter tanggal)
$total_products = fetchData($conn, "SELECT COUNT(id) FROM products");
$total_users = fetchData($conn, "SELECT COUNT(id) FROM users");
$total_categories = fetchData($conn, "SELECT COUNT(id) FROM categories");

// Data Order yang terfilter
// NOTE: total_orders sekarang hanya menghitung pesanan dalam rentang tanggal filter, 
// TANPA mempertimbangkan status.
$total_orders = fetchData($conn, "SELECT COUNT(id) FROM orders" . $filter_sql);


// Mengambil data dengan filter status. Jika $filter_sql ada, tambahkan klausa AND
$pending_orders = fetchData($conn, "SELECT COUNT(id) FROM orders " . (empty($filter_sql) ? "WHERE" : $filter_sql . " AND") . " order_status = 'Menunggu Pembayaran'");

// Total Pendapatan HANYA dari Completed Orders
$total_revenue_sql = "SELECT SUM(final_amount) FROM orders " . (empty($filter_sql) ? "WHERE" : $filter_sql . " AND") . " order_status = 'Selesai'";
$total_revenue_result = fetchData($conn, $total_revenue_sql);
$total_revenue = "Rp" . number_format($total_revenue_result, 0, ',', '.');


// --------------------------------------------------------------------
// 4. DATA UNTUK DONUT CHART (SELURUH STATUS PESANAN BARU)
// --------------------------------------------------------------------
$sql_order_status = "
    SELECT 
        order_status, 
        COUNT(id) AS status_count 
    FROM orders 
    " . $filter_sql . "
    GROUP BY order_status
    -- Urutan Status (Selesai, Dikirim, Diproses, Menunggu Pembayaran, Dibatalkan)
    ORDER BY FIELD(order_status, 'Selesai', 'Dikirim', 'Diproses', 'Menunggu Pembayaran', 'Dibatalkan')
";

$order_status_data_array = fetchArrayData($conn, $sql_order_status);

$order_status_labels = array_column($order_status_data_array, 'order_status');
$order_status_data = array_column($order_status_data_array, 'status_count');

// Mengambil Completed count untuk Card/Center Text Donut
$completed_orders = 0;
foreach ($order_status_data_array as $item) {
    if ($item['order_status'] == 'Selesai') {
        $completed_orders = (int)$item['status_count'];
        break;
    }
}


// Konversi data PHP ke format JSON agar bisa dibaca oleh JavaScript (Chart.js)
$json_order_status_labels = json_encode($order_status_labels);
$json_order_status_data = json_encode(array_map('intval', $order_status_data));


// --------------------------------------------------------------------
// 5. DATA UNTUK PIE CHART (Kategori Terlaris)
// --------------------------------------------------------------------
// Query untuk Pie Chart menggunakan tabel order_details (sesuai skema database)
// Catatan: Jika Anda ingin memfilter hanya Completed Orders, tambahkan " AND o.order_status = 'Selesai'"
$sql_category_sales = "
    SELECT 
        c.name AS category_name, 
        SUM(od.quantity) AS total_sold 
    FROM orders o
    JOIN order_details od ON o.id = od.order_id 
    JOIN products p ON od.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    " . (empty($filter_sql) ? "" : $filter_sql) . "
    GROUP BY c.name
    ORDER BY total_sold DESC
    LIMIT 5
";

$category_sales = fetchArrayData($conn, $sql_category_sales);

$category_labels = array_column($category_sales, 'category_name');
$category_data = array_column($category_sales, 'total_sold');

// Konversi data PHP ke format JSON agar bisa dibaca oleh JavaScript (Chart.js)
$json_category_labels = json_encode($category_labels);
$json_category_data = json_encode(array_map('intval', $category_data));


// --------------------------------------------------------------------
// 6. BARU: DATA UNTUK LINE CHART (Tren Penjualan Bulanan)
// --------------------------------------------------------------------

// Jika filter tanggal tidak spesifik (tidak disetel), gunakan 6 bulan terakhir.
// Jika filter disetel, gunakan grouping per bulan atau per hari tergantung rentang.
// Di sini kita asumsikan untuk filter default akan menampilkan per bulan
$sql_monthly_sales = "
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') AS order_month,
        SUM(final_amount) AS monthly_revenue
    FROM orders
    WHERE order_status = 'Selesai'
    " . $filter_sql . "
    GROUP BY order_month
    ORDER BY order_month ASC
";

// Jika filter tanggal tidak disetel, limit ke 6 bulan terakhir
if (empty($filter_sql)) {
    // Ambil 6 bulan terakhir secara default
    $sql_monthly_sales = "
        SELECT
            DATE_FORMAT(order_date, '%Y-%m') AS order_month,
            SUM(final_amount) AS monthly_revenue
        FROM orders
        WHERE order_status = 'Selesai' AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY order_month
        ORDER BY order_month ASC
    ";
}


$monthly_sales_data = fetchArrayData($conn, $sql_monthly_sales);

// Format label bulan ke nama yang lebih mudah dibaca (e.g., '2025-01' -> 'Jan 25')
$monthly_sales_labels = [];
$monthly_sales_revenue = [];
foreach ($monthly_sales_data as $row) {
    // Mengubah format YYYY-MM menjadi Mmm YY
    $timestamp = strtotime($row['order_month'] . '-01');
    $formatted_label = date('M Y', $timestamp);
    $monthly_sales_labels[] = $formatted_label;
    $monthly_sales_revenue[] = $row['monthly_revenue'];
}

$json_monthly_sales_labels = json_encode($monthly_sales_labels);
$json_monthly_sales_revenue = json_encode(array_map('intval', $monthly_sales_revenue));


// --------------------------------------------------------------------
// 7. BARU: DATA UNTUK BAR CHART (Top 5 Produk Terlaris)
// --------------------------------------------------------------------
$sql_top_products = "
    SELECT
        p.name AS product_name,
        SUM(od.quantity) AS total_sold
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
    WHERE o.order_status = 'Selesai' -- Hanya hitung dari pesanan selesai
    " . (empty($filter_sql) ? "" : $filter_sql . " AND o.order_status = 'Selesai'") . "
    GROUP BY p.name
    ORDER BY total_sold DESC
    LIMIT 5
";

$top_products_data = fetchArrayData($conn, $sql_top_products);

$top_product_labels = array_column($top_products_data, 'product_name');
$top_product_data = array_column($top_products_data, 'total_sold');

$json_top_product_labels = json_encode($top_product_labels);
$json_top_product_data = json_encode(array_map('intval', $top_product_data));

?>
