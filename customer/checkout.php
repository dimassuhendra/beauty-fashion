<?php
session_start();
include '../db_connect.php'; 
include 'proses/get_checkout.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Beauty Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        
    </style>
    </head>
<body>

<?php include 'navbar.php'; ?>

<div class="container my-5">
    <h2 class="mb-4 text-pink-primary fw-bold">Checkout Pesanan</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
        </div>
    <?php elseif (empty($cart_items)): ?>
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-shopping-cart me-2"></i> Keranjang belanja Anda kosong. Silakan kembali ke halaman produk.
        </div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4 checkout-summary-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-pink-primary"><i class="fas fa-map-marker-alt me-2"></i> Alamat Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($user_addresses)): ?>
                            <?php foreach ($user_addresses as $address): ?>
                                <div class="form-check p-3 mb-2 border rounded">
                                    <input class="form-check-input" type="radio" name="address_id" id="address_<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>" <?php echo ($selected_address['id'] == $address['id'] ? 'checked' : ''); ?> required>
                                    <label class="form-check-label w-100" for="address_<?php echo $address['id']; ?>">
                                        <strong class="d-block"><?php echo htmlspecialchars($address['recipient_name']); ?> - (<?php echo htmlspecialchars($address['phone_number']); ?>)</strong>
                                        <small class="d-block text-muted"><?php echo htmlspecialchars($address['label']); ?> (<?php echo ($address['is_active'] ? 'Default' : 'Pilihan'); ?>)</small>
                                        <p class="mb-0 small"><?php echo htmlspecialchars($address['full_address'] . ', ' . $address['city'] . ', ' . $address['postal_code']); ?></p>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <small class="text-muted"><a href="profile.php?tab=address" class="text-pink-primary text-decoration-none"><i class="fas fa-plus-circle me-1"></i> Tambah Alamat Baru</a></small>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-exclamation-circle me-2"></i> Anda belum memiliki alamat. <a href="tambah_alamat.php" class="alert-link text-pink-dark">Tambahkan sekarang.</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 checkout-summary-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-pink-primary"><i class="fas fa-credit-card me-2"></i> Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_tf" value="Transfer Bank" checked required>
                            <label class="form-check-label" for="payment_tf">Transfer Bank (BCA/Mandiri)</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_ewallet" value="E-Wallet" required>
                            <label class="form-check-label" for="payment_ewallet">E-Wallet (Dana/Gopay/OVO)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="COD" required>
                            <label class="form-check-label" for="payment_cod">Cash On Delivery (COD)</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow checkout-summary-card sticky-top">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-pink-primary"><i class="fas fa-list-alt me-2"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 border-bottom pb-2">
                            <p class="fw-bold mb-1">Item Pesanan:</p>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex mb-2 align-items-center">
                                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? '../assets/img/product-placeholder.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="me-2 product-image-sm">
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small fw-bold text-truncate"><?php echo htmlspecialchars($item['name']); ?></p>
                                        <small class="text-muted"><?php echo format_rupiah($item['price']); ?> x <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <span class="small fw-bold text-pink-primary"><?php echo format_rupiah($item['subtotal']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-item mb-2">
                            <span class="text-muted">Subtotal (<?php echo count($cart_items); ?> item)</span>
                            <span id="subtotal_display"><?php echo format_rupiah($total_subtotal); ?></span>
                            <input type="hidden" id="subtotal_value" value="<?php echo $total_subtotal; ?>">
                        </div>
                        <div class="summary-item mb-2">
                            <span class="text-muted">Biaya Pengiriman</span>
                            <span id="shipping_display"><?php echo format_rupiah(20000); ?></span> 
                            <input type="hidden" id="shipping_value" value="20000"> 
                        </div>
                        <div class="summary-item mb-3">
                            <span class="text-muted">Diskon (Kode Kupon)</span>
                            <span class="text-danger" id="discount_display">- <?php echo format_rupiah(0); ?></span>
                            <input type="hidden" id="discount_value" value="0">
                        </div>

                        <div class="summary-item total fw-bold">
                            <span class="h5 mb-0 text-pink-primary">Total Pembayaran</span>
                            <span class="h5 mb-0 text-pink-primary" id="grand_total_display"></span>
                            <input type="hidden" id="grand_total_value" name="grand_total">
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" name="checkout" class="btn btn-pink-primary btn-lg" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                                <i class="fas fa-box-open me-2"></i> Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php 
// 1. Panggil Footer (Asumsi file ada di customer/footer.php)
include '../footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // FUNGSI UNTUK FORMAT RUPIAH DI JAVASCRIPT
    function formatRupiahJs(angka) {
        if (typeof angka !== 'number') {
            angka = parseInt(angka);
        }
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp' + ribuan;
    }

    // FUNGSI UNTUK MENGHITUNG TOTAL
    function calculateTotal() {
        const subtotal = parseInt(document.getElementById('subtotal_value').value);
        const shipping = parseInt(document.getElementById('shipping_value').value);
        const discount = parseInt(document.getElementById('discount_value').value);

        const grandTotal = subtotal + shipping - discount;

        // Update tampilan
        document.getElementById('grand_total_display').innerText = formatRupiahJs(grandTotal);
        
        // Update input hidden untuk dikirim ke PHP
        document.getElementById('grand_total_value').value = grandTotal;
    }

    // Panggil saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });

    // Validasi Sederhana
    const checkoutForm = document.getElementById('checkoutForm');
    checkoutForm.addEventListener('submit', function(e) {
        if (!document.querySelector('input[name="address_id"]:checked')) {
            e.preventDefault();
            alert("Mohon pilih alamat pengiriman.");
            return false;
        }
        if (!document.querySelector('input[name="payment_method"]:checked')) {
            e.preventDefault();
            alert("Mohon pilih metode pembayaran.");
            return false;
        }
    });

</script>
</body>
</html>