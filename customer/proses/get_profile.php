<?php 
// --- DEFENISI USER ID & AUTENTIKASI ---
$userId = $_SESSION['user_id'] ?? 1; // Menggunakan ID 1 sebagai default untuk demo
if (!isset($_SESSION['user_id']) && !isset($_SESSION['debug_mode'])) {
    // Jika tidak ada sesi dan bukan mode debug, redirect ke halaman login
    // header('Location: login.php');
    // exit;
}
// Catatan: Jika Anda menggunakan ID 1 untuk testing, hapus komentar pada baris autentikasi di atas saat deploy riil.

$message = '';
$error = '';
$user = null;
$addresses = [];

// ====================================================================
// 2. FUNGSI UTILITY
// ====================================================================

// Mengambil data pengguna
function fetchUserData($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}

// Mengambil data alamat
function fetchUserAddresses($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, label, recipient_name, phone_number, full_address, city, postal_code, is_active FROM user_addresses WHERE user_id = ? ORDER BY is_active DESC, updated_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}


// ====================================================================
// 3. HANDLER PERMINTAAN POST
// ====================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'update_profile') {
        $name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // FIX: Mengganti variabel yang tidak terdefinisi ($full_name) dengan ($name)
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone_number = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $phone, $userId); 
        if ($stmt->execute()) {
            $message = "Informasi profil berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
        $stmt->close();
    } 
    
    // --- MANAJEMEN ALAMAT ---
    elseif ($action == 'add_address') {
        $addressCount = count(fetchUserAddresses($conn, $userId));
        if ($addressCount >= 3) {
            $error = "Anda hanya diizinkan menyimpan maksimal 3 alamat.";
        } else {
            $label = $_POST['label'];
            $recipient_name = $_POST['recipient_name'];
            $phone_number = $_POST['phone_number'];
            $full_address = $_POST['full_address'];
            $city = $_POST['city'];
            $postal_code = $_POST['postal_code'];
            $isActive = ($addressCount == 0) ? 1 : 0; // Set aktif jika alamat pertama

            $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, label, recipient_name, phone_number, full_address, city, postal_code, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssi", $userId, $label, $recipient_name, $phone_number, $full_address, $city, $postal_code, $isActive);
            if ($stmt->execute()) {
                $message = "Alamat baru berhasil ditambahkan.";
            } else {
                $error = "Gagal menambah alamat: " . $conn->error;
            }
            $stmt->close();
        }
    } 
    elseif ($action == 'edit_address') {
        $id = (int)$_POST['address_id'];
        $label = $_POST['label'];
        $recipient_name = $_POST['recipient_name'];
        $phone_number = $_POST['phone_number'];
        $full_address = $_POST['full_address'];
        $city = $_POST['city'];
        $postal_code = $_POST['postal_code'];

        $stmt = $conn->prepare("UPDATE user_addresses SET label = ?, recipient_name = ?, phone_number = ?, full_address = ?, city = ?, postal_code = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssssii", $label, $recipient_name, $phone_number, $full_address, $city, $postal_code, $id, $userId);
        if ($stmt->execute()) {
            $message = "Alamat berhasil diperbarui.";
        } else {
            $error = "Gagal mengedit alamat: " . $conn->error;
        }
        $stmt->close();
    }
    elseif ($action == 'delete_address') {
        $id = (int)$_POST['address_id'];
        $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ? AND is_active = 0");
        $stmt->bind_param("ii", $id, $userId);
        if ($stmt->execute()) {
             // Jika alamat yang dihapus adalah yang aktif, set alamat pertama yang tersisa menjadi aktif (Opsional)
            $message = "Alamat berhasil dihapus.";
        } else {
            $error = "Gagal menghapus alamat. Alamat utama tidak dapat dihapus.";
        }
        $stmt->close();
    }
    elseif ($action == 'set_active_address') {
        $id = (int)$_POST['address_id'];
        
        // 1. Nonaktifkan semua alamat user
        $conn->query("UPDATE user_addresses SET is_active = 0 WHERE user_id = {$userId}");
        
        // 2. Aktifkan alamat yang dipilih
        $stmt = $conn->prepare("UPDATE user_addresses SET is_active = 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        if ($stmt->execute()) {
            $message = "Alamat utama berhasil diubah.";
        } else {
            $error = "Gagal mengubah alamat utama: " . $conn->error;
        }
        $stmt->close();
    }
    
    // --- UBAH KATA SANDI ---
    elseif ($action == 'change_password') {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $user_data = fetchUserData($conn, $userId); // Ambil data user lagi, termasuk hash password
        
        if ($new_password !== $confirm_password) {
            $error = "Kata sandi baru dan konfirmasi tidak cocok.";
        } else if (!password_verify($old_password, $user_data['password'])) { // Asumsi kolom 'password' ada di tabel 'users'
            $error = "Kata sandi lama salah.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $userId);
            if ($stmt->execute()) {
                $message = "Kata sandi berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui kata sandi: " . $conn->error;
            }
            $stmt->close();
        }
    }
    
    // Redirect untuk mencegah resubmission form setelah POST
    if ($message || $error) {
        header('Location: profile.php?tab=' . ($_POST['current_tab'] ?? 'profile') . '&msg=' . urlencode($message) . '&err=' . urlencode($error));
        exit;
    }
}

// ====================================================================
// 4. AMBIL DATA TERBARU & HANDLE URL PARAMETERS
// ====================================================================

// Ambil data terbaru setelah semua operasi POST
$user = fetchUserData($conn, $userId);
$addresses = fetchUserAddresses($conn, $userId);
$maxAddresses = 3;
$currentAddressCount = count($addresses);
$currentTab = $_GET['tab'] ?? 'profile';
$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// Hitung total item keranjang untuk navbar (simulasi atau ganti dengan kode riil)
$cartCount = 0; 
// Jika Anda ingin mengambil dari database seperti di cart.php, masukkan kodenya di sini.
if (isset($conn)) {
    $countStmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $cartCount = (int)($countResult['total'] ?? 0);
    $countStmt->close();
}
?>