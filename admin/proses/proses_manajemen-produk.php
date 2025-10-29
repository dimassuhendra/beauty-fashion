<?php
// PASTIKAN BARIS INI ADA DI PALING ATAS
session_start();

// Tentukan zona waktu
date_default_timezone_set('Asia/Jakarta');

// --- Bagian 1: Konfigurasi Koneksi Database ---
$servername = "localhost";
$username = "root"; // Ganti dengan username DB Anda
$password = "";     // Ganti dengan password DB Anda
$dbname = "beauty"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Simpan error ke session dan redirect
    $_SESSION['feedback_msg'] = "Koneksi database gagal: " . $conn->connect_error;
    $_SESSION['feedback_type'] = "danger";
    header("Location: ../manajemen-produk.php");
    exit;
}

// --- Bagian 2: Logika Pemrosesan Aksi (Create, Update, Delete) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ------------------------------------
    // Aksi Create (Tambah Produk) atau Update (Edit Produk)
    // ------------------------------------
    
    // Ambil data dari form
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['id'] ?? 0;
    $category_id = (int)($_POST['category_id'] ?? 0);
    $sku = $conn->real_escape_string($_POST['sku'] ?? '');
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $image_url = null; 
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    // Logika Upload Gambar
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        
        // Path relatif dari file ini (admin/proses/) ke folder target (admin/uploads/product/)
        $target_dir = "../uploads/product/"; 
        
        // Pastikan folder target ada dan bisa dibuat
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                 $_SESSION['feedback_msg'] = "Gagal membuat folder upload: " . $target_dir;
                 $_SESSION['feedback_type'] = "danger";
                 header("Location: ../manajemen-produk.php");
                 exit;
            }
        }
        
        $file_name = basename($_FILES["product_image"]["name"]);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = $sku . '-' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_file_name;
        
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $image_url = $conn->real_escape_string($new_file_name); 
            } else {
                $_SESSION['feedback_msg'] = "Gagal mengupload gambar. Pastikan folder 'admin/uploads/product' memiliki izin tulis.";
                $_SESSION['feedback_type'] = "danger";
                header("Location: ../manajemen-produk.php");
                exit;
            }
        } else {
            $_SESSION['feedback_msg'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
            $_SESSION['feedback_type'] = "danger";
            header("Location: ../manajemen-produk.php");
            exit;
        }
    }

    if ($action === 'add') {
        // Aksi Tambah Produk (CREATE)
        $sql = "INSERT INTO products (category_id, sku, name, slug, description, price, stock, image_url, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssdiss", $category_id, $sku, $name, $slug, $description, $price, $stock, $image_url, $is_active);
        
        if ($stmt->execute()) {
            $_SESSION['feedback_msg'] = "Produk **$name** berhasil ditambahkan!";
            $_SESSION['feedback_type'] = "success";
        } else {
            $_SESSION['feedback_msg'] = "Error saat menambahkan produk: " . $stmt->error;
            $_SESSION['feedback_type'] = "danger";
        }
        $stmt->close();
        
    } elseif ($action === 'edit' && $product_id > 0) {
        // Aksi Edit Produk (UPDATE)
        
        if ($image_url) {
            // Case 1: Gambar DIGANTI (10 parameter)
            $sql = "UPDATE products SET 
                    category_id = ?, sku = ?, name = ?, slug = ?, description = ?, 
                    price = ?, stock = ?, image_url = ?, is_active = ?
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssdsisii", $category_id, $sku, $name, $slug, $description, $price, $stock, $image_url, $is_active, $product_id);
            
            // Hapus gambar lama
            if ($old_image_data = $conn->query("SELECT image_url FROM products WHERE id = $product_id")->fetch_assoc()) {
                 $old_image_file = $old_image_data['image_url'];
                 if ($old_image_file && file_exists("../uploads/product/" . $old_image_file)) {
                     unlink("../uploads/product/" . $old_image_file);
                 }
            }

        } else {
            // Case 2: Gambar TIDAK Diganti (9 parameter)
            $sql = "UPDATE products SET 
                    category_id = ?, sku = ?, name = ?, slug = ?, description = ?, 
                    price = ?, stock = ?, is_active = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssdsiii", $category_id, $sku, $name, $slug, $description, $price, $stock, $is_active, $product_id); 
        }

        if ($stmt->execute()) {
            $_SESSION['feedback_msg'] = "Produk **$name** berhasil diperbarui!";
            $_SESSION['feedback_type'] = "success";
        } else {
            $_SESSION['feedback_msg'] = "Error saat memperbarui produk: " . $stmt->error;
            $_SESSION['feedback_type'] = "danger";
        }
        $stmt->close();
    }

    // Alihkan kembali ke halaman manajemen produk tanpa parameter URL
    header("Location: ../manajemen-produk.php");
    exit;

} elseif (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    // ------------------------------------
    // Aksi Delete (Hapus Produk)
    // ------------------------------------
    $product_id = (int)$_GET['id'];
    
    if ($product_id > 0) {
        // Ambil data produk dulu
        $stmt_select = $conn->prepare("SELECT name, image_url FROM products WHERE id = ?");
        $stmt_select->bind_param("i", $product_id);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();
        $product_data = $result_select->fetch_assoc();
        $stmt_select->close();

        if ($product_data) {
            $product_name = $product_data['name'];
            $old_image_file = $product_data['image_url'];
            
            // Hapus data dari database
            $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt_delete->bind_param("i", $product_id);
            
            if ($stmt_delete->execute()) {
                // Hapus file gambar dari server
                if ($old_image_file && file_exists("../uploads/product/" . $old_image_file)) {
                    unlink("../uploads/product/" . $old_image_file);
                }
                $_SESSION['feedback_msg'] = "Produk **$product_name** berhasil dihapus!";
                $_SESSION['feedback_type'] = "success";
            } else {
                $_SESSION['feedback_msg'] = "Error saat menghapus produk: " . $stmt_delete->error;
                $_SESSION['feedback_type'] = "danger";
            }
            $stmt_delete->close();
        } else {
            $_SESSION['feedback_msg'] = "Produk tidak ditemukan.";
            $_SESSION['feedback_type'] = "warning";
        }
    } else {
        $_SESSION['feedback_msg'] = "ID produk tidak valid.";
        $_SESSION['feedback_type'] = "danger";
    }

    // Alihkan kembali ke halaman manajemen produk tanpa parameter URL
    header("Location: ../manajemen-produk.php");
    exit;
}

$conn->close();

// Default redirect
header("Location: ../manajemen-produk.php");
exit;
?>