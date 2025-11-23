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

/**
 * Fungsi bantu untuk menentukan warna badge stok berdasarkan batas
 * Batas: <= 5 (Danger), <= 10 (Warning), Lainnya (Success)
 * @param int $stock
 * @return string (Nama kelas Bootstrap: danger, warning, success)
 */
function getStockBadgeClass($stock) {
    if ($stock <= 5) {
        return 'danger'; 
    } elseif ($stock <= 10) {
        return 'warning'; 
    } else {
        return 'success'; 
    }
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
    
    // --- TANGANI TAMBAH PRODUK ---
    if ($action === 'add') {
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

    // --- TANGANI EDIT PRODUK ---
    elseif ($action === 'edit') {
        $id = (int)$_POST['product_id'];
        $name = $conn->real_escape_string($_POST['name']);
        $category_id = (int)$_POST['category_id'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $image_url_update = null; 
        $target_dir = "../uploads/product/"; 
        
        $sku_query = $conn->query("SELECT sku, image_url FROM products WHERE id = $id");
        if ($sku_query && $sku_query->num_rows > 0) {
            $product_data = $sku_query->fetch_assoc();
            $old_image = $product_data['image_url'] ?? null;
            $sku_product = $product_data['sku'] ?? 'unknown'; 
        } else {
            $old_image = null;
            $sku_product = 'unknown';
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $new_file_name = $sku_product . '-' . time() . '.' . $file_extension;
            $target_file = $target_dir . $new_file_name;

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url_update = $new_file_name;
                
                if (!empty($old_image) && file_exists($target_dir . $old_image)) {
                    unlink($target_dir . $old_image);
                }
            }
        }
        
        $sql = "UPDATE products SET 
                    name='$name', 
                    category_id='$category_id', 
                    price='$price', 
                    stock='$stock', 
                    is_active='$is_active'";
        
        if ($image_url_update !== null) {
            $sql .= ", image_url='$image_url_update'";
        }
        $sql .= ", slug='" . slugify($name) . "'"; 

        $sql .= " WHERE id='$id'";

        if ($conn->query($sql)) {
            $message_gambar = $image_url_update !== null ? " (termasuk gambar baru)" : "";
            $message = "Produk <b>$name</b> berhasil diperbarui" . $message_gambar . ".";
            $message_type = 'success';
        } else {
            $message = "Error saat update: " . $conn->error;
            $message_type = 'danger';
        }
    }
    
    // --- TANGANI HAPUS PRODUK ---
    elseif ($action === 'delete') {
        $id = (int)$_POST['product_id'];
        
        $image_query = $conn->query("SELECT image_url FROM products WHERE id = $id");
        $image_to_delete = null;
        if ($image_query) {
             $image_data = $image_query->fetch_assoc();
             $image_to_delete = $image_data['image_url'] ?? null;
        }
        
        $sql = "DELETE FROM products WHERE id='$id'";
        if ($conn->query($sql)) {
            
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
// Ditambahkan 'total_sold' ke kolom sort yang tersedia
$sort_columns = ['id', 'sku', 'name', 'category_name', 'price', 'stock', 'is_active', 'average_rating', 'total_sold'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

// Query untuk menghitung total produk (untuk pagination)
$count_sql = "SELECT COUNT(p.id) FROM products p $search_query";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data produk (Termasuk Rating dan Total Terjual)
$sql = "
SELECT
p.id, p.sku, p.name, p.price, p.stock, p.is_active, p.image_url,
c.name as category_name, c.id as category_id,
COALESCE(AVG(r.rating), 0) as average_rating,
COALESCE(SUM(oi.quantity), 0) as total_sold  -- FITUR BARU: Total Terjual
FROM products p
JOIN categories c ON p.category_id = c.id
LEFT JOIN reviews r ON p.id = r.product_id -- Gabungkan dengan tabel reviews
LEFT JOIN order_details oi ON p.id = oi.product_id -- FITUR BARU: Gabungkan dengan order_items
$search_query
GROUP BY p.id, p.sku, p.name, p.price, p.stock, p.is_active, p.image_url, c.name, c.id
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
// Tidak ada lagi logika pengambilan data untuk card analisis, sesuai permintaan.

?>