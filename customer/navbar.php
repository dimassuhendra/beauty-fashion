<?php
// ====================================================================
// LOGIKA PHP UNTUK MENGAMBIL JUMLAH KERANJANG

// Asumsi: Variabel $conn sudah didefinisikan di file induk
$userId = $_SESSION['user_id'] ?? 1; 
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

// Menentukan nama file saat ini untuk penandaan link aktif
// Contoh: Mengambil 'orders.php' dari URL
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<style>
    /* Default style untuk nav-link */
.navbar-pink .nav-link {
    color: #ffffff;
    position: relative;
    /* Hilangkan garis bawah default pada link */
    text-decoration: none; 
    transition: all 0.3s ease; /* Transisi untuk efek halus */
}

/* Garis bawah akan dibuat menggunakan pseudo-element */
.navbar-pink .nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    background-color: #ff79c6; /* Warna garis bawah aktif/hover */
    transition: width 0.3s ease;
}

/* Efek saat di-hover */
.navbar-pink .nav-link:hover::after {
    width: 100%; /* Garis muncul penuh saat di-hover */
}

/* Efek untuk link yang aktif (Garis muncul penuh dan permanen) */
.navbar-pink .active-link::after {
    width: 100%;
}

/* Opsional: Teks aktif bisa diberi warna/berat berbeda */
.navbar-pink .active-link {
    font-weight: bold;
    color: #ff79c6 !important; /* Contoh: teks berwarna pink */
}
</style>

<nav class="navbar navbar-expand-lg navbar-pink fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">BeautyFashion Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php $active_class = function($page) use ($current_page) {
                    return $current_page === $page ? 'active-link' : '';
                }; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?= $active_class('products.php') ?>" href="products.php"><i class="fas fa-store"></i> Belanja Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_class('index.php') ?>" href="index.php"><i class="fas fa-tachometer-alt"></i>
                        Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_class('orders.php') ?>" href="orders.php"><i class="fas fa-box-open"></i> Pesanan Anda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_class('profile.php') ?>" href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $active_class('complaint.php') ?>" href="complaint.php"><i class="fas fa-exclamation-triangle"></i> Ajukan Komplain</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <a href="cart.php" class="btn btn-cart me-3 position-relative nav-link <?= $active_class('complaint.php') ?>">
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