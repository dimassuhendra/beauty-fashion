<?php 
// --- DEFENISI USER ID ---
// Ganti dengan mekanisme otentikasi riil Anda
$userId = $_SESSION['user_id'] ?? 1; // Menggunakan ID 1 sebagai default untuk demo (ID 1 ada di tabel users)

// --- FUNGSI FORMAT RUPIAH ---
// Pastikan fungsi ini tersedia. Jika belum ada di db_connect.php, definisikan di sini:
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        if ($angka === null) return 'Rp 0';
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

// ====================================================================
// 1. LOGIKA HAPUS DAN UPDATE KUANTITAS
// ====================================================================
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = $_POST['item_id'] ?? 0;
    
    if ($action == 'update_quantity' && $itemId > 0) {
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($quantity <= 0) {
            $error = "Kuantitas tidak valid.";
        } else {
            // Lakukan update di database
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $itemId, $userId);

            if ($stmt->execute()) {
                $message = "Kuantitas berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui kuantitas.";
            }
            $stmt->close();
        }

    } elseif ($action == 'remove_item' && $itemId > 0) {
        // Lakukan penghapusan dari database
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $itemId, $userId);

        if ($stmt->execute()) {
            $message = "Produk berhasil dihapus dari keranjang.";
        } else {
            $error = "Gagal menghapus produk.";
        }
        $stmt->close();
    }
    // Redirect untuk mencegah resubmission form
    header('Location: cart.php');
    exit;
}

// ====================================================================
// 2. LOGIKA MENGAMBIL DATA KERANJANG
// ====================================================================

$cartItems = [];
$totalAmount = 0;
$totalItems = 0;

$sql = "SELECT 
            ci.id AS cart_item_id, 
            ci.quantity, 
            p.id AS product_id,
            p.name, 
            p.price, 
            p.image_url, 
            p.stock
        FROM 
            cart_items ci
        JOIN 
            products p ON ci.product_id = p.id
        WHERE 
            ci.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Hitung subtotal untuk setiap item
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $totalAmount += $row['subtotal'];
        $totalItems += $row['quantity'];
        $cartItems[] = $row;
    }
}
$stmt->close();

// Update jumlah barang di navbar (untuk digunakan di navbar.php)
$cartCount = $totalItems;
?>