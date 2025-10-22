<?php
session_start();
// Pastikan path ke koneksi.php sudah benar
include '../db_connect.php'; 

// --- Fungsi Pembantu ---
function format_rupiah($price) {
    return 'Rp ' . number_format($price, 0, ',', '.');
}

// --- Logika Pengambilan Data ---
$products = [];
$categories = [];
// Ambil ID kategori dari URL (query parameter), jika ada
$current_category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// 1. Ambil semua kategori untuk filter sidebar
$sql_cat = "SELECT id, name FROM categories ORDER BY name ASC";
$result_cat = $conn->query($sql_cat);
if ($result_cat && $result_cat->num_rows > 0) {
    while ($row = $result_cat->fetch_assoc()) {
        $categories[] = $row;
    }
}

// 2. Query utama untuk mengambil produk
$sql_products = "SELECT p.id, p.name, p.price, p.slug, p.image_url, c.name AS category_name
                 FROM products p
                 JOIN categories c ON p.category_id = c.id
                 WHERE p.is_active = 1"; // Hanya produk yang aktif

// Tambahkan filter kategori jika ID kategori tersedia
if ($current_category_id) {
    // Penggunaan prepare statement disarankan untuk keamanan, tapi di sini menggunakan string concatenation sederhana
    $sql_products .= " AND p.category_id = " . $current_category_id;
}

// Urutkan (contoh: berdasarkan tanggal terbaru)
$sql_products .= " ORDER BY p.created_at DESC";

$result_products = $conn->query($sql_products);

if ($result_products && $result_products->num_rows > 0) {
    while ($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
}
$total_products = count($products);

// Tentukan nama kategori saat ini untuk judul halaman
$current_category_name = "Semua Produk";
if ($current_category_id && !empty($categories)) {
    foreach ($categories as $cat) {
        if ((int)$cat['id'] === $current_category_id) {
            $current_category_name = $cat['name'];
            break;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_category_name); ?> | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
    /* Styling tambahan untuk halaman ini */
    .product-card-img {
        height: 300px;
        /* Tinggi gambar yang seragam */
        object-fit: cover;
    }

    .sidebar {
        background-color: #fff0f5;
        /* Warna pink muda untuk sidebar */
        padding: 20px;
        border-radius: 8px;
    }

    .list-group-item.active-category {
        background-color: #ff69b4 !important;
        /* Warna pink utama */
        border-color: #ff69b4 !important;
        color: white !important;
        font-weight: bold;
    }

    .text-pink-primary {
        color: #ff69b4 !important;
    }

    .bg-pink-primary {
        background-color: #ff69b4 !important;
    }
    </style>
</head>

<body>
    <header>
        <?php include 'navbar.php'; // Asumsi navbar.php ada ?>
    </header>

    <div class="container" style="margin-top: 80px; min-height: 70vh;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php"
                        class="text-decoration-none text-pink-primary">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($current_category_name); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="sidebar shadow-sm">
                    <h5 class="text-pink-primary fw-bold mb-3"><i class="fas fa-filter me-2"></i> Kategori</h5>
                    <div class="list-group">
                        <a href="all_product.php"
                            class="list-group-item list-group-item-action <?php echo is_null($current_category_id) ? 'active-category' : ''; ?>">
                            Semua Produk
                        </a>

                        <?php foreach ($categories as $cat): ?>
                        <a href="all_product.php?category=<?php echo $cat['id']; ?>"
                            class="list-group-item list-group-item-action <?php echo ((int)$current_category_id === (int)$cat['id']) ? 'active-category' : ''; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="sidebar shadow-sm mt-4">
                    <h5 class="text-pink-primary fw-bold mb-3"><i class="fas fa-sort-amount-down me-2"></i> Urutkan</h5>
                    <select class="form-select">
                        <option selected>Terbaru</option>
                        <option>Harga Termurah</option>
                        <option>Harga Termahal</option>
                        <option>Nama (A-Z)</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-9">
                <h3 class="mb-4 text-pink-primary fw-bold">
                    <?php echo htmlspecialchars($current_category_name); ?>
                    <span class="badge bg-pink-primary fs-6"><?php echo $total_products; ?></span>
                </h3>

                <?php if ($total_products > 0): ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 product-item">
                            <a href="product_detail.php?slug=<?php echo htmlspecialchars($product['slug']); ?>"
                                class="text-decoration-none text-dark">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? "https://via.placeholder.com/300x400/ffe0f0/333333?text=" . urlencode($product['name'])); ?>"
                                    class="card-img-top product-card-img"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">
                                        <?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <h6 class="card-title text-truncate fw-normal">
                                        <?php echo htmlspecialchars($product['name']); ?></h6>
                                    <p class="card-text fw-bold text-pink-primary mt-2">
                                        <?php echo format_rupiah($product['price']); ?></p>
                                </div>
                            </a>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <button class="btn btn-sm btn-pink w-100">
                                    <i class="fas fa-shopping-cart me-1"></i> Beli
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-center mt-5">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">Berikutnya</a></li>
                        </ul>
                    </nav>
                </div>

                <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> Maaf, tidak ada produk yang ditemukan
                    <?php echo $current_category_id ? "untuk kategori **" . htmlspecialchars($current_category_name) . "**." : "saat ini."; ?>
                    <a href="all_product.php" class="alert-link text-pink-primary">Tampilkan Semua Produk</a>.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; // Asumsi footer.php ada ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php 
// Menutup koneksi database di akhir skrip
$conn->close(); 
?>