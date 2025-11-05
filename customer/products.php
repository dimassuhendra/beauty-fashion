<?php
session_start();
include '../db_connect.php'; 
include 'proses/get_products.php'; 

// --- DEFINISI VARIABEL FILTER (Asumsi dari get_products.php) ---
if (!isset($products)) { $products = []; }
if (!isset($categories)) { $categories = []; }
if (!isset($searchQuery)) { $searchQuery = ''; }
if (!isset($categoryFilter)) { $categoryFilter = 'all'; }
if (!isset($priceOrder)) { $priceOrder = 'default'; }
if (!isset($stockStatus)) { $stockStatus = 'available'; }

// Fungsi sederhana untuk format Rupiah (Pastikan fungsi ini ada di file include atau definisikan di sini)
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        if ($angka === null) return 'Rp 0';
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <main class="py-5">
        <div class="container">
            <h2 class="mb-4 text-center" style="color: #e91e63; font-weight: 700;">Katalog Produk Unggulan</h2>
            <p class="text-center text-muted mb-5">Temukan produk kecantikan dan fashion terbaik kami.</p>

            <div class="row">
                <div class="col-lg-3">
                    <form method="GET" action="products.php" class="filter-card">
                        <h5><i class="fas fa-filter me-2"></i> Filter Produk</h5>

                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Produk</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Nama produk..." value="<?= htmlspecialchars($searchQuery); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="all">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>"
                                    <?= ($categoryFilter == $cat['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Urutan Harga</label>
                            <select class="form-select" id="order" name="order">
                                <option value="default" <?= ($priceOrder == 'default') ? 'selected' : ''; ?>>Terbaru
                                </option>
                                <option value="low" <?= ($priceOrder == 'low') ? 'selected' : ''; ?>>Harga Termurah
                                </option>
                                <option value="high" <?= ($priceOrder == 'high') ? 'selected' : ''; ?>>Harga Termahal
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block">Status Stok</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status-available"
                                    value="available" <?= ($stockStatus == 'available') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status-available">Tersedia</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status-all" value="all"
                                    <?= ($stockStatus == 'all') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status-all">Semua</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status-unavailable"
                                    value="unavailable" <?= ($stockStatus == 'unavailable') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status-unavailable">Habis</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-apply w-100">Tampilkan Produk</button>
                    </form>
                </div>

                <div class="col-lg-9">
                    <?php if (!empty($products)): ?>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card product-card h-100">
                                <img src="../uploads/product/<?= htmlspecialchars($product['image_url'] ?? 'default.jpg'); ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <p class="card-category mb-1"><?= htmlspecialchars($product['category_name']); ?>
                                    </p>
                                    <h5 class="card-title fw-bold" style="font-size: 1.1rem;">
                                        <?= htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-price mb-2"><?= formatRupiah($product['price']); ?></p>

                                    <div class="d-flex justify-content-between align-items-center mt-auto mb-3">
                                        <?php if ($product['stock'] > 0): ?>
                                        <span class="stock-label text-success"><i class="fas fa-check-circle"></i> Stok:
                                            <?= $product['stock']; ?></span>
                                        <?php else: ?>
                                        <span class="stock-label text-danger"><i class="fas fa-times-circle"></i> Stok
                                            Habis</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="product_detail.php?id=<?= $product['id']; ?>"
                                            class="btn btn-outline-buy btn-sm">
                                            <i class="fas fa-eye"></i> Detail Produk
                                        </a>

                                        <?php if ($product['stock'] > 0): ?>
                                        <button class="btn btn-buy" onclick="addToCart(<?= $product['id']; ?>)">
                                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                        </button>

                                        <button class="btn btn-quick-buy" onclick="quickBuy(<?= $product['id']; ?>)">
                                            <i class="fas fa-money-bill-wave"></i> Belanja Langsung
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-secondary" disabled>Stok Habis</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> Produk tidak ditemukan sesuai kriteria filter
                        Anda.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <?php include '../footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Fungsi untuk menambah ke keranjang
    function addToCart(productId) {
        // Ambil elemen penghitung keranjang di navbar (ID: cart-count)
        const cartCountElement = document.getElementById('cart-count');

        // Kirim permintaan AJAX ke add_to_cart.php
        fetch('proses/proses_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                }) // Selalu tambahkan 1
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tampilkan notifikasi sukses
                    alert(data.message);

                    // === PENTING: UPDATE JUMLAH KERANJANG DI NAVBAR ===
                    if (cartCountElement) {
                        cartCountElement.innerText = data.cart_count;
                    }

                } else {
                    alert('Gagal menambahkan produk: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi saat menambah ke keranjang.');
            });
    }

    // Fungsi untuk belanja langsung (pertahankan fungsi lama)
    function quickBuy(productId) {
        if (confirm("Anda akan langsung diarahkan ke halaman Checkout dengan produk ini.")) {
            // Arahkan langsung ke halaman checkout, sambil membawa ID produk
            window.location.href = `checkout.php?quick_buy_id=${productId}&qty=1`;
        }
    }
    </script>
</body>

</html>