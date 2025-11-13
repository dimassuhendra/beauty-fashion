<?php
session_start();
@require_once 'db_connect.php'; 
include 'proses/proses_products.php';
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
                                    <i class="fas fa-image me-2 text-muted"></i>
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

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="loginModalLabel"><i class="fas fa-lock me-2"></i> Perlu Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Anda harus **Masuk (Login)** terlebih dahulu untuk melihat detail produk ini dan melakukan
                    pembelian.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a id="modalLoginButton" href="login.php" class="btn btn-primary">Lanjut ke Halaman Login</a>
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
    // 2. Logic Check Login dan Redirect (Button Detail)
    // ====================================================

    // Asumsi: Kita cek status login dari variabel sesi PHP.
    const isUserLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

    document.querySelectorAll('.btn-detail-produk').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const detailUrl = `product_detail.php?id=${productId}`;

            if (isUserLoggedIn) {
                // Jika sudah login, langsung ke halaman detail
                window.location.href = detailUrl;
            } else {
                // Jika belum login, tampilkan modal konfirmasi
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();

                // Tombol di modal akan diarahkan ke halaman login
                document.getElementById('modalLoginButton').href =
                    `login.php?redirect_to=${encodeURIComponent(detailUrl)}`;
            }
        });
    });

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