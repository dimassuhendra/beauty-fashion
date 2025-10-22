<?php
session_start();
include '../db_connect.php'; 
include 'proses/get_index.php'; 
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Tampil Cantik & Modis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="mb-5">
        <?php include 'navbar.php' ?>

        <div class="p-5 text-center hero" style="margin-top: 56px;">
            <div class="container py-5">
                <?php if ($is_logged_in): ?>
                <h1 class="display-4 fw-bold text-pink-primary mb-3">Selamat Datang Kembali,
                    <?php echo explode(' ', $user_name)[0]; ?>!</h1>
                <h5 class="lead mb-4 text-dark">
                    Kami telah menyiapkan penawaran terbaru khusus untuk Anda. **Yuk, lihat koleksi baru yang baru
                    masuk!**
                </h5>
                <a href="#produk-tersedia" class="btn btn-pink btn-lg mt-3 shadow-lg">Lanjutkan Belanja ✨</a>
                <?php else: ?>
                <h1 class="display-4 fw-bold text-pink-primary mb-3">Tampil Memukau Setiap Hari</h1>
                <h5 class="lead mb-4 text-dark">
                    Beauty Fashion adalah destinasi utama Anda untuk koleksi pakaian wanita terkini. **Temukan gaya
                    Anda, pancarkan percaya diri Anda.**
                </h5>
                <a href="#produk-tersedia" class="btn btn-pink btn-lg mt-3 shadow-lg">Lihat Koleksi Kami ✨</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    ---

    <section id="special-offer" class="bg-white">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Penawaran Spesial untuk Anda</h2>
            <div id="offerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    $coupon_chunks = array_chunk($coupons, 3);
                    $first = true;
                    if (!empty($coupon_chunks)):
                    foreach ($coupon_chunks as $chunk): 
                    ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($chunk as $coupon): ?>
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center">
                                    <img src="https://via.placeholder.com/400x300/ff99cc/ffffff?text=KODE%20<?php echo urlencode($coupon['coupon_code']); ?>"
                                        class="card-img-top" alt="Kupon">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary"><?php echo $coupon['coupon_code']; ?>
                                        </h5>
                                        <p class="card-text">
                                            Dapatkan Diskon
                                            **<?php echo number_format($coupon['discount_value'], 0); echo ($coupon['discount_type'] == 'percent' ? '%' : ' IDR'); ?>**.
                                            Min. belanja <?php echo format_rupiah($coupon['minimum_purchase']); ?>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php 
                    $first = false;
                    endforeach; 
                    else:
                    ?>
                    <div class="carousel-item active">
                        <div class="text-center p-5">Saat ini belum ada penawaran spesial yang aktif.</div>
                    </div>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#offerCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"
                        style="filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(287deg) brightness(100%) contrast(100%);"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#offerCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"
                        style="filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(287deg) brightness(100%) contrast(100%);"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    ---

    <section id="populer-terkini" class="hero">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Populer Terkini 🔥</h2>
            <div id="popularCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                    $popular_chunks = array_chunk($popular_products, 3);
                    $first_pop = true;
                    if (!empty($popular_chunks)):
                    foreach ($popular_chunks as $chunk): 
                    ?>
                    <div class="carousel-item <?php echo $first_pop ? 'active' : ''; ?>">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <?php foreach ($chunk as $product): ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <img src="<?php echo htmlspecialchars($product['image_url'] ?? "https://via.placeholder.com/400x500/ff69b4/ffffff?text=" . urlencode($product['name'])); ?>"
                                        class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="card-body">
                                        <p class="text-pink-secondary fw-bold small mb-1">
                                            <?php echo htmlspecialchars($product['category_name']); ?></p>
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text text-muted fw-bold">
                                            <?php echo format_rupiah($product['price']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php 
                    $first_pop = false;
                    endforeach; 
                    else:
                    ?>
                    <div class="carousel-item active">
                        <div class="text-center p-5">Tidak ada produk populer saat ini.</div>
                    </div>
                    <?php endif; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#popularCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"
                        style="filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(287deg) brightness(100%) contrast(100%);"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#popularCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"
                        style="filter: invert(100%) sepia(100%) saturate(0%) hue-rotate(287deg) brightness(100%) contrast(100%);"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>

    <section id="produk-tersedia" class="bg-white">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Koleksi Produk Tersedia</h2>

            <div class="row row-cols-2 row-cols-md-5 g-4">
                <?php if (!empty($collection_products)): ?>
                <?php foreach ($collection_products as $product): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($product['image_url'] ?? "https://via.placeholder.com/300x400/ffe0f0/333333?text=" . urlencode($product['name'])); ?>"
                            class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body text-center">
                            <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <p class="card-text fw-bold text-pink-primary">
                                <?php echo format_rupiah($product['price']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12 text-center p-5">
                    <p class="lead text-muted">Maaf, belum ada produk yang tersedia saat ini.</p>
                </div>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5">
                <a href="all-product.php" class="btn btn-lg btn-outline-pink btn-pink">Lihat Semua Produk Lainnya
                    (<?php echo $conn->query("SELECT COUNT(id) FROM products WHERE is_active = 1")->fetch_row()[0]; ?>+)</a>
            </div>
        </div>
    </section>

    ---

    <?php include '../footer.php'; 
    // Menutup koneksi database di akhir skrip
    $conn->close();
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>