<?php
session_start();
include '../db_connect.php'; // Pastikan path ini benar!

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

$notification = "";

// 2. Cek apakah ada ID pesanan yang dikirim melalui GET
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header("Location: orders.php?notification=" . urlencode("Gagal: ID pesanan tidak valid."));
    exit;
}

$order_id = (int)$_GET['order_id'];

// 3. Logika Pembaruan Status (Disesuaikan)
global $conn;

// Status baru: 'Diproses'
$new_status = 'Diproses';

// Pastikan hanya pesanan 'Menunggu Pembayaran' milik user ini yang bisa diubah
// QUERY DIHILANGKAN: payment_status_note = ?
$stmt = $conn->prepare("
    UPDATE orders 
    SET 
        order_status = ?, 
        order_date = NOW(), 
        updated_at = NOW()
    WHERE 
        id = ? AND user_id = ? AND order_status = 'Menunggu Pembayaran'
");

// BIND PARAMETER DIHILANGKAN: string 'payment_status_note'
// Hanya tersisa: status (string), order_id (integer), user_id (integer)
$stmt->bind_param("sii", $new_status, $order_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $notification = "Konfirmasi pembayaran berhasil! Pesanan Anda kini berstatus **'Diproses'**.";
    } else {
        $notification = "Gagal: Pesanan tidak ditemukan, bukan milik Anda, atau statusnya sudah di luar 'Menunggu Pembayaran'.";
    }
} else {
    $notification = "Gagal memproses pembayaran: " . $conn->error;
}

$stmt->close();
$conn->close();

// 4. Redirect kembali ke halaman orders.php dengan notifikasi
header("Location: orders.php?notification=" . urlencode($notification));
exit;
?>