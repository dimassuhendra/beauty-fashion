<?php
// PASTIKAN Anda telah menyertakan (include) file koneksi database di bagian awal file ini.
// Contoh: include 'koneksi.php';

// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Arahkan ke halaman login jika belum login
    header("Location: login.php");
    exit;
}

// Variabel $conn HARUS didefinisikan di file yang meng-include ini (misal: db_connect.php)
if (!isset($conn)) {
    $error_message = "Kesalahan: Variabel koneksi \$conn tidak tersedia.";
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_subtotal = 0;
$user_addresses = [];
$selected_address = null;
$success_message = '';
$shipping_cost = 20000;
$discount_amount = 0.00;

// --- START: PENAMBAHAN UNTUK KUPLING KUPLING
$coupon_code_input = ''; 
$coupon_error = '';
$applied_coupon_id = null;
$applied_coupon_code = null;
// --- END: PENAMBAHAN UNTUK KUPLING KUPLING

// FUNGSI UNTUK FORMAT RUPIAH
function format_rupiah($angka)
{
    return 'Rp' . number_format($angka, 0, ',', '.');
}

// --- 1. Fetch Cart Items ---
if (isset($conn)) {
    $sql_cart = "SELECT 
                    ci.id AS cart_item_id, 
                    ci.quantity, 
                    p.id AS product_id, 
                    p.name, 
                    p.price, 
                    p.image_url 
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.id
                 WHERE ci.user_id = ?";

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
        $error_message = "Gagal mengambil data keranjang.";
    }
}

// --- 2. Fetch User Addresses ---
if (isset($conn)) {
    $sql_address = "SELECT 
                        id, 
                        user_id, 
                        label, 
                        recipient_name, 
                        phone_number, 
                        full_address, 
                        city, 
                        postal_code, 
                        is_active
                      FROM user_addresses 
                      WHERE user_id = ? 
                      ORDER BY is_active DESC, id ASC";

    if ($stmt_address = $conn->prepare($sql_address)) {
        $stmt_address->bind_param("i", $user_id);
        $stmt_address->execute();
        $result_address = $stmt_address->get_result();

        while ($row = $result_address->fetch_assoc()) {
            $user_addresses[] = $row;
            if ($row['is_active'] == 1) {
                $selected_address = $row;
            }
        }
        if (!$selected_address && !empty($user_addresses)) {
            $selected_address = $user_addresses[0];
        }
        $stmt_address->close();
    } else {
        $error_message = "Gagal mengambil data alamat pengguna.";
    }
}

// --- START: LOGIKA KUPLING KUPLING DAN PERHITUNGAN DISKON ---

// Cek jika ada kode kupon yang dikirimkan melalui POST untuk diterapkan
if (isset($_POST['apply_coupon']) && isset($_POST['coupon_code'])) {
    $coupon_code_input = trim($_POST['coupon_code']);
    // Simpan kode sementara di session untuk validasi ulang
    $_SESSION['temp_coupon_code'] = $coupon_code_input; 
} elseif (isset($_SESSION['temp_coupon_code'])) {
    $coupon_code_input = $_SESSION['temp_coupon_code']; // Ambil dari session jika halaman direload
}

if (!empty($coupon_code_input) && isset($conn) && $total_subtotal > 0) {
    // 1. Ambil data kupon dari database
    $sql_coupon = "SELECT id, discount_type, discount_value, minimum_purchase, valid_from, valid_until, usage_limit, used_count, is_active 
                   FROM coupons 
                   WHERE coupon_code = ? AND is_active = 1";
    
    if ($stmt_coupon = $conn->prepare($sql_coupon)) {
        $stmt_coupon->bind_param("s", $coupon_code_input);
        $stmt_coupon->execute();
        $result_coupon = $stmt_coupon->get_result();
        $coupon = $result_coupon->fetch_assoc();
        $stmt_coupon->close();

        if ($coupon) {
            $today = date('Y-m-d');
            
            // 2. Validasi Kupon
            if ($coupon['valid_from'] > $today) {
                $coupon_error = "Kupon belum berlaku.";
            } elseif ($coupon['valid_until'] < $today) {
                $coupon_error = "Kupon sudah kadaluarsa.";
            } elseif ($total_subtotal < $coupon['minimum_purchase']) {
                $coupon_error = "Pembelian minimum untuk kupon ini adalah " . format_rupiah($coupon['minimum_purchase']) . ".";
            } elseif ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
                 $coupon_error = "Kupon ini sudah mencapai batas penggunaan.";
            } else {
                // Kupon Valid - Hitung diskon
                $applied_coupon_id = $coupon['id'];
                $applied_coupon_code = $coupon_code_input;

                if ($coupon['discount_type'] == 'fixed') {
                    $discount_amount = min((float)$coupon['discount_value'], $total_subtotal); // Diskon tidak boleh melebihi subtotal
                } elseif ($coupon['discount_type'] == 'percent') {
                    $discount_amount = $total_subtotal * ((float)$coupon['discount_value'] / 100);
                }
                
                // Simpan data kupon yang berhasil diterapkan ke session
                $_SESSION['applied_coupon'] = [
                    'id' => $applied_coupon_id,
                    'code' => $applied_coupon_code,
                    'discount_amount' => $discount_amount,
                ];
            }
        } else {
            $coupon_error = "Kode kupon tidak valid atau tidak aktif.";
        }
    } else {
        $coupon_error = "Gagal menyiapkan query kupon.";
    }
    
    // Jika ada error kupon, hapus dari session
    if ($coupon_error !== '') {
        unset($_SESSION['applied_coupon']);
        unset($_SESSION['temp_coupon_code']);
    }
} 
// Ambil kupon yang sudah berhasil diterapkan dari session jika ada
elseif (isset($_SESSION['applied_coupon'])) {
    $applied_coupon_id = $_SESSION['applied_coupon']['id'];
    $applied_coupon_code = $_SESSION['applied_coupon']['code'];
    $discount_amount = $_SESSION['applied_coupon']['discount_amount'];
    $coupon_code_input = $applied_coupon_code;
}
// --- END: LOGIKA KUPLING KUPLING DAN PERHITUNGAN DISKON ---


