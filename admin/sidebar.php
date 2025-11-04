<?php
// Tentukan halaman yang sedang aktif. Kami asumsikan nama file dashboard adalah 'dashboard.php'.
// Jika ada file lain, tambahkan di sini.
$active_page = basename($_SERVER['PHP_SELF']);
?>

<head>
    <link rel="stylesheet" href="style.css">
    <style>
    /* ==================================================== */
    /* START: SIDEBAR STYLE DARI USER */
    /* ==================================================== */
    #sidebar {
        width: 250px;
        min-width: 250px;
        background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
        color: white;
        position: fixed;
        /* Ubah ke fixed */
        height: 100vh;
        /* Tambah tinggi 100% viewport */
        top: 0;
        left: 0;
        overflow: hidden;
        transition: all 0.3s;
        z-index: 100;
    }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        z-index: 10;
    }

    #sidebar ul.components {
        padding: 20px 0;
        list-style-type: none;
        position: relative;
        z-index: 10;
    }

    #sidebar ul li a {
        padding: 10px;
        font-size: 1.1em;
        display: block;
        color: white;
        text-decoration: none;
        transition: background 0.3s;
    }

    #sidebar ul li a:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
    }

    /* Penyesuaian untuk menu aktif */
    #sidebar ul li.active a {
        background: rgba(255, 255, 255, 0.25) !important;
        color: white;
        border-left: 5px solid var(--bs-active-bg);
        padding-left: 15px !important;
    }

    /* Gaya untuk Ornamen Bulat (Circle) */
    .circle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
        z-index: 1;
    }

    .circle:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 5%;
        left: 5%;
    }

    .circle:nth-child(2) {
        width: 50px;
        height: 50px;
        top: 25%;
        right: 10%;
    }

    .circle:nth-child(3) {
        width: 70px;
        height: 70px;
        bottom: 30%;
        left: 15%;
    }

    .circle:nth-child(4) {
        width: 100px;
        height: 100px;
        top: 60%;
        right: -20%;
    }

    .circle:nth-child(5) {
        width: 60px;
        height: 60px;
        bottom: 5%;
        left: 5%;
    }

    /* ==================================================== */
    /* END: SIDEBAR STYLE DARI USER */
    /* ==================================================== */
    </style>
</head>

<nav id="sidebar">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>

    <div class="sidebar-header">
        <h3 class="fw-bold">Beauty Admin</h3>
        <small class="text-white-75">Panel Kontrol</small>
    </div>

    <ul class="components">
        <li class="<?php echo ($active_page == 'dashboard.php' || $active_page == 'dashboard.php' ? 'active' : ''); ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Utama</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-produk.php' ? 'active' : ''); ?>">
            <a href="manajemen-produk.php"><i class="fas fa-boxes me-2"></i> Manajemen Produk</a>
        </li>
        <li class="<?php echo ($active_page == 'pesanan-baru.php' ? 'active' : ''); ?>">
            <a href="pesanan-baru.php"><i class="fas fa-clipboard-list me-2"></i> Pesanan Baru</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-pelanggan.php' ? 'active' : ''); ?>">
            <a href="manajemen-pelanggan.php"><i class="fas fa-users me-2"></i> Manajemen Pelanggan</a>
        </li>
        <li class="<?php echo ($active_page == 'laporan-penjualan.php' ? 'active' : ''); ?>">
            <a href="laporan-penjualan.php"><i class="fas fa-chart-line me-2"></i> Laporan Penjualan</a>
        </li>
        <li class="<?php echo ($active_page == 'kelola-promo.php' ? 'active' : ''); ?>">
            <a href="kelola-promo.php"><i class="fas fa-tags me-2"></i> Kelola Promo</a>
        </li>
    </ul>

    <div style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px; z-index: 10;">
        <button id="mode-toggle" class="btn btn-sm btn-light d-block w-100 fw-bold mb-2">
            <i class="fas fa-sun"></i> Light Mode
        </button>

        <a href="login.php" class="btn btn-sm btn-light d-block text-pink-primary fw-bold">
            <i class="fas fa-sign-out-alt me-2"></i> Keluar
        </a>
    </div>
</nav>