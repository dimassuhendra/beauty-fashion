<?php
$message = '';
$error = '';
$orders = [];
$userId = $_SESSION['user_id'] ?? 1;

function fetchUserCompletedOrders($conn, $userId) {
    // Ambil pesanan yang sudah Completed (atau Shipped/Completed)
    $stmt = $conn->prepare("SELECT id, order_code, order_date FROM orders WHERE user_id = ? AND order_status IN ('Shipped', 'Completed') ORDER BY order_date DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

$orders = fetchUserCompletedOrders($conn, $userId);


// 2. Handler POST untuk Pengajuan Komplain
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') == 'submit_complaint') {
    
    $order_id = empty($_POST['order_id']) ? NULL : (int)$_POST['order_id']; // Bisa NULL
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $attachment_url = NULL; // Logika upload file (gambar/bukti)

    if (empty($subject) || empty($description)) {
        $error = "Judul dan deskripsi komplain harus diisi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, order_id, subject, description, attachment_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $userId, $order_id, $subject, $description, $attachment_url);
        
        if ($stmt->execute()) {
            $message = "Komplain Anda **berhasil diajukan** dengan ID #{$stmt->insert_id}. Kami akan segera menindaklanjutinya.";
            // Reset form
            $_POST = []; 
        } else {
            $error = "Gagal mengajukan komplain: " . $conn->error;
        }
        $stmt->close();
    }
}
?>