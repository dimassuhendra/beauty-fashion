<?php
// Catatan: Asumsi $conn (koneksi database) sudah tersedia dari db_connect.php 
// yang di-include di orders.php sebelum file ini di-include.
// Asumsi: $current_status (dari orders.php) sudah tersedia.

// Fungsi utilitas
function format_rupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi untuk mapping status ENUM database ke tampilan Indonesia dan Class CSS
 */
function get_status_data($db_status)
{
    $data = [
        'display' => $db_status,
        'class' => 'status-default'
    ];

    // Sesuaikan nama kelas CSS dengan yang ada di orders.php
    switch ($db_status) {
        case 'Menunggu Pembayaran':
            $data['display'] = 'Menunggu Pembayaran';
            $data['class'] = 'status-menunggu';
            break;
        case 'Diproses':
            $data['display'] = 'Sedang Diproses';
            $data['class'] = 'status-diproses';
            break;
        case 'Dikirim':
            $data['display'] = 'Dalam Pengiriman';
            $data['class'] = 'status-dikirim';
            break;
        case 'Selesai':
            $data['display'] = 'Selesai';
            $data['class'] = 'status-selesai';
            break;
        case 'Dibatalkan':
            $data['display'] = 'Dibatalkan';
            $data['class'] = 'status-dibatalkan';
            break;
        case 'Gagal':
            $data['display'] = 'Gagal';
            $data['class'] = 'status-gagal';
            break;
    }
    return $data;
}

// --- 2. LOGIKA PENGAMBILAN DATA PESANAN (FIXED FOR FILTERING) ---

// Pastikan $current_status tersedia (diperoleh dari orders.php)
$current_status = isset($current_status) ? $current_status : 'Semua';

// Ganti baris ini dengan pengambilan ID dari session Anda yang sebenarnya
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1;

$orders = [];
$error_message = '';

if ($user_id > 0) {
    // 1. Persiapan kondisi dan parameter dasar
    $where_condition = "o.user_id = ?";
    $param_types = "i";
    $params = [$user_id];

    // ** LOGIKA UTAMA UNTUK FILTER STATUS **
    // Tambahkan kondisi filter status jika status BUKAN 'Semua'
    if ($current_status !== 'Semua') {
        $where_condition .= " AND o.order_status = ?";
        $param_types .= "s";
        // Tambahkan status ke parameter
        $params[] = $current_status;
    }
    // ** AKHIR LOGIKA FILTER **

    // QUERY DENGAN PENYESUAIAN NAMA KOLOM DAN TABEL BERDASARKAN SKEMA
    $sql = "SELECT 
                o.id, 
                o.order_code, 
                o.order_date,
                o.total_amount, 
                o.order_status,
                (SELECT COUNT(od.id) FROM order_details od WHERE od.order_id = o.id) AS items_count
            FROM 
                orders o
            WHERE 
                {$where_condition}
            ORDER BY 
                o.order_date DESC";

    if ($stmt = $conn->prepare($sql)) {
        // Binding parameter dinamis
        $stmt->bind_param($param_types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Ubah format tanggal
                $row['date'] = date('d M Y', strtotime($row['order_date']));
                $orders[] = $row;
            }
        } else {
            // Ubah pesan error agar lebih spesifik jika filter aktif
            if ($current_status !== 'Semua') {
                $error_message = "Tidak ada pesanan dengan status '{$current_status}' saat ini.";
            } else {
                $error_message = "Anda belum memiliki pesanan saat ini.";
            }

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
    $cancel_id = (int) $_POST['cancel_order_id'];

    // Hanya izinkan pembatalan jika statusnya 'Menunggu Pembayaran'
    $sql_cancel = "UPDATE orders SET order_status = 'Dibatalkan', updated_at = NOW() WHERE id = ? AND user_id = ? AND order_status = 'Menunggu Pembayaran'";

    if ($stmt_cancel = $conn->prepare($sql_cancel)) {
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
?>