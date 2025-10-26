<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Tampil Cantik & Modis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="mb-5">
        <?php include 'navbar.php' ?>

        <div class="p-5 text-center hero" style="margin-top: 56px;">
            <div class="container py-5">
                <h1 class="display-4 fw-bold text-pink-primary mb-3">Tampil Memukau Setiap Hari</h1>
                <h5 class="lead mb-4 text-dark">
                    Beauty Fashion adalah destinasi utama Anda untuk koleksi pakaian wanita terkini. **Temukan gaya
                    Anda, pancarkan percaya diri Anda.**
                </h5>
                <a href="#produk-tersedia" class="btn btn-pink btn-lg mt-3 shadow-lg">Lihat Koleksi Kami ✨</a>
            </div>
        </div>
    </header>

    <section id="special-offer" class="bg-white">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Penawaran Spesial untuk Anda</h2>
            <div id="offerCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center">
                                    <img src="assets/img/1aaa.png" class="card-img-top" alt="Gratis Ongkir">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Gratis Ongkir</h5>
                                        <p class="card-text">Belanja minimal Rp 150.000, dapatkan pengiriman gratis ke
                                            seluruh pulau Jawa.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center">
                                    <img src="assets/img/2bb.png" class="card-img-top" alt="Diskon 50%">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Diskon Tengah Tahun</h5>
                                        <p class="card-text">Dapatkan potongan hingga 50% untuk semua Dress Musim Panas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center">
                                    <img src="assets/img/3cc.png" class="card-img-top" alt="Beli 1 Gratis 1">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Promo Beli 1 Gratis 1</h5>
                                        <p class="card-text">Khusus untuk koleksi Aksesori dan Tas pilihan. Jangan
                                            sampai kehabisan!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center"><img
                                        src="https://via.placeholder.com/400x300/f06292/ffffff?text=VOUCHER+BARU"
                                        class="card-img-top" alt="Voucher Baru">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Voucher Member</h5>
                                        <p class="card-text">Daftar sekarang dan dapatkan voucher belanja Rp 25.000.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center"><img
                                        src="https://via.placeholder.com/400x300/ec407a/ffffff?text=CASHBACK"
                                        class="card-img-top" alt="Cashback">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Cashback 10%</h5>
                                        <p class="card-text">Pembayaran via e-wallet tertentu, dapatkan cashback!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm h-100 text-center"><img
                                        src="https://via.placeholder.com/400x300/e91e63/ffffff?text=EXTRA+SALE"
                                        class="card-img-top" alt="Extra Sale">
                                    <div class="card-body">
                                        <h5 class="card-title text-pink-primary">Extra Sale</h5>
                                        <p class="card-text">Diskon tambahan 10% untuk pengguna kartu kredit Bank X.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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


    <section id="populer-terkini" class="hero">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Populer Terkini 🔥</h2>
            <div id="popularCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/ff69b4/ffffff?text=BLOUSE+PINK"
                                        class="card-img-top" alt="Blouse Pink">
                                    <div class="card-body">
                                        <h5 class="card-title">Blouse Rajut Elegan</h5>
                                        <p class="card-text text-muted">Rp 199.000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/ff3399/ffffff?text=SKIRT+JEANS"
                                        class="card-img-top" alt="Skirt Jeans">
                                    <div class="card-body">
                                        <h5 class="card-title">Rok Jeans A-Line</h5>
                                        <p class="card-text text-muted">Rp 245.000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/ff99cc/ffffff?text=DRESS+FLORAL"
                                        class="card-img-top" alt="Dress Floral">
                                    <div class="card-body">
                                        <h5 class="card-title">Dress Floral Midi</h5>
                                        <p class="card-text text-muted">Rp 350.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/f06292/ffffff?text=TAS+MINI"
                                        class="card-img-top" alt="Tas Mini">
                                    <div class="card-body">
                                        <h5 class="card-title">Tas Selempang Mini</h5>
                                        <p class="card-text text-muted">Rp 120.000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/ec407a/ffffff?text=HEELS+HITAM"
                                        class="card-img-top" alt="Heels Hitam">
                                    <div class="card-body">
                                        <h5 class="card-title">High Heels Klasik</h5>
                                        <p class="card-text text-muted">Rp 280.000</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card h-100 shadow-sm"><img
                                        src="https://via.placeholder.com/400x500/e91e63/ffffff?text=JAKET+DENIM"
                                        class="card-img-top" alt="Jaket Denim">
                                    <div class="card-body">
                                        <h5 class="card-title">Jaket Denim Oversize</h5>
                                        <p class="card-text text-muted">Rp 410.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    ---

    <section id="produk-tersedia" class="bg-white">
        <div class="container">
            <h2 class="text-center mb-5 text-pink-primary fw-bold">Koleksi Produk Tersedia</h2>

            <div class="row row-cols-2 row-cols-md-5 g-4">
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x400/ffe0f0/333333?text=PRODUK+1" class="card-img-top"
                            alt="Produk 1">
                        <div class="card-body text-center">
                            <h6 class="card-title">Celana Kulot Nyaman</h6>
                            <p class="card-text fw-bold text-pink-primary">Rp 175.000</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x400/ffccf2/333333?text=PRODUK+2" class="card-img-top"
                            alt="Produk 2">
                        <div class="card-body text-center">
                            <h6 class="card-title">Kemeja Linen Oversize</h6>
                            <p class="card-text fw-bold text-pink-primary">Rp 210.000</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x400/ffb3e6/333333?text=PRODUK+3" class="card-img-top"
                            alt="Produk 3">
                        <div class="card-body text-center">
                            <h6 class="card-title">Cardigan Rajut</h6>
                            <p class="card-text fw-bold text-pink-primary">Rp 185.000</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x400/ff99da/333333?text=PRODUK+4" class="card-img-top"
                            alt="Produk 4">
                        <div class="card-body text-center">
                            <h6 class="card-title">Sandals Jepit Gaya</h6>
                            <p class="card-text fw-bold text-pink-primary">Rp 95.000</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="https://via.placeholder.com/300x400/ff80cc/333333?text=PRODUK+5" class="card-img-top"
                            alt="Produk 5">
                        <div class="card-body text-center">
                            <h6 class="card-title">Inner Manset Premium</h6>
                            <p class="card-text fw-bold text-pink-primary">Rp 75.000</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="all_product.php" class="btn btn-lg btn-outline-pink btn-pink">Lihat Semua Produk Lainnya
                    (150+)</a>
            </div>
        </div>
    </section>

    ---

    <?php include 'footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>