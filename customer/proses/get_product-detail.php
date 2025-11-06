<?php
// Pastikan Anda telah menyertakan db_connect.php yang berisi koneksi ($conn)
include '../../db_connect.php'; 

header('Content-Type: application/json');

// Cek apakah product ID diberikan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required.']);
    exit;
}

$productId = (int)$_GET['id'];

// Query untuk mengambil SEMUA detail produk, termasuk kategori
// Asumsi: products.category_id berelasi dengan categories.id
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();
$conn->close();

if ($product) {
    // Format harga
    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) {
            if ($angka === null) return 'Rp 0';
            return 'Rp ' . number_format($angka, 0, ',', '.');
        }
    }
    $product['price_formatted'] = formatRupiah($product['price']);
    
    // Siapkan respons sukses
    echo json_encode(['success' => true, 'product' => $product]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
}
?>