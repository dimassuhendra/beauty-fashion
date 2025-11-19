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

    <?php include 'navbar.php'; ?>

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
                        <a href="#" class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#orderDetailModal"
                            onclick="loadOrderDetail(<?= $order['id'] ?>)">Lihat Detail</a>
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

    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel"><i class="fas fa-file-invoice me-2"></i> Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-content-placeholder" class="text-center p-5">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Memuat detail pesanan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; // Panggil file footer Anda ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Awal JavaScript Halaman Pesanan
    function confirmCancel(orderCode, orderId) {
        if (confirm("Apakah Anda yakin ingin membatalkan pesanan " + orderCode +
                "? Tindakan ini tidak dapat dibatalkan.")) {
            // Membuat form dinamis untuk mengirim POST request pembatalan
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'pesanan_anda.php'; // Ganti dengan nama file Anda jika berbeda
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'cancel_order_id';
            input.value = orderId;

            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Fungsi baru untuk memuat detail pesanan melalui AJAX
    function loadOrderDetail(orderId) {
        const modalBody = document.getElementById('modal-content-placeholder');
        modalBody.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat detail pesanan...</p>';
        
        // Menggunakan Fetch API untuk permintaan AJAX
        fetch('proses/get_orders-detail.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan konten HTML yang dikembalikan oleh PHP
                modalBody.innerHTML = data.html;
            } else {
                // Tampilkan pesan error
                modalBody.innerHTML = '<div class="alert alert-danger" role="alert"><i class="fas fa-times-circle me-2"></i>' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            modalBody.innerHTML = '<div class="alert alert-danger" role="alert">Terjadi kesalahan koneksi atau server.</div>';
        });
    }

    // Akhir JavaScript Halaman Pesanan
    </script>
</body>

</html>