<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty-Fashion | Toko Pakaian Pria & Wanita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
    /* ------------------------------------- */
    /* 1. Kustomisasi Tema Pink */
    /* ------------------------------------- */
    :root {
        /* Warna Dasar untuk Light Mode */
        --primary-pink: #ff69b4;
        /* Hot Pink */
        --light-bg: #f8f9fa;
        /* Background umum */
        --light-text: #212529;
        /* Teks umum */
    }

    /* ------------------------------------- */
    /* 2. Dark Mode Styles */
    /* ------------------------------------- */
    [data-bs-theme="dark"] {
        /* Warna untuk Dark Mode */
        --bs-body-bg: #212529;
        /* Background gelap */
        --bs-body-color: #f8f9fa;
        /* Teks terang */
        --bs-card-bg: #343a40;
        /* Card gelap */
        --bs-card-color: #f8f9fa;
    }

    /* Terapkan warna primary pink kustom ke Bootstrap */
    .btn-primary,
    .bg-primary,
    .text-primary {
        --bs-btn-bg: var(--primary-pink);
        --bs-btn-border-color: var(--primary-pink);
        --bs-btn-hover-bg: #e55a9b;
        /* Pink sedikit lebih gelap */
        --bs-btn-hover-border-color: #e55a9b;
        --bs-btn-active-bg: #cc4f8a;
        --bs-btn-active-border-color: #cc4f8a;
        --bs-btn-color: #fff;
    }

    /* Navigasi kustom untuk memastikan tombol dark mode terlihat */
    .navbar-custom {
        background-color: var(--light-bg);
        /* Warna latar nav default (light) */
    }

    [data-bs-theme="dark"] .navbar-custom {
        background-color: var(--bs-body-bg) !important;
        border-bottom: 1px solid #495057;
    }

    /* Kustomisasi Teks Link Nav */
    .navbar-nav .nav-link {
        color: var(--light-text);
        font-weight: 500;
    }

    [data-bs-theme="dark"] .navbar-nav .nav-link {
        color: #f8f9fa;
    }

    /* Efek hover pada produk */
    .product-card {
        transition: transform 0.3s ease-in-out;
        border-color: rgba(255, 105, 180, 0.3);
        /* Pink border ringan */
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(255, 105, 180, 0.4);
    }

    /* Hero section styling */
    .hero-section {
        padding: 100px 0;
        background: linear-gradient(135deg, var(--primary-pink), #ffc0cb);
        /* Pink Gradient */
        color: #fff;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    [data-bs-theme="dark"] .hero-section {
        background: linear-gradient(135deg, #1c1f23, var(--primary-pink));
    }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="#pria">Pakaian Pria</a></li>
                    <li class="nav-item"><a class="nav-link" href="#wanita">Pakaian Wanita</a></li>
                    <li class="nav-item"><a class="nav-link" href="#lainnya">Aksesoris</a></li>
                </ul>
                <div class="d-flex">
                    <button class="btn btn-outline-primary me-2">Masuk / Daftar</button>

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
        <h2 class="text-center mb-5" style="color: var(--primary-pink);">Produk Unggulan</h2>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-img-top bg-light text-center p-5" style="height: 250px;">

                        <p class="text-muted mt-2">Gambar Produk Wanita</p>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Dress Elegan (ID: 001)</h5>
                        <p class="card-text text-success fw-bold">Rp 250.000</p>
                        <p class="card-text small text-muted">Stok: 12 (Data dari Database)</p>
                        <a href="#" class="btn btn-primary btn-sm mt-2">Beli Sekarang</a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-img-top bg-light text-center p-5" style="height: 250px;">

                        <p class="text-muted mt-2">Gambar Produk Pria</p>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Kemeja Casual Pria (ID: 002)</h5>
                        <p class="card-text text-success fw-bold">Rp 180.000</p>
                        <p class="card-text small text-muted">Stok: 25 (Data dari Database)</p>
                        <a href="#" class="btn btn-primary btn-sm mt-2">Beli Sekarang</a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-img-top bg-light text-center p-5" style="height: 250px;">

                        <p class="text-muted mt-2">Gambar Aksesori</p>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Tas Fashion Mewah (ID: 003)</h5>
                        <p class="card-text text-success fw-bold">Rp 350.000</p>
                        <p class="card-text small text-muted">Stok: 8 (Data dari Database)</p>
                        <a href="#" class="btn btn-primary btn-sm mt-2">Beli Sekarang</a>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 product-card">
                    <div class="card-img-top bg-light text-center p-5" style="height: 250px;">

                        <p class="text-muted mt-2">Gambar Produk Wanita</p>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Blazer Wanita Kantor (ID: 004)</h5>
                        <p class="card-text text-success fw-bold">Rp 315.000</p>
                        <p class="card-text small text-muted">Stok: 15 (Data dari Database)</p>
                        <a href="#" class="btn btn-primary btn-sm mt-2">Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-outline-primary btn-lg">Lihat Semua Koleksi</button>
            <p class="mt-3 small text-muted">Seluruh produk di atas diambil secara dinamis dari **Database** oleh sistem
                *Backend* Anda.</p>
        </div>
    </main>

    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container text-center">
            <p>&copy; 2025 Beauty-Fashion. Konten ini dikelola penuh oleh **Admin**.</p>
            <div class="small">
                <a href="#" class="text-white mx-2">Kebijakan Privasi</a> |
                <a href="#" class="text-white mx-2">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    </script>
</body>

</html>