<?php
// Path ke db_connect.php harus keluar dua kali karena file ini ada di subfolder 'proses'
require_once '../../db_connect.php'; 

if (isset($_GET['action']) && $_GET['action'] === 'update_status' && isset($_GET['id']) && isset($_GET['status'])) {
    
    // Sanitasi input
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $new_status = mysqli_real_escape_string($conn, $_GET['status']);

    // List status yang valid
    $valid_statuses = ['Pending Payment', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
    
    if (!in_array($new_status, $valid_statuses)) {
        $message = "Status pesanan tidak valid.";
        header("Location: ../pesanan-baru.php?status=error&message=" . urlencode($message));
        exit();
    }

    // Query UPDATE menggunakan Prepared Statements untuk keamanan
    $query = "UPDATE orders SET order_status = ?, updated_at = NOW() WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            // Berhasil
            $message = "Status pesanan Kode ID " . $order_id . " berhasil diubah menjadi " . $new_status . ".";
            header("Location: ../pesanan-baru.php?status=success&message=" . urlencode($message));
            exit();
        } else {
            // Gagal eksekusi
            $message = "Gagal mengubah status pesanan: " . $stmt->error;
            header("Location: ../pesanan-baru.php?status=error&message=" . urlencode($message));
            exit();
        }
        $stmt->close();
    } else {
        // Gagal membuat prepared statement
        $message = "Error prepared statement: " . $conn->error;
        header("Location: ../pesanan-baru.php?status=error&message=" . urlencode($message));
        exit();
    }
} else {
    $message = "Aksi atau parameter tidak valid.";
    header("Location: ../pesanan-baru.php?status=error&message=" . urlencode($message));
    exit();
}

mysqli_close($conn);
?>