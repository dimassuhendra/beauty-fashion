<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Semua Koleksi</title>
    <!-- Link Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Link Font Awesome untuk Ikon Filter -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ8u7S1iJt4zVdYc+p7qL0r1K6z5b7XF8bBfA6R2I5J9j8pG6P/T8Z6fA0V9zQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/style.css">

    <style>
    /* CSS Kustom untuk nuansa Pink - Konsisten dengan halaman lain */
    :root {
        --bs-pink-primary: #ff69b4;
        /* Hot Pink */
        --bs-pink-secondary: #fce4ec;
        /* Light Pink */
        --bs-text-dark: #343a40;
    }

    .bg-pink-primary {
        background-color: var(--bs-pink-primary) !important;
    }

    .text-pink-primary {
        color: var(--bs-pink-primary) !important;
    }

    .bg-pink-secondary {
        background-color: var(--bs-pink-secondary) !important;
    }

    .btn-pink {
        background-color: var(--bs-pink-primary);
        border-color: var(--bs-pink-primary);
        color: white;
    }

    .btn-pink:hover {
        background-color: #e91e63;
        border-color: #e91e63;
        color: white;
    }

    .navbar-pink .nav-link:hover {
        color: var(--bs-pink-primary) !important;
    }

    /* Styling Produk Card yang Elegan */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 105, 180, 0.2);
        /* Shadow dengan nuansa pink */
    }

    .product-card img {
        border-bottom: 1px solid var(--bs-pink-secondary);
        height: 300px;
        /* Tinggi seragam untuk tampilan grid yang rapi */
        object-fit: cover;
    }

    .product-card .card-body {
        padding: 1.25rem;
        text-align: center;
    }

    /* Styling Filter Sidebar */
    .filter-sidebar {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .filter-group h6 {
        border-bottom: 2px solid var(--bs-pink-primary);
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        color: var(--bs-pink-primary);
    }

    .form-check-input:checked {
        background-color: var(--bs-pink-primary);
        border-color: var(--bs-pink-primary);
    }

    .form-select:focus,
    .form-control:focus {
        border-color: var(--bs-pink-primary);
        box-shadow: 0 0 0 0.25rem rgba(255, 105, 180, 0.25);
    }

    /* Margin untuk Header agar tidak tertutup Navbar */
    main {
        margin-top: 3rem;
        padding-top: 56px;
    }

    /* Responsive untuk filter di mobile */
    @media (max-width: 991px) {
        .filter-sidebar {
            margin-bottom: 2rem;
        }
    }
    </style>
</head>

<body>

    <!-- Navigasi (Sama seperti index.html) -->
    <header>
        <?php include 'navbar.php' ?>
    </header>

    <!-- Konten Utama: Filter Sidebar dan Produk Grid -->
    <main class="py-5 bg-pink-secondary">
        <div class="container">
            <h1 class="text-center mb-5 text-pink-primary fw-bold">Semua Koleksi Terbaik Kami</h1>

            <div class="row">
                <!-- Kolom Kiri: Filter Sidebar (Lebar 3/12) -->
                <div class="col-lg-3">
                    <div class="filter-sidebar">
                        <h4 class="mb-4 text-pink-primary"><i class="fas fa-sliders-h me-2"></i> Filter Produk</h4>

                        <!-- Form Pencarian -->
                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Cari Cepat</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari Produk">
                                <button class="btn btn-pink" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <!-- Filter Kategori -->
                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Kategori</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="catDress" checked>
                                <label class="form-check-label" for="catDress">Dress (25)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="catAtasan">
                                <label class="form-check-label" for="catAtasan">Atasan & Blouse (40)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="catBawahan">
                                <label class="form-check-label" for="catBawahan">Bawahan (35)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="catAksesoris">
                                <label class="form-check-label" for="catAksesoris">Aksesoris (50)</label>
                            </div>
                        </div>

                        <!-- Filter Harga (Range Sederhana) -->
                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Rentang Harga</h6>
                            <input type="range" class="form-range" min="50000" max="1000000" step="50000"
                                id="priceRange">
                            <div class="d-flex justify-content-between">
                                <small>Rp 50.000</small>
                                <small>Rp 1.000.000</small>
                            </div>
                        </div>

                        <!-- Tombol Terapkan Filter -->
                        <div class="d-grid mb-3">
                            <button class="btn btn-pink"><i class="fas fa-filter me-2"></i> Terapkan Filter</button>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Produk Listing (Lebar 9/12) -->
                <div class="col-lg-9">
                    <!-- Sorting Dropdown (Harga & Abjad) -->
                    <div class="d-flex justify-content-end mb-4">
                        <div class="col-md-4 col-sm-6">
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Urutkan Berdasarkan...</option>
                                <option value="1">Harga: Terendah ke Tertinggi</option>
                                <option value="2">Harga: Tertinggi ke Terendah</option>
                                <option value="3">Nama Produk: A - Z (Abjad)</option>
                                <option value="4">Nama Produk: Z - A (Abjad)</option>
                                <option value="5">Terbaru</option>
                            </select>
                        </div>
                    </div>

                    <!-- Produk Grid -->
                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4">
                        <!-- Produk Item (Total 12 contoh) -->
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ffb6c1/ffffff?text=ELEGANT+DRESS"
                                    class="card-img-top" alt="Elegant Dress">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Elegant Chiffon Dress</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 385.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/f08080/ffffff?text=SILK+BLOUSE"
                                    class="card-img-top" alt="Silk Blouse">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Atasan Silk Premium</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 220.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/dda0dd/ffffff?text=CULOTTES"
                                    class="card-img-top" alt="Culottes">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Celana Kulot Casual</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 160.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ee82ee/ffffff?text=JEANS"
                                    class="card-img-top" alt="Skinny Jeans">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Skinny Jeans Putih</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 299.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 2 Produk -->
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ffc0cb/333333?text=MINI+BAG"
                                    class="card-img-top" alt="Mini Bag">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Tas Tangan Mini</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 175.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ff69b4/ffffff?text=SWEATER"
                                    class="card-img-top" alt="Sweater Rajut">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Sweater Rajut Hangat</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 240.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ff1493/ffffff?text=HEELS"
                                    class="card-img-top" alt="Pointed Heels">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Pointed Heels Classic</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 450.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/c71585/ffffff?text=WATCH"
                                    class="card-img-top" alt="Jam Tangan">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Jam Tangan Minimalis</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 520.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>

                        <!-- Baris 3 Produk -->
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ffb6c1/333333?text=TOP+TIE"
                                    class="card-img-top" alt="Top Tie">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Kemeja Tie-Dye</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 195.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/f08080/333333?text=BLAZER"
                                    class="card-img-top" alt="Blazer">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Blazer Kerja Wanita</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 340.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/dda0dd/333333?text=SCARF"
                                    class="card-img-top" alt="Scarf">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">Scarf Motif Bunga</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 75.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card product-card shadow-sm h-100">
                                <img src="https://via.placeholder.com/300x400/ee82ee/333333?text=PANTS"
                                    class="card-img-top" alt="Pants">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold">High-Waist Pants</h6>
                                    <p class="card-text text-pink-primary fw-bold">Rp 210.000</p>
                                    <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                        Beli</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Pagination -->
                    <nav class="mt-5 d-flex justify-content-center">
                        <ul class="pagination">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active" aria-current="page"><a
                                    class="page-link bg-pink-primary border-pink-primary" href="#">1</a></li>
                            <li class="page-item"><a class="page-link text-pink-primary" href="#">2</a></li>
                            <li class="page-item"><a class="page-link text-pink-primary" href="#">3</a></li>
                            <li class="page-item"><a class="page-link text-pink-primary" href="#">Next</a></li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </main>

    <!-- Footer (Placeholder, Anda bisa salin footer lengkap dari index.html) -->
    <?php include 'footer.php' ?>

    <!-- Link Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>



</html>