<?php
require_once '../../db_connect.php';

// Fungsi bantuan untuk membuat slug
function create_slug($text) {
    // Ganti non-alphanumeric dengan tanda hubung
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterasi (mengubah karakter non-latin ke latin)
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Hapus karakter yang tidak diinginkan
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text;
}

// 2. Tentukan Aksi
$action = $_REQUEST['action'] ?? ''; // Ambil dari GET atau POST

// Fungsi untuk redirect dengan pesan notifikasi
function redirect_with_message($status, $message) {
    $status_param = $status == 'success' ? 'success' : 'error';
    header("Location: ../manajemen-produk.php?status={$status_param}&message=" . urlencode($message));
    exit();
}

// 3. Logika Pemrosesan
switch ($action) {
    // ===================================
    // LOGIKA HAPUS PRODUK (DELETE) - Menggunakan GET
    // ===================================
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            redirect_with_message('error', 'ID produk tidak valid.');
        }

        $query = "DELETE FROM products WHERE id = $id";

        if (mysqli_query($conn, $query)) {
            if (mysqli_affected_rows($conn) > 0) {
                redirect_with_message('success', 'Produk berhasil dihapus.');
            } else {
                redirect_with_message('error', 'Produk tidak ditemukan atau sudah terhapus.');
            }
        } else {
            redirect_with_message('error', 'Gagal menghapus produk: ' . mysqli_error($conn));
        }
        break;

    // ===================================
    // LOGIKA TAMBAH/EDIT PRODUK (CREATE/UPDATE) - Menggunakan POST
    // ===================================
    case 'add':
    case 'edit':
        $id = (int)($_POST['productId'] ?? 0);
        $name = mysqli_real_escape_string($conn, $_POST['productName']);
        $sku = mysqli_real_escape_string($conn, $_POST['productSKU']);
        $category_id = (int)$_POST['productCategory'];
        $price = (float)$_POST['productPrice'];
        $stock = (int)$_POST['productStock'];
        $description = mysqli_real_escape_string($conn, $_POST['productDescription']);
        $image_url = mysqli_real_escape_string($conn, $_POST['productImage']);
        $is_active = (int)$_POST['productStatus'];
        
        // Cek data wajib
        if (empty($name) || empty($sku) || $category_id <= 0 || $price < 0) {
            redirect_with_message('error', 'Semua kolom wajib harus diisi dengan benar.');
        }
        
        // Buat SLUG
        $slug = create_slug($name);

        if ($action === 'add') {
            $query = "INSERT INTO products (name, sku, slug, category_id, price, stock, description, image_url, is_active, created_at, updated_at) 
                      VALUES ('$name', '$sku', '$slug', $category_id, $price, $stock, '$description', '$image_url', $is_active, NOW(), NOW())";
            $success_message = 'Produk baru berhasil ditambahkan.';
            $error_prefix = 'Gagal menambahkan produk';
        } else { // action === 'edit'
            if ($id <= 0) {
                redirect_with_message('error', 'ID produk tidak valid untuk diperbarui.');
            }
            $query = "UPDATE products SET 
                          name = '$name', 
                          sku = '$sku', 
                          slug = '$slug', 
                          category_id = $category_id, 
                          price = $price, 
                          stock = $stock, 
                          description = '$description', 
                          image_url = '$image_url', 
                          is_active = $is_active,
                          updated_at = NOW()
                      WHERE id = $id";
            $success_message = 'Produk berhasil diperbarui.';
            $error_prefix = 'Gagal memperbarui produk';
        }

        if (mysqli_query($conn, $query)) {
            redirect_with_message('success', $success_message);
        } else {
            $error_message = (mysqli_errno($conn) == 1062) ? 'SKU atau Slug sudah terdaftar.' : $error_prefix . ': ' . mysqli_error($conn);
            redirect_with_message('error', $error_message);
        }
        break;

    default:
        // Jika tidak ada aksi yang valid, redirect saja
        header("Location: ../manajemen-produk.php");
        exit();
        break;
}

mysqli_close($conn);
?>