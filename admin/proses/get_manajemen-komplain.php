<?php
// ====================================================================
// proses/get_manajemen-komplain.php
// FILE INI HANYA MENYIAPKAN DATA PHP, BUKAN MENAMPILKAN HTML
// ====================================================================

session_start();
// Asumsikan path koneksi dari direktori admin
include '../db_connect.php'; 
// Asumsikan Anda memiliki mekanisme otentikasi Admin di sini
// include 'admin_auth_check.php'; 

// Variabel untuk menyorot navigasi sidebar
$active_page = 'manajemen-komplain.php';

// Konfigurasi Pagination dan Filter
$limit = 10; // Default limit
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$start = ($page - 1) * $limit;
$filter_status = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';

// Array Status Komplain yang Valid
$valid_statuses = ['Open', 'In Progress', 'Resolved', 'Closed'];

// Pesan dari redirect (digunakan di template manajemen-komplain.php)
$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';


// ====================================================================
// BAGIAN 2: LOGIKA POST (UPDATE STATUS/RESPONS)
// ====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $complaint_id = $_POST['complaint_id'] ?? null;
    // URL redirect harus mencakup filter saat ini
    $redirect_url = 'manajemen-komplain.php?status=' . ($_POST['current_tab'] ?? 'all');
    
    if ($action == 'update_status' && $complaint_id) {
        $new_status = $_POST['new_status'] ?? null;
        $admin_response = $_POST['admin_response'] ?? null; 
        
        if (in_array($new_status, $valid_statuses)) {
            $stmt = $conn->prepare("UPDATE complaints SET status = ?, admin_response = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $new_status, $admin_response, $complaint_id);

            if ($stmt->execute()) {
                $message = urlencode("Status komplain ID $complaint_id berhasil diperbarui menjadi $new_status.");
                header("Location: $redirect_url&msg=$message");
                exit;
            } else {
                $error = urlencode("Gagal memperbarui status komplain: " . $stmt->error);
                header("Location: $redirect_url&err=$error");
                exit;
            }
        } else {
            $error = urlencode("Status tidak valid.");
            header("Location: $redirect_url&err=$error");
            exit;
        }
    }
}


// ====================================================================
// BAGIAN 3: LOGIKA GET (FETCH DATA KOMPLAIN)
// ====================================================================

$base_query = "
    SELECT 
        c.id, c.subject, c.status, c.created_at, c.order_id, c.description, c.admin_response,
        u.full_name AS customer_name, u.email AS customer_email,
        o.order_code
    FROM complaints c
    JOIN users u ON c.user_id = u.id
    LEFT JOIN orders o ON c.order_id = o.id
";

$where_clauses = [];
$param_types = '';
$params = [];

// Filter Status
if ($filter_status !== 'all' && in_array($filter_status, $valid_statuses)) {
    $where_clauses[] = "c.status = ?";
    $param_types .= 's';
    $params[] = $filter_status;
}

// Filter Pencarian
if (!empty($search_query)) {
    $search_term = "%$search_query%";
    $where_clauses[] = "(c.subject LIKE ? OR u.full_name LIKE ? OR o.order_code LIKE ?)";
    $param_types .= 'sss';
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";
$order_sql = " ORDER BY c.created_at DESC";

// 2. Query Total Data untuk Pagination
$total_query = "SELECT COUNT(*) FROM complaints c JOIN users u ON c.user_id = u.id LEFT JOIN orders o ON c.order_id = o.id" . $where_sql;
$stmt_total = $conn->prepare($total_query);
if (!empty($param_types)) {
    $stmt_total->bind_param($param_types, ...$params); 
}
$stmt_total->execute();
$stmt_total->bind_result($total_results);
$stmt_total->fetch();
$stmt_total->close();

$total_pages = ceil($total_results / $limit);


// 3. Query Data Komplain per Halaman
$final_query = $base_query . $where_sql . $order_sql . " LIMIT ?, ?";
$stmt = $conn->prepare($final_query);

$params_data = $params;
$param_types_data = $param_types . 'ii';
$params_data[] = $start;
$params_data[] = $limit;

if (!empty($params_data)) {
    $stmt->bind_param($param_types_data, ...$params_data);
} else {
    // Should not happen if params are used correctly, but for safety
    $stmt->bind_param('ii', $start, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 4. Ambil jumlah komplain yang masih 'Open' untuk notifikasi
$stmt_open_count = $conn->prepare("SELECT COUNT(*) FROM complaints WHERE status = 'Open'");
$stmt_open_count->execute();
$stmt_open_count->bind_result($open_complaints_count);
$stmt_open_count->fetch();
$stmt_open_count->close();

$conn->close();
?>