<?php
// Pastikan file ini di-include setelah db_connect.php dan sesi sudah dimulai (jika menggunakan sesi)
// $conn adalah variabel koneksi database

// Pastikan sesi dimulai jika digunakan untuk notifikasi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ========================================================
// FUNGSI VALIDASI
// ========================================================
function validate_coupon_data($data) {
    $errors = [];
    
    if (empty($data['coupon_code'])) {
        $errors[] = "Kode Kupon wajib diisi.";
    } elseif (!preg_match('/^[A-Z0-9_-]+$/', $data['coupon_code'])) {
         $errors[] = "Kode Kupon hanya boleh berisi huruf kapital, angka, garis bawah, atau strip.";
    }

    if (!in_array($data['discount_type'], ['fixed', 'percent'])) {
        $errors[] = "Tipe Diskon tidak valid.";
    }

    if (!is_numeric($data['discount_value']) || $data['discount_value'] <= 0) {
        $errors[] = "Nilai Diskon harus berupa angka positif.";
    } elseif ($data['discount_type'] == 'percent' && $data['discount_value'] > 100) {
        $errors[] = "Persentase diskon tidak boleh lebih dari 100.";
    }

    if (empty($data['valid_from']) || empty($data['valid_until'])) {
        $errors[] = "Tanggal berlaku wajib diisi.";
    } elseif (strtotime($data['valid_from']) > strtotime($data['valid_until'])) {
        $errors[] = "Tanggal Mulai tidak boleh lebih dari Tanggal Berakhir.";
    }

    if (!empty($data['usage_limit']) && (!is_numeric($data['usage_limit']) || $data['usage_limit'] < 1)) {
        $errors[] = "Batasan Penggunaan harus berupa angka positif.";
    }
    
    if (!is_numeric($data['minimum_purchase']) || $data['minimum_purchase'] < 0) {
        $errors[] = "Minimum Pembelian harus berupa angka non-negatif.";
    }

    return $errors;
}

// ========================================================
// PROSES CRUD (POST & GET)
// ========================================================

// --------------------------------------------------------
// 1. TAMBAH/EDIT KUPON (POST)
// --------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $coupon_id = intval($_POST['coupon_id'] ?? 0);
    
    // Ambil dan bersihkan data
    $coupon_code = strtoupper(trim($_POST['coupon_code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $minimum_purchase = floatval($_POST['minimum_purchase']);
    $valid_from = $_POST['valid_from'];
    $valid_until = $_POST['valid_until'];
    $usage_limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $data = [
        'coupon_code' => $coupon_code,
        'discount_type' => $discount_type,
        'discount_value' => $discount_value,
        'minimum_purchase' => $minimum_purchase,
        'valid_from' => $valid_from,
        'valid_until' => $valid_until,
        'usage_limit' => $usage_limit,
    ];

    $errors = validate_coupon_data($data);

    if (empty($errors)) {
        // Cek duplikasi kode kupon
        $check_sql = "SELECT id FROM coupons WHERE coupon_code = ? AND id != ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("si", $coupon_code, $coupon_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $_SESSION['message'] = "Kode kupon **$coupon_code** sudah ada. Silakan gunakan kode lain.";
            $_SESSION['msg_type'] = "danger";
            $stmt_check->close();
            header("Location: manajemen-diskon.php");
            exit();
        }
        $stmt_check->close();


        try {
            if ($action === 'add_coupon') {
                $sql = "INSERT INTO coupons (coupon_code, discount_type, discount_value, minimum_purchase, valid_from, valid_until, usage_limit, is_active) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssddssii", $coupon_code, $discount_type, $discount_value, $minimum_purchase, $valid_from, $valid_until, $usage_limit, $is_active);
                
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Kupon diskon **$coupon_code** berhasil ditambahkan!";
                    $_SESSION['msg_type'] = "success";
                } else {
                    throw new Exception("Gagal menambahkan kupon: " . $stmt->error);
                }
            } elseif ($action === 'edit_coupon' && $coupon_id > 0) {
                $sql = "UPDATE coupons SET coupon_code = ?, discount_type = ?, discount_value = ?, minimum_purchase = ?, valid_from = ?, valid_until = ?, usage_limit = ?, is_active = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssddssiii", $coupon_code, $discount_type, $discount_value, $minimum_purchase, $valid_from, $valid_until, $usage_limit, $is_active, $coupon_id);

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Kupon diskon **$coupon_code** berhasil diperbarui!";
                    $_SESSION['msg_type'] = "success";
                } else {
                    throw new Exception("Gagal memperbarui kupon: " . $stmt->error);
                }
            } else {
                $_SESSION['message'] = "Aksi tidak valid atau Kupon ID tidak ditemukan.";
                $_SESSION['msg_type'] = "danger";
            }

            if (isset($stmt)) $stmt->close();
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['message'] = "Terjadi kesalahan saat memproses data: " . $e->getMessage();
            $_SESSION['msg_type'] = "danger";
        }
    } else {
        // Jika ada error validasi
        $_SESSION['message'] = "Gagal memproses kupon: " . implode(", ", $errors);
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: manajemen-diskon.php");
    exit();
}

// --------------------------------------------------------
// 2. HAPUS KUPON (GET)
// --------------------------------------------------------
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    try {
        $sql = "DELETE FROM coupons WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Kupon diskon berhasil dihapus.";
            $_SESSION['msg_type'] = "success";
        } else {
            throw new Exception("Gagal menghapus kupon: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus kupon: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: manajemen-diskon.php");
    exit();
}

// --------------------------------------------------------
// 3. TOGGLE STATUS KUPON (GET)
// --------------------------------------------------------
if (isset($_GET['toggle_status']) && isset($_GET['current_status'])) {
    $coupon_id = intval($_GET['toggle_status']);
    $current_status = intval($_GET['current_status']);
    $new_status = $current_status == 1 ? 0 : 1;
    $status_text = $new_status == 1 ? 'diaktifkan' : 'dinonaktifkan';

    try {
        $sql = "UPDATE coupons SET is_active = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_status, $coupon_id);

        if ($stmt->execute()) {
            // Ambil kode kupon untuk notifikasi yang lebih informatif
            $code_sql = "SELECT coupon_code FROM coupons WHERE id = ?";
            $code_stmt = $conn->prepare($code_sql);
            $code_stmt->bind_param("i", $coupon_id);
            $code_stmt->execute();
            $code_result = $code_stmt->get_result();
            $code_row = $code_result->fetch_assoc();
            $coupon_code = $code_row['coupon_code'] ?? "ID #$coupon_id";
            $code_stmt->close();

            $_SESSION['message'] = "Kupon **$coupon_code** berhasil $status_text.";
            $_SESSION['msg_type'] = "success";
        } else {
            throw new Exception("Gagal mengubah status: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['message'] = "Terjadi kesalahan saat mengubah status kupon: " . $e->getMessage();
        $_SESSION['msg_type'] = "danger";
    }

    header("Location: manajemen-diskon.php");
    exit();
}
?>