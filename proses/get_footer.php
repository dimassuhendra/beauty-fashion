<?php
// ASUMSI: File ini sudah di-include di halaman utama (misalnya index.php)
// ASUMSI: '../db_connect.php' atau 'db_connect.php' sudah di-include
//         dan variabel $conn (koneksi database) sudah tersedia.

// 1. Ambil data Pengaturan Situs dari DB
$settings = [];
$sql_get_footer = "SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('email_contact', 'phone_contact', 'office_address')";
$result_get_footer = $conn->query($sql_get_footer);

if ($result_get_footer && $result_get_footer->num_rows > 0) {
    while ($row = $result_get_footer->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// 2. Definisikan variabel footer dengan fallback (nilai default)
$footer_email = $settings['email_contact'] ?? 'beautyfashionlampung@gmail.com';
$footer_phone = $settings['phone_contact'] ?? '+62 823-0601-7068';
$footer_address = $settings['office_address'] ?? 'Alamat Office belum diatur.';
