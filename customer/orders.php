<?php
// Pastikan sesi dimulai di awal setiap file yang membutuhkan sesi
session_start(); 

include '../db_connect.php';
include 'proses/get_orders.php';

// Logika pembatalan pesanan (diambil dari get_orders.php yang Anda berikan sebelumnya)
// Pastikan logika ini berada di get_orders.php atau di file ini sebelum output HTML dimulai
// (Asumsi: Logika ini sudah berfungsi di file get_orders.php atau di atas include)
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
    
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelOrderModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Pembatalan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan membatalkan pesanan:</p>
                    <h4 class="text-danger fw-bold" id="cancel-order-code"></h4>
                    <p>Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.</p>
                    <p class="small text-muted">Pembatalan hanya berlaku untuk pesanan dengan status 'Menunggu Pembayaran'.</p>
                    
                    <form id="cancelOrderForm" method="POST" action="orders.php" style="display: none;">
                        <input type="hidden" name="cancel_order_id" id="cancel-order-id-input">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" onclick="cancelOrderConfirmed()">Ya, Batalkan Pesanan</button>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Awal JavaScript Halaman Pesanan
    
    // FUNGSI BARU: Menggantikan fungsi confirm() lama
    function confirmCancel(orderCode, orderId) {
        // 1. Tampilkan kode pesanan di dalam modal
        document.getElementById('cancel-order-code').innerText = orderCode;
        
        // 2. Simpan ID pesanan ke input tersembunyi
        document.getElementById('cancel-order-id-input').value = orderId;

        // 3. Tampilkan Modal Pembatalan
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
        cancelModal.show();
    }
    
    // FUNGSI BARU: Dipanggil saat user menekan tombol "Ya, Batalkan Pesanan" di modal
    function cancelOrderConfirmed() {
        // Ambil form tersembunyi dan submit
        var form = document.getElementById('cancelOrderForm');
        
        // Penting: Pastikan action form mengarah ke file ini sendiri
        form.action = 'orders.php'; 
        
        form.submit();
        // Halaman akan di-refresh, dan PHP akan memproses pembatalan
    }

    // Fungsi untuk memuat detail pesanan melalui AJAX (Tidak ada perubahan, hanya untuk konteks)
    function loadOrderDetail(orderId) {
        const modalBody = document.getElementById('modal-content-placeholder');
        modalBody.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat detail pesanan...</p>';
        
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
                modalBody.innerHTML = data.html;
            } else {
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