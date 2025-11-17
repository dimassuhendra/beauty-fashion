<?php
// ====================================================================
// LOGIKA PHP UNTUK MENGAMBIL JUMLAH KERANJANG
// CATATAN: Variabel $conn harus sudah didefinisikan di file induk (index.php, products.php, dll.)

// Asumsi 1: Menggunakan simulasi data keranjang (jika belum ada database)
// $cartCount = 5; 

// Asumsi 2: Jika Anda sudah memiliki data riil di database (Ganti dengan kode riil Anda)

$userId = $_SESSION['user_id'] ?? 1; // Pastikan User ID terdefinisi
$cartCount = 0;
if (isset($conn)) {
    // Sesuaikan nama tabel Anda (misalnya: cart_items, keranjang)
    $stmt = $conn->prepare("SELECT SUM(quantity) AS count FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartData = $result->fetch_assoc();
    $cartCount = $cartData['count'] ?? 0;
    $stmt->close();
}

// Saya akan menggunakan variabel global $cartCount yang harus Anda definisikan di file induk
$cartCount = $cartCount ?? 0; // Menggunakan 0 sebagai default jika variabel belum didefinisikan
?>

<nav class="navbar navbar-expand-lg navbar-pink fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">BeautyFashion Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="products.php"><i class="fas fa-store"></i> Belanja Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php"><i class="fas fa-tachometer-alt"></i>
                        Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php"><i class="fas fa-box-open"></i> Pesanan Anda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="complaint.php"><i class="fas fa-exclamation-triangle"></i> Ajukan Komplain</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">

                <a href="cart.php" class="btn btn-cart me-3 position-relative">
                    <i class="fas fa-shopping-cart"></i> Keranjang
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        id="cart-count">
                        <?= $cartCount; ?>
                        <span class="visually-hidden">items in cart</span>
                    </span>
                </a>

                <button class="btn btn-logout" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i>
                    Keluar</button>
            </div>
        </div>
    </div>
</nav>