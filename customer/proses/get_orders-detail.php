<?php
session_start();
header('Content-Type: application/json');

// Pastikan path ini benar relatif terhadap get_order_details.php
include '../db_connect.php'; 
include 'get_orders.php'; // Memuat format_rupiah dan get_status_data

$response = [
    'success' => false,
    'html' => '',
    'message' => ''
];

// Pastikan user sudah login
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id === 0) {
    // Menghindari pesan error HTML
    $response['message'] = 'Anda harus login untuk melihat detail pesanan.';
    echo json_encode($response);
    exit;
}

// Ambil order_id dari request POST
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($order_id === 0) {
    $response['message'] = 'ID Pesanan tidak valid.';
    echo json_encode($response);
    exit;
}

// Periksa koneksi
if ($conn->connect_error) {
    $response['message'] = "Koneksi database gagal: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}


// --- 1. Ambil Data Header Pesanan dan Alamat ---
$sql_header = "SELECT 
    o.id, 
    o.order_code, 
    o.order_date, 
    o.total_amount, 
    o.order_status, 
    o.payment_method, 
    ua.recipient_name,
    ua.phone_number,
    ua.full_address,
    ua.city,
    ua.province,
    ua.postal_code
    FROM orders o
    -- Join ke user_addresses menggunakan shipping_address_id
    JOIN user_addresses ua ON o.shipping_address_id = ua.id
    WHERE o.id = ? AND o.user_id = ?";

$order = null;
if ($stmt_header = $conn->prepare($sql_header)) {
    $stmt_header->bind_param("ii", $order_id, $user_id);
    $stmt_header->execute();
    $result_header = $stmt_header->get_result();
    $order = $result_header->fetch_assoc();
    $stmt_header->close();
}

if (!$order) {
    $response['message'] = 'Pesanan tidak ditemukan, bukan milik Anda, atau alamat pengiriman tidak valid.';
    echo json_encode($response);
    exit;
}

// --- 2. Ambil Data Detail Item Pesanan ---
$sql_details = "SELECT 
    p.name AS product_name, 
    od.quantity, 
    od.unit_price, 
    od.subtotal    
    FROM order_details od
    JOIN products p ON od.product_id = p.id 
    WHERE od.order_id = ?";

$items = [];
if ($stmt_details = $conn->prepare($sql_details)) {
    $stmt_details->bind_param("i", $order_id);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();
    while ($row = $result_details->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt_details->close();
}

// --- 3. Bangun Tampilan HTML Modal ---
ob_start(); // Mulai output buffering

$status_data = get_status_data($order['order_status']);

// Gabungkan alamat lengkap
$full_address_display = 
    $order['full_address'] . ', ' . 
    $order['city'] . ', ' . 
    $order['province'] . ', ' . 
    $order['postal_code'];
?>

<div class="row">
    <div class="col-md-6 mb-3">
        <h5><i class="fas fa-info-circle me-1"></i> Informasi Dasar</h5>
        <table class="table table-sm table-borderless">
            <tr>
                <td class="fw-bold">Kode Pesanan</td>
                <td><?= htmlspecialchars($order['order_code']) ?></td>
            </tr>
            <tr>
                <td class="fw-bold">Tanggal Pesan</td>
                <td><?= date('d F Y H:i', strtotime($order['order_date'])) ?></td>
            </tr>
            <tr>
                <td class="fw-bold">Metode Bayar</td>
                <td><?= htmlspecialchars($order['payment_method']) ?></td>
            </tr>
            <tr>
                <td class="fw-bold">Status</td>
                <td><span class="badge <?= $status_data['class'] ?>"><?= htmlspecialchars($status_data['display']) ?></span></td>
            </tr>
        </table>
    </div>

    <div class="col-md-6 mb-3">
        <h5><i class="fas fa-map-marker-alt me-1"></i> Alamat Pengiriman</h5>
        <p class="mb-0">
            **Penerima:** <?= htmlspecialchars($order['recipient_name']) ?> (<?= htmlspecialchars($order['phone_number']) ?>)
        </p>
        <p class="small text-muted">
            <?= nl2br(htmlspecialchars($full_address_display)) ?>
        </p>
    </div>
</div>

<hr>

<h5><i class="fas fa-box me-1"></i> Rincian Produk</h5>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-center">Kuantitas</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-center"><?= htmlspecialchars($item['quantity']) ?></td>
                <td class="text-end"><?= format_rupiah($item['unit_price']) ?></td>
                <td class="text-end"><?= format_rupiah($item['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Total Pembayaran</td>
                <td class="text-end fw-bold text-danger"><?= format_rupiah($order['final_amount']) ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php 
// Akhiri output buffering dan masukkan konten ke dalam response
$response['html'] = ob_get_clean(); 
$response['success'] = true;

// Tutup koneksi
if (isset($conn) && $conn->ping()) {
    $conn->close();
}

echo json_encode($response);
?>