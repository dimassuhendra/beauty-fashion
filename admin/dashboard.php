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

// Hitung rasio penyelesaian untuk Gauge Chart
$completion_ratio = ($total_orders > 0) ? round(($completed_orders / $total_orders) * 100, 1) : 0;
// Data untuk Gauge Chart: [Completed, Sisa Persentase]
$gauge_data = [$completion_ratio, 100 - $completion_ratio];
$json_gauge_data = json_encode($gauge_data);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Beauty Fashion</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Dashboard Overview</h1>
            <p class="lead">Ringkasan data toko Beauty Fashion.</p>
        </header>

        <!-- ============================================ -->
        <!-- Bagian Card  -->
        <!-- ============================================ -->
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
                        <?php if ($current_start_date || $current_end_date): ?>
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
        
        <!-- ============================================ -->
        <!-- Bagian Chart/Diagram  -->
        <!-- ============================================ -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Tren Pendapatan Bulanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Distribusi Status Pesanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="orderDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Rasio Pesanan Selesai/Total Pesanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="conversionGaugeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row g-4">
            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Kategori Terlaris (Quantity)</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="categoryPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center text-pink-primary mb-4">Rasio Selesai/Total Pesanan</h5>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="topProductBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>


    <script>
        Chart.register(ChartDataLabels); // Daftarkan plugin untuk digunakan

        // Data PHP yang di-encode menjadi JSON
        const categoryLabels = <?php echo $json_category_labels; ?>;
        const categoryData = <?php echo $json_category_data; ?>;
        const orderStatusLabels = <?php echo $json_order_status_labels; ?>;
        const orderStatusData = <?php echo $json_order_status_data; ?>;
        const totalOrders = <?php echo (int) $total_orders; ?>;
        const completedOrders = <?php echo (int) $completed_orders; ?>;

        // DATA BARU
        const monthlySalesLabels = <?php echo $json_monthly_sales_labels; ?>;
        const monthlySalesRevenue = <?php echo $json_monthly_sales_revenue; ?>;
        const topProductLabels = <?php echo $json_top_product_labels; ?>;
        const topProductData = <?php echo $json_top_product_data; ?>;
        const gaugeData = <?php echo $json_gauge_data; ?>; // [Completed %, Sisa %]

        // Warna Pink untuk Branding
        const PINK_PRIMARY = '#ff69b4';
        const PINK_LIGHT = '#ffc0cb';
        const CHART_COLORS = [
            PINK_PRIMARY, '#ff4d94', '#ff80bf', '#ffb3d9', '#ffe6f2', '#f0f8ff'
        ];

        // ====================================================
        // 1. Logic Dark/Light Mode
        // (Kode Dark/Light Mode Anda di sini)
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

        // Plugin untuk menampilkan rasio Completed di tengah Donut Order Status (sudah ada)
        const donutCenterText = {
            id: 'donutCenterText',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data } = chart;
                ctx.save();
                const x = chart.getDatasetMeta(0).data[0].x;
                const y = chart.getDatasetMeta(0).data[0].y;

                const textColor = chart.options.plugins.legend.labels.color;
                // Hitung persentase Completed
                const percentage = totalOrders > 0 ? ((completedOrders / totalOrders) * 100).toFixed(1) + '%' : '0%';

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bolder 24px sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText(percentage, x, y - 10);

                ctx.font = '12px sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText('Pesanan Selesai', x, y + 15);
                ctx.restore();
            }
        };

        // Plugin untuk menampilkan nilai di tengah Gauge Chart
        const gaugeCenterText = {
            id: 'gaugeCenterText',
            beforeDatasetsDraw(chart, args, pluginOptions) {
                const { ctx, data } = chart;
                const completion = data.datasets[0].data[0];
                const x = chart.getDatasetMeta(0).data[0].x;
                const y = chart.getDatasetMeta(0).data[0].y;
                const textColor = chart.options.plugins.legend.labels.color;

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';

                // Angka Persentase
                ctx.font = 'bolder 36px sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText(completion + '%', x, y - 15);

                // Label
                ctx.font = '14px sans-serif';
                ctx.fillStyle = textColor;
                ctx.fillText('Rasio Selesai', x, y + 15);
                ctx.restore();
            }
        };


        function updateChartAppearance() {
            const chartColor = body.classList.contains('dark-mode') ? '#e0e0e0' : '#212529';
            renderCharts(chartColor);
        }

        function renderCharts(textColor) {
            // Hancurkan chart lama jika ada
            if (window.categoryChart) window.categoryChart.destroy();
            if (window.orderChart) window.orderChart.destroy();
            if (window.revenueChart) window.revenueChart.destroy(); // BARU
            if (window.productChart) window.productChart.destroy(); // BARU
            if (window.gaugeChart) window.gaugeChart.destroy(); // BARU


            // --- 1. Line Chart: Tren Pendapatan Bulanan ---
            const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
            window.revenueChart = new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: monthlySalesLabels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: monthlySalesRevenue,
                        backgroundColor: 'rgba(255, 105, 180, 0.2)', // Pink Light
                        borderColor: PINK_PRIMARY,
                        pointBackgroundColor: PINK_PRIMARY,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        // Format Rupiah
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Pendapatan (Juta Rp)', color: textColor },
                            ticks: {
                                color: textColor,
                                // Format Y-axis menjadi Rupiah singkat (misal 10.000.000 menjadi 10 Jt)
                                callback: function (value, index, values) {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(0) + ' Jt';
                                    }
                                    return value;
                                }
                            }
                        },
                        x: {
                            ticks: { color: textColor }
                        }
                    }
                }
            });


            // --- 2. Donut Chart: Seluruh Status Pesanan ---
            const ctxDonut = document.getElementById('orderDonutChart').getContext('2d');

            const STATUS_COLORS = [
                '#28a745', // Selesai (Hijau)
                '#007bff', // Dikirim (Biru)
                '#ffc107', // Diproses (Kuning/Warning)
                '#6c757d', // Menunggu Pembayaran (Abu-abu)
                '#dc3545'  // Dibatalkan (Merah/Danger)
            ];


            window.orderChart = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: orderStatusLabels,
                    datasets: [{
                        data: orderStatusData,
                        backgroundColor: STATUS_COLORS,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 10
                            }
                        },
                        title: { display: false }
                    }
                },
                plugins: [donutCenterText]
            });

            // --- 5. Gauge Chart: Rasio Selesai / Total Pesanan (Simulasi Doughnut) ---
            const ctxGauge = document.getElementById('conversionGaugeChart').getContext('2d');
            window.gaugeChart = new Chart(ctxGauge, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Sisa'],
                    datasets: [{
                        data: gaugeData,
                        backgroundColor: ['#28a745', '#e9ecef'], // Hijau dan Abu-abu terang
                        hoverOffset: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    rotation: 270, // Mulai dari bawah
                    circumference: 180, // Hanya setengah lingkaran
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        title: { display: false }
                    }
                },
                plugins: [gaugeCenterText]
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