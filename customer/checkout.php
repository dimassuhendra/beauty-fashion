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
        .text-pink-primary { color: #d63384; } /* Contoh warna pink */
        .btn-pink-primary { background-color: #d63384; border-color: #d63384; color: white; }
        .btn-pink-primary:hover { background-color: #c21a6d; border-color: #c21a6d; color: white; }
        .product-image-sm { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
        .summary-item { display: flex; justify-content: space-between; }
        .checkout-summary-card { border: 1px solid #f8f9fa; }
        .sticky-top { top: 20px; }
    </style>
</head>
<body>

<?php 
// Asumsi file navbar.php ada di direktori yang sama
include 'navbar.php'; 
?>

<div class="container my-5">
    <h2 class="mb-4 text-pink-primary fw-bold">Checkout Pesanan</h2>

    <?php 
    // Tampilkan error dari proses/get_checkout.php
    if (!empty($error_message)): 
    ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_message; ?>
        </div>
    <?php 
    // Tampilkan peringatan keranjang kosong
    elseif (empty($cart_items)): 
    ?>
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-shopping-cart me-2"></i> Keranjang belanja Anda **kosong**. Silakan kembali ke halaman produk.
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
                                <div class="form-check p-3 mb-2 border rounded <?php echo ($selected_address['id'] == $address['id'] ? 'border-pink-primary bg-light' : ''); ?>">
                                    <input class="form-check-input" type="radio" name="address_id" id="address_<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>" <?php echo ($selected_address && $selected_address['id'] == $address['id'] ? 'checked' : ''); ?> required>
                                    <label class="form-check-label w-100" for="address_<?php echo $address['id']; ?>">
                                        <strong class="d-block"><?php echo htmlspecialchars($address['recipient_name']); ?> - (<?php echo htmlspecialchars($address['phone_number']); ?>)</strong>
                                        <small class="d-block text-muted"><?php echo htmlspecialchars($address['label']); ?> <?php echo ($address['is_active'] ? '<span class="badge bg-secondary">Default</span>' : ''); ?></small>
                                        <p class="mb-0 small text-wrap"><?php echo htmlspecialchars($address['full_address'] . ', ' . $address['city'] . ', ' . $address['postal_code']); ?></p>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <small class="text-muted"><a href="profile.php?tab=address" class="text-pink-primary text-decoration-none"><i class="fas fa-plus-circle me-1"></i> Tambah Alamat Baru</a></small>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-exclamation-circle me-2"></i> Anda belum memiliki alamat. <a href="profile.php?tab=address" class="alert-link text-pink-primary">Tambahkan sekarang.</a>
                                <input type="hidden" name="address_id" value=""> 
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 checkout-summary-card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-pink-primary"><i class="fas fa-credit-card me-2"></i> Metode Pembayaran</h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 border rounded p-3">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="payment_method" 
                                    id="payment_qris" 
                                    value="QRIS" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseQris" 
                                    aria-expanded="true" 
                                    aria-controls="collapseQris"
                                    checked 
                                    required
                                >
                                <label class="form-check-label fw-bold" for="payment_qris" style="cursor: pointer !important;">
                                    QRIS (Semua E-Wallet/Bank)
                                </label>
                            </div>

                            <div class="collapse show mt-2" id="collapseQris" data-bs-parent="#paymentOptions">
                                <p class="text-muted small">Scan kode di bawah menggunakan aplikasi pembayaran Anda:</p>
                                <div class="qris-container">
                                    <img src="../assets/img/qris.jpeg" class="img-fluid w-100 card-bayar" alt="Kode QRIS Pembayaran">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 border rounded p-3">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="payment_method" 
                                    id="payment_tf" 
                                    value="Transfer Bank" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseTransfer" 
                                    aria-expanded="false" 
                                    aria-controls="collapseTransfer" 
                                    required
                                >
                                <label class="form-check-label fw-bold" for="payment_tf" style="cursor: pointer;">
                                    Transfer Bank BCA
                                </label>
                            </div>
                            
                            <div class="collapse mt-2" id="collapseTransfer" data-bs-parent="#paymentOptions">
                                <small class="d-block text-muted">
                                    Rekening Tujuan: BCA a.n. Beauty Fashion - 0661401191
                                </small>
                                <button class="btn btn-sm btn-outline-secondary mt-1">Salin Nomor Rekening</button>
                            </div>
                        </div>

                        <div class="mb-3 border rounded p-3">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="payment_method" 
                                    id="payment_ewallet" 
                                    value="E-Wallet" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapseEwallet" 
                                    aria-expanded="false" 
                                    aria-controls="collapseEwallet" 
                                    required
                                >
                                <label class="form-check-label fw-bold" for="payment_ewallet" style="cursor: pointer;">
                                    E-Wallet (Dana/Gopay/OVO)
                                </label>
                            </div>

                            <div class="collapse mt-2" id="collapseEwallet" data-bs-parent="#paymentOptions">
                                <small class="d-block text-muted">
                                    Nomor E-Wallet: 0857-8080-9099
                                </small>
                                <button class="btn btn-sm btn-outline-secondary mt-1">Salin Nomor</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-body">
                        <img src="../assets/img/qris.jpeg" class="card-bayar w-100">

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_tf" value="Transfer Bank" checked required>
                            <label class="form-check-label" for="payment_tf">Transfer Bank BCA</label>
                            <small class="d-block text-muted">BCA a.n. Beauty Fashion - 0661401191</small>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_ewallet" value="E-Wallet" required>
                            <label class="form-check-label" for="payment_ewallet">E-Wallet (Dana/Gopay/OVO)</label>
                            <small class="d-block text-muted">0857-8080-9099</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="COD" required>
                            <label class="form-check-label" for="payment_cod">Cash On Delivery (COD)</label>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow checkout-summary-card sticky-top">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 text-pink-primary"><i class="fas fa-list-alt me-2"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 border-bottom pb-2" style="max-height: 250px; overflow-y: auto;">
                            <p class="fw-bold mb-1">Item Pesanan:</p>
                            <?php 
                            if (!empty($cart_items)):
                                foreach ($cart_items as $item): 
                            ?>
                                    <div class="d-flex mb-2 align-items-center">
                                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? '../assets/img/product-placeholder.png'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="me-2 product-image-sm">
                                        <div class="flex-grow-1">
                                            <p class="mb-0 small fw-bold text-truncate"><?php echo htmlspecialchars($item['name']); ?></p>
                                            <small class="text-muted"><?php echo format_rupiah($item['price']); ?> x <?php echo $item['quantity']; ?></small>
                                        </div>
                                        <span class="small fw-bold text-pink-primary"><?php echo format_rupiah($item['subtotal']); ?></span>
                                    </div>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                                <p class="small text-center text-muted">Keranjang kosong</p>
                            <?php
                            endif;
                            ?>
                        </div>

                        <div class="summary-item mb-2">
                            <span class="text-muted">Subtotal (<?php echo count($cart_items); ?> item)</span>
                            <span id="subtotal_display"><?php echo format_rupiah($total_subtotal); ?></span>
                            <input type="hidden" id="subtotal_value" value="<?php echo $total_subtotal; ?>">
                        </div>
                        <div class="summary-item mb-2">
                            <span class="text-muted">Biaya Pengiriman</span>
                            <span id="shipping_display"><?php echo format_rupiah($shipping_cost); ?></span> 
                            <input type="hidden" id="shipping_value" value="<?php echo $shipping_cost; ?>"> 
                        </div>
                        <div class="summary-item mb-3">
                            <span class="text-muted">Diskon (Kode Kupon)</span>
                            <span class="text-danger" id="discount_display">- <?php echo format_rupiah($discount_amount); ?></span>
                            <input type="hidden" id="discount_value" value="<?php echo $discount_amount; ?>">
                        </div>

                        <div class="summary-item total fw-bold border-top pt-3 mt-3">
                            <span class="h5 mb-0 text-pink-primary">Total Pembayaran</span>
                            <span class="h5 mb-0 text-pink-primary" id="grand_total_display"></span>
                            <input type="hidden" id="grand_total_value" name="grand_total"> 
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" name="checkout" class="btn btn-pink-primary btn-lg" <?php echo (empty($cart_items) || empty($selected_address)) ? 'disabled' : ''; ?>>
                                <i class="fas fa-box-open me-2"></i> Buat Pesanan
                            </button>
                            <?php 
                            if (empty($cart_items)) {
                                echo '<small class="text-danger text-center mt-2">Keranjang kosong.</small>';
                            } elseif (empty($selected_address)) {
                                echo '<small class="text-danger text-center mt-2">Pilih/Tambahkan alamat pengiriman.</small>';
                            }
                            ?>
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
        // Pastikan angka adalah string
        let number_string = angka.toString();
        // Hapus tanda koma (jika ada) dan konversi ke integer
        number_string = number_string.replace(/[^,\d]/g, '').toString();
        
        let split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            
        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp' + rupiah;
    }

    // FUNGSI UNTUK MENGHITUNG TOTAL
    function calculateTotal() {
        // Ambil nilai dari input hidden (dalam bentuk integer)
        const subtotal = parseInt(document.getElementById('subtotal_value').value) || 0;
        const shipping = parseInt(document.getElementById('shipping_value').value) || 0;
        const discount = parseInt(document.getElementById('discount_value').value) || 0;

        const grandTotal = subtotal + shipping - discount;

        // Update tampilan
        document.getElementById('grand_total_display').innerText = formatRupiahJs(grandTotal);
        
        // Update input hidden untuk dikirim ke PHP
        document.getElementById('grand_total_value').value = grandTotal;
    }

    // FUNGSI UNTUK MENAMPILKAN ALERT KETIKA POST BERHASIL (diperlukan mekanisme session/URL)
    // Karena Anda meminta alert setelah submit, kita harus menangkap pesan dari URL/Session
    // Namun, karena proses submit berhasil akan me-redirect ke order_success.php, 
    // alert konfirmasi akan lebih cocok diletakkan di order_success.php
    
    // Jika Anda ingin alert *ketika* error/gagal checkout (sebelum redirect):
    <?php if (!empty($error_message)): ?>
        alert("Checkout Gagal:\n<?= addslashes($error_message) ?>");
    <?php endif; ?>
    
    // Panggil saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();

        // Pengecekan status radio button (alamat) saat page load
        const addressRadios = document.querySelectorAll('input[name="address_id"]');
        if (addressRadios.length > 0) {
            addressRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update tampilan visual saat radio berubah
                    document.querySelectorAll('.form-check.p-3').forEach(div => {
                        div.classList.remove('border-pink-primary', 'bg-light');
                    });
                    if (this.checked) {
                        this.closest('.form-check.p-3').classList.add('border-pink-primary', 'bg-light');
                    }
                });

                // Terapkan class pada yang terpilih di awal
                if (radio.checked) {
                    radio.closest('.form-check.p-3').classList.add('border-pink-primary', 'bg-light');
                }
            });
        }
    });

    // Validasi Sederhana
    const checkoutForm = document.getElementById('checkoutForm');
    checkoutForm.addEventListener('submit', function(e) {
        if (document.querySelectorAll('input[name="address_id"]:checked').length === 0) {
            e.preventDefault();
            alert("Mohon pilih alamat pengiriman.");
            return false;
        }
        if (document.querySelectorAll('input[name="payment_method"]:checked').length === 0) {
            e.preventDefault();
            alert("Mohon pilih metode pembayaran.");
            return false;
        }
        // Jika semua validasi JS lolos, biarkan form ter-submit
    });

</script>
</body>
</html>