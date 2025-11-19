<?php
session_start();

// --- PANGGIL FILE KONEKSI DATABASE ---
// Asumsi: File ini akan membuat variabel koneksi, misalnya $conn
include '../db_connect.php'; 
include 'proses/get_index.php'; 
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php' ?>

    <main class="py-5">
        <div class="container">
            <h2 class="mb-4 text-center" style="color: #ff69b4; font-weight: 700;">Selamat Datang Kembali,
                <?= htmlspecialchars($customerData['full_name']); ?>!</h2>
            <p class="text-center text-muted mb-5">Semua ringkasan aktivitas akun Anda ada di sini.</p>

            <div class="row g-4 mb-5">
                <?php $i = 1; foreach ($stats as $stat): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-pink card-<?= $i++; ?> h-100 p-3 text-center">
                        <div class="card-body">
                            <i class="fas <?= $stat['icon']; ?> card-icon"></i>
                            <p class="card-title mt-2"><?= $stat['title']; ?></p>
                            <h5 class="card-text"><?= $stat['value']; ?></h5>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
                        <h4 style="color: #ff69b4;">Aktivitas Pesanan Terkini</h4>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <?php
                                        // Menentukan warna badge berdasarkan status pesanan
                                        $badgeClass = '';
                                        switch ($order['order_status']) {
                                            case 'Completed': $badgeClass = 'bg-success'; break;
                                            case 'Shipped': $badgeClass = 'bg-primary'; break;
                                            case 'Processing': $badgeClass = 'bg-info text-dark'; break;
                                            case 'Pending Payment': $badgeClass = 'bg-warning text-dark'; break;
                                            case 'Cancelled': $badgeClass = 'bg-danger'; break;
                                            default: $badgeClass = 'bg-secondary';
                                        }
                                    ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pesanan #<?= htmlspecialchars($order['order_code']); ?>
                                (<?= date('d M Y', strtotime($order['order_date'])); ?>)
                                <span
                                    class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($order['order_status']); ?></span>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <li class="list-group-item text-center text-muted">Belum ada riwayat pesanan yang tercatat.
                            </li>
                            <?php endif; ?>
                        </ul>
                        <a href="orders.php" class="btn btn-sm mt-3"
                            style="background-color: #f06292; color: white;">Lihat Semua Pesanan <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 text-center" style="background-color: #ffcdd2; border-radius: 15px;">
                        <h5 class="fw-bold" style="color: #c2185b;">PROMO SPESIAL!</h5>
                        <p class="text-muted">Dapatkan diskon 20% untuk semua produk kategori Skincare Pink.</p>
                        <a href="products.php">
                            <button class="btn btn-sm mt-2" style="background-color: #e91e63; color: white;">Lihat
                                Produk
                                Sekarang</button>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include '../footer.php' ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Simulasikan JavaScript untuk tombol logout
    function handleLogout() {
        if (confirm("Anda yakin ingin keluar dari sesi ini?")) {
            // Di sini Anda akan mengarahkan user ke halaman logout:
            window.location.href = 'proses/proses_logout.php';
        }
    }
    </script>
</body>

</html>