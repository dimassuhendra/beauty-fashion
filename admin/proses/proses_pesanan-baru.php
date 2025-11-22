<?php 
// FILE: proses/proses_pesanan-baru.php

// 1. Pengecekan Koneksi
// Koneksi $conn seharusnya sudah tersedia karena di-include oleh pesanan-baru.php.
// Jika diakses terpisah atau ada masalah, koneksi akan dicek.
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. TANGANI AKSI UBAH STATUS PESANAN
// --------------------------------------------------------------------

$message = '';
$message_type = '';

// Cek apakah ini adalah request POST untuk update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {

    // Ambil dan bersihkan input
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);

    if ($order_id > 0) {
        // PENTING: Gunakan Prepared Statement di lingkungan produksi
        $sql = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";

        if ($conn->query($sql)) {
            $message = "Status Pesanan #$order_id berhasil diperbarui menjadi <b>$new_status</b>.";
            $message_type = 'success';
        } else {
            $message = "Error saat memperbarui status: " . $conn->error;
            $message_type = 'danger';
        }
    }
}


// --------------------------------------------------------------------
// 3. PENGATURAN DAN PENGAMBILAN DATA PESANAN (Untuk Tabel & Summary)
// --------------------------------------------------------------------

// Status yang akan di-monitor
$statuses_to_monitor = [
    'Menunggu Pembayaran' => ['icon' => 'fas fa-clock', 'color' => 'warning'],
    'Diproses' => ['icon' => 'fas fa-cogs', 'color' => 'info'],
    'Dikirim' => ['icon' => 'fas fa-truck', 'color' => 'primary'],
    'Selesai' => ['icon' => 'fas fa-check-circle', 'color' => 'success'],
    'Dibatalkan' => ['icon' => 'fas fa-times-circle', 'color' => 'danger']
];

// Ambil jumlah pesanan per status
$status_counts = [];
foreach (array_keys($statuses_to_monitor) as $status) {
    $sql_count = "SELECT COUNT(id) FROM orders WHERE order_status = '$status'";
    $result_count = $conn->query($sql_count);
    $status_counts[$status] = $result_count ? $result_count->fetch_row()[0] : 0;
}


// --- Pengaturan Tabel ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Search
$search_query = "";
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
if (!empty($search)) {
    // Mencari berdasarkan kode pesanan atau nama user
    $search_query = " WHERE o.order_code LIKE '%$search%' OR u.name LIKE '%$search%' ";
}

// Filtering by Status
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
if (!empty($filter_status)) {
    // Tambahkan WHERE jika belum ada search_query, atau tambahkan AND
    if (empty($search_query)) {
        $search_query = " WHERE o.order_status = '$filter_status' ";
    } else {
        $search_query .= " AND o.order_status = '$filter_status' ";
    }
}

// Sorting
$sort_columns = ['id', 'order_code', 'name', 'final_amount', 'order_status', 'order_date'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

// Query untuk menghitung total pesanan (untuk pagination)
$count_sql = "
    SELECT COUNT(o.id)
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    $search_query
";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data pesanan
$sql = "
    SELECT
        o.id, o.order_code, o.final_amount, o.order_status, o.order_date, o.payment_method,
        u.full_name as user_name, u.email as user_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    $search_query
    ORDER BY $sort $order
    LIMIT $limit OFFSET $offset
";
$orders_result = $conn->query($sql);
$orders = [];
if ($orders_result) {
    while ($row = $orders_result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Fungsi bantu untuk membuat link sorting
function get_sort_link($column, $current_sort, $current_order, $current_limit, $current_search, $current_status) {
    $new_order = ($current_sort == $column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $params = [
        'sort' => $column,
        'order' => $new_order,
        'limit' => $current_limit,
        's' => $current_search,
        'status' => $current_status
    ];
    return '?' . http_build_query(array_filter($params));
}

// Fungsi bantu untuk membuat link filter status
function get_status_link($status, $current_limit, $current_search) {
    $params = [
        'limit' => $current_limit,
        's' => $current_search,
        'status' => $status
    ];
    return '?' . http_build_query(array_filter($params));
}
?>