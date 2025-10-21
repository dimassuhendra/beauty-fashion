<?php
// Pastikan path ke db_connect.php sudah benar
require_once '../db_connect.php'; 

if (isset($_POST['register_submit'])) {
    
    // 1. Ambil dan sanitasi data
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone_number = mysqli_real_escape_string($conn, trim($_POST['phone_number']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Pesan error default
    $error_message = '';

    // 2. Validasi input
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password minimal 6 karakter.";
    }

    // 3. Cek ketersediaan Email (menggunakan Prepared Statement)
    if (empty($error_message)) {
        $check_query = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $conn->prepare($check_query);
        if ($stmt_check) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                $error_message = "Email ini sudah terdaftar. Silakan gunakan email lain.";
            }
            $stmt_check->close();
        } else {
            // Error database
            $error_message = "Terjadi kesalahan sistem saat memeriksa email.";
        }
    }

    // 4. Jika semua validasi lolos, masukkan ke database
    if (empty($error_message)) {
        // Hashing password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert_query = "INSERT INTO users (full_name, email, password, phone_number) VALUES (?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($insert_query);
        if ($stmt_insert) {
            $stmt_insert->bind_param("ssss", $full_name, $email, $hashed_password, $phone_number);
            
            if ($stmt_insert->execute()) {
                // Sukses: Redirect ke halaman login dengan pesan sukses
                $message = "Akun berhasil dibuat! Silakan masuk.";
                header("Location: ../login.php?status=success&message=" . urlencode($message));
                exit();
            } else {
                // Gagal insert
                $error_message = "Pendaftaran gagal. Silakan coba lagi.";
            }
            $stmt_insert->close();
        } else {
            $error_message = "Terjadi kesalahan sistem saat menyimpan data.";
        }
    }
    
    // Jika ada error, redirect kembali ke halaman register dengan pesan error
    if (!empty($error_message)) {
        header("Location: ../signup.php?status=error&message=" . urlencode($error_message));
        exit();
    }
} else {
    // Jika diakses tanpa submit form
    header("Location: ../signup.php");
    exit();
}

mysqli_close($conn);
?>