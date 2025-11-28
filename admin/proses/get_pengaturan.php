<?php
// processes/get_site_settings.php

// Pastikan koneksi sudah ada dari file utama
if (!isset($conn)) {
    // Jika belum ada, asumsikan include file db_connect.php
    // include '../db_connect.php'; 
    // Jika tidak ingin error, Anda bisa menambahkan ini:
    // echo "Error: Koneksi database tidak ditemukan.";
    // exit;
}

$settings = [];
$sql_get = "SELECT setting_key, setting_value FROM site_settings";
$result_get = $conn->query($sql_get);

if ($result_get && $result_get->num_rows > 0) {
    while ($row = $result_get->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Definisikan variabel untuk form (jika data belum ada di DB, gunakan default)
$site_name = $settings['site_name'] ?? 'Beauty Fashion';
$site_tagline = $settings['site_tagline'] ?? 'Fashion Muslimah Terbaik';
$email_contact = $settings['email_contact'] ?? 'support@beautyfashion.com';
$phone_contact = $settings['phone_contact'] ?? '081234567890';
$shipping_cost = $settings['shipping_cost'] ?? 20000;

// Variabel untuk notifikasi
$notification = '';
?>