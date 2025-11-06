<?php 
// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. PENGATURAN DAN PENGAMBILAN DATA PELANGGAN
// --------------------------------------------------------------------

// --- Data Summary ---
$summary = [
'total_users' => 0,
'users_with_orders' => 0,
'total_orders_placed' => 0,
'total_revenue' => 0
];

// 1. Total Pelanggan
$sql_total_users = "SELECT COUNT(id) FROM users";
$result = $conn->query($sql_total_users);
$summary['total_users'] = $result ? $result->fetch_row()[0] : 0;

// 2. Total Pelanggan yang Pernah Order (asumsi tabel orders berelasi ke user_id)
$sql_users_with_orders = "SELECT COUNT(DISTINCT user_id) FROM orders";
$result = $conn->query($sql_users_with_orders);
$summary['users_with_orders'] = $result ? $result->fetch_row()[0] : 0;

// 3. Total Jumlah Pesanan
$sql_total_orders = "SELECT COUNT(id) FROM orders";
$result = $conn->query($sql_total_orders);
$summary['total_orders_placed'] = $result ? $result->fetch_row()[0] : 0;

// 4. Total Revenue (hanya dari pesanan yang sudah Completed)
$sql_total_revenue = "SELECT SUM(final_amount) FROM orders WHERE order_status = 'Completed'";
$result = $conn->query($sql_total_revenue);
$summary['total_revenue'] = $result ? $result->fetch_row()[0] : 0;

// 5. Total keranjang yang dimasukkan
$total_cart_items_sql = "SELECT SUM(quantity) AS total_cart_items FROM cart_items";
$total_cart_items_result = $conn->query($total_cart_items_sql);
$total_cart_items = $total_cart_items_result->fetch_assoc()['total_cart_items'] ?? 0;
$summary['total_cart_items'] = $total_cart_items;


// --- Pengaturan Tabel ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Search
$search_query_where = "";
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
if (!empty($search)) {
// Mencari berdasarkan nama atau email user
$search_query_where = " WHERE u.full_name LIKE '%$search%' OR u.email LIKE '%$search%' ";
}

// Sorting
$sort_columns = ['id', 'name', 'email', 'order_count', 'total_spent', 'registration_date'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) :
'DESC';

// Query untuk menghitung total baris yang difilter (untuk pagination)
// Ini harus menggunakan query utama agar hasil hitungan sesuai dengan filter/search
$count_sql = "
SELECT COUNT(u.id)
FROM users u
$search_query_where
";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);


// Query utama untuk mengambil data pelanggan
$sql = "
SELECT
u.id, u.full_name, u.email, u.phone_number, u.created_at as registration_date,
COUNT(o.id) as order_count,
SUM(CASE WHEN o.order_status = 'Completed' THEN o.final_amount ELSE 0 END) as total_spent,
COALESCE(SUM(ci.quantity), 0) AS cart_item_count
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
LEFT JOIN 
cart_items ci ON u.id = ci.user_id
$search_query_where
GROUP BY u.id
ORDER BY $sort $order
LIMIT $limit OFFSET $offset
";

$users_result = $conn->query($sql);
$users = [];
if ($users_result) {
while ($row = $users_result->fetch_assoc()) {
$users[] = $row;
}
}

// Fungsi bantu untuk membuat link sorting
function get_sort_link($column, $current_sort, $current_order, $current_limit, $current_search) {
$new_order = ($current_sort == $column && $current_order == 'ASC') ? 'DESC' : 'ASC';
$params = [
'sort' => $column,
'order' => $new_order,
'limit' => $current_limit,
's' => $current_search
];
return '?' . http_build_query(array_filter($params));
}
?>