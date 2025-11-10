<?php
// Mulai sesi (penting untuk menyimpan status login)
session_start();

// --- Pengaturan Koneksi Database ---
// Ganti nilai-nilai berikut sesuai dengan konfigurasi database Anda
$host = 'localhost'; // Ganti jika host database Anda berbeda
$db = 'beauty-fashion';      // Nama database
$user = 'root';      // Username database
$pass = '';          // Password database (kosongkan jika tidak ada)

// Membuat koneksi ke database menggunakan MySQLi (disarankan)
$conn = new mysqli($host, $user, $pass, $db);

// Memeriksa koneksi
if ($conn->connect_error) {
    // Tampilkan pesan error dan hentikan script jika koneksi gagal
    die("Koneksi gagal: " . $conn->connect_error);
}

// --- Proses Login ---

// 1. Memeriksa apakah data form telah dikirim (menggunakan method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Mengambil dan membersihkan data input
    // Gunakan trim() untuk menghapus spasi di awal/akhir dan real_escape_string untuk keamanan
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 3. Memastikan input tidak kosong
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Username dan password harus diisi.";
        header("Location: ../login.php"); // Ganti ke halaman login Anda jika berbeda
        exit();
    }

    // 4. Query untuk mengambil admin berdasarkan username
    // Perhatian: Password di database `admins` harus di-hash (misal: menggunakan password_hash()).
    // Asumsi: Field `password` di database menyimpan hash dari password.
    $sql = "SELECT id, username, password, email FROM admins WHERE username = ?";

    // Persiapan statement
    $stmt = $conn->prepare($sql);
    
    // Binding parameter (s untuk string)
    $stmt->bind_param("s", $username);
    
    // Eksekusi statement
    $stmt->execute();
    
    // Ambil hasil
    $result = $stmt->get_result();

    // 5. Verifikasi Admin
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Verifikasi password yang di-hash
        // Jika password di database di-hash, gunakan password_verify()
        if (password_verify($password, $admin['password'])) {
            // Jika login berhasil:

            // A. Simpan data admin ke dalam sesi
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // B. Hapus pesan error sebelumnya (jika ada)
            unset($_SESSION['login_error']);

            // C. Redirect ke halaman dashboard
            header("Location: ../dashboard.php");
            exit();
        } else {
            // Password tidak cocok
            $_SESSION['login_error'] = "Username atau password salah.";
            header("Location: ../login.php"); // Ganti ke halaman login Anda
            exit();
        }
    } else {
        // Username tidak ditemukan
        $_SESSION['login_error'] = "Username atau password salah.";
        header("Location: ../login.php"); // Ganti ke halaman login Anda
        exit();
    }

    // Tutup statement
    $stmt->close();
} else {
    // Jika diakses tanpa submit form, redirect kembali ke halaman login
    header("Location: ../login.php"); // Ganti ke halaman login Anda
    exit();
}

// Tutup koneksi database
$conn->close();
?>