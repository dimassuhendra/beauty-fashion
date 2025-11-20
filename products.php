<?php
session_start();
@require_once 'db_connect.php'; 
include 'proses/proses_products.php'; 

// Cek status login
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Produk | Beauty-Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    
    <style>
        /* CSS untuk memperbaiki tampilan gambar produk */
        .product-card .card-img-top {
            height: 200px; /* Atur tinggi tetap untuk konsistensi kartu */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-body-bg); /* Mengikuti tema body */
            border-bottom: 1px solid var(--bs-border-color);
        }

        .product-image-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Penting: Memastikan gambar mengisi area container */
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image-thumb {
            transform: scale(1.05);
        }

        /* Style tambahan untuk gambar di dalam modal */
        #productDetailModal .modal-product-image {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 0.25rem;
        }
        
        .text-pink {
            color: #ff69b4 !important; /* Contoh warna pink untuk konsistensi tema */
        }
    </style>
</head>

<body data-bs-theme="light">
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold text-pink" href="index.php">Beauty-Fashion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Semua Produk</a></li>
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

    <main class="container py-5">
        <h1 class="text-center mb-5 text-pink">Katalog Produk Lengkap</h1>

        <div class="row">
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="card shadow-sm p-3 mb-4 filter-card">
                        <h5 class="mb-3 text-pink"><i class="fas fa-filter me-2"></i> Filter Produk</h5>
                        <form method="GET" action="products.php" id="filterForm">
                            <?php foreach ($query_params as $key => $val): ?>
                            <?php if ($key !== 'category' && $key !== 'stock' && $key !== 'min_price' && $key !== 'max_price' && $key !== 'search' && $key !== 'sort_by' && $key !== 'page'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                value="<?php echo htmlspecialchars($val); ?>">
                            <?php endif; ?>
                            <?php endforeach; ?>

                            <div class="mb-3">
                                <label for="category" class="form-label small">Kategori</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['slug']); ?>"
                                        <?php echo ($category_filter === $cat['slug']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label small">Ketersediaan Stok</label>
                                <select class="form-select" id="stock" name="stock">
                                    <option value="available"
                                        <?php echo ($stock_filter === 'available') ? 'selected' : ''; ?>>Tersedia (Ready
                                        Stock)</option>
                                    <option value="all" <?php echo ($stock_filter === 'all') ? 'selected' : ''; ?>>Semua
                                        Stok (Termasuk Habis)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Range Harga</label>
                                <input type="number" class="form-control mb-2" name="min_price"
                                    placeholder="Harga Minimum (Rp)"
                                    value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                                <input type="number" class="form-control" name="max_price"
                                    placeholder="Harga Maksimum (Rp)"
                                    value="<?php echo $max_price < 999999999 ? $max_price : ''; ?>">
                            </div>

                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>
                                Terapkan Filter</button>
                            <a href="products.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Reset Filter</a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row mb-4 align-items-center">
                    <div class="col-md-5">
                        <form method="GET" action="products.php" id="sortForm">
                            <?php foreach ($query_params as $key => $val): ?>
                            <?php if ($key !== 'sort_by' && $key !== 'page'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                value="<?php echo htmlspecialchars($val); ?>">
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <div class="input-group">
                                <span class="input-group-text small"><i class="fas fa-sort-amount-down me-1"></i>
                                    Urutkan:</span>
                                <select class="form-select form-select-sm" name="sort_by"
                                    onchange="document.getElementById('sortForm').submit()">
                                    <option value="latest" <?php echo ($sort_by === 'latest') ? 'selected' : ''; ?>>
                                        Terbaru</option>
                                    <option value="price_asc"
                                        <?php echo ($sort_by === 'price_asc') ? 'selected' : ''; ?>>Harga Terendah
                                    </option>
                                    <option value="price_desc"
                                        <?php echo ($sort_by === 'price_desc') ? 'selected' : ''; ?>>Harga Tertinggi
                                    </option>
                                    <option value="name_asc" <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>
                                        Nama A-Z</option>
                                    <option value="name_desc"
                                        <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>>Nama Z-A</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-7">
                        <form method="GET" action="products.php" id="searchForm">
                            <?php foreach ($query_params as $key => $val): ?>
                            <?php if ($key !== 'search' && $key !== 'page'): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>"
                                value="<?php echo htmlspecialchars($val); ?>">
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari Nama atau SKU Produk..."
                                    value="<?php echo htmlspecialchars($search_term); ?>">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                <?php if (!empty($search_term)): ?>
                                <a href="<?php echo get_query_url(['search' => '', 'page' => 1]); ?>"
                                    class="btn btn-outline-secondary" title="Hapus Pencarian"><i
                                        class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 mt-3">
                        <p class="text-muted small mb-0">Total ditemukan: <span
                                class="fw-bold"><?php echo number_format($total_products, 0); ?></span> produk.</p>
                    </div>
                </div>

                <?php if (!$is_connected): ?>
                <div class="alert alert-danger text-center" role="alert">
                    <i class="fas fa-database me-2"></i> **Kesalahan Koneksi Database:** <?php echo $error_message; ?>
                </div>
                <?php elseif ($total_products === 0): ?>
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada produk yang sesuai dengan kriteria filter Anda.
                </div>
                <?php else: ?>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 product-card shadow-sm">
                            <div class="card-img-top">
                                <?php if (!empty($product['image_url'])): ?>
                                <img src="uploads/product/<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>"
                                    class="product-image-thumb">
                                <?php else: ?>
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-subtitle mb-1 text-muted small">
                                    <?php echo htmlspecialchars($product['category_name'] ?? 'Tanpa Kategori'); ?></h6>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="product-price fw-bold">Rp
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <p class="card-text small text-muted">
                                    Stok:
                                    <span
                                        class="fw-bold text-<?php echo ($product['stock'] > 0) ? 'success' : 'danger'; ?>">
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

                <?php if ($total_pages > 1): ?>
                <nav aria-label="Product Page Navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo get_query_url(['page' => $current_page - 1]); ?>"
                                aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo get_query_url(['page' => $i]); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo get_query_url(['page' => $current_page + 1]); ?>"
                                aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php' ?>

    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailModalLabel"><i class="fas fa-info-circle me-2"></i> Detail Produk</h5>
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
    /* START: JS Halaman Produk Selengkapnya */

    // ====================================================
    // 1. Logika Dark/Light Mode
    // ====================================================
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const icon = themeToggle.querySelector('i');
    const STORAGE_KEY = 'theme-mode';

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

    const savedTheme = localStorage.getItem(STORAGE_KEY);
    const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const initialTheme = savedTheme || preferredTheme;
    applyTheme(initialTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        applyTheme(newTheme);
    });

    // ====================================================
    // 2. Logic Tampilkan Modal Detail Produk (Menggunakan AJAX)
    // ====================================================

    const isUserLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const currentUrl = encodeURIComponent(window.location.href);

    document.querySelectorAll('.btn-detail-produk').forEach(button => {
        button.addEventListener('click', function() {
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
            success: function(response) {
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
                            <a href="login.php?redirect_to=${currentUrl}" class="btn btn-warning">
                                <i class="fas fa-lock me-1"></i> Masuk untuk Beli
                            </a>
                        `;
                    }
                    footerArea.html(footerHtml);

                } else {
                    contentArea.html('<div class="alert alert-danger text-center">Gagal memuat detail produk. ' + (response.message || 'Data tidak ditemukan.') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                contentArea.html('<div class="alert alert-danger text-center">Terjadi kesalahan saat menghubungi server. Pastikan file `fetch_product_detail.php` sudah tersedia dan berfungsi.</div>');
                console.error("AJAX Error:", status, error);
            }
        });
    }

    // Fungsi placeholder untuk aksi Tambah ke Keranjang
    function addToCart(productId) {
        alert('Aksi: Tambah Produk ID ' + productId + ' ke Keranjang (Memerlukan implementasi AJAX/PHP)');
        // Implementasi logik penambahan ke keranjang harus dilakukan di sini.
    }


    // ====================================================
    // 3. Logic Form Submit Sederhana
    // ====================================================

    // Otomatis submit saat kategori atau stok diubah
    document.getElementById('category').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('stock').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    /* END: JS Halaman Produk Selengkapnya */
    </script>
</body>

</html>