<?php
// Selalu mulai sesi
session_start();

// Pastikan path ke db_connect.php sudah benar
require_once '../db_connect.php'; 

if (isset($_POST['login_submit'])) {
    
    // 1. Ambil dan sanitasi data
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    // Pesan error default
    $error_message = "Email atau password salah.";
    
    // 2. Query data pengguna berdasarkan email
    $query = "SELECT id, full_name, email, password FROM users WHERE email = ?";
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // 3. Verifikasi password dengan hash yang tersimpan
            // Kita gunakan password_verify karena password di-hash saat register
            if (password_verify($password, $user['password'])) {
                
                // Login Berhasil!
                
                // 4. Set Session Variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                
                // 5. Handle "Ingat Saya" (Optional)
                if (isset($_POST['remember_me'])) {
                    // Logika "Ingat Saya" (misalnya menyimpan cookie)
                    // Anda bisa tambahkan logika cookie di sini jika diperlukan
                }

                // 6. Redirect ke halaman user setelah sukses login
                header("Location: ../customer/index.php"); // Ganti dengan halaman utama/dashboard user
                exit();
                
            } else {
                // Password salah
                $message = $error_message; 
                header("Location: ../login.php?status=error&message=" . urlencode($message));
                exit();
            }
        } else {
            // Email tidak ditemukan
            $message = $error_message;
            header("Location: ../login.php?status=error&message=" . urlencode($message));
            exit();
        }

        $stmt->close();
    } else {
        // Error prepared statement
        $message = "Terjadi kesalahan sistem. Coba lagi nanti.";
        header("Location: ../login.php?status=error&message=" . urlencode($message));
        exit();
    }
} else {
    // Jika diakses tanpa submit form
    header("Location: ../login.php");
    exit();
}

mysqli_close($conn);
?>