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
        SELECT id, name, price, stock
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
    
    // Tutup koneksi setelah selesai mengambil data
    $conn->close();
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
                <div class="card h-100 product-card">
                    <div class="card-img-top bg-light text-center p-5"
                        style="height: 250px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-tshirt fa-3x text-muted"></i>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-success fw-bold">Rp
                            <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                        <p class="card-text small text-muted">Stok:
                            <?php echo number_format($product['stock'], 0); ?> (ID:
                            <?php echo $product['id']; ?>)</p>
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>"
                            class="btn btn-primary btn-sm mt-2">Beli Sekarang</a>
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
    </script>
</body>

</html>