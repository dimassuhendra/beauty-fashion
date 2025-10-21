<?php
// Pastikan session_start() sudah dipanggil di index.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
// Ambil nama depan saja untuk sambutan personal
$user_name = $is_logged_in ? explode(' ', $_SESSION['user_name'])[0] : '';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-pink-primary" href="index.php">
            <i class="fas fa-shopping-bag me-2"></i>Beauty Fashion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#produk-tersedia">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Kategori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Tentang Kami</a>
                </li>
            </ul>

            <div class="d-flex align-items-center">

                <?php if ($is_logged_in): ?>
                <a href="cart.php" class="btn btn-outline-pink me-3 position-relative" title="Keranjang Belanja">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.6em;">
                        3 <span class="visually-hidden">items in cart</span>
                    </span>
                </a>

                <div class="dropdown">
                    <button class="btn btn-pink dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($user_name); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-cog me-2"></i> Pengaturan
                                Akun</a></li>
                        <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box-open me-2"></i> Pesanan
                                Saya</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="proses_logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                    </ul>
                </div>

                <?php else: ?>
                <a href="../login.php" class="btn btn-outline-pink me-2">Masuk</a>
                <a href="../register.php" class="btn btn-pink">Daftar</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>