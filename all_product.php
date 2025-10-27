<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Semua Koleksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJ8u7S1iJt4zVdYc+p7qL0r1K6z5b7XF8bBfA6R2I5J9j8pG6P/T8Z6fA0V9zQ=="
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

    <header>
        <?php include 'navbar.php' ?>
    </header>

    <main class="py-5 bg-pink-secondary">
        <div class="container">
            <h1 class="text-center mb-5 text-pink-primary fw-bold">Semua Koleksi Terbaik Kami</h1>

            <div class="row">
                <div class="col-lg-3">
                    <div class="filter-sidebar">
                        <h4 class="mb-4 text-pink-primary"><i class="fas fa-sliders-h me-2"></i> Filter Produk</h4>

                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Cari Cepat</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari Produk"
                                    id="searchKeyword">
                                <button class="btn btn-pink" type="button" id="applySearchButton"><i
                                        class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Kategori</h6>
                            <div class="form-check">
                                <input class="form-check-input category-filter" type="checkbox" id="catDress"
                                    value="dress" checked>
                                <label class="form-check-label" for="catDress">Dress (25)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input category-filter" type="checkbox" id="catAtasan"
                                    value="atasan">
                                <label class="form-check-label" for="catAtasan">Atasan & Blouse (40)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input category-filter" type="checkbox" id="catBawahan"
                                    value="bawahan">
                                <label class="form-check-label" for="catBawahan">Bawahan (35)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input category-filter" type="checkbox" id="catAksesoris"
                                    value="aksesoris">
                                <label class="form-check-label" for="catAksesoris">Aksesoris (50)</label>
                            </div>
                        </div>

                        <div class="filter-group mb-4">
                            <h6 class="text-pink-primary">Rentang Harga (<span id="priceValue">Rp 1.000.000</span>)</h6>
                            <input type="range" class="form-range" min="50000" max="1000000" step="50000"
                                id="priceRange" value="1000000">
                            <div class="d-flex justify-content-between">
                                <small>Rp 50.000</small>
                                <small>Rp 1.000.000</small>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button class="btn btn-pink" id="applyFilterButton"><i class="fas fa-filter me-2"></i>
                                Terapkan Filter</button>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-outline-secondary" id="resetFilterButton">Reset Filter</button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="d-flex justify-content-end mb-4">
                        <div class="col-md-4 col-sm-6">
                            <select class="form-select" id="sortSelect" aria-label="Urutkan Berdasarkan...">
                                <option value="default" selected>Urutkan Berdasarkan...</option>
                                <option value="price-asc">Harga: Terendah ke Tertinggi</option>
                                <option value="price-desc">Harga: Tertinggi ke Terendah</option>
                                <option value="name-asc">Nama Produk: A - Z (Abjad)</option>
                                <option value="name-desc">Nama Produk: Z - A (Abjad)</option>
                                <option value="newest">Terbaru</option>
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4" id="productGrid">
                    </div>

                    <nav class="mt-5 d-flex justify-content-center">
                    </nav>

                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
    // Data Produk Statis (Disimpan di JavaScript agar filter bisa bekerja tanpa backend/AJAX)
    const allProducts = [{
            id: 1,
            name: "Elegant Chiffon Dress",
            category: "dress",
            price: 385000,
            image: "https://via.placeholder.com/300x400/ffb6c1/ffffff?text=ELEGANT+DRESS",
            date: new Date('2025-01-15')
        },
        {
            id: 2,
            name: "Atasan Silk Premium",
            category: "atasan",
            price: 220000,
            image: "https://via.placeholder.com/300x400/f08080/ffffff?text=SILK+BLOUSE",
            date: new Date('2025-01-20')
        },
        {
            id: 3,
            name: "Celana Kulot Casual",
            category: "bawahan",
            price: 160000,
            image: "https://via.placeholder.com/300x400/dda0dd/ffffff?text=CULOTTES",
            date: new Date('2025-02-01')
        },
        {
            id: 4,
            name: "Skinny Jeans Putih",
            category: "bawahan",
            price: 299000,
            image: "https://via.placeholder.com/300x400/ee82ee/ffffff?text=JEANS",
            date: new Date('2025-02-10')
        },
        {
            id: 5,
            name: "Tas Tangan Mini",
            category: "aksesoris",
            price: 175000,
            image: "https://via.placeholder.com/300x400/ffc0cb/333333?text=MINI+BAG",
            date: new Date('2025-03-05')
        },
        {
            id: 6,
            name: "Sweater Rajut Hangat",
            category: "atasan",
            price: 240000,
            image: "https://via.placeholder.com/300x400/ff69b4/ffffff?text=SWEATER",
            date: new Date('2025-03-12')
        },
        {
            id: 7,
            name: "Pointed Heels Classic",
            category: "aksesoris",
            price: 450000,
            image: "https://via.placeholder.com/300x400/ff1493/ffffff?text=HEELS",
            date: new Date('2025-04-01')
        },
        {
            id: 8,
            name: "Jam Tangan Minimalis",
            category: "aksesoris",
            price: 520000,
            image: "https://via.placeholder.com/300x400/c71585/ffffff?text=WATCH",
            date: new Date('2025-04-10')
        },
        {
            id: 9,
            name: "Kemeja Tie-Dye",
            category: "atasan",
            price: 195000,
            image: "https://via.placeholder.com/300x400/ffb6c1/333333?text=TOP+TIE",
            date: new Date('2025-04-20')
        },
        {
            id: 10,
            name: "Blazer Kerja Wanita",
            category: "atasan",
            price: 340000,
            image: "https://via.placeholder.com/300x400/f08080/333333?text=BLAZER",
            date: new Date('2025-05-01')
        },
        {
            id: 11,
            name: "Scarf Motif Bunga",
            category: "aksesoris",
            price: 75000,
            image: "https://via.placeholder.com/300x400/dda0dd/333333?text=SCARF",
            date: new Date('2025-05-15')
        },
        {
            id: 12,
            name: "High-Waist Pants",
            category: "bawahan",
            price: 210000,
            image: "https://via.placeholder.com/300x400/ee82ee/333333?text=PANTS",
            date: new Date('2025-05-25')
        },
        {
            id: 13,
            name: "Floral Summer Dress",
            category: "dress",
            price: 420000,
            image: "https://via.placeholder.com/300x400/ff69b4/ffffff?text=FLORAL+DRESS",
            date: new Date('2025-06-01')
        },
        {
            id: 14,
            name: "Basic Cotton T-shirt",
            category: "atasan",
            price: 95000,
            image: "https://via.placeholder.com/300x400/ffc0cb/333333?text=T-SHIRT",
            date: new Date('2025-06-10')
        },
        {
            id: 15,
            name: "Pleated Skirt",
            category: "bawahan",
            price: 180000,
            image: "https://via.placeholder.com/300x400/ff1493/ffffff?text=SKIRT",
            date: new Date('2025-06-20')
        }
        // Tambahkan data produk lain di sini
    ];

    let filteredProducts = [...allProducts]; // Salinan awal data produk

    const productGrid = document.getElementById('productGrid');
    const searchKeyword = document.getElementById('searchKeyword');
    const priceRange = document.getElementById('priceRange');
    const priceValueSpan = document.getElementById('priceValue');
    const categoryCheckboxes = document.querySelectorAll('.category-filter');
    const sortSelect = document.getElementById('sortSelect');
    const applyFilterButton = document.getElementById('applyFilterButton');
    const applySearchButton = document.getElementById('applySearchButton');
    const resetFilterButton = document.getElementById('resetFilterButton');

    // Fungsi untuk memformat angka menjadi Rupiah
    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    // Fungsi untuk merender produk ke dalam grid
    const renderProducts = (products) => {
        productGrid.innerHTML = ''; // Kosongkan grid
        if (products.length === 0) {
            productGrid.innerHTML =
                `<div class="col-12"><div class="alert alert-warning text-center" role="alert">Tidak ada produk yang cocok dengan filter.</div></div>`;
            return;
        }

        products.forEach(product => {
            const productHtml = `
                    <div class="col product-item" data-category="${product.category}" data-price="${product.price}">
                        <div class="card product-card shadow-sm h-100">
                            <img src="${product.image}"
                                class="card-img-top" alt="${product.name}">
                            <div class="card-body">
                                <h6 class="card-title fw-bold">${product.name}</h6>
                                <p class="card-text text-pink-primary fw-bold">${formatRupiah(product.price)}</p>
                                <button class="btn btn-sm btn-pink w-100 mt-2"><i class="fas fa-shopping-cart"></i>
                                    Beli</button>
                            </div>
                        </div>
                    </div>
                `;
            productGrid.insertAdjacentHTML('beforeend', productHtml);
        });
    };

    // Fungsi Filter Utama
    const applyFilter = () => {
        let tempProducts = [...allProducts];

        // 1. Filter Kategori
        const selectedCategories = Array.from(categoryCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedCategories.length > 0) {
            tempProducts = tempProducts.filter(product => selectedCategories.includes(product.category));
        }

        // 2. Filter Harga (Max Price)
        const maxPrice = parseInt(priceRange.value);
        tempProducts = tempProducts.filter(product => product.price <= maxPrice);

        // 3. Filter Pencarian (Keyword)
        const keyword = searchKeyword.value.toLowerCase().trim();
        if (keyword) {
            tempProducts = tempProducts.filter(product =>
                product.name.toLowerCase().includes(keyword)
            );
        }

        filteredProducts = tempProducts;
        applySort(); // Terapkan sorting setelah filtering
    };

    // Fungsi Sorting
    const applySort = () => {
        const sortValue = sortSelect.value;
        let sortedProducts = [...filteredProducts];

        switch (sortValue) {
            case 'price-asc':
                sortedProducts.sort((a, b) => a.price - b.price);
                break;
            case 'price-desc':
                sortedProducts.sort((a, b) => b.price - a.price);
                break;
            case 'name-asc':
                sortedProducts.sort((a, b) => a.name.localeCompare(b.name));
                break;
            case 'name-desc':
                sortedProducts.sort((a, b) => b.name.localeCompare(a.name));
                break;
            case 'newest':
                sortedProducts.sort((a, b) => b.date - a.date);
                break;
            default:
                // Tidak ada sorting, gunakan urutan default dari allProducts
                // Untuk menjaga konsistensi, bisa diurutkan berdasarkan ID atau tanggal awal jika ada.
                // Untuk saat ini, kita biarkan saja sesuai urutan filteredProducts.
                break;
        }

        renderProducts(sortedProducts);
    };

    // Fungsi Reset Filter
    const resetFilters = () => {
        // Reset Pencarian
        searchKeyword.value = '';

        // Reset Kategori (Pilih semua atau tidak sama sekali, tergantung default Anda)
        categoryCheckboxes.forEach(checkbox => {
            // Biarkan default awal: Dress terpilih
            checkbox.checked = checkbox.value === 'dress';
        });

        // Reset Harga
        const maxRange = priceRange.max;
        priceRange.value = maxRange;
        priceValueSpan.textContent = formatRupiah(maxRange);

        // Reset Sorting
        sortSelect.value = 'default';

        // Terapkan ulang filter
        applyFilter();
    };

    // --- Event Listeners ---

    // Update nilai harga di Rentang Harga saat slider digeser
    priceRange.addEventListener('input', () => {
        priceValueSpan.textContent = formatRupiah(parseInt(priceRange.value));
    });

    // Terapkan filter saat tombol "Terapkan Filter" diklik
    applyFilterButton.addEventListener('click', applyFilter);

    // Terapkan filter saat tombol search diklik (ini hanya untuk keyword)
    applySearchButton.addEventListener('click', applyFilter);

    // Terapkan filter saat tombol Enter di kolom pencarian
    searchKeyword.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFilter();
        }
    });

    // Terapkan filter saat checkbox kategori diubah
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilter);
    });

    // Terapkan sorting saat dropdown diubah
    sortSelect.addEventListener('change', applySort);

    // Reset Filter
    resetFilterButton.addEventListener('click', resetFilters);

    // Inisialisasi: Panggil renderProducts saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        // Tampilkan harga awal
        priceValueSpan.textContent = formatRupiah(parseInt(priceRange.value));
        // Terapkan filter awal (dengan default filter yang ada)
        applyFilter();
    });
    </script>
</body>

</html>