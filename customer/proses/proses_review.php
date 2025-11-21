<?php
session_start();
header('Content-Type: application/json');

include '../../db_connect.php'; 

// Cek User Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk memberi ulasan.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Cek Data POST
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
$comment_text = trim(filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_STRING));

if (!$product_id || !$order_id || $rating < 1 || $rating > 5 || empty($comment_text)) {
    echo json_encode(['success' => false, 'message' => 'Data ulasan tidak lengkap atau tidak valid.']);
    exit;
}

// 1. Cek apakah produk ini benar-benar ada di pesanan user dan statusnya Completed
$sql_check = "SELECT 
                o.order_status
              FROM 
                orders o
              JOIN 
                order_details od ON o.id = od.order_id
              WHERE 
                o.id = ? AND od.product_id = ? AND o.user_id = ?";

if ($stmt = $conn->prepare($sql_check)) {
    $stmt->bind_param("iii", $order_id, $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_data = $result->fetch_assoc();
    $stmt->close();

    if (!$order_data) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan dalam pesanan ini atau pesanan bukan milik Anda.']);
        exit;
    }

    if ($order_data['order_status'] !== 'Completed') {
        echo json_encode(['success' => false, 'message' => 'Ulasan hanya dapat diberikan untuk pesanan yang sudah selesai (Completed).']);
        exit;
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan query cek pesanan.']);
    exit;
}


// 2. Cek apakah user sudah pernah mengulas produk ini di pesanan ini (MENGGUNAKAN KOLOM order_id BARU)
$sql_check_review = "SELECT id FROM reviews WHERE user_id = ? AND product_id = ? AND order_id = ?";
if ($stmt_check = $conn->prepare($sql_check_review)) {
    $stmt_check->bind_param("iii", $user_id, $product_id, $order_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $stmt_check->close();
        echo json_encode(['success' => false, 'message' => 'Anda sudah memberikan ulasan untuk produk ini pada pesanan yang sama.']);
        exit;
    }
    $stmt_check->close();
}


// 3. Masukkan data ulasan
$sql_insert = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment_text) 
               VALUES (?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql_insert)) {
    $stmt->bind_param("iiiis", $user_id, $product_id, $order_id, $rating, $comment_text);
    
    if ($stmt->execute()) {
        // Asumsi: Tidak perlu commit/rollback jika tidak menggunakan transaksi, tapi tidak ada salahnya.
        // $conn->commit(); 
        echo json_encode(['success' => true, 'message' => 'Ulasan berhasil dikirim.']);
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan ulasan: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyiapkan query insert ulasan: ' . $conn->error]);
}

$conn->close();
?>