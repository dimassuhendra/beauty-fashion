<?php
session_start();
include '../db_connect.php'; 
include 'proses/get_cart.php'; 
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <main class="py-5">
        <div class="container">
            <h2 class="cart-header mb-4 text-center">Keranjang Belanja Anda</h2>

            <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>


            <div class="row">
                <div class="col-lg-8">
                    <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                    <div class="card cart-item-card p-3">
                        <div class="row g-0 align-items-center">

                            <div class="col-md-2">
                                <img src="../uploads/product/<?= htmlspecialchars($item['image_url'] ?? '../assets/img/default.jpg'); ?>"
                                    class="cart-item-image" alt="<?= htmlspecialchars($item['name']); ?>">
                            </div>

                            <div class="col-md-6 item-details">
                                <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($item['name']); ?></h5>
                                <p class="text-muted mb-0">Harga Satuan: <?= formatRupiah($item['price']); ?></p>
                                <p class="fw-bold">Subtotal: <?= formatRupiah($item['subtotal']); ?></p>
                            </div>

                            <div class="col-md-4 text-end">
                                <form method="POST" action="cart.php" class="d-inline-block">
                                    <input type="hidden" name="item_id" value="<?= $item['cart_item_id']; ?>">
                                    <input type="hidden" name="action" value="update_quantity">

                                    <div class="input-group mb-2" style="width: 130px; margin-left: auto;">
                                        <input type="number" name="quantity" class="form-control quantity-input"
                                            value="<?= $item['quantity']; ?>" min="1" max="<?= $item['stock']; ?>"
                                            onchange="this.form.submit()">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">Update</button>
                                    </div>
                                </form>

                                <form method="POST" action="cart.php" class="d-inline-block"
                                    onsubmit="return confirmRemove();">
                                    <input type="hidden" name="item_id" value="<?= $item['cart_item_id']; ?>">
                                    <input type="hidden" name="action" value="remove_item">
                                    <button type="submit" class="btn btn-sm btn-remove">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>

                                <?php if ($item['stock'] == 0): ?>
                                <span class="badge bg-danger ms-2">Stok Habis</span>
                                <?php elseif ($item['quantity'] > $item['stock']): ?>
                                <span class="badge bg-warning text-dark ms-2">Stok Kurang (Max
                                    <?= $item['stock']; ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="alert alert-info text-center py-4" role="alert">
                        <i class="fas fa-info-circle me-2"></i> Keranjang belanja Anda kosong. Yuk, <a
                            href="products.php" class="alert-link">mulai belanja!</a>
                    </div>
                    <?php endif; ?>

                    <a href="products.php" class="btn btn-outline-secondary mt-3"><i class="fas fa-arrow-left"></i>
                        Lanjutkan Belanja</a>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary sticky-top" style="top: 80px;">
                        <h4 class="mb-3">Ringkasan Pesanan</h4>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Item Total:</span>
                            <span class="fw-bold"><?= $totalItems; ?> Produk</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total Pembayaran:</h5>
                            <h5 class="fw-bold text-danger"><?= formatRupiah($totalAmount); ?></h5>
                        </div>

                        <?php 
                        $isCartValid = true;
                        foreach ($cartItems as $item) {
                            if ($item['quantity'] > $item['stock'] || $item['stock'] == 0) {
                                $isCartValid = false;
                                break;
                            }
                        }
                        ?>

                        <a href="<?= $isCartValid ? 'checkout.php' : '#'; ?>"
                            class="btn btn-checkout w-100 py-2 <?= !$isCartValid ? 'disabled' : ''; ?>"
                            <?= !$isCartValid ? 'aria-disabled="true"' : ''; ?>>
                            <i class="fas fa-credit-card me-2"></i> Lanjutkan ke Checkout
                        </a>

                        <?php if (!$isCartValid): ?>
                        <small class="text-danger d-block mt-2 text-center">Harap perbaiki kuantitas item atau hapus
                            produk yang habis sebelum checkout.</small>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Fungsi JS untuk konfirmasi penghapusan
    function confirmRemove() {
        return confirm("Apakah Anda yakin ingin menghapus produk ini dari keranjang?");
    }

    // Fungsi ini bisa digunakan untuk reload navbar count jika Anda menggunakan AJAX
    /*
    function updateCartCountDisplay(newCount) {
        document.getElementById('cart-count').innerText = newCount;
    }
    */
    </script>
</body>

</html>