<?php
// Pastikan sesi dimulai di awal setiap file yang membutuhkan sesi
session_start(); 

include '../db_connect.php';
include 'proses/get_orders.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Anda - Beauty Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; // Panggil file navbar Anda ?>

    <div class="profile-hero">
        <div class="container">
            <h1><i class="fas fa-user-circle me-2"></i> Daftar Pesanan Anda</h1>
            <p class="lead">Lihat Riwayat Pesanan</p>
        </div>
    </div>

    <main class="container mb-5">
        <div class="container">
            <?php if (!empty($notification)): ?>
            <div class="alert <?= strpos($notification, 'gagal') !== false ? 'alert-error' : 'alert-success' ?>">
                <?= htmlspecialchars($notification) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error_message) && empty($orders)): ?>
            <div class="alert alert-info text-center py-4" role="alert">
                <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($error_message) ?> <p>Yuk, <a
                        href="products.php" class="alert-link">mulai belanja!</a></p>
            </div>
            <?php else: ?>
            <div class="order-list">
                <?php foreach ($orders as $order): 
                    // Ambil status yang sudah dipetakan
                    $status_data = get_status_data($order['order_status']);
                ?>
                <div class="order-card">
                    <div class="order-card-header">
                        <span class="order-code"><?= htmlspecialchars($order['order_code']) ?></span>
                        <span class="order-date">Tanggal Pesan: <?= htmlspecialchars($order['date']) ?></span>
                    </div>

                    <div class="order-detail-row">
                        <span class="order-detail-label">Jumlah Produk</span>
                        <span class="order-detail-value"><?= htmlspecialchars($order['items_count']) ?> Item</span>
                    </div>

                    <div class="order-detail-row">
                        <span class="order-detail-label">Status Pesanan</span>
                        <span
                            class="order-status <?= $status_data['class'] ?>"><?= htmlspecialchars($status_data['display']) ?></span>
                    </div>

                    <div class="order-detail-row">
                        <span class="order-detail-label">Total Pembayaran</span>
                        <span class="order-detail-value"
                            style="color: var(--pink-primary); font-size: 1.2em;"><?= format_rupiah($order['total_amount']) ?></span>
                    </div>

                    <div class="order-actions">
                        <a href="detail_pesanan.php?id=<?= $order['id'] ?>" class="btn btn-outline">Lihat Detail</a>

                        <?php 
                        // Hanya tampilkan tombol batal jika statusnya Pending Payment
                        if ($order['order_status'] === 'Pending Payment'): 
                        ?>
                        <button class="btn btn-pink"
                            onclick="confirmCancel('<?= htmlspecialchars($order['order_code']) ?>', <?= $order['id'] ?>)">Batalkan
                            Pesanan</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include '../footer.php'; // Panggil file footer Anda ?>

    <script>
    // Awal JavaScript Halaman Pesanan
    function confirmCancel(orderCode, orderId) {
        if (confirm("Apakah Anda yakin ingin membatalkan pesanan " + orderCode +
                "? Tindakan ini tidak dapat dibatalkan.")) {

            // Membuat form dinamis untuk mengirim POST request pembatalan
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'pesanan_anda.php'; // Aksi ke halaman ini sendiri

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cancel_order_id';
            input.value = orderId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    // Akhir JavaScript Halaman Pesanan
    </script>
</body>

</html>