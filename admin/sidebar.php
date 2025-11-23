<?php
$active_page = basename($_SERVER['PHP_SELF']);
?>

<head>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ==================================================== */
        /* START: SIDEBAR STYLE DARI USER */
        /* ==================================================== */
        :root {
            /* Asumsi Anda memiliki variabel CSS ini di style.css */
            --bs-gradient-start: #ff69b4;
            /* Pink Primary */
            --bs-gradient-end: #ffa0c0;
            /* Pink Light */
            --bs-active-bg: #ff4d94;
            /* Pink Darker for active border */
        }

        #sidebar {
            width: 250px;
            min-width: 250px;
            background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
            color: white;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            overflow-y: auto;
            overflow-x: hidden;
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

        /* Gaya untuk Judul Kategori */
        .sidebar-heading {
            padding: 10px 20px 5px 20px;
            font-size: 0.85em;
            color: rgba(255, 255, 255, 0.7);
            /* Lebih redup */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
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

        <li class="sidebar-heading">
            UMUM
        </li>
        <li class="<?php echo ($active_page == 'dashboard.php' ? 'active' : ''); ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Utama</a>
        </li>
        <li class="sidebar-heading">
            MANAJEMEN TOKO
        </li>
        <li class="<?php echo ($active_page == 'manajemen-produk.php' ? 'active' : ''); ?>">
            <a href="manajemen-produk.php"><i class="fas fa-boxes me-2"></i> Manajemen Produk</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-kategori.php' ? 'active' : ''); ?>">
            <a href="manajemen-kategori.php"><i class="fas fa-tags me-2"></i> Manajemen Kategori</a>
        </li>
        <li class="<?php echo ($active_page == 'pesanan-baru.php' ? 'active' : ''); ?>">
            <a href="pesanan-baru.php"><i class="fas fa-clipboard-list me-2"></i> Manajemen Pesanan</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-komplain.php' ? 'active' : ''); ?>">
            <a href="manajemen-komplain.php"><i class="fas fa-exclamation-circle me-2"></i> Manajemen Komplain</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-diskon.php' ? 'active' : ''); ?>">
            <a href="manajemen-diskon.php"><i class="fas fa-gift me-2"></i> Manajemen Diskon</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-pelanggan.php' ? 'active' : ''); ?>">
            <a href="manajemen-pelanggan.php"><i class="fas fa-users me-2"></i> Manajemen Pelanggan</a>
        </li>

        <li class="sidebar-heading">
            LAPORAN
        </li>
        <li class="<?php echo ($active_page == 'manajemen-penjualan.php' ? 'active' : ''); ?>">
            <a href="manajemen-penjualan.php"><i class="fas fa-chart-line me-2"></i> Laporan Penjualan</a>
        </li>

        <li class="sidebar-heading">
            PENGATURAN
        </li>
        <li class="<?php echo ($active_page == 'pengaturan.php' ? 'active' : ''); ?>">
            <a href="pengaturan.php"><i class="fas fa-cog me-2"></i> Pengaturan Situs</a>
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