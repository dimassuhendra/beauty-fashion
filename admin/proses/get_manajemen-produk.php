<?php 
// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

/**
 * Fungsi untuk mengonversi string menjadi slug (format URL)
 * @param string $text
 * @return string
 */
function slugify($text, string $divider = '-')
{
    // Hapus spasi di awal dan akhir
    $text = trim($text);
    // Ganti karakter non-alphanumeric menjadi spasi (kecuali -)
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    // Transliterasi karakter (misal: ü menjadi ue)
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Ubah ke huruf kecil
    $text = strtolower($text);
    // Hapus karakter yang tidak diinginkan
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Hapus divider ganda
    $text = preg_replace('~-+~', $divider, $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}


// --------------------------------------------------------------------
// 2. TANGANI AKSI CRUD (Produk dan Kategori)
// --------------------------------------------------------------------

$message = '';
$message_type = '';

// Ambil pesan dari URL setelah redirect (jika ada)
if (isset($_GET['message']) && isset($_GET['message_type'])) {
    $message = htmlspecialchars($_GET['message']);
    $message_type = htmlspecialchars($_GET['message_type']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- TANGANI TAMBAH KATEGORI (Menggunakan prepared statement, bagus!) ---
    if ($action == 'add_category') {
        $name = $_POST['name'] ?? '';
        $slug = slugify($name);

        if (!empty($name)) {
            $check_sql = "SELECT id FROM categories WHERE name = ? OR slug = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $name, $slug);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $message = "Gagal: Nama kategori atau slug sudah ada.";
                $message_type = "danger";
            } else {
                $insert_sql = "INSERT INTO categories (name, slug) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                
                if ($insert_stmt->bind_param("ss", $name, $slug) && $insert_stmt->execute()) {
                    $message = "Kategori <b>" . htmlspecialchars($name) . "</b> berhasil ditambahkan!";
                    $message_type = "success";
                    
                    // Redirect untuk menghilangkan data POST
                    header("Location: manajemen-produk.php?message=" . urlencode($message) . "&message_type=" . $message_type);
                    exit();
                } else {
                    $message = "Gagal menambahkan kategori: " . $conn->error;
                    $message_type = "danger";
                }
            }
            $check_stmt->close();
        } else {
            $message = "Gagal: Nama kategori tidak boleh kosong.";
            $message_type = "danger";
        }
    }
    
    // --- TANGANI TAMBAH PRODUK ---
    elseif ($action === 'add') {
        $category_id = (int)$_POST['category_id'];
        $sku = $conn->real_escape_string($_POST['sku']);
        $name = $conn->real_escape_string($_POST['name']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = $conn->real_escape_string($_POST['description']);
        $slug = slugify($name);

        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/product/"; 
            $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $new_file_name = $sku . '-' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_file_name;

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $new_file_name;
            }
        }

        // is_active diset default 1 saat tambah produk
        $sql = "INSERT INTO products (category_id, sku, name, slug, description, price, stock, image_url, is_active)
        VALUES ('$category_id', '$sku', '$name', '$slug', '$description', '$price', '$stock', " . ($image_url ? "'$image_url'" : 'NULL') . ", 1)";

        if ($conn->query($sql)) {
            $message = "Produk <b>$name</b> berhasil ditambahkan.";
            $message_type = 'success';
        } else {
            $message = "Error: " . $conn->error;
            $message_type = 'danger';
        }
    }

    // --------------------------------------------------------------------
    // --- TANGANI EDIT PRODUK (BAGIAN INI YANG DIPERBAIKI) ---
    // --------------------------------------------------------------------
    elseif ($action === 'edit') {
        $id = (int)$_POST['product_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $category_id = (int)$_POST['category_id']; // Ambil Kategori ID
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_url_update = null; 
        $target_dir = "../uploads/product/"; 
        
        // 1. Ambil Data Produk Lama (khususnya SKU dan image_url lama)
        // Perlu ambil SKU untuk penamaan file baru, dan image_url lama untuk dihapus
        $sku_query = $conn->query("SELECT sku, image_url FROM products WHERE id = $id");
        if ($sku_query && $sku_query->num_rows > 0) {
            $product_data = $sku_query->fetch_assoc();
            $old_image = $product_data['image_url'] ?? null;
            $sku_product = $product_data['sku'] ?? 'unknown'; // Gunakan SKU untuk penamaan file
        } else {
            // Produk tidak ditemukan, hentikan proses update gambar
            $old_image = null;
            $sku_product = 'unknown';
        }


        // 2. Cek dan Proses Upload Gambar Baru (Jika Ada)
        // Cek jika file terkirim DAN tidak ada error (UPLOAD_ERR_OK = 0)
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $new_file_name = $sku_product . '-' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_file_name;

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url_update = $new_file_name;
                
                // Hapus gambar lama (jika ada)
                if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                    unlink($target_dir . $old_image);
                }
            } else {
                $message = "Gagal mengupload gambar baru.";
                $message_type = "danger";
                // Jika upload gagal, $image_url_update tetap null, dan query akan mengabaikannya.
            }
        }
        
        // 3. Buat Query Update Dinamis
        
        // Bagian dasar query update (Update nama, kategori, harga, stok, dan status)
        $sql = "UPDATE products SET 
                    name='$name', 
                    category_id='$category_id', 
                    price='$price', 
                    stock='$stock', 
                    is_active='$is_active'";
        
        // Tambahkan update image_url HANYA JIKA ada gambar baru yang berhasil diunggah
        if ($image_url_update !== null) {
            $sql .= ", image_url='$image_url_update'";
        }
        // Tambahkan update slug
        $sql .= ", slug='" . slugify($name) . "'"; 

        $sql .= " WHERE id='$id'";

        // 4. Eksekusi Query
        if ($conn->query($sql)) {
            $message_gambar = $image_url_update !== null ? " (termasuk gambar baru)" : "";
            $message = "Produk <b>$name</b> berhasil diperbarui" . $message_gambar . ".";
            $message_type = 'success';
        } else {
            $message = "Error saat update: " . $conn->error;
            $message_type = 'danger';
        }
    }
    // --------------------------------------------------------------------
    
    // --- TANGANI HAPUS PRODUK (Ditambahkan logika hapus gambar fisik) ---
    elseif ($action === 'delete') {
        $id = (int)$_POST['product_id'];
        
        // 1. Sebelum menghapus record dari DB, ambil nama file gambarnya untuk dihapus
        $image_query = $conn->query("SELECT image_url FROM products WHERE id = $id");
        $image_to_delete = null;
        if ($image_query) {
             $image_data = $image_query->fetch_assoc();
             $image_to_delete = $image_data['image_url'] ?? null;
        }
        
        // 2. Hapus record dari DB
        $sql = "DELETE FROM products WHERE id='$id'";
        if ($conn->query($sql)) {
            
            // 3. Hapus file fisik gambar (jika ada)
            $target_dir = "../uploads/product/";
            if (!empty($image_to_delete) && file_exists($target_dir . $image_to_delete)) {
                unlink($target_dir . $image_to_delete);
            }
            
            $message = "Produk berhasil dihapus.";
            $message_type = 'warning';
        } else {
            $message = "Error saat menghapus: " . $conn->error;
            $message_type = 'danger';
        }
    }
}


