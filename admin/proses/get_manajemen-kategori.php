<?php
// Pastikan koneksi database ($conn) sudah tersedia dari 'db_connect.php'
if (!isset($conn)) {
    die("Koneksi database tidak ditemukan.");
}

// 1. Query SQL untuk mengambil semua kategori dan menghitung jumlah produk di setiap kategori.
// Kita menggunakan LEFT JOIN dengan tabel products untuk memastikan kategori yang belum memiliki produk (product_count = 0) tetap tampil.
$sql = "
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

// 2. Eksekusi Query
$result = $conn->query($sql);

// 3. Inisialisasi array untuk menampung data kategori
$categories = [];

if ($result) {
    if ($result->num_rows > 0) {
        // Ambil setiap baris hasil query
        while ($row = $result->fetch_assoc()) {
            // Konversi nilai 'product_count' menjadi integer
            $row['product_count'] = (int) $row['product_count'];
            
            // Tambahkan data ke array $categories
            $categories[] = $row;
        }
    }
} else {
    // Penanganan error jika query gagal
    // Dalam lingkungan produksi, sebaiknya log error ini daripada menampilkannya.
    // Contoh: error_log("Error fetching categories: " . $conn->error);
    echo "Error: " . $conn->error;
}

// Catatan: Variabel $categories kini sudah siap digunakan di file manajemen-kategori.php
// yang melakukan include file ini.
// Kita tidak perlu menutup koneksi di sini karena mungkin akan digunakan lagi di halaman utama.

?>