<?php 
// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. PENGATURAN FILTER TANGGAL (TIDAK ADA PERUBAHAN)
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
// 3. QUERY RINGKASAN (SUMMARY CARDS) - LOGIKA DIREVISI
// --------------------------------------------------------------------

$sql_summary = "
    SELECT 
        -- Menghitung total order (apapun statusnya)
        COUNT(id) as total_orders,
        -- Menghitung Revenue HANYA dari order_status = 'Selesai'
        SUM(CASE WHEN order_status = 'Selesai' THEN final_amount ELSE 0 END) as total_revenue
    FROM orders
    $date_filter_clause
";

$summary_result = $conn->query($sql_summary);
$summary = $summary_result->fetch_assoc();


// --------------------------------------------------------------------
// 4. QUERY LAPORAN HARIAN (TABEL) - LOGIKA DIREVISI
// --------------------------------------------------------------------

$sql_daily = "
    SELECT 
        DATE(order_date) as report_date,
        -- Menghitung orders yang dicatat hari itu (apapun statusnya)
        COUNT(id) as daily_orders,
        -- Menghitung Revenue HANYA dari order_status = 'Selesai'
        SUM(CASE WHEN order_status = 'Selesai' THEN final_amount ELSE 0 END) as daily_revenue,
        -- Menghitung jumlah pesanan yang Selesai
        SUM(CASE WHEN order_status = 'Selesai' THEN 1 ELSE 0 END) as daily_completed_orders
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

// Fungsi pembantu untuk memformat tanggal (TIDAK ADA PERUBAHAN)
function format_date_indo($date_str) {
    $bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', $date_str);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]-1] . ' ' . $pecah[0];
}
?>
