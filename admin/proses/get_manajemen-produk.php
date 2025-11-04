<?php 
// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. TANGANI AKSI CRUD (Sederhana - Dalam implementasi nyata, gunakan prepared statements!)
// --------------------------------------------------------------------

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['action'])) {
$action = $_POST['action'];

// --- TANGANI TAMBAH PRODUK ---
if ($action === 'add') {
$category_id = $_POST['category_id'];
$sku = $conn->real_escape_string($_POST['sku']);
$name = $conn->real_escape_string($_POST['name']);
$price = (float)$_POST['price'];
$stock = (int)$_POST['stock'];
$description = $conn->real_escape_string($_POST['description']);
$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))); // Simple slug generation

$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
$target_dir = "../uploads/product/"; // Lokasi penyimpanan gambar
$file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
$new_file_name = $sku . '-' . time() . '.' . $file_extension;
$target_file = $target_dir . $new_file_name;

if (!is_dir($target_dir)) {
mkdir($target_dir, 0777, true);
}

if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
$image_url = $new_file_name;
}
}

$sql = "INSERT INTO products (category_id, sku, name, slug, description, price, stock, image_url, is_active)
VALUES ('$category_id', '$sku', '$name', '$slug', '$description', '$price', '$stock', '$image_url', 1)";

if ($conn->query($sql)) {
$message = "Produk <b>$name</b> berhasil ditambahkan.";
$message_type = 'success';
} else {
$message = "Error: " . $conn->error;
$message_type = 'danger';
}
}

// --- TANGANI EDIT PRODUK ---
elseif ($action === 'edit') {
$id = (int)$_POST['product_id'];
$name = $conn->real_escape_string($_POST['name']);
$price = (float)$_POST['price'];
$stock = (int)$_POST['stock'];
$is_active = isset($_POST['is_active']) ? 1 : 0;
// Di sini Anda akan mengimplementasikan logika update ke DB

// Contoh Update Query (PENTING: Gunakan Prepared Statements di Produksi)
$sql = "UPDATE products SET name='$name', price='$price', stock='$stock', is_active='$is_active' WHERE id='$id'";
if ($conn->query($sql)) {
$message = "Produk <b>$name</b> berhasil diperbarui.";
$message_type = 'success';
} else {
$message = "Error saat update: " . $conn->error;
$message_type = 'danger';
}
}

// --- TANGANI HAPUS PRODUK ---
elseif ($action === 'delete') {
$id = (int)$_POST['product_id'];
// Di sini Anda akan mengimplementasikan logika hapus dari DB

// Contoh Delete Query (PENTING: Gunakan Prepared Statements di Produksi)
$sql = "DELETE FROM products WHERE id='$id'";
if ($conn->query($sql)) {
$message = "Produk berhasil dihapus.";
$message_type = 'warning';
} else {
$message = "Error saat menghapus: " . $conn->error;
$message_type = 'danger';
}
}
}
}


// --------------------------------------------------------------------
// 3. PENGATURAN DAN PENGAMBILAN DATA PRODUK (Untuk Tabel)
// --------------------------------------------------------------------

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Search
$search_query = "";
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
if (!empty($search)) {
$search_query = " WHERE p.name LIKE '%$search%' OR p.sku LIKE '%$search%' ";
}

// Sorting
$sort_columns = ['id', 'sku', 'name', 'category_name', 'price', 'stock', 'is_active'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) :
'DESC';

// Query untuk menghitung total produk (untuk pagination)
$count_sql = "SELECT COUNT(p.id) FROM products p $search_query";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data produk
$sql = "
SELECT
p.id, p.sku, p.name, p.price, p.stock, p.is_active, p.image_url,
c.name as category_name, c.id as category_id
FROM products p
JOIN categories c ON p.category_id = c.id
$search_query
ORDER BY $sort $order
LIMIT $limit OFFSET $offset
";

$products_result = $conn->query($sql);
$products = [];
if ($products_result) {
while ($row = $products_result->fetch_assoc()) {
$products[] = $row;
}
}

// Ambil semua kategori untuk modal tambah/edit
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
if ($categories_result) {
while ($row = $categories_result->fetch_assoc()) {
$categories[] = $row;
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