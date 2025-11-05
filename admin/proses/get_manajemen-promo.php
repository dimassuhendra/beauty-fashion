<?php
// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. TANGANI AKSI CRUD (Sederhana - Anda perlu melengkapi logic ini)
// --------------------------------------------------------------------

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // PENTING: Di sini Anda harus menambahkan logic INSERT/UPDATE/DELETE ke tabel `coupons`.
    // Contoh Sederhana (Hanya Menampilkan Pesan Sukses):
    if ($action === 'add' || $action === 'edit') {
        // Asumsi data sudah divalidasi dan disanitasi
        $code = htmlspecialchars($_POST['coupon_code']);
        $message = "Kode promo <b>$code</b> berhasil " . ($action === 'add' ? "ditambahkan" : "diperbarui") . ". (Lengkapi logic database Anda)";
        $message_type = 'success';
    } elseif ($action === 'delete') {
         $message = "Promo berhasil dihapus. (Lengkapi logic database Anda)";
        $message_type = 'success';
    } elseif ($action === 'toggle_status') {
         $message = "Status promo berhasil diperbarui. (Lengkapi logic database Anda)";
        $message_type = 'info';
    }
}


// --------------------------------------------------------------------
// 3. PENGAMBILAN DATA PROMO/KUPON
// --------------------------------------------------------------------

// Ambil semua kupon dari database
$sql = "
    SELECT *, 
        CASE 
            WHEN is_active = 0 THEN 'Nonaktif'
            WHEN valid_until < CURDATE() THEN 'Kedaluwarsa'
            WHEN valid_from > CURDATE() THEN 'Belum Aktif'
            ELSE 'Aktif'
        END as status_label,
        -- Tambahkan ini untuk hitungan di Summary Cards
        (SELECT COUNT(id) FROM orders WHERE coupon_code = c.coupon_code) as used_count
    FROM coupons c
    ORDER BY status_label DESC, valid_until ASC
";

$result = $conn->query($sql);
$coupons = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
}

// Menghitung Summary
$total_coupons = count($coupons);
$active_coupons = 0;
$expired_coupons = 0;
$total_used = 0;

foreach ($coupons as $c) {
    if ($c['status_label'] === 'Aktif') {
        $active_coupons++;
    } elseif ($c['status_label'] === 'Kedaluwarsa') {
        $expired_coupons++;
    }
    // Asumsi ada kolom 'used_count' di tabel Anda (atau hitungan dari tabel orders)
    $total_used += $c['used_count'] ?? 0;
}
?>