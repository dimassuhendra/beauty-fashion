<?php
session_start();
include '../../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Enkripsi password menggunakan password_hash()
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan statement SQL untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Pendaftaran berhasil! Silakan <a href='../login.php'>login</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>