<?php 
// --- PENGAMANAN SESI ---
// Menggunakan 1 sebagai simulasi user ID jika sesi belum diatur.
$userId = $_SESSION['user_id'] ?? 1; 

if (empty($userId)) {
    // Arahkan ke halaman login jika sesi tidak ditemukan
    header("Location: ../login.php");
    exit();
}

// Fungsi sederhana untuk format Rupiah
function formatRupiah($angka) {
    if ($angka === null) return 'Rp 0';
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// ----------------------------------------------------
// 1. AMBIL DATA KATEGORI UNTUK FILTER
// ----------------------------------------------------
$categories = [];
$cat_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($cat_result) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// ----------------------------------------------------
// 2. TANGANI INPUT FILTER
// ----------------------------------------------------
$categoryFilter = $_GET['category'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';
$priceOrder = $_GET['order'] ?? 'default';
$stockStatus = $_GET['status'] ?? 'available';
$products = [];

// ----------------------------------------------------
// 3. SUSUN QUERY DAN BINDING PARAMETER
// ----------------------------------------------------
$sql = "
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = 1 
";
$params = [];
$types = '';

// Filter Kategori
if ($categoryFilter !== 'all' && is_numeric($categoryFilter)) {
    $sql .= " AND p.category_id = ?";
    $types .= 'i';
    $params[] = (int)$categoryFilter;
}

// Filter Pencarian
if (!empty($searchQuery)) {
    $sql .= " AND p.name LIKE ?";
    $types .= 's';
    $params[] = '%' . $searchQuery . '%';
}

// Filter Status Stok
if ($stockStatus === 'available') {
    // Tampilkan hanya yang stok > 0
    $sql .= " AND p.stock > 0";
} elseif ($stockStatus === 'unavailable') {
    // Tampilkan hanya yang stok = 0
    $sql .= " AND p.stock = 0";
}
// Jika 'all', tidak ada klausa stok ditambahkan.

// Urutan Harga
$orderBy = " ORDER BY p.created_at DESC"; // Default: terbaru
if ($priceOrder === 'low') {
    $orderBy = " ORDER BY p.price ASC";
} elseif ($priceOrder === 'high') {
    $orderBy = " ORDER BY p.price DESC";
}

$sql .= $orderBy;

// ----------------------------------------------------
// 4. EKSEKUSI QUERY
// ----------------------------------------------------
$stmt = $conn->prepare($sql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$productsResult = $stmt->get_result();

if ($productsResult) {
    while ($row = $productsResult->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
// $conn->close(); // Tutup koneksi jika tidak digunakan di file include lainnya
?>