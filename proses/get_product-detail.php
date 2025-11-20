<?php
// fetch_product_detail.php
header('Content-Type: application/json');

@require_once '../db_connect.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID Produk tidak valid.']);
    exit;
}

$product_id = (int)$_GET['id'];
$response = ['success' => false, 'data' => null, 'message' => ''];

// Asumsi $conn adalah objek koneksi database dari db_connect.php
if (isset($conn)) {
    try {
        // Query 1: Ambil detail produk dan agregat rating
        $stmt = $conn->prepare("
            SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.stock, 
                p.description, 
                p.image_url, 
                p.sku,
                c.name as category_name,
                (SELECT AVG(rating) FROM reviews WHERE product_id = p.id) as avg_rating,
                (SELECT COUNT(id) FROM reviews WHERE product_id = p.id) as total_reviews
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($product = $result->fetch_assoc()) {
            // Pembersihan data
            $product['avg_rating'] = $product['avg_rating'] ? round((float)$product['avg_rating'], 1) : 0;
            $product['total_reviews'] = (int)$product['total_reviews'];
            
            // Query 2: Ambil daftar ulasan terbaru (Maksimal 3)
            $reviews = [];
            $review_stmt = $conn->prepare("
                SELECT 
                    r.rating, 
                    r.comment_text, 
                    u.full_name as reviewer_name, -- Asumsi kolom nama pengguna adalah 'username'
                    r.created_at
                FROM reviews r
                JOIN users u ON r.user_id = u.id -- Asumsi ada tabel 'users' untuk nama reviewer
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC
                LIMIT 5
            ");
            $review_stmt->bind_param("i", $product_id);
            $review_stmt->execute();
            $review_result = $review_stmt->get_result();
            
            while ($review = $review_result->fetch_assoc()) {
                $reviews[] = $review;
            }
            $review_stmt->close();
            
            // Gabungkan ulasan ke dalam data produk
            $product['reviews'] = $reviews; 
            
            $response['success'] = true;
            $response['data'] = $product;

        } else {
            $response['message'] = 'Produk tidak ditemukan.';
        }

        $stmt->close();
    } catch (\Throwable $e) {
        $response['message'] = 'Kesalahan database.';
    }
} else {
    $response['message'] = 'Koneksi database gagal.';
}

echo json_encode($response);
?>