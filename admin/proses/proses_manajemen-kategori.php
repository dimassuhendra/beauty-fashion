<?php
// Pastikan koneksi database ($conn) sudah tersedia
include '../../db_connect.php'; // Sesuaikan path ini jika perlu (Asumsi: proses/ keluar ke admin/, lalu keluar lagi ke root/)

// Sertakan file fungsi atau library yang Anda butuhkan (misal fungsi sanitasi, redirect, dll.)
// Jika tidak ada, kita gunakan fungsi dasar PHP.

// Fungsi untuk membuat slug dari nama kategori
function create_slug($text) {
    // 1. Konversi ke huruf kecil
    $text = strtolower($text);
    // 2. Ganti karakter non-alfanumerik/spasi menjadi tanda hubung
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // 3. Ganti spasi menjadi tanda hubung
    $text = preg_replace('/[\s-]+/', '-', $text);
    // 4. Hapus tanda hubung di awal/akhir
    $text = trim($text, '-');
    return $text;
}

// ====================================================================
// LOGIKA CRUD
// ====================================================================

// --- 1. PROSES TAMBAH & EDIT (CREATE & UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi dan ambil data dari POST
    $id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $name = $conn->real_escape_string(trim($_POST['category_name']));
    $description = $conn->real_escape_string(trim($_POST['category_description']));
    $slug = create_slug($name);

    if (empty($name)) {
        // Handle error: Nama kategori wajib diisi
        header("Location: ../manajemen-kategori.php?status=error&message=Nama kategori wajib diisi.");
        exit;
    }

    // Cek duplikasi nama kategori
    $check_sql = "SELECT id FROM categories WHERE name = '$name' AND id != $id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Handle error: Kategori sudah ada
        header("Location: ../manajemen-kategori.php?status=error&message=Nama kategori '$name' sudah ada.");
        exit;
    }

    if ($id == 0) {
        // --- CREATE (Tambah Baru) ---
        $insert_sql = "INSERT INTO categories (name, slug, description) 
                       VALUES ('$name', '$slug', '$description')";
        
        if ($conn->query($insert_sql) === TRUE) {
            header("Location: ../manajemen-kategori.php?status=success&message=Kategori '$name' berhasil ditambahkan!");
        } else {
            header("Location: ../manajemen-kategori.php?status=error&message=Gagal menambahkan kategori. Error: " . $conn->error);
        }
    } else {
        // --- UPDATE (Edit Data) ---
        $update_sql = "UPDATE categories SET 
                       name = '$name', 
                       slug = '$slug', 
                       description = '$description',
                       updated_at = CURRENT_TIMESTAMP()
                       WHERE id = $id";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: ../manajemen-kategori.php?status=success&message=Kategori ID:$id berhasil diperbarui!");
        } else {
            header("Location: ../manajemen-kategori.php?status=error&message=Gagal memperbarui kategori ID:$id. Error: " . $conn->error);
        }
    }
    
    exit;
}

// --- 2. PROSES HAPUS (DELETE) ---
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = intval($_GET['id']);
    
    // Opsional: Cek apakah kategori memiliki produk yang terhubung
    $check_products = $conn->query("SELECT COUNT(id) AS product_count FROM products WHERE category_id = $id_to_delete");
    $product_data = $check_products->fetch_assoc();

    if ($product_data['product_count'] > 0) {
        // Peringatan: Tidak dapat menghapus kategori yang masih memiliki produk
        header("Location: ../manajemen-kategori.php?status=warning&message=Tidak dapat menghapus kategori ID:$id_to_delete. Masih terdapat " . $product_data['product_count'] . " produk yang terhubung.");
        exit;
    }

    // Query DELETE
    $delete_sql = "DELETE FROM categories WHERE id = $id_to_delete";

    if ($conn->query($delete_sql) === TRUE) {
        header("Location: ../manajemen-kategori.php?status=success&message=Kategori ID:$id_to_delete berhasil dihapus.");
    } else {
        header("Location: ../manajemen-kategori.php?status=error&message=Gagal menghapus kategori ID:$id_to_delete. Error: " . $conn->error);
    }
    exit;
}

// Jika tidak ada aksi yang dikenali
header("Location: ../manajemen-kategori.php");
exit;
?>