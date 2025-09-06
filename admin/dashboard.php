<?php
session_start();

// Cek apakah sesi admin_id tidak ada atau kosong
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    // Jika tidak ada sesi, arahkan pengguna kembali ke halaman login
    header("Location: login.php");
    exit();
}

// Jika sesi ada, halaman akan dilanjutkan
// Anda bisa mengambil informasi admin dari sesi untuk ditampilkan di halaman
$admin_username = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="active">Dashboard</a></li>
                    <li><a href="#">Manajemen Produk</a></li>
                    <li><a href="#">Manajemen Pesanan</a></li>
                    <li><a href="#">Manajemen Pengguna</a></li>
                    <li><a href="#">Pengaturan Website</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header-admin">
                <h1>Dashboard</h1>
            </header>

            <p>Halo, <?php echo htmlspecialchars($admin_username); ?>! Selamat datang di dashboard.</p>

            <section class="dashboard-widgets">
                <div class="widget">
                    <h3>Total Produk</h3>
                    <p class="widget-value">125</p>
                </div>
                <div class="widget">
                    <h3>Pesanan Baru</h3>
                    <p class="widget-value">12</p>
                </div>
                <div class="widget">
                    <h3>Pendapatan Bulan Ini</h3>
                    <p class="widget-value">Rp. 5.600.000</p>
                </div>
            </section>

            <section class="recent-orders">
                <h2>Pesanan Terkini</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#001</td>
                            <td>Budi Santoso</td>
                            <td><span class="status-pending">Menunggu</span></td>
                            <td>Rp. 250.000</td>
                            <td><button class="action-btn">Lihat</button></td>
                        </tr>
                        <tr>
                            <td>#002</td>
                            <td>Siti Aisyah</td>
                            <td><span class="status-completed">Selesai</span></td>
                            <td>Rp. 300.000</td>
                            <td><button class="action-btn">Lihat</button></td>
                        </tr>
                        <tr>
                            <td>#003</td>
                            <td>Joko Anwar</td>
                            <td><span class="status-shipped">Dikirim</span></td>
                            <td>Rp. 180.000</td>
                            <td><button class="action-btn">Lihat</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

</body>

</html>