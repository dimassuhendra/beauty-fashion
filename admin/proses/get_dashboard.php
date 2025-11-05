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
$total_orders = fetchData($conn, "SELECT COUNT(id) FROM orders" . $filter_sql);


// Mengambil data dengan filter status. Jika $filter_sql ada, tambahkan klausa AND
$pending_orders = fetchData($conn, "SELECT COUNT(id) FROM orders " . (empty($filter_sql) ? "WHERE" : $filter_sql . " AND") . " order_status = 'Pending Payment'");

// Total Pendapatan
$total_revenue_sql = "SELECT SUM(final_amount) FROM orders " . (empty($filter_sql) ? "WHERE" : $filter_sql . " AND") . " order_status = 'Completed'";
$total_revenue_result = fetchData($conn, $total_revenue_sql);
$total_revenue = "Rp" . number_format($total_revenue_result, 0, ',', '.');


// --------------------------------------------------------------------
// 4. DATA UNTUK DONUT CHART (Persentase Pesanan Selesai)
// --------------------------------------------------------------------
$completed_orders = fetchData($conn, "SELECT COUNT(id) FROM orders " . (empty($filter_sql) ? "WHERE" : $filter_sql . " AND") . " order_status = 'Completed'");

// Persiapan data untuk Chart.js (completed, not completed)
$json_order_completion_data = json_encode([
    (int)$completed_orders, 
    (int)$total_orders - (int)$completed_orders // Sisanya (Pesanan yang tidak Completed)
]);


// --------------------------------------------------------------------
// 5. DATA UNTUK PIE CHART (Kategori Terlaris)
// --------------------------------------------------------------------
// Query untuk Pie Chart menggunakan tabel order_details (sesuai skema database)
$sql_category_sales = "
    SELECT 
        c.name AS category_name, 
        SUM(od.quantity) AS total_sold 
    FROM orders o
    JOIN order_details od ON o.id = od.order_id -- FIX: Menggunakan order_details
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

?>