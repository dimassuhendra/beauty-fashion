<?php
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Jika ingin menghancurkan sesi secara penuh, hapus juga cookie sesi.
// Catatan: Ini akan menghancurkan sesi, bukan hanya data sesi.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi
session_destroy();

// Redirect ke halaman login atau index dengan pesan sukses
header("Location: ../../login.php?status=success&message=" . urlencode("Anda telah berhasil keluar."));
exit;
?>