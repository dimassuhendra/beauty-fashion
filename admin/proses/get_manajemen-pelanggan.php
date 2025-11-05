<?php
// admin/proses/get_customer_detail.php

header('Content-Type: application/json');

include '../../db_connect.php'; // Sesuaikan path koneksi Anda

$response = ['success' => false, 'message' => 'Invalid Request'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Status color mapping (diulang untuk konsistensi)
    $statuses_to_monitor = [
        'Pending Payment' => 'warning',
        'Processing' => 'info',
        'Shipped' => 'primary',
        'Completed' => 'success',
        'Cancelled' => 'danger'
    ];

    // --- 1. Ambil Detail Pelanggan & Statistik ---
    $sql_user = "
        SELECT 
            u.id, u.full_name, u.email, u.phone_number, u.created_at,
            COUNT(o.id) as order_count,
            SUM(CASE WHEN o.order_status = 'Completed' THEN o.final_amount ELSE 0 END) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.id = o.user_id
        WHERE u.id = $user_id
        GROUP BY u.id
        LIMIT 1
    ";

    $result_user = $conn->query($sql_user);
    
    if ($result_user && $user = $result_user->fetch_assoc()) {
        
        // Format data untuk output
        $user['registration_date_formatted'] = date('d F Y', strtotime($user['created_at']));
        $user['total_spent'] = (float)$user['total_spent'];
        $user['order_count'] = (int)$user['order_count'];
        
        $user['avg_spent'] = ($user['order_count'] > 0) 
                            ? 'Rp' . number_format($user['total_spent'] / $user['order_count'], 0, ',', '.') 
                            : 'Rp0';

        
        // --- 2. Ambil Riwayat Pesanan Terakhir ---
        $sql_orders = "
            SELECT 
                order_code, order_date, final_amount, order_status
            FROM orders
            WHERE user_id = $user_id
            ORDER BY order_date DESC
            LIMIT 5
        ";
        $result_orders = $conn->query($sql_orders);
        $orders = [];
        if ($result_orders) {
            while ($row = $result_orders->fetch_assoc()) {
                $row['order_date_formatted'] = date('d M Y', strtotime($row['order_date']));
                $row['final_amount'] = (float)$row['final_amount'];
                $row['status_color'] = $statuses_to_monitor[$row['order_status']] ?? 'secondary';
                $orders[] = $row;
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Detail pelanggan berhasil diambil.';
        $response['data'] = [
            'user' => $user,
            'orders' => $orders,
        ];

    } else {
        $response['message'] = 'Pelanggan tidak ditemukan.';
    }
}

echo json_encode($response);
$conn->close();
?>