// --------------------------------------------------------------------
// 3. PENGATURAN DAN PENGAMBILAN DATA PRODUK (Untuk Tabel)
// --------------------------------------------------------------------

// Pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Search
$search_query = "";
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
if (!empty($search)) {
    $search_query = " WHERE p.name LIKE '%$search%' OR p.sku LIKE '%$search%' ";
}

// Sorting
$sort_columns = ['id', 'sku', 'name', 'category_name', 'price', 'stock', 'is_active'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

// Query untuk menghitung total produk (untuk pagination)
$count_sql = "SELECT COUNT(p.id) FROM products p $search_query";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data produk
$sql = "
SELECT
p.id, p.sku, p.name, p.price, p.stock, p.is_active, p.image_url,
c.name as category_name, c.id as category_id
FROM products p
JOIN categories c ON p.category_id = c.id
$search_query
ORDER BY $sort $order
LIMIT $limit OFFSET $offset
";

$products_result = $conn->query($sql);
$products = [];
if ($products_result) {
    while ($row = $products_result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Ambil semua kategori untuk modal tambah/edit
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fungsi bantu untuk membuat link sorting
function get_sort_link($column, $current_sort, $current_order, $current_limit, $current_search) {
    $new_order = ($current_sort == $column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $params = [
        'sort' => $column,
        'order' => $new_order,
        'limit' => $current_limit,
        's' => $current_search
    ];
    return '?' . http_build_query(array_filter($params));
}
?>