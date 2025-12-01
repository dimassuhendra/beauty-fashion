<?php
@require_once 'db_connect.php';

// Pengecekan koneksi (mengatasi error undefined variable $conn)
if (!isset($conn) || @$conn->connect_error) {
    // Tentukan pesan error yang akan ditampilkan di produk card
    $error_message = "Gagal terhubung ke database: " . (@$conn ? $conn->connect_error : "Variabel koneksi (\$conn) tidak terdefinisi. Cek path include 'db_connect.php'.");
    $is_connected = false;
    $products = [];
} else {
    $is_connected = true;
    $error_message = '';

    // Query untuk mengambil 4 produk unggulan
    $sql = "
            SELECT *
            FROM products 
            -- Ambil 4 produk terbaru atau produk dengan stok terbanyak
            ORDER BY id DESC 
            LIMIT 4
        ";

    $result = $conn->query($sql);
    $products = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }

    // $conn->close();
}

// --------------------------------------------------------------------
// 2. TENTUKAN TAMPILAN BERDASARKAN KONEKSI
// --------------------------------------------------------------------

$featured_title = $is_connected ? "Produk Unggulan" : "Status Sistem";

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty-Fashion | Toko Pakaian Pria & Wanita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body data-bs-theme="light">

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" style="color: var(--primary-pink);" href="#">Beauty-Fashion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href="products.php?category=alat-sholat-pria&stock=available&min_price=&max_price=">Pakaian
                            Pria</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href="products.php?category=pakaian-wanita&stock=available&min_price=&max_price=">Pakaian
                            Wanita</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href="products.php?category=aksesori-muslim&stock=available&min_price=&max_price=">Aksesoris</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="login.php" class="btn btn-outline-primary me-2">Masuk / Daftar</a>

                    <button id="theme-toggle" class="btn btn-outline-secondary" title="Ganti Mode Gelap/Terang">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Gaya Terbaik Anda, Hanya di Beauty-Fashion</h1>
            <p class="lead mb-4">Temukan koleksi pakaian pria dan wanita terbaru dengan desain yang stylish dan harga
                terjangkau. Konten ini dapat dikontrol dari **Admin Dashboard**.</p>
            <a href="#produk" class="btn btn-light btn-lg fw-bold" style="color: var(--primary-pink);">Lihat Koleksi
                Sekarang</a>
        </div>
    </header>

    <main class="container py-5" id="produk">
        <h2 class="text-center mb-5" style="color: var(--primary-pink);"><?php echo $featured_title; ?></h2>

        <?php if (!$is_connected): ?>
            <div class="alert alert-danger text-center" role="alert">
                <i class="fas fa-database me-2"></i> **Kesalahan Koneksi Database:** <?php echo $error_message; ?>
                <p class="mb-0 mt-2 small">Produk tidak dapat dimuat. Harap periksa file `db_connect.php` dan jalurnya.</p>
            </div>
        <?php elseif (empty($products)): ?>
            <div class="alert alert-info text-center" role="alert">
                <i class="fas fa-info-circle me-2"></i> **Informasi:** Belum ada produk unggulan yang ditemukan di database.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm">
                            <div class="card-img-top">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="uploads/product/<?php echo $product['image_url']; ?>"
                                        alt="<?php echo $product['name']; ?>" class="product-image-thumb">
                                <?php else: ?>
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-1 text-muted small">
                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Tanpa Kategori'); ?>
                                </h6>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="product-price fw-bold">Rp
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </p>
                                <p class="card-text small text-muted">
                                    Stok:
                                    <span class="fw-bold text-<?php echo ($product['stock'] > 0) ? 'success' : 'danger'; ?>">
                                        <?php echo number_format($product['stock'], 0); ?>
                                    </span>
                                </p>
                                <button class="btn btn-primary w-100 btn-detail-produk"
                                    data-product-id="<?php echo $product['id']; ?>">
                                    Lihat Detail Produk
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="products.php" class="btn btn-outline-primary btn-lg">Lihat Semua Koleksi</a>
            <p class="mt-3 small text-muted">Seluruh produk di atas diambil secara dinamis dari **Database** oleh sistem
                *Backend* Anda.</p>
        </div>
    </main>

    <?php include 'footer.php' ?>

    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailModalLabel"><i class="fas fa-info-circle me-2"></i> Detail
                        Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-product-content">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat detail produk...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="modal-product-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        /* START: JS Halaman Pengguna (Dark Mode) */
        // Logika Dark/Light Mode
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = themeToggle.querySelector('i');
        const STORAGE_KEY = 'theme-mode';

        // Fungsi untuk menerapkan tema
        function applyTheme(theme) {
            body.setAttribute('data-bs-theme', theme);
            localStorage.setItem(STORAGE_KEY, theme);
            if (theme === 'dark') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }

        // Cek tema yang tersimpan (jika ada) atau gunakan preferensi sistem
        const savedTheme = localStorage.getItem(STORAGE_KEY);
        const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const initialTheme = savedTheme || preferredTheme;
        applyTheme(initialTheme);


        // Event listener untuk tombol toggle
        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
        });
        /* END: JS Halaman Pengguna (Dark Mode) */

        // ====================================================
        // 2. Logic Tampilkan Modal Detail Produk (Menggunakan AJAX)
        // ====================================================

        const currentUrl = encodeURIComponent(window.location.href);

        document.querySelectorAll('.btn-detail-produk').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                showProductDetailModal(productId);
            });
        });

        /**
         * Fungsi pembantu untuk menghasilkan ikon rating bintang
         * @param {number} rating - Nilai rating (0.0 hingga 5.0)
         * @returns {string} HTML string untuk ikon bintang
         */
        function generateStarRating(rating) {
            const ratingValue = parseFloat(rating) || 0;
            const fullStars = Math.floor(ratingValue);
            const halfStar = ratingValue % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            let starsHtml = '';

            // Bintang Penuh
            for (let i = 0; i < fullStars; i++) {
                starsHtml += '<i class="fas fa-star text-warning"></i>';
            }
            // Bintang Setengah
            if (halfStar) {
                starsHtml += '<i class="fas fa-star-half-alt text-warning"></i>';
            }
            // Bintang Kosong
            for (let i = 0; i < emptyStars; i++) {
                starsHtml += '<i class="far fa-star text-warning"></i>';
            }

            return starsHtml;
        }

        function showProductDetailModal(productId) {
            const modalElement = document.getElementById('productDetailModal');
            const modal = new bootstrap.Modal(modalElement);
            const contentArea = $('#modal-product-content');
            const footerArea = $('#modal-product-footer');

            // 1. Tampilkan loading state di modal
            contentArea.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail produk...</p>
            </div>
            `);
            footerArea.html('<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>');
            modal.show();

            // 2. Lakukan panggilan AJAX untuk mengambil data produk
            $.ajax({
                url: 'proses/get_product-detail.php',
                method: 'GET',
                data: { id: productId },
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data) {
                        const product = response.data;

                        // Buat HTML rating rata-rata
                        const ratingHtml = product.avg_rating > 0
                            ? `<div class="mb-3">
                                ${generateStarRating(product.avg_rating)} 
                                <span class="fw-bold me-2 ms-1">${product.avg_rating.toFixed(1)}</span>
                                <span class="text-muted small">(${product.total_reviews} Ulasan)</span>
                            </div>`
                            : `<p class="text-muted small">Belum ada ulasan.</p>`;

                        // --- GENERATE ULASAN BARU ---
                        let reviewsHtml = '';
                        if (product.reviews && product.reviews.length > 0) {
                            reviewsHtml += '<h6><i class="fas fa-comments me-1"></i> Ulasan Terbaru:</h6>';
                            // Tambahkan scrollbar jika ulasan banyak
                            reviewsHtml += '<div class="list-group list-group-flush review-list" style="max-height: 180px; overflow-y: auto;">';

                            product.reviews.forEach(review => {
                                const reviewStars = generateStarRating(review.rating);
                                const date = new Date(review.created_at);
                                const formattedDate = date.toLocaleDateString('id-ID'); // Format tanggal Indonesia

                                reviewsHtml += `
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 fw-bold text-pink">${review.reviewer_name || 'Pengguna Anonim'}</h6>
                                        <small class="text-muted">${formattedDate}</small>
                                    </div>
                                    <div class="mb-1">${reviewStars}</div>
                                    <p class="mb-1 small">${review.comment_text}</p>
                                </div>
                            `;
                            });

                            reviewsHtml += '</div>';
                        } else {
                            reviewsHtml = '<h6 class="mt-3"><i class="fas fa-comments me-1"></i> Ulasan Terbaru:</h6><p class="text-muted small">Jadilah yang pertama memberikan ulasan!</p>';
                        }
                        // --- END ULASAN BARU ---


                        // Konten Modal (HTML yang disusun ulang)
                        let htmlContent = `
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <img src="uploads/product/${product.image_url}" alt="${product.name}" class="modal-product-image mb-3 w-100 shadow-sm">
                            </div>
                            <div class="col-md-7">
                                <h2 class="text-pink">${product.name}</h2>
                                
                                ${ratingHtml} 

                                <h3 class="fw-bold mb-3">Rp ${new Intl.NumberFormat('id-ID').format(product.price)}</h3>
                                
                                <p class="text-muted small mb-1">SKU: ${product.sku || 'N/A'}</p>
                                <p class="text-muted small mb-3">Kategori: ${product.category_name || 'N/A'}</p>
                                
                                <p>Stok Tersedia: 
                                    <span class="fw-bold text-${product.stock > 0 ? 'success' : 'danger'}">
                                        ${new Intl.NumberFormat('id-ID').format(product.stock)}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-align-left me-1"></i> Deskripsi Produk:</h6>
                                <div class="modal-description" style="max-height: 180px; overflow-y: auto;">
                                    <p class="mb-4 small">${product.description || 'Tidak ada deskripsi yang tersedia.'}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                ${reviewsHtml}
                            </div>
                        </div>
                    `;
                        contentArea.html(htmlContent);

                        // Footer Modal (Tombol Aksi)
                        let footerHtml = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>';

                        if (isUserLoggedIn) {
                            // Tampilkan tombol Beli/Tambah Keranjang jika sudah login
                            footerHtml += `
                            <button class="btn btn-primary" data-product-id="${product.id}" onclick="addToCart(this.getAttribute('data-product-id'))">
                                <i class="fas fa-cart-plus me-1"></i> Tambah ke Keranjang
                            </button>
                        `;
                        } else {
                            // Tampilkan tombol Login jika belum login
                            footerHtml += `
                            <a href="login.php?redirect_to=${currentUrl}" class="btn btn-modal-sign">
                                <i class="fas fa-lock me-1"></i> Masuk untuk Beli
                            </a>
                        `;
                        }
                        footerArea.html(footerHtml);

                    } else {
                        contentArea.html('<div class="alert alert-danger text-center">Gagal memuat detail produk. ' + (response.message || 'Data tidak ditemukan.') + '</div>');
                    }
                },
                error: function (xhr, status, error) {
                    contentArea.html('<div class="alert alert-danger text-center">Terjadi kesalahan saat menghubungi server. Pastikan file `fetch_product_detail.php` sudah tersedia dan berfungsi.</div>');
                    console.error("AJAX Error:", status, error);
                }
            });
        }
    </script>
</body>

</html>