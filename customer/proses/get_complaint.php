<?php
$message = '';
$error = '';
$orders = [];
$userId = $_SESSION['user_id'] ?? 1;

function fetchUserCompletedOrders($conn, $userId) {
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


// ====================================================== //
// LOGIKA PENGAMBILAN RIWAYAT KOMPLAIN
// ====================================================== //
$complaints = [];

$sql = "SELECT
            c.id, c.subject, c.description, c.status, c.created_at, c.admin_response,
            o.order_code
        FROM
            complaints c
        LEFT JOIN
            orders o ON c.order_id = o.id
        WHERE
            c.user_id = ?
        ORDER BY
            c.created_at DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['order_code'] = $row['order_code'] ?? '-';
        $row['admin_response'] = $row['admin_response'] ?? null;
        $complaints[] = $row;
    }
    $stmt->close();
} else {
    error_log("SQL Prepare Error: " . $conn->error);
    $error = "Gagal memuat riwayat komplain. Silakan coba lagi.";
}

// ====================================================== //
// LOGIKA POST FORM KOMPLAIN
// ====================================================== //
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['action'] ?? '') == 'submit_complaint') {
    
    $order_id = empty($_POST['order_id']) ? NULL : (int)$_POST['order_id'];
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $attachment_url = NULL;

    if (empty($subject) || empty($description)) {
        $error = "Judul dan deskripsi komplain harus diisi.";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, order_id, subject, description, attachment_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $userId, $order_id, $subject, $description, $attachment_url);
        
        if ($stmt->execute()) {
            $message = "Komplain Anda **berhasil diajukan** dengan ID #{$stmt->insert_id}. Kami akan segera menindaklanjutinya.";
            $_POST = []; 
        } else {
            $error = "Gagal mengajukan komplain: " . $conn->error;
        }
        $stmt->close();
    }
}
?>