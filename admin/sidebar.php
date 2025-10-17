<?php
// Tentukan halaman yang sedang aktif. Kami asumsikan nama file dashboard adalah 'dashboard.php'.
// Jika ada file lain, tambahkan di sini.
$active_page = basename($_SERVER['PHP_SELF']);
?>

<style>
/* Styling khusus Sidebar dengan gradasi dan ornamen */
#sidebar {
    width: 250px;
    min-width: 250px;
    /* Menggunakan variabel global dari file utama */
    background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
    color: white;
    /* Mengubah warna teks utama menjadi putih/terang */
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    z-index: 100;
    /* Pastikan sidebar di atas elemen lain */
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
    /* Hover transparan terang */
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
</style>

<nav id="sidebar">
    <!-- Ornamen Bulat -->
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>

    <div class="sidebar-header">
        <h3 class="fw-bold"><i class="fas fa-gem me-2"></i> Beauty Admin</h3>
        <small class="text-white-75">Panel Kontrol</small>
    </div>

    <ul class="components">
        <li class="<?php echo ($active_page == 'dashboard.php' ? 'active' : ''); ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Utama</a>
        </li>
        <li class="<?php echo ($active_page == 'manajemen-produk.php' ? 'active' : ''); ?>">
            <a href="manajemen-produk.php"><i class="fas fa-boxes me-2"></i> Manajemen Produk</a>
        </li>
        <li class="<?php echo ($active_page == 'pesanan_baru.php' ? 'active' : ''); ?>">
            <a href="pesanan_baru.php"><i class="fas fa-clipboard-list me-2"></i> Pesanan Baru</a>
        </li>
        <li class="<?php echo ($active_page == 'data_pelanggan.php' ? 'active' : ''); ?>">
            <a href="data_pelanggan.php"><i class="fas fa-users me-2"></i> Data Pelanggan</a>
        </li>
        <li class="<?php echo ($active_page == 'laporan_penjualan.php' ? 'active' : ''); ?>">
            <a href="laporan_penjualan.php"><i class="fas fa-chart-line me-2"></i> Laporan Penjualan</a>
        </li>
        <li class="<?php echo ($active_page == 'kelola_promo.php' ? 'active' : ''); ?>">
            <a href="kelola_promo.php"><i class="fas fa-tags me-2"></i> Kelola Promo</a>
        </li>
    </ul>

    <ul class="list-unstyled components mt-3">
        <li style="position: absolute; bottom: 20px; width: 100%; padding: 0 20px; z-index: 10;">
            <a href="admin_login.php" class="btn btn-sm btn-light d-block text-pink-primary fw-bold">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </a>
        </li>
    </ul>
</nav>