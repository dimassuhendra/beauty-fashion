<?php
session_start();
header('Content-Type: application/json');

require_once '../../db_connect.php'; 
require_once 'get_orders.php'; // Ganti include menjadi require_once

// Menggunakan format data yang lebih terstruktur untuk JS (sesuai orders.php yang baru)
$response = [
    'success' => false,
    'order' => null, // Mengganti 'html' dengan data terstruktur
    'message' => ''
];

// Pastikan user sudah login
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id === 0) {
    $response['message'] = 'Anda harus login untuk melihat detail pesanan.';
    echo json_encode($response);
    exit;
}

// Ambil order_id dari request POST
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($order_id === 0) {
    $response['message'] = 'ID Pesanan tidak valid.';
    echo json_encode($response);
    exit;
}

// Periksa koneksi
if ($conn->connect_error) {
    $response['message'] = "Koneksi database gagal: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// --- 1. Ambil Data Header Pesanan dan Alamat ---
$sql_header = "SELECT 
    o.id, 
    o.order_code, 
    DATE_FORMAT(o.order_date, '%d %M %Y %H:%i') AS order_date, 
    o.total_amount, 
    o.final_amount, 
    o.order_status, 
    o.payment_method, 
    ua.recipient_name,
    ua.phone_number,
    ua.full_address,
    ua.city,
    ua.province,
    ua.postal_code
    FROM orders o
    JOIN user_addresses ua ON o.shipping_address_id = ua.id
    WHERE o.id = ? AND o.user_id = ?";

$order = null;
if ($stmt_header = $conn->prepare($sql_header)) {
    $stmt_header->bind_param("ii", $order_id, $user_id);
    $stmt_header->execute();
    $result_header = $stmt_header->get_result();
    $order = $result_header->fetch_assoc();
    $stmt_header->close();
}

if (!$order) {
    $response['message'] = 'Pesanan tidak ditemukan atau bukan milik Anda.';
    echo json_encode($response);
    exit;
}

// Tambahkan data status yang dibutuhkan JS
$status_data = get_status_data($order['order_status']);
$order['status_display'] = $status_data['display'];
$order['status_class'] = $status_data['class'];
$order['address'] = [
    'receiver_name' => $order['recipient_name'],
    'phone_number' => $order['phone_number'],
    'street' => $order['full_address'],
    'city' => $order['city'],
    'province' => $order['province'],
    'postal_code' => $order['postal_code'],
];
// Bersihkan kolom-kolom yang sudah dikelompokkan
unset($order['recipient_name'], $order['phone_number'], $order['full_address'], $order['city'], $order['province'], $order['postal_code']);


// --- 2. Ambil Data Detail Item Pesanan (dengan Status Ulasan) ---
$sql_details = "SELECT 
    od.product_id,
    p.name AS product_name, 
    p.image_url,
    od.quantity, 
    od.unit_price AS price, -- Ganti unit_price menjadi price agar konsisten dengan JS
    od.subtotal,
    -- Cek apakah produk ini sudah diulas oleh user pada order ini
    (
        SELECT COUNT(r.id) 
        FROM reviews r 
        WHERE r.user_id = o.user_id AND r.product_id = od.product_id AND r.order_id = o.id
    ) AS has_reviewed,
    -- Ambil rating yang diberikan
    (
        SELECT r.rating
        FROM reviews r 
        WHERE r.user_id = o.user_id AND r.product_id = od.product_id AND r.order_id = o.id
        LIMIT 1
    ) AS user_rating
    FROM order_details od
    JOIN products p ON od.product_id = p.id 
    JOIN orders o ON od.order_id = o.id
    WHERE od.order_id = ?";

$items = [];
if ($stmt_details = $conn->prepare($sql_details)) {
    $stmt_details->bind_param("i", $order_id);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();
    while ($row = $result_details->fetch_assoc()) {
        // Konversi tipe data untuk JS
        $row['has_reviewed'] = (int)$row['has_reviewed'];
        $row['user_rating'] = $row['user_rating'] !== null ? (float)$row['user_rating'] : 0;
        $items[] = $row;
    }
    $stmt_details->close();
}

// Gabungkan data
$order['items'] = $items;

// --- 3. Siapkan Response JSON ---
$response['order'] = $order;
$response['success'] = true;

// Tutup koneksi
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

echo json_encode($response);
?>