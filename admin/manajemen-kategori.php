<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Menggunakan file koneksi database
include '../db_connect.php';

// Ambil data kategori, hitungan produk, dan data chart
include 'proses/get_manajemen-kategori.php';

// Pastikan variabel $categories ada
$categories = $categories ?? []; 
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori | Beauty Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="style.css">
    
    <style>
        .chart-container {
            height: 250px; /* Tinggi Chart agar seragam */
            position: relative;
        }
        /* Style card untuk tampilan dark mode yang lebih baik */
        .card-dark-mode {
            background-color: #343a40; 
            color: #f8f9fa;
        }
    </style>
</head>

<body id="body-admin">
    <?php 
    // Asumsi: File ini ada dan memiliki ID 'mode-toggle' jika logic dark/light mode digunakan
    include 'sidebar.php'; 
    ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Kategori</h1>
            <p class="lead">Kelola, atur, dan pantau statistik kategori produk Anda.</p>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Tren Pendapatan Bulanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Distribusi Status Pesanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="barChartProduct"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Top 5 Produk Terlaris (Unit)</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="barChartLowStock"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#categoryModal"
                id="btn-add-category">
                <i class="fas fa-plus me-1"></i> Tambah Kategori Baru
            </button>
        </div>

        <div class="card shadow-sm p-4">
            <h5 class="mb-4 card-title-dark-mode">Daftar Kategori Produk</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 5%;">ID</th>
                            <th scope="col" style="width: 25%;">Nama Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" style="width: 15%;">Jumlah Produk</th>
                            <th scope="col" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="text-center"><?php echo $cat['id']; ?></td>
                                    <td class="fw-bold">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-dark">
                                            <?php echo number_format($cat['product_count'] ?? 0, 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning edit-btn"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#categoryModal"
                                            data-id="<?php echo $cat['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($cat['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($cat['description']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="proses/proses_manajemen-kategori.php?action=delete&id=<?php echo $cat['id']; ?>"
                                            class="btn btn-sm btn-danger delete-btn"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kategori <?php echo htmlspecialchars($cat['name']); ?>?');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kategori yang ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm" action="proses/proses_manajemen-kategori.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category_id">

                        <div class="mb-3">
                            <label for="category_name" class="form-label">Nama Kategori
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_description" class="form-label">Deskripsi Kategori</label>
                            <textarea class="form-control" id="category_description" name="category_description"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink" id="saveCategoryBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Notifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="statusMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        // Data Chart yang diambil dari PHP (get_manajemen-kategori.php)
        const chartData = <?php echo $chart_data_json; ?>;

        // ====================================================
        // Logic Dark/Light Mode (Kode asli Anda)
        // ====================================================
        const body = document.getElementById('body-admin');
        const modeToggle = document.getElementById('mode-toggle');

        function applyMode(isInitialLoad = true) {
            let savedMode = localStorage.getItem('theme') || 'light';

            if (!isInitialLoad && modeToggle) {
                savedMode = body.classList.contains('dark-mode') ? 'light' : 'dark';
                localStorage.setItem('theme', savedMode);
            }

            if (savedMode === 'dark') {
                body.classList.add('dark-mode');
                document.querySelectorAll('.modal-content').forEach(el => el.classList.add('card-dark-mode'));
                document.querySelectorAll('.card').forEach(el => el.classList.add('card-dark-mode'));
            } else {
                body.classList.remove('dark-mode');
                document.querySelectorAll('.modal-content').forEach(el => el.classList.remove('card-dark-mode'));
                document.querySelectorAll('.card').forEach(el => el.classList.remove('card-dark-mode'));
            }

            // (Kode update modeToggle icon/text dihilangkan agar fokus pada fitur inti, 
            // namun logika dark mode utama tetap dipertahankan)
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyMode(true);
            initCharts(); // Panggil inisiasi Chart di sini
            checkAndShowStatusModal(); // Panggil cek notifikasi
        });

        if (modeToggle) {
            modeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                applyMode(false);
            });
        }

        // ====================================================
        // Logic INISIASI CHART
        // ====================================================
        function initCharts() {
            const productLabels = chartData.product_distribution.labels;
            const productCounts = chartData.product_distribution.data;
            const lowStockLabels = chartData.low_stock.labels;
            const lowStockCounts = chartData.low_stock.data;
            
            // Fungsi untuk menghasilkan warna acak
            const generateColors = (count) => {
                const colors = [];
                for(let i = 0; i < count; i++) {
                    // Warna dinamis
                    colors.push(`hsl(${i * (360 / (count || 1)) + 120}, 70%, 50%)`); 
                }
                return colors;
            };
            const colors = generateColors(productLabels.length);

            const customPalette = [
                '#FF69B4', // Hot Pink
                '#C8548F', // Deep Pink
                '#FFB3D9', // Light Pink
                '#E0A6BF', // Dusty Rose
                '#C8B3E0', // Lavender Pastel
                '#FF9C9C', // Warm Peach
                '#EEDD82', // Soft Gold
                '#FFF8E7',  // Creamy White
                '#AFAFAF', // Light Gray
                '#36454F' // Charcoal Gray
            ];
            
            // Gunakan palet ini untuk Chart 1 & 2
            const chartColors = productLabels.map((_, index) => 
                customPalette[index % customPalette.length] // Ulangi warna jika kategori lebih banyak dari palet
            );
            
            // --- Chart 1: Pie Chart Distribusi Produk ---
            new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Jumlah Produk',
                        data: productCounts,
                        backgroundColor: chartColors,
                        hoverOffset: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // --- Chart 2: Bar Chart Jumlah Produk ---
            new Chart(document.getElementById('barChartProduct'), {
                type: 'bar',
                data: {
                    labels: productLabels,
                    datasets: [{
                        label: 'Jumlah Produk',
                        data: productCounts,
                        backgroundColor: chartColors,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });

            // --- Chart 3: Bar Chart Stok Terendah ---
            new Chart(document.getElementById('barChartLowStock'), {
                type: 'bar',
                data: {
                    labels: lowStockLabels,
                    datasets: [{
                        label: 'Total Stok Tersedia',
                        data: lowStockCounts,
                        backgroundColor: chartColors,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }


        // ====================================================
        // Logic JavaScript untuk Manajemen Kategori (JQuery)
        // ====================================================
        $(document).ready(function () {
            // 1. Saat tombol 'Tambah Kategori Baru' diklik
            $('#btn-add-category').on('click', function () {
                $('#categoryModalLabel').text('Tambah Kategori Baru');
                $('#categoryForm')[0].reset(); // Kosongkan formulir
                $('#category_id').val(''); // Pastikan ID kosong (mode tambah)
                $('#saveCategoryBtn').text('Simpan');
            });

            // 2. Saat tombol 'Edit' diklik
            $('.edit-btn').on('click', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                $('#categoryModalLabel').text('Edit Kategori: ' + name);
                $('#category_id').val(id);
                $('#category_name').val(name);
                $('#category_description').val(description);
                $('#saveCategoryBtn').text('Update');
                // Modal otomatis terbuka karena ada data-bs-toggle="modal"
            });
        });

        // ====================================================
        // Logic MODAL NOTIFIKASI (Pure JS)
        // ====================================================
        function checkAndShowStatusModal() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const message = urlParams.get('message');
            
            if (status && message) {
                const statusModalElement = document.getElementById('statusModal');
                const statusModal = new bootstrap.Modal(statusModalElement);
                const statusMessage = document.getElementById('statusMessage');
                const statusModalLabel = document.getElementById('statusModalLabel');
                
                // Hapus parameter URL agar modal tidak muncul lagi saat refresh
                history.replaceState(null, '', window.location.pathname);

                statusMessage.innerHTML = decodeURIComponent(message);
                
                // Atur styling dan judul berdasarkan status
                let className;
                if (status === 'success') {
                    statusModalLabel.textContent = 'Operasi Berhasil!';
                    className = 'text-success';
                } else if (status === 'error') {
                    statusModalLabel.textContent = 'Operasi Gagal!';
                    className = 'text-danger';
                } else if (status === 'warning') {
                    statusModalLabel.textContent = 'Peringatan!';
                    className = 'text-warning';
                }
                
                // Tambahkan class ke pesan
                statusMessage.className = className;
                
                statusModal.show();
            }
        }
    </script>
</body>

</html>