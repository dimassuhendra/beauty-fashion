<?php 
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