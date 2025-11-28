<?php
// processes/save_site_settings.php

include '../../db_connect.php'; // Asumsikan lokasi file koneksi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $site_name = $_POST['site_name'] ?? '';
    $site_tagline = $_POST['site_tagline'] ?? '';
    $email_contact = $_POST['email_contact'] ?? '';
    $phone_contact = $_POST['phone_contact'] ?? '';
    $shipping_cost = $_POST['shipping_cost'] ?? 0;

    // Data yang akan di-update/insert
    $data_to_save = [
        'site_name' => $site_name,
        'site_tagline' => $site_tagline,
        'email_contact' => $email_contact,
        'phone_contact' => $phone_contact,
        'shipping_cost' => $shipping_cost,
    ];

    $success_count = 0;
    $total_settings = count($data_to_save);

    // Siapkan statement update/insert
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) 
                            VALUES (?, ?) 
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    if ($stmt) {
        foreach ($data_to_save as $key => $value) {
            // Bind parameter
            $stmt->bind_param("ss", $key, $value);
            
            // Eksekusi statement
            if ($stmt->execute()) {
                $success_count++;
            }
        }
        $stmt->close();
    } else {
        // Error persiapan statement
        $notification = [
            'type' => 'danger',
            'message' => 'Error saat menyiapkan query: ' . $conn->error
        ];
    }
    
    // Set notifikasi berdasarkan hasil eksekusi
    if ($success_count == $total_settings) {
        $notification = [
            'type' => 'success',
            'message' => 'Pengaturan situs berhasil diperbarui.'
        ];
    } elseif ($success_count > 0) {
        $notification = [
            'type' => 'warning',
            'message' => 'Beberapa pengaturan berhasil disimpan, tetapi ada ' . ($total_settings - $success_count) . ' yang gagal.'
        ];
    }

    // Redirect kembali ke halaman pengaturan situs dengan notifikasi
    // Gunakan session untuk menyimpan notifikasi
    session_start();
    $_SESSION['notification'] = $notification;
    header('Location: ../pengaturan.php');
    exit;

} else {
    // Jika diakses tidak melalui POST, redirect
    header('Location: ../pengaturan.php');
    exit;
}
?>