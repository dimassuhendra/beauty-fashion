<?php
// Pastikan sesi dimulai di awal setiap file yang membutuhkan sesi
session_start(); 

include '../db_connect.php';
// Asumsi: File ini mendefinisikan $orders, get_status_data(), dan format_rupiah()
include 'proses/get_orders.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Logika pembatalan pesanan (diambil dari get_orders.php yang Anda berikan sebelumnya)
// ... (Logika pembatalan berada di get_orders.php atau di atas ini)

// Placeholder untuk fungsi format_rupiah (pastikan ini ada di salah satu file include)
if (!function_exists('format_rupiah')) {
    function format_rupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}
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
    <style>
        /* Gaya tambahan untuk modal ulasan */
        .rating-stars i {
            font-size: 1.5rem;
            cursor: pointer;
            color: #ccc;
            transition: color 0.2s;
        }

        .rating-stars i.selected {
            color: #ffc107;
        }

        .rating-stars i:hover {
            color: #ffc107;
        }
    </style>
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
    
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-pink text-white">
                    <h5 class="modal-title" id="reviewModalLabel"><i class="fas fa-star me-2"></i> Beri Ulasan Produk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="review-product-name" class="fw-bold mb-3">Nama Produk</h5>
                    <p class="small text-muted mb-4">Berikan penilaian Anda untuk produk ini.</p>

                    <form id="reviewForm">
                        <input type="hidden" name="product_id" id="review-product-id">
                        <input type="hidden" name="order_id" id="review-order-id">
                        <input type="hidden" name="rating" id="review-rating" value="0"> 

                        <div class="mb-3 text-center">
                            <label class="form-label d-block fw-bold">Nilai Anda:</label>
                            <div class="rating-stars" data-rating="0" id="rating-container">
                                <i class="far fa-star" data-value="1"></i>
                                <i class="far fa-star" data-value="2"></i>
                                <i class="far fa-star" data-value="3"></i>
                                <i class="far fa-star" data-value="4"></i>
                                <i class="far fa-star" data-value="5"></i>
                            </div>
                            <p class="text-muted small mt-2" id="rating-text">Belum ada nilai</p>
                        </div>

                        <div class="mb-3">
                            <label for="reviewComment" class="form-label fw-bold">Komentar:</label>
                            <textarea class="form-control" id="reviewComment" name="comment_text" rows="3" required
                                placeholder="Berikan ulasan Anda mengenai produk ini..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-pink" onclick="submitReview()">Kirim Ulasan</button>
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
    
    // Global variable untuk menyimpan detail pesanan yang sedang dilihat
    let currentOrderDetail = null;
    
    // --- FUNGSI PEMBANTU ---
    // Fungsi untuk menampilkan bintang rating
    function getStarRating(rating, isStatic = true) {
        const roundedRating = Math.round(rating);
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            const starClass = i <= roundedRating ? 'fas' : 'far';
            stars += `<i class="${starClass} fa-star text-warning me-1" ${isStatic ? '' : `data-value="${i}"`}></i>`;
        }
        return stars;
    }
    
    // Fungsi untuk format Rupiah
    function formatRupiahJs(angka) {
        if (angka === null || angka === undefined) return 'Rp 0';
        angka = parseFloat(angka);
        if (isNaN(angka)) return 'Rp 0';
        return 'Rp ' + angka.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // --- 1. FUNGSI UNTUK DETAIL PESANAN (REVISI) ---
    function renderOrderDetail(order) {
        let html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Kode Pesanan:</strong> <span class="fw-bold text-pink">${order.order_code}</span></p>
                    <p class="mb-1"><strong>Tanggal Pesan:</strong> ${order.order_date}</p>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${order.status_class}">${order.status_display}</span></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><strong>Metode Bayar:</strong> ${order.payment_method}</p>
                    <p class="mb-1"><strong>Total Bayar:</strong> <span class="fw-bold text-success fs-5">${formatRupiahJs(order.total_amount)}</span></p>
                </div>
            </div>
            <hr>
            <h6><i class="fas fa-map-marker-alt me-2"></i> Alamat Pengiriman</h6>
            <div class="p-3 bg-light rounded mb-4 small">
                <strong>${order.address.receiver_name}</strong> (${order.address.phone_number})<br>
                ${order.address.street}, ${order.address.city}, ${order.address.province} - ${order.address.postal_code}
            </div>
            
            <h6><i class="fas fa-box-open me-2"></i> Produk Dipesan</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Kuantitas</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        order.items.forEach(item => {
            // Tentukan tombol ulasan
            let reviewAction = '';
            if (order.order_status === 'Completed' && item.has_reviewed === 0) {
                reviewAction = `
                    <button class="btn btn-sm btn-outline-pink mt-1" 
                            onclick="openReviewModal(${order.id}, ${item.product_id}, '${item.product_name}')">
                        <i class="fas fa-pen"></i> Ulas
                    </button>
                `;
            } else if (item.has_reviewed === 1) {
                reviewAction = `<span class="badge bg-success mt-1"><i class="fas fa-check"></i> Sudah Diulas</span>`;
            } else {
                 reviewAction = `<span class="text-muted small">T/A</span>`;
            }

            html += `
                <tr>
                    <td>
                        <img src="../uploads/product/${item.image_url || 'default.jpg'}" alt="${item.product_name}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <span class="fw-bold">${item.product_name}</span>
                        ${item.has_reviewed === 1 ? `<br><small class="text-warning">${getStarRating(item.user_rating)}</small>` : ''}
                    </td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end">${formatRupiahJs(item.price)}</td>
                    <td class="text-end">${formatRupiahJs(item.subtotal)}</td>
                    <td class="text-center">${reviewAction}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        return html;
    }
    
    function loadOrderDetail(orderId) {
        const modalBody = document.getElementById('modal-content-placeholder');
        modalBody.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Memuat detail pesanan...</p></div>';
        
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
                currentOrderDetail = data.order; // Simpan data order
                modalBody.innerHTML = renderOrderDetail(data.order);
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger" role="alert"><i class="fas fa-times-circle me-2"></i>' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            modalBody.innerHTML = '<div class="alert alert-danger" role="alert">Terjadi kesalahan koneksi atau server.</div>';
        });
    }

    // --- 2. FUNGSI UNTUK MODAL ULASAN (BARU) ---
    const ratingContainer = document.getElementById('rating-container');
    const reviewRatingInput = document.getElementById('review-rating');
    const ratingText = document.getElementById('rating-text');

    // Event listener untuk memilih rating
    ratingContainer.querySelectorAll('i').forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            reviewRatingInput.value = value;
            updateStarDisplay(value);
        });
        star.addEventListener('mouseover', function() {
            const value = parseInt(this.getAttribute('data-value'));
            updateStarDisplay(value, true);
        });
        star.addEventListener('mouseout', function() {
            updateStarDisplay(parseInt(reviewRatingInput.value));
        });
    });

    function updateStarDisplay(selectedRating, isHover = false) {
        let text = 'Belum ada nilai';
        if (selectedRating > 0) {
            text = `${selectedRating} Bintang`;
        }
        ratingText.innerText = text;

        ratingContainer.querySelectorAll('i').forEach(star => {
            const value = parseInt(star.getAttribute('data-value'));
            if (value <= selectedRating) {
                star.className = 'fas fa-star selected';
            } else {
                star.className = 'far fa-star';
            }
        });
        // Jika tidak hover, kembalikan ke nilai yang dipilih
        if (!isHover) {
            ratingContainer.setAttribute('data-rating', selectedRating);
        }
    }

    // Fungsi membuka modal ulasan
    function openReviewModal(orderId, productId, productName) {
        // Isi data tersembunyi
        document.getElementById('review-product-id').value = productId;
        document.getElementById('review-order-id').value = orderId;
        
        // Reset dan isi tampilan
        document.getElementById('review-product-name').innerText = productName;
        document.getElementById('reviewComment').value = '';
        reviewRatingInput.value = 0;
        updateStarDisplay(0);

        // Tampilkan modal
        var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        reviewModal.show();
    }
    
    // Fungsi mengirim ulasan ke server
    function submitReview() {
        const productId = document.getElementById('review-product-id').value;
        const orderId = document.getElementById('review-order-id').value;
        const rating = document.getElementById('review-rating').value;
        const comment_text = document.getElementById('reviewComment').value.trim();
        
        if (rating < 1 || rating > 5) {
            alert('Mohon berikan rating (1-5 bintang).');
            return;
        }
        if (comment_text.length < 5) {
            alert('Ulasan minimal 5 karakter.');
            return;
        }

        // Tampilkan loading/disable tombol
        const submitButton = document.querySelector('#reviewModal .btn-pink');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengirim...';

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('order_id', orderId);
        formData.append('rating', rating);
        formData.append('comment_text', comment_text);

        fetch('proses/proses_review.php', {
            method: 'POST',
            body: formData 
        })
        .then(response => response.json())
        .then(data => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Kirim Ulasan';

            if (data.success) {
                alert('Ulasan berhasil dikirim! Terima kasih.');
                // Sembunyikan modal ulasan
                var reviewModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                reviewModal.hide();
                // Refresh detail pesanan di modal (atau refresh halaman)
                loadOrderDetail(orderId);
            } else {
                alert('Gagal mengirim ulasan: ' + (data.message || 'Terjadi kesalahan server.'));
            }
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Kirim Ulasan';
            console.error('Error submitting review:', error);
            alert('Kesalahan koneksi saat mengirim ulasan.');
        });
    }

    // --- FUNGSI PEMBATALAN (LAMA) ---
    function confirmCancel(orderCode, orderId) {
        // 1. Tampilkan kode pesanan di dalam modal
        document.getElementById('cancel-order-code').innerText = orderCode;
        
        // 2. Simpan ID pesanan ke input tersembunyi
        document.getElementById('cancel-order-id-input').value = orderId;

        // 3. Tampilkan Modal Pembatalan
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
        cancelModal.show();
    }
    
    function cancelOrderConfirmed() {
        // Ambil form tersembunyi dan submit
        var form = document.getElementById('cancelOrderForm');
        form.action = 'orders.php'; 
        form.submit();
    }

    </script>
</body>

</html>