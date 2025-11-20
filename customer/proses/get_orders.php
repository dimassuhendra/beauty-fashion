<?php 
// Fungsi utilitas
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi untuk mapping status ENUM database ke tampilan Indonesia dan Class CSS
 * Status ENUM dari DB: 'Pending Payment', 'Processing', 'Shipped', 'Completed', 'Cancelled'
 */
function get_status_data($db_status) {
    $data = [
        'display' => $db_status,
        'class' => 'status-default'
    ];
    
    switch ($db_status) {
        case 'Pending Payment':
            $data['display'] = 'Menunggu Pembayaran';
            $data['class'] = 'status-PendingPayment';
            break;
        case 'Processing':
            $data['display'] = 'Sedang Diproses';
            $data['class'] = 'status-Processing';
            break;
        case 'Shipped':
            $data['display'] = 'Dalam Pengiriman';
            $data['class'] = 'status-Shipped';
            break;
        case 'Completed':
            $data['display'] = 'Selesai';
            $data['class'] = 'status-Completed';
            break;
        case 'Cancelled':
            $data['display'] = 'Dibatalkan';
            $data['class'] = 'status-Cancelled';
            break;
    }
    return $data;
}

// --- 2. LOGIKA PENGAMBILAN DATA PESANAN ---
// Ganti baris ini dengan pengambilan ID dari session Anda yang sebenarnya
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1; 

$orders = [];
$error_message = '';

if ($user_id > 0) {
    // QUERY DENGAN PENYESUAIAN NAMA KOLOM DAN TABEL BERDASARKAN SKEMA
    $sql = "SELECT 
                o.id, 
                o.order_code, 
                o.order_date,        -- Nama kolom yang benar
                o.total_amount, 
                o.order_status,      -- Nama kolom yang benar
                (SELECT COUNT(od.id) FROM order_details od WHERE od.order_id = o.id) AS items_count -- Nama tabel yang benar
            FROM 
                orders o
            WHERE 
                o.user_id = ?
            ORDER BY 
                o.order_date DESC";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Ubah format tanggal
                $row['date'] = date('d M Y', strtotime($row['order_date']));
                $orders[] = $row;
            }
        } else {
            $error_message = "Anda belum memiliki pesanan saat ini.";
        }
        $stmt->close();
    } else {
        $error_message = "Terjadi kesalahan dalam query database: " . $conn->error;
    }
} else {
    $error_message = "Anda harus login untuk melihat daftar pesanan.";
}


// --- 3. LOGIKA PEMBATALAN PESANAN (POST Request) ---
$notification = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = (int)$_POST['cancel_order_id'];
    
    // Hapus pembuatan koneksi baru. Cukup gunakan $conn yang sudah tersedia.
    
    // Hanya izinkan pembatalan jika statusnya 'Pending Payment'
    $sql_cancel = "UPDATE orders SET order_status = 'Cancelled', updated_at = NOW() WHERE id = ? AND user_id = ? AND order_status IN ('Pending Payment')";
    
    if ($stmt_cancel = $conn->prepare($sql_cancel)) { // <<< GUNAKAN $conn
        $stmt_cancel->bind_param("ii", $cancel_id, $user_id);
        
        if ($stmt_cancel->execute()) {
            if ($stmt_cancel->affected_rows > 0) {
                $notification = 'Pesanan berhasil dibatalkan.';
            } else {
                $notification = 'Gagal membatalkan pesanan. Status pesanan mungkin sudah berubah atau pesanan bukan milik Anda.';
            }
        } else {
            $notification = 'Gagal membatalkan pesanan karena kesalahan sistem.';
        }
        $stmt_cancel->close();
    }
}