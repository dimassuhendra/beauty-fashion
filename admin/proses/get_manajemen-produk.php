<?php 
$query_products = "
SELECT
p.*,
c.name AS category_name
FROM products p
JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
";
$result_products = mysqli_query($conn, $query_products);

// 3. LOGIKA UNTUK MENGAMBIL DATA KATEGORI
$query_categories = "SELECT id, name FROM categories ORDER BY name ASC";
$result_categories = mysqli_query($conn, $query_categories);

// 4. LOGIKA UNTUK MODE EDIT (Jika ada parameter 'edit_id' di URL)
$is_edit = false;
$product_data = [];
$edit_id = (int)($_GET['edit_id'] ?? 0);

if ($edit_id > 0) {
$query_edit = "SELECT * FROM products WHERE id = $edit_id";
$result_edit = mysqli_query($conn, $query_edit);
if ($product_data = mysqli_fetch_assoc($result_edit)) {
$is_edit = true;
} else {
// Jika ID tidak ditemukan, hapus parameter edit_id dari URL
header("Location: manajemen-produk.php");
exit();
}
}
?>