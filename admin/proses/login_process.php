<?php
session_start();
include '../../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    // Siapkan statement SQL untuk mencari user berdasarkan username atau email
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Password benar, buat sesi
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_username'] = $username;
            header("Location: ../dashboard.php"); // Arahkan ke halaman admin
            exit();
        } else {
            echo "Password salah.";
        }
    } else {
        echo "Username atau email tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
}
?>