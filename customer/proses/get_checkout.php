<?php 
if (!isset($_SESSION['user_id'])) {
    // Arahkan ke halaman login jika belum login
    header("Location: login.php");
    exit;
}

// Pastikan variabel koneksi menggunakan $conn
if (!isset($conn)) {
    die("Kesalahan: Variabel koneksi \$conn tidak tersedia. Pastikan file koneksi.php mendefinisikan \$conn.");
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_subtotal = 0; // Total harga semua item
$user_addresses = [];
$selected_address = null;

// FUNGSI UNTUK FORMAT RUPIAH
function format_rupiah($angka){
    return 'Rp' . number_format($angka, 0, ',', '.');
}

// 2. Fetch Cart Items
$sql_cart = "SELECT ci.id, ci.quantity, p.id as product_id, p.name, p.price, p.image_url 
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = ?";

// Menggunakan $conn
if ($stmt_cart = $conn->prepare($sql_cart)) {
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    while ($row = $result_cart->fetch_assoc()) {
        $subtotal = $row['price'] * $row['quantity'];
        $row['subtotal'] = $subtotal;
        $total_subtotal += $subtotal;
        $cart_items[] = $row;
    }
    $stmt_cart->close();
} else {
    // Error handling
    die("Error preparing cart query: " . $conn->error);
}

// 3. Fetch User Addresses
// Menggunakan kolom is_active dan phone_number
$sql_address = "SELECT id, user_id, label, recipient_name, phone_number, full_address, city, postal_code, is_active
                FROM user_addresses 
                WHERE user_id = ? 
                ORDER BY is_active DESC";

// Menggunakan $conn
if ($stmt_address = $conn->prepare($sql_address)) {
    $stmt_address->bind_param("i", $user_id);
    $stmt_address->execute();
    $result_address = $stmt_address->get_result();
    
    while ($row = $result_address->fetch_assoc()) {
        $user_addresses[] = $row;
        // Menggunakan is_active sebagai penanda default
        if ($row['is_active'] == 1) {
            $selected_address = $row;
        }
    }
    // Jika tidak ada yang aktif/default, pilih yang pertama
    if (!$selected_address && !empty($user_addresses)) {
        $selected_address = $user_addresses[0];
    }
    $stmt_address->close();
} else {
    // Error handling
    die("Error preparing address query: " . $conn->error);
}

// 4. Handle Checkout Submission (Transaksi)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    
    // Mulai Transaksi untuk menjaga integritas data
    $conn->begin_transaction();
    
    try {
        if (empty($cart_items)) {
            throw new Exception("Keranjang belanja Anda kosong.");
        } 
        if (empty($_POST['address_id'])) {
            throw new Exception("Mohon pilih alamat pengiriman.");
        } 
        if (empty($_POST['payment_method'])) {
            throw new Exception("Mohon pilih metode pembayaran.");
        }

        // Data yang diperlukan
        $address_id = (int)$_POST['address_id'];
        $payment_method = $_POST['payment_method'];
        $shipping_cost = 20000; // Contoh biaya kirim statis
        $discount_amount = 0.00; // Default: belum ada kupon
        
        // Total kalkulasi
        $total_amount_items = $total_subtotal; // Subtotal semua produk
        $final_amount = $total_amount_items + $shipping_cost - $discount_amount;
        $order_status = 'Pending Payment'; // Sesuai enum di database

        // Cek kembali stok (Penting dalam aplikasi nyata)

        // 1. Masukkan data ke tabel orders
        // Menyimpan: user_id, order_code, total_amount (items subtotal), shipping_address_id, 
        // discount_amount, final_amount (grand total), payment_method, order_status
        $sql_insert_order = "INSERT INTO orders 
                            (user_id, order_code, total_amount, shipping_address_id, coupon_id, discount_amount, final_amount, payment_method, order_status) 
                            VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?)";
        
        // Menggunakan $conn
        if ($stmt_order = $conn->prepare($sql_insert_order)) {
            // total_amount, discount_amount, final_amount bertipe DECIMAL(10,2) di DB, jadi gunakan 'd' (double)
            $stmt_order->bind_param("isiddss", 
                $user_id, 
                $order_code = 'ORD-' . time() . rand(100, 999), 
                $total_amount_items, 
                $address_id, 
                $discount_amount, 
                $final_amount, 
                $payment_method, 
                $order_status
            );
            
            if (!$stmt_order->execute()) {
                throw new Exception("Gagal membuat pesanan (Order). Error: " . $stmt_order->error);
            }
            $order_id = $stmt_order->insert_id;
            $stmt_order->close();

            // 2. Pindahkan item keranjang ke order_details
            $sql_insert_detail = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";
            
            // Menggunakan $conn
            if ($stmt_detail = $conn->prepare($sql_insert_detail)) {
                
                foreach ($cart_items as $item) {
                    // price dan subtotal bertipe DECIMAL(10,2), gunakan 'd'
                    $stmt_detail->bind_param("iiidd", 
                        $order_id, 
                        $item['product_id'], 
                        $item['quantity'], 
                        $item['price'], // unit_price
                        $item['subtotal']
                    );
                    if (!$stmt_detail->execute()) {
                        throw new Exception("Gagal menyimpan detail pesanan. Error: " . $stmt_detail->error);
                    }
                }
                $stmt_detail->close();

                // 3. Kosongkan keranjang
                $sql_clear_cart = "DELETE FROM cart_items WHERE user_id = ?";
                // Menggunakan $conn
                if ($stmt_clear = $conn->prepare($sql_clear_cart)) {
                    $stmt_clear->bind_param("i", $user_id);
                    if (!$stmt_clear->execute()) {
                        throw new Exception("Gagal mengosongkan keranjang. Error: " . $stmt_clear->error);
                    }
                    $stmt_clear->close();
                }

                // Jika semua berhasil, COMMIT transaksi
                $conn->commit();

                // Redirect ke halaman sukses/detail pesanan
                header("Location: order_success.php?code=" . $order_code);
                exit;

            } else {
                 throw new Exception("Error saat menyiapkan query detail pesanan: " . $conn->error);
            }
        } else {
            throw new Exception("Error saat menyiapkan query pesanan: " . $conn->error);
        }

    } catch (Exception $e) {
        // Jika ada kegagalan, ROLLBACK transaksi
        $conn->rollback();
        $error_message = "Proses Checkout Gagal: " . $e->getMessage();
    }
}
?>