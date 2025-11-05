<?php
// ====================================================================
// 1. SETUP KONEKSI DAN AKTIF PAGE
// ====================================================================

// Menggunakan file koneksi database yang Anda miliki (asumsi mendefinisikan $conn)
include '../db_connect.php';
include 'proses/get_dashboard.php';
// Menangkap nilai filter saat ini untuk ditampilkan kembali di input
$current_start_date = $start_date ?? '';
$current_end_date = $end_date ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>

    </style>
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Dashboard Overview</h1>
            <p class="lead">Ringkasan data toko Beauty Fashion.</p>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-xl-3">
                <div class="card shadow-sm h-100 p-3">
                    <h5 class="card-title text-center text-pink-primary mb-3">Filter Data Tanggal</h5>
                    <form method="GET" class="d-flex flex-column">
                        <div class="mb-2">
                            <label for="start_date" class="form-label small">Dari Tanggal:</label>
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm"
                                value="<?php echo $current_start_date; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label small">Sampai Tanggal:</label>
                            <input type="date" id="end_date" name="end_date" class="form-control form-control-sm"
                                value="<?php echo $current_end_date; ?>">
                        </div>
                        <button type="submit" class="btn btn-sm btn-pink w-100">Terapkan Filter</button>
                        <?php if($current_start_date || $current_end_date): ?>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary w-100 mt-2">Reset Filter</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="row g-4 h-100">
                    <div class="col-xl-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-muted fw-bold text-uppercase mb-1">Total Produk</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $total_products; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-box fa-2x opacity-25"
                                            style="color: var(--pink-color) !important;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-muted fw-bold text-uppercase mb-1">Total Pelanggan</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $total_users; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-users fa-2x opacity-25"
                                            style="color: var(--pink-color) !important;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-muted fw-bold text-uppercase mb-1">Total Pesanan Masuk</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $total_orders; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-shopping-cart fa-2x opacity-25"
                                            style="color: var(--pink-color) !important;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-muted fw-bold text-uppercase mb-1">Total Kategori</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $total_categories; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-tag fa-2x opacity-25"
                                            style="color: var(--pink-color) !important;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                        <div class="card shadow-sm h-100 border-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-success fw-bold text-uppercase mb-1">Total Pendapatan
                                            (Completed)</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $total_revenue; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-dollar-sign fa-2x opacity-25 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-md-6">
                        <div class="card shadow-sm h-100 border-warning">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <div class="text-warning fw-bold text-uppercase mb-1">Pesanan Tertunda (Pending
                                            Payment)</div>
                                        <div class="h3 mb-0 fw-bold"><?php echo $pending_orders; ?></div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <i class="fas fa-clock fa-2x opacity-25 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Kategori Terlaris (Berdasarkan
                            Quantity Terjual)</h5>
                        <div class="chart-container">
                            <canvas id="categoryPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Tingkat Penyelesaian Pesanan</h5>
                        <div class="chart-container">
                            <canvas id="orderDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Data PHP yang di-encode menjadi JSON (dari proses/get_dashboard.php)
    const categoryLabels = <?php echo $json_category_labels; ?>;
    const categoryData = <?php echo $json_category_data; ?>;
    const orderCompletionData = <?php echo $json_order_completion_data; ?>;
    const totalOrders = <?php echo (int)$total_orders; ?>;

    // Warna Pink untuk Branding
    const PINK_PRIMARY = '#ff69b4';
    const PINK_LIGHT = '#ffc0cb';
    const CHART_COLORS = [
        PINK_PRIMARY, '#ff4d94', '#ff80bf', '#ffb3d9', '#ffe6f2', '#f0f8ff'
    ];

    // ====================================================
    // 1. Logic Dark/Light Mode
    // ====================================================
    const body = document.getElementById('body-admin');
    const modeToggle = document.getElementById('mode-toggle');

    function applyMode() {
        const savedMode = localStorage.getItem('theme') || 'light';
        if (savedMode === 'dark') {
            body.classList.add('dark-mode');
            modeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
            modeToggle.classList.remove('text-pink-primary');
        } else {
            body.classList.remove('dark-mode');
            modeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
            modeToggle.classList.add('text-pink-primary');
        }
    }

    modeToggle.addEventListener('click', () => {
        const isDarkMode = body.classList.toggle('dark-mode');

        if (isDarkMode) {
            localStorage.setItem('theme', 'dark');
            modeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
            modeToggle.classList.remove('text-pink-primary');
        } else {
            localStorage.setItem('theme', 'light');
            modeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
            modeToggle.classList.add('text-pink-primary');
        }
        // Perlu update chart saat mode berubah
        updateChartAppearance();
    });

    applyMode();

    // ====================================================
    // 2. Logic Chart.js
    // ====================================================

    function updateChartAppearance() {
        // Logika untuk mengubah warna teks/grid chart saat mode berubah
        const chartColor = body.classList.contains('dark-mode') ? '#e0e0e0' : '#212529';
        // Placeholder: Dalam implementasi nyata, Anda akan memanggil destroy() dan recreate chart.
        // Untuk demo ini, kita hanya akan memanggil fungsi renderChart
        renderCharts(chartColor);
    }

    function renderCharts(textColor) {
        // Hancurkan chart lama jika ada
        if (window.categoryChart) window.categoryChart.destroy();
        if (window.orderChart) window.orderChart.destroy();

        // --- Pie Chart: Kategori Terlaris ---
        const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
        window.categoryChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: CHART_COLORS,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting untuk kontrol tinggi/lebar
                plugins: {
                    legend: {
                        labels: {
                            color: textColor
                        }
                    },
                    title: {
                        display: false
                    }
                }
            }
        });

        // --- Donut Chart: Tingkat Penyelesaian Pesanan ---
        const ctxDonut = document.getElementById('orderDonutChart').getContext('2d');
        const dataLabels = ['Selesai (Completed)', 'Belum Selesai'];

        // Tambahkan label persentase di tengah Donut Chart
        const donutCenterText = {
            id: 'donutCenterText',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const {
                    ctx,
                    data
                } = chart;
                ctx.save();
                const x = chart.getDatasetMeta(0).data[0].x;
                const y = chart.getDatasetMeta(0).data[0].y;

                const completedValue = data.datasets[0].data[0];
                const total = completedValue + data.datasets[0].data[1];
                const percentage = total > 0 ? ((completedValue / total) * 100).toFixed(1) + '%' : '0%';

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bolder 24px sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText(percentage, x, y);

                ctx.font = '12px sans-serif';
                ctx.fillStyle = chart.options.plugins.legend.labels.color;
                ctx.fillText('Completed', x, y + 20);
                ctx.restore();
            }
        };

        window.orderChart = new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: dataLabels,
                datasets: [{
                    data: orderCompletionData,
                    backgroundColor: [
                        '#28a745', // Green (Success for Completed)
                        '#dc3545' // Red (Danger for Not Completed)
                    ],
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Ukuran lubang Donut Chart
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor
                        }
                    },
                    title: {
                        display: false
                    }
                }
            },
            plugins: [donutCenterText]
        });
    }

    // Panggil renderCharts setelah DOM dimuat
    document.addEventListener('DOMContentLoaded', () => {
        // Panggil setelah mode diterapkan untuk mendapatkan warna teks yang benar
        updateChartAppearance();
    });
    </script>
</body>

</html>