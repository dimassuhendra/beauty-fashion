<?php 
// --- PENGAMANAN SESI ---
// Ganti baris di bawah ini dengan kode Anda yang sebenarnya setelah login:
$userId = $_SESSION['user_id'] ?? 1; // Menggunakan 1 sebagai simulasi user ID jika sesi belum diatur

if (empty($userId)) {
    // Arahkan ke halaman login jika sesi tidak ditemukan
    header("Location: ../login.php");
    exit();
}

// Fungsi sederhana untuk format Rupiah
function formatRupiah($angka) {
    if ($angka === null) return 'Rp 0';
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// ----------------------------------------------------
// 1. AMBIL DATA PROFIL CUSTOMER (dari tabel `users`)
// ----------------------------------------------------
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$customerData = $result->fetch_assoc();
$stmt->close();

if (!$customerData) {
    // Jika ID tidak valid, paksa logout atau berikan pesan error
    // Contoh: header("Location: ../logout.php"); exit();
    $customerData = ['full_name' => 'Pengguna Tidak Ditemukan', 'email' => ''];
}

// ----------------------------------------------------
// 2. AMBIL STATISTIK AKUN (dari tabel `orders`)
// ----------------------------------------------------
// Pengecualian status 'Cancelled'
$statsQuery = "
    SELECT 
        COUNT(id) AS totalOrders,
        SUM(final_amount) AS totalSpending,
        MAX(order_date) AS lastOrderDate
    FROM orders 
    WHERE user_id = ? AND order_status != 'Cancelled'
";
$stmt = $conn->prepare($statsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$statsResult = $stmt->get_result();
$orderStats = $statsResult->fetch_assoc();
$stmt->close();

// ----------------------------------------------------
// 3. AMBIL 3 PESANAN TERKINI (dari tabel `orders`)
// ----------------------------------------------------
$recentOrdersQuery = "
    SELECT order_code, order_status, order_date
    FROM orders 
    WHERE user_id = ? 
    ORDER BY order_date DESC 
    LIMIT 3
";
$stmt = $conn->prepare($recentOrdersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ----------------------------------------------------
// 4. SUSUN DATA UNTUK TAMPILAN
// ----------------------------------------------------
$totalOrders = $orderStats['totalOrders'] ?? 0;
$totalSpending = $orderStats['totalSpending'] ?? 0;
$lastOrderDate = $orderStats['lastOrderDate'] ? date('d F Y', strtotime($orderStats['lastOrderDate'])) : 'Belum Ada Pesanan';
$unreadNotifications = 0; // Sebaiknya ambil dari tabel notifikasi, sementara diset 0.

$stats = [
    ['icon' => 'fa-shopping-bag', 'title' => 'Total Pesanan', 'value' => $totalOrders . ' Transaksi'],
    ['icon' => 'fa-wallet', 'title' => 'Total Pembelanjaan', 'value' => formatRupiah($totalSpending)],
    ['icon' => 'fa-calendar-alt', 'title' => 'Pesanan Terakhir', 'value' => $lastOrderDate],
    ['icon' => 'fa-bell', 'title' => 'Notifikasi Baru', 'value' => $unreadNotifications . ' Pesan'],
];

// Catatan: Jika $conn adalah objek global dari db_connect.php, biarkan koneksi terbuka
// jika diperlukan oleh file lain, atau tutup di sini: $conn->close();
?>