<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Menggunakan file koneksi database
include '../db_connect.php';

// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. PENGATURAN FILTER TANGGAL
// --------------------------------------------------------------------

// Default: Ambil data mulai dari awal bulan hingga hari ini
$default_start = date('Y-m-01');
$default_end = date('Y-m-d');

$start_date = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : $default_start;
$end_date = isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : $default_end;

// Pastikan filter tanggal valid dan end_date tidak lebih kecil dari start_date
if (strtotime($start_date) > strtotime($end_date)) {
    $end_date = $start_date; // Koreksi jika salah input
}

// Klausa WHERE untuk filter
$date_filter_clause = " WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date' ";


// --------------------------------------------------------------------
// 3. QUERY RINGKASAN (SUMMARY CARDS)
// --------------------------------------------------------------------

$sql_summary = "
    SELECT 
        COUNT(id) as total_orders,
        SUM(final_amount) as total_revenue_gross,
        SUM(CASE WHEN order_status = 'Completed' THEN final_amount ELSE 0 END) as total_revenue_net
    FROM orders
    $date_filter_clause
";

$summary_result = $conn->query($sql_summary);
$summary = $summary_result->fetch_assoc();


// --------------------------------------------------------------------
// 4. QUERY LAPORAN HARIAN (TABEL)
// --------------------------------------------------------------------

$sql_daily = "
    SELECT 
        DATE(order_date) as report_date,
        COUNT(id) as daily_orders,
        SUM(final_amount) as daily_revenue,
        SUM(CASE WHEN order_status = 'Completed' THEN final_amount ELSE 0 END) as daily_net_revenue,
        SUM(CASE WHEN order_status = 'Completed' THEN 1 ELSE 0 END) as daily_completed_orders
    FROM orders
    $date_filter_clause
    GROUP BY report_date
    ORDER BY report_date DESC
";

$daily_result = $conn->query($sql_daily);
$daily_sales = [];
if ($daily_result) {
    while ($row = $daily_result->fetch_assoc()) {
        $daily_sales[] = $row;
    }
}

// Fungsi pembantu untuk memformat tanggal
function format_date_indo($date_str) {
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', $date_str);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]-1] . ' ' . $pecah[0];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Laporan Penjualan</h1>
            <p class="lead">Analisis penjualan mendalam berdasarkan periode waktu.</p>
        </header>

        <div class="card shadow-sm p-4 mb-4 filter-card">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label for="start_date" class="form-label small text-muted">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                        value="<?php echo $start_date; ?>" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label for="end_date" class="form-label small text-muted">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                        value="<?php echo $end_date; ?>" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <button type="submit" class="btn btn-pink w-100"><i class="fas fa-filter me-1"></i> Terapkan
                        Filter</button>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="proses/export_sales.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>"
                        class="btn btn-outline-success w-100 export-btn">
                        <i class="fas fa-file-excel me-1"></i> Ekspor (.XLSX)
                    </a>
                </div>
            </form>
            <small class="mt-3 text-muted">
                Menampilkan data penjualan dari <b><?php echo format_date_indo($start_date); ?></b> sampai
                <b><?php echo format_date_indo($end_date); ?></b>.
            </small>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card shadow-sm report-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt fa-3x text-info me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Pesanan</p>
                                <h4 class="card-title mb-0">
                                    <?php echo number_format($summary['total_orders'] ?? 0, 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="card shadow-sm report-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line fa-3x text-pink-primary me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Pendapatan (Gross)</p>
                                <h4 class="card-title mb-0 text-pink-primary">
                                    Rp<?php echo number_format($summary['total_revenue_gross'] ?? 0, 0, ',', '.'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 col-12">
                <div class="card shadow-sm report-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-dollar-sign fa-3x text-success me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Pendapatan Bersih (Completed)</p>
                                <h4 class="card-title mb-0 text-success">
                                    Rp<?php echo number_format($summary['total_revenue_net'] ?? 0, 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <h5 class="mb-4 card-title-dark-mode">Rincian Penjualan Harian</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped table-bordered sales-report-table">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 15%;">Tanggal</th>
                            <th scope="col">Total Pesanan</th>
                            <th scope="col">Pesanan Selesai</th>
                            <th scope="col">Pendapatan Kotor (Rp)</th>
                            <th scope="col">Pendapatan Bersih (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($daily_sales) > 0): ?>
                        <?php foreach ($daily_sales as $d): ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo format_date_indo($d['report_date']); ?></span></td>
                            <td class="text-center"><?php echo number_format($d['daily_orders'], 0); ?></td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    <?php echo number_format($d['daily_completed_orders'], 0); ?>
                                </span>
                            </td>
                            <td class="text-end fw-bold text-pink-primary">
                                <?php echo number_format($d['daily_revenue'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-end fw-bold text-success">
                                <?php echo number_format($d['daily_net_revenue'], 0, ',', '.'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data penjualan yang ditemukan pada periode
                                ini.</td>
                        </tr>
                        <?php endif; ?>

                        <tr class="table-dark report-total-row">
                            <td class="fw-bold">TOTAL PERIODE</td>
                            <td class="text-center fw-bold">
                                <?php echo number_format($summary['total_orders'] ?? 0, 0); ?></td>
                            <td class="text-center fw-bold">
                                <?php 
                                    // Hitung total pesanan selesai dalam periode
                                    $total_completed = array_sum(array_column($daily_sales, 'daily_completed_orders'));
                                    echo number_format($total_completed, 0);
                                ?>
                            </td>
                            <td class="text-end fw-bold text-pink-primary">
                                Rp<?php echo number_format($summary['total_revenue_gross'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-end fw-bold text-success">
                                Rp<?php echo number_format($summary['total_revenue_net'] ?? 0, 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // ====================================================
    // Logic Dark/Light Mode (FIXED - Diambil dari file sebelumnya)
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
        } else {
            body.classList.remove('dark-mode');
        }

        // Terapkan styling ke komponen modal (jika ada)
        document.querySelectorAll('.modal-content').forEach(el => {
            if (savedMode === 'dark') {
                el.classList.add('dark-mode');
            } else {
                el.classList.remove('dark-mode');
            }
        });

        if (modeToggle) {
            if (savedMode === 'dark') {
                // Asumsi #mode-toggle berada di sidebar
                modeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
                modeToggle.classList.remove('text-pink-primary');
            } else {
                modeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
                modeToggle.classList.add('text-pink-primary');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyMode(true);
    });

    if (modeToggle) {
        modeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            applyMode(false);
        });
    }

    // ====================================================
    // Logic Filter Validation (Opsional)
    // ====================================================
    document.querySelector('.filter-card form').addEventListener('submit', function(e) {
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        if (new Date(start) > new Date(end)) {
            e.preventDefault();
            alert('Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir.');
        }
    });
    </script>
</body>

</html>