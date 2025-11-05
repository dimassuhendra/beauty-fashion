<?php
// ====================================================================
// FILE: add_to_cart.php
// Bertanggung jawab untuk menambah/mengupdate item di tabel cart_items
// ====================================================================

session_start();

// 1. Sertakan Koneksi Database
// Sesuaikan path ke file db_connect.php Anda
include '../../db_connect.php'; 

// 2. Tentukan User ID (Pastikan user sudah login)
// Ganti dengan mekanisme otentikasi riil Anda
$userId = $_SESSION['user_id'] ?? 1; // Menggunakan ID 1 sebagai default untuk demo

// 3. Set Header Response (Untuk komunikasi AJAX)
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Gagal', 'cart_count' => 0];

// 4. Ambil data dari request POST (AJAX)
// Menggunakan file_get_contents dan json_decode untuk membaca data JSON dari AJAX
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$productId = (int)($data['product_id'] ?? 0);
$quantity = (int)($data['quantity'] ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    $response['message'] = 'ID Produk atau Kuantitas tidak valid.';
} else {
    // Cek apakah produk sudah ada di keranjang user
    $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Jika produk sudah ada: UPDATE kuantitas (tambah 1)
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        
        $updateStmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newQuantity, $row['id']);
        
        if ($updateStmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Kuantitas produk di keranjang berhasil ditambahkan.';
        } else {
            $response['message'] = 'Gagal mengupdate kuantitas.';
        }
        $updateStmt->close();
        
    } else {
        // Jika produk belum ada: INSERT item baru
        $insertStmt = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iii", $userId, $productId, $quantity);
        
        if ($insertStmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Produk berhasil ditambahkan ke keranjang.';
        } else {
            $response['message'] = 'Gagal menambahkan produk baru.';
        }
        $insertStmt->close();
    }
    $stmt->close();
    
    // Setelah operasi selesai, hitung ulang total item di keranjang
    $countStmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $response['cart_count'] = (int)($countResult['total'] ?? 0);
    $countStmt->close();
}

echo json_encode($response);
exit;
?>