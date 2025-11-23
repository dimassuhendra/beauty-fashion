<?php
// Pastikan koneksi database ($conn) sudah tersedia
if (!isset($conn)) {
    die("Koneksi database tidak ditemukan.");
}

// -----------------------------------------------------------
// 1. QUERY UNTUK DATA TABEL & CHART 1 & 2 (Distribusi Produk)
// -----------------------------------------------------------
$sql_categories = "
SELECT 
    c.id, 
    c.name, 
    c.slug,
    c.description, 
    COUNT(p.id) AS product_count,
    c.created_at
FROM 
    categories c
LEFT JOIN 
    products p ON c.id = p.category_id
GROUP BY 
    c.id, c.name, c.slug, c.description, c.created_at
ORDER BY 
    c.name ASC;
";

$result_categories = $conn->query($sql_categories);
$categories = []; // Untuk Tabel HTML
$chart_labels = []; // Untuk Label Chart
$chart_product_counts = []; // Untuk Data Chart

if ($result_categories) {
    while ($row = $result_categories->fetch_assoc()) {
        $row['product_count'] = (int) $row['product_count'];
        $categories[] = $row;
        
        // Simpan data untuk Chart 1 & 2
        $chart_labels[] = $row['name'];
        $chart_product_counts[] = $row['product_count'];
    }
}


// -----------------------------------------------------------
// 2. QUERY UNTUK CHART 3 (3 Kategori Stok Terendah)
// -----------------------------------------------------------
$sql_low_stock = "
SELECT
    c.name,
    IFNULL(SUM(p.stock), 0) AS total_stock_available
FROM
    categories c
LEFT JOIN
    products p ON c.id = p.category_id
GROUP BY
    c.id, c.name
ORDER BY
    total_stock_available ASC
LIMIT 3;
";

$result_low_stock = $conn->query($sql_low_stock);
$chart_low_stock_labels = [];
$chart_low_stock_counts = [];

if ($result_low_stock) {
    while ($row = $result_low_stock->fetch_assoc()) {
        $chart_low_stock_labels[] = $row['name'];
        $chart_low_stock_counts[] = (int) $row['total_stock_available'];
    }
}


// -----------------------------------------------------------
// 3. FORMAT DATA CHART KE JSON
// -----------------------------------------------------------
$chart_data = [
    'product_distribution' => [
        'labels' => $chart_labels,
        'data' => $chart_product_counts
    ],
    'low_stock' => [
        'labels' => $chart_low_stock_labels,
        'data' => $chart_low_stock_counts
    ]
];

// Variabel ini akan di-encode ke JSON di file manajemen-kategori.php
$chart_data_json = json_encode($chart_data);

?>