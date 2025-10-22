<?php 
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name']) : ''; 

// --- 1. Ambil Data Kupon Aktif (untuk Penawaran Spesial) ---
$coupons = [];
// Menggunakan kolom 'valid_until' dari schema Anda
$sql_coupons = "SELECT coupon_code, discount_type, discount_value, minimum_purchase 
                FROM coupons 
                WHERE is_active = 1 AND valid_until >= CURDATE()
                ORDER BY discount_value DESC 
                LIMIT 6";
$result_coupons = $conn->query($sql_coupons);

if ($result_coupons && $result_coupons->num_rows > 0) {
    while ($row = $result_coupons->fetch_assoc()) {
        $coupons[] = $row;
    }
}


// --- 2. Ambil Data Produk Populer (Contoh: 6 Produk Terbaru) ---
$popular_products = [];
// Menggunakan JOIN antara products dan categories, diurutkan berdasarkan 'created_at' (terbaru)
$sql_popular = "SELECT p.name, p.price, p.image_url, p.slug, c.name AS category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 
                ORDER BY p.created_at DESC 
                LIMIT 6"; 
$result_popular = $conn->query($sql_popular);

if ($result_popular && $result_popular->num_rows > 0) {
    while ($row = $result_popular->fetch_assoc()) {
        $popular_products[] = $row;
    }
}

// --- 3. Ambil Data Koleksi Produk Tersedia (Contoh: 10 Produk Acak) ---
$collection_products = [];
$sql_collection = "SELECT name, price, image_url, slug 
                   FROM products 
                   WHERE is_active = 1 
                   ORDER BY RAND() 
                   LIMIT 10";
$result_collection = $conn->query($sql_collection);

if ($result_collection && $result_collection->num_rows > 0) {
    while ($row = $result_collection->fetch_assoc()) {
        $collection_products[] = $row;
    }
}

// Format harga
function format_rupiah($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

?>