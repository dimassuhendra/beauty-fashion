<?php
// admin/proses/get_dashboard.php

// Pastikan koneksi $conn sudah tersedia dari '../db_connect.php'
if (!isset($conn) || $conn->connect_error) {
    // Jika koneksi gagal, set $conn menjadi null untuk mencegah error
    $conn = null;
}

// --------------------------------------------------------------------
## 1. Filter Tanggal & Klausa WHERE
// --------------------------------------------------------------------
$filter_date_clause = "";
$where_separator = " WHERE "; // Default separator adalah WHERE

// Tangkap parameter tanggal dari URL
$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

if ($start_date && $end_date) {
    // FIX: Menggunakan klausa order_date
    $filter_date_clause = " DATE(order_date) BETWEEN '$start_date' AND '$end_date' ";
} else if ($start_date) {
    $filter_date_clause = " DATE(order_date) >= '$start_date' ";
} else if ($end_date) {
    $filter_date_clause = " DATE(order_date) <= '$end_date' ";
}

// $full_filter_sql sekarang mengandung klausa WHERE hanya jika ada filter tanggal.
$full_filter_sql = !empty($filter_date_clause) ? $where_separator . $filter_date_clause : "";

// Fungsi untuk menggabungkan klausa (menggantikan logika WHERE/AND yang kompleks)
function combine_filter($filter_sql, $new_condition)
{
    if (empty($filter_sql)) {
        return " WHERE " . $new_condition;
    }
    // Jika filter_sql sudah ada (berarti sudah ada WHERE), tambahkan AND
    return $filter_sql . " AND " . $new_condition;
}

// --------------------------------------------------------------------
## 2. Fungsi Utama Pengambilan Data (TIDAK ADA PERUBAHAN)
// --------------------------------------------------------------------

function fetchData($conn, $sql)
{
    if (!$conn)
        return 0;

    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_row();
        // Menggunakan number_format di sini untuk memastikan angka yang dikembalikan adalah format numerik murni
        return $row[0] ?? 0;
    }
    return 0;
}

function fetchArrayData($conn, $sql)
{
    if (!$conn)
        return [];

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
## 3. Pengambilan Data untuk Card (LOGIKA FILTER DIPERBAIKI)
// --------------------------------------------------------------------
// Data non-order (tidak terpengaruh filter tanggal)
$total_products = fetchData($conn, "SELECT COUNT(id) FROM products");
$total_users = fetchData($conn, "SELECT COUNT(id) FROM users");
$total_categories = fetchData($conn, "SELECT COUNT(id) FROM categories");

// Data Order yang terfilter
// Total Orders dalam rentang tanggal
$total_orders = fetchData($conn, "SELECT COUNT(id) FROM orders" . $full_filter_sql);


// Perbaikan: Menggunakan fungsi combine_filter
$pending_orders_condition = " order_status = 'Menunggu Pembayaran'";
$pending_orders_sql = combine_filter($full_filter_sql, $pending_orders_condition);

$pending_orders = fetchData($conn, "SELECT COUNT(id) FROM orders " . $pending_orders_sql);


// Total Pendapatan HANYA dari Completed Orders
$completed_condition = " order_status = 'Selesai'";
$total_revenue_sql_combined = combine_filter($full_filter_sql, $completed_condition);

$total_revenue_sql = "SELECT SUM(final_amount) FROM orders " . $total_revenue_sql_combined;
$total_revenue_result = fetchData($conn, $total_revenue_sql);

// Format Rupiah di sini (setelah fetch, bukan di dalam query)
$total_revenue = "Rp" . number_format($total_revenue_result, 0, ',', '.');


// --------------------------------------------------------------------
## 4. Data untuk Donut Chart (TIDAK ADA PERUBAHAN SIGNIFIKAN)
// --------------------------------------------------------------------
$sql_order_status = "
    SELECT 
        order_status, 
        COUNT(id) AS status_count 
    FROM orders 
    " . $full_filter_sql . "
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
        $completed_orders = (int) $item['status_count'];
        break;
    }
}


// Konversi data PHP ke format JSON agar bisa dibaca oleh JavaScript (Chart.js)
$json_order_status_labels = json_encode($order_status_labels);
$json_order_status_data = json_encode(array_map('intval', $order_status_data));


// --------------------------------------------------------------------
## 5. Data untuk Pie Chart (Kategori Terlaris)
// --------------------------------------------------------------------
// Catatan: Query ini akan menghitung SEMUA order dalam rentang tanggal, tidak peduli statusnya.
// Jika ingin hanya Completed Orders, tambahkan "AND o.order_status = 'Selesai'" di klausa filter.
$sql_category_sales = "
    SELECT 
        c.name AS category_name, 
        SUM(od.quantity) AS total_sold 
    FROM orders o
    JOIN order_details od ON o.id = od.order_id 
    JOIN products p ON od.product_id = p.id
    JOIN categories c ON p.category_id = c.id
    " . $full_filter_sql . "
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
## 6. Data untuk Line Chart (Tren Penjualan Bulanan)
// --------------------------------------------------------------------
// Logika filter bulanan/6 bulan tetap sama.
// Perhatikan: $filter_date_clause digunakan untuk menguji keberadaan filter
$default_monthly_filter = "";
if (empty($filter_date_clause)) {
    // Ambil 6 bulan terakhir secara default jika tidak ada filter tanggal spesifik
    $default_monthly_filter = " AND order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) ";
}
$completed_status_filter = " order_status = 'Selesai' ";

// Membuat klausa WHERE utama yang mencakup filter status Selesai
$line_chart_where = " WHERE " . $completed_status_filter . $default_monthly_filter;

// Jika ada filter tanggal spesifik, gunakan fungsi combine_filter dengan $full_filter_sql
if (!empty($filter_date_clause)) {
    $line_chart_where = combine_filter($full_filter_sql, $completed_status_filter);
}


$sql_monthly_sales = "
    SELECT
        DATE_FORMAT(order_date, '%Y-%m') AS order_month,
        SUM(final_amount) AS monthly_revenue
    FROM orders
    " . $line_chart_where . "
    GROUP BY order_month
    ORDER BY order_month ASC
";

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
## 7. Data untuk Bar Chart (Top 5 Produk Terlaris) - LOGIKA FILTER DIPERBAIKI
// --------------------------------------------------------------------
// Filter status Selesai
$bar_chart_where = combine_filter($full_filter_sql, " o.order_status = 'Selesai' ");

$sql_top_products = "
    SELECT
        p.name AS product_name,
        SUM(od.quantity) AS total_sold
    FROM order_details od
    JOIN orders o ON od.order_id = o.id
    JOIN products p ON od.product_id = p.id
    " . $bar_chart_where . " -- Menggunakan klausa WHERE yang digabungkan
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