<?php
// admin/proses/get_order_detail.php

header('Content-Type: application/json');

include '../../db_connect.php'; // Sesuaikan path koneksi Anda

$response = ['success' => false, 'message' => 'Invalid Request'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    
    // Status color mapping (diulang dari manajemen-pesanan.php)
    $statuses_to_monitor = [
        'Menunggu Pembayaran' => 'warning',
        'Diproses' => 'info',
        'Dikirim' => 'primary',
        'Selesai' => 'success',
        'Dibatalkan' => 'danger'
    ];

    // --- 1. Ambil Detail Pesanan Utama ---
    $sql_order = "
        SELECT 
            o.*, 
            u.full_name as user_name, u.email as user_email,
            oa.full_address as shipping_address_line1,
            oa.city as shipping_address_city,
            oa.postal_code as shipping_address_postcode
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN user_addresses oa ON o.id = oa.order_id
        WHERE o.id = $order_id
        LIMIT 1
    ";

    $result_order = $conn->query($sql_order);
    
    if ($result_order && $order = $result_order->fetch_assoc()) {
        
        // Format data untuk output
        $order['order_date_formatted'] = date('d F Y H:i:s', strtotime($order['order_date']));
        $order['total_amount'] = (float)$order['total_amount'];
        $order['discount_amount'] = (float)$order['discount_amount'];
        $order['shipping_cost'] = (float)$order['shipping_cost'];
        $order['final_amount'] = (float)$order['final_amount'];
        
        // --- 2. Ambil Item Pesanan ---
        // Menggunakan tabel order_details sesuai skema database Anda
        $sql_items = "
            SELECT 
                od.product_id, od.unit_price, od.quantity, 
                p.image_url 
            FROM order_details od
            LEFT JOIN products p ON od.product_id = p.id
            WHERE od.id = $order_id
        ";
        $result_items = $conn->query($sql_items);
        $items = [];
        if ($result_items) {
            while ($row = $result_items->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Detail pesanan berhasil diambil.';
        $response['data'] = [
            'order' => $order,
            'items' => $items,
            'status_color' => $statuses_to_monitor[$order['order_status']] ?? 'secondary'
        ];

    } else {
        $response['message'] = 'Pesanan tidak ditemukan.';
    }
}

echo json_encode($response);
$conn->close();
?>