// Kalkulasi Total Akhir
$total_amount_items = $total_subtotal; 
$final_amount = $total_amount_items + $shipping_cost - $discount_amount;
if ($final_amount < 0) $final_amount = 0.00; // Pastikan total tidak negatif


// --- 3. Handle Checkout Submission (Transaksi) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $posted_address_id = isset($_POST['address_id']) ? (int) $_POST['address_id'] : 0;
    $posted_payment_method = $_POST['payment_method'] ?? '';
    $posted_grand_total = (float) ($_POST['grand_total'] ?? 0); 

    $conn->begin_transaction();

    try {
        if (empty($cart_items)) {
            throw new Exception("Keranjang belanja Anda kosong. Mohon tambahkan produk terlebih dahulu.");
        }
        if ($posted_address_id == 0) {
            throw new Exception("Mohon pilih alamat pengiriman.");
        }
        if (empty($posted_payment_method)) {
            throw new Exception("Mohon pilih metode pembayaran.");
        }
        
        $address_found = false;
        foreach ($user_addresses as $addr) {
            if ($addr['id'] == $posted_address_id) {
                $address_found = true;
                break;
            }
        }
        if (!$address_found) {
            throw new Exception("Alamat pengiriman yang dipilih tidak valid.");
        }

        // --- START: Ambil Data Kupon Final Untuk DB ---
        $coupon_id_db = null;
        $discount_amount_db = 0.00;
        
        if (isset($_SESSION['applied_coupon'])) {
            $coupon_id_db = $_SESSION['applied_coupon']['id'];
            $discount_amount_db = $_SESSION['applied_coupon']['discount_amount'];
        }
        // --- END: Ambil Data Kupon Final Untuk DB ---

        $address_id = $posted_address_id;
        $payment_method = $posted_payment_method;
        $total_amount_items_db = $total_subtotal; 
        
        // Hitung ulang final_amount (termasuk diskon)
        $final_amount_db = $total_amount_items_db + $shipping_cost - $discount_amount_db;
        if ($final_amount_db < 0) $final_amount_db = 0.00;
        
        $order_status = 'Menunggu Pembayaran'; 
        $order_code = 'ORD-' . date('YmdHis') . rand(1000, 9999);
        
        $sql_insert_order = "INSERT INTO orders 
                              (user_id, order_code, total_amount, shipping_address_id, coupon_id, discount_amount, final_amount, payment_method, order_status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

        // Penanganan INSERT dengan coupon_id yang mungkin NULL
        if ($coupon_id_db === null) {
            // Jika tidak ada kupon, ganti placeholder coupon_id dengan NULL literal di query
            $sql_insert_order = "INSERT INTO orders 
                             (user_id, order_code, total_amount, shipping_address_id, coupon_id, discount_amount, final_amount, payment_method, order_status) 
                             VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?)";
            
            if ($stmt_order = $conn->prepare($sql_insert_order)) {
                $stmt_order->bind_param("isidsss", 
                    $user_id, 
                    $order_code, 
                    $total_amount_items_db, 
                    $address_id, 
                    $discount_amount_db, 
                    $final_amount_db, 
                    $payment_method, 
                    $order_status
                );
            } else {
                 throw new Exception("Error saat menyiapkan query pesanan (tanpa kupon): " . $conn->error);
            }
        } else {
            // Jika ada kupon, gunakan bind_param dengan coupon_id
            if ($stmt_order = $conn->prepare($sql_insert_order)) {
                $stmt_order->bind_param("isididsss", // isididsss: i=user_id, s=order_code, i=total_amount (decimal), i=shipping_address_id, i=coupon_id, d=discount_amount, d=final_amount, s=payment_method, s=order_status
                    $user_id, 
                    $order_code, 
                    $total_amount_items_db, 
                    $address_id, 
                    $coupon_id_db, 
                    $discount_amount_db, 
                    $final_amount_db, 
                    $payment_method, 
                    $order_status
                );
            } else {
                 throw new Exception("Error saat menyiapkan query pesanan (dengan kupon): " . $conn->error);
            }
        }

        if (!$stmt_order->execute()) {
            throw new Exception("Gagal membuat pesanan (Order). Error: " . $stmt_order->error);
        }
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        // --- START: Update Coupon Usage Count ---
        if ($coupon_id_db !== null) {
            $sql_update_coupon = "UPDATE coupons SET used_count = used_count + 1 WHERE id = ?";
            if ($stmt_coupon_update = $conn->prepare($sql_update_coupon)) {
                $stmt_coupon_update->bind_param("i", $coupon_id_db);
                if (!$stmt_coupon_update->execute()) {
                    throw new Exception("Gagal memperbarui jumlah penggunaan kupon. Error: " . $stmt_coupon_update->error);
                }
                $stmt_coupon_update->close();
            } else {
                throw new Exception("Error menyiapkan query update kupon: " . $conn->error);
            }
        }
        // --- END: Update Coupon Usage Count ---

        $sql_insert_detail = "INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)";

        if ($stmt_detail = $conn->prepare($sql_insert_detail)) {
            foreach ($cart_items as $item) {
                $sql_check_stock = "SELECT stock FROM products WHERE id = ?";
                if ($stmt_check = $conn->prepare($sql_check_stock)) {
                    $stmt_check->bind_param("i", $item['product_id']);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();
                    $product_data = $result_check->fetch_assoc();
                    $stmt_check->close();
                    
                    if (!$product_data || $product_data['stock'] < $item['quantity']) {
                        throw new Exception("Stok produk '{$item['name']}' tidak mencukupi ({$product_data['stock']} tersisa).");
                    }
                } else {
                    throw new Exception("Error menyiapkan query cek stok.");
                }
                
                $stmt_detail->bind_param(
                    "iiidd",
                    $order_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal']
                );
                if (!$stmt_detail->execute()) {
                    throw new Exception("Gagal menyimpan detail pesanan. Error: " . $stmt_detail->error);
                }
                
                $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
                if ($stmt_stock = $conn->prepare($sql_update_stock)) {
                    $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
                    if (!$stmt_stock->execute()) {
                        throw new Exception("Gagal memperbarui stok produk ID " . $item['product_id'] . ". Error: " . $stmt_stock->error);
                    }
                    $stmt_stock->close();
                } else {
                    throw new Exception("Error menyiapkan query update stok: " . $conn->error);
                }
            }
            $stmt_detail->close();

            $sql_clear_cart = "DELETE FROM cart_items WHERE user_id = ?";
            if ($stmt_clear = $conn->prepare($sql_clear_cart)) {
                $stmt_clear->bind_param("i", $user_id);
                if (!$stmt_clear->execute()) {
                    throw new Exception("Gagal mengosongkan keranjang. Error: " . $stmt_clear->error);
                }
                $stmt_clear->close();
            }

            $conn->commit();

            // Set session untuk modal sukses (TIDAK redirect)
            $_SESSION['checkout_success'] = true;
            $_SESSION['order_code'] = $order_code;
            $_SESSION['final_amount'] = $final_amount_db;
            $_SESSION['payment_method'] = $payment_method;
            
            // --- START: Hapus Data Kupon dari Session Setelah Sukses Checkout ---
            unset($_SESSION['applied_coupon']);
            unset($_SESSION['temp_coupon_code']);
            // --- END: Hapus Data Kupon dari Session Setelah Sukses Checkout ---


            // Reload halaman untuk menampilkan modal (atau gunakan AJAX jika ingin tanpa reload)
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;

        } else {
            throw new Exception("Error saat menyiapkan query detail pesanan: " . $conn->error);
        }

    } catch (Exception $e) {
        $conn->rollback();
        
        // --- START: Pertahankan data kupon di session jika rollback terjadi ---
        if (isset($_SESSION['applied_coupon'])) {
            $discount_amount = $_SESSION['applied_coupon']['discount_amount'];
            $final_amount = $total_amount_items + $shipping_cost - $discount_amount;
        }
        // --- END: Pertahankan data kupon di session jika rollback terjadi ---

        $error_message = "Proses Checkout Gagal: " . $e->getMessage();
    }
}
?>
