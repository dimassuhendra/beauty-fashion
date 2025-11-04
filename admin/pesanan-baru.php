<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Menggunakan file koneksi database
include '../db_connect.php';

// Pastikan koneksi $conn sudah tersedia
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Koneksi tidak terdefinisi."));
}

// --------------------------------------------------------------------
// 2. TANGANI AKSI UBAH STATUS PESANAN
// --------------------------------------------------------------------

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    if ($order_id > 0) {
        // PENTING: Gunakan Prepared Statement di lingkungan produksi
        $sql = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";
        
        if ($conn->query($sql)) {
            $message = "Status Pesanan #$order_id berhasil diperbarui menjadi <b>$new_status</b>.";
            $message_type = 'success';
        } else {
            $message = "Error saat memperbarui status: " . $conn->error;
            $message_type = 'danger';
        }
    }
}


// --------------------------------------------------------------------
// 3. PENGATURAN DAN PENGAMBILAN DATA PESANAN (Untuk Tabel & Summary)
// --------------------------------------------------------------------

// Status yang akan di-monitor
$statuses_to_monitor = [
    'Pending Payment' => ['icon' => 'fas fa-clock', 'color' => 'warning'],
    'Processing' => ['icon' => 'fas fa-cogs', 'color' => 'info'],
    'Shipped' => ['icon' => 'fas fa-truck', 'color' => 'primary'],
    'Completed' => ['icon' => 'fas fa-check-circle', 'color' => 'success'],
    'Cancelled' => ['icon' => 'fas fa-times-circle', 'color' => 'danger']
];

// Ambil jumlah pesanan per status
$status_counts = [];
foreach (array_keys($statuses_to_monitor) as $status) {
    $sql_count = "SELECT COUNT(id) FROM orders WHERE order_status = '$status'";
    $result_count = $conn->query($sql_count);
    $status_counts[$status] = $result_count ? $result_count->fetch_row()[0] : 0;
}


// --- Pengaturan Tabel ---
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Search
$search_query = "";
$search = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
if (!empty($search)) {
    // Mencari berdasarkan kode pesanan atau nama user
    $search_query = " WHERE o.order_code LIKE '%$search%' OR u.name LIKE '%$search%' ";
}

// Filtering by Status
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
if (!empty($filter_status)) {
    // Tambahkan WHERE jika belum ada search_query, atau tambahkan AND
    if (empty($search_query)) {
        $search_query = " WHERE o.order_status = '$filter_status' ";
    } else {
        $search_query .= " AND o.order_status = '$filter_status' ";
    }
}

// Sorting
$sort_columns = ['id', 'order_code', 'name', 'final_amount', 'order_status', 'order_date'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $sort_columns) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

// Query untuk menghitung total pesanan (untuk pagination)
$count_sql = "
    SELECT COUNT(o.id) 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    $search_query
";
$total_result = $conn->query($count_sql);
$total_rows = $total_result ? $total_result->fetch_row()[0] : 0;
$total_pages = ceil($total_rows / $limit);

// Query untuk mengambil data pesanan
$sql = "
    SELECT 
        o.id, o.order_code, o.final_amount, o.order_status, o.order_date, o.payment_method,
        u.full_name as user_name, u.email as user_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    $search_query
    ORDER BY $sort $order
    LIMIT $limit OFFSET $offset
";

$orders_result = $conn->query($sql);
$orders = [];
if ($orders_result) {
    while ($row = $orders_result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Fungsi bantu untuk membuat link sorting
function get_sort_link($column, $current_sort, $current_order, $current_limit, $current_search, $current_status) {
    $new_order = ($current_sort == $column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $params = [
        'sort' => $column,
        'order' => $new_order,
        'limit' => $current_limit,
        's' => $current_search,
        'status' => $current_status
    ];
    return '?' . http_build_query(array_filter($params));
}
// Fungsi bantu untuk membuat link filter status
function get_status_link($status, $current_limit, $current_search) {
    $params = [
        'limit' => $current_limit,
        's' => $current_search,
        'status' => $status
    ];
    return '?' . http_build_query(array_filter($params));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Pesanan</h1>
            <p class="lead">Kelola dan pantau status semua pesanan pelanggan.</p>
        </header>

        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <?php foreach ($statuses_to_monitor as $status => $details): 
                $count = $status_counts[$status] ?? 0;
                $is_active_filter = $filter_status === $status ? 'active-status-card' : '';
            ?>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="<?php echo get_status_link($status, $limit, $search); ?>" class="text-decoration-none">
                    <div class="card shadow-sm order-summary-card <?php echo $is_active_filter; ?>">
                        <div class="card-body text-center">
                            <i
                                class="<?php echo $details['icon']; ?> fa-2x text-<?php echo $details['color']; ?> mb-2"></i>
                            <h5 class="card-title mb-0"><?php echo $count; ?></h5>
                            <p class="card-text small text-muted"><?php echo $status; ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            <div class="col-lg-2 col-md-4 col-6">
                <a href="manajemen-pesanan.php?limit=<?php echo $limit; ?>" class="text-decoration-none">
                    <div
                        class="card shadow-sm order-summary-card <?php echo empty($filter_status) ? 'active-status-card' : ''; ?>">
                        <div class="card-body text-center">
                            <i class="fas fa-list fa-2x text-pink-primary mb-2"></i>
                            <h5 class="card-title mb-0"><?php echo $total_rows; ?></h5>
                            <p class="card-text small text-muted">Total Semua</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h5 class="mb-0 card-title-dark-mode">Daftar Pesanan
                    <?php if (!empty($filter_status)): ?>
                    <span class="badge bg-pink-light">Status: <?php echo htmlspecialchars($filter_status); ?></span>
                    <?php endif; ?>
                </h5>

                <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">

                    <div class="input-group input-group-sm" style="width: auto;">
                        <label class="input-group-text" for="limit">Tampil</label>
                        <select name="limit" id="limit" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="s" class="form-control" placeholder="Cari Kode/Nama User..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search) || !empty($filter_status)): ?>
                        <a href="manajemen-pesanan.php?limit=<?php echo $limit; ?>" class="btn btn-outline-danger"
                            title="Reset Pencarian/Filter"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead>
                        <tr>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('id', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'id' ? 'active-sort' : ''; ?>">
                                    #
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'id' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('order_code', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'order_code' ? 'active-sort' : ''; ?>">
                                    Kode Pesanan
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'order_code' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('user_name', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'user_name' ? 'active-sort' : ''; ?>">
                                    Pelanggan
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'user_name' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('final_amount', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'final_amount' ? 'active-sort' : ''; ?>">
                                    Total
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'final_amount' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('order_date', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'order_date' ? 'active-sort' : ''; ?>">
                                    Tanggal
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'order_date' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">Pembayaran</th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('order_status', $sort, $order, $limit, $search, $filter_status); ?>"
                                    class="sortable-header <?php echo $sort == 'order_status' ? 'active-sort' : ''; ?>">
                                    Status
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'order_status' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?php echo $o['id']; ?></td>
                            <td><span class="fw-bold"><?php echo $o['order_code']; ?></span></td>
                            <td>
                                <?php echo htmlspecialchars($o['user_name']); ?>
                                <small
                                    class="d-block text-muted"><?php echo htmlspecialchars($o['user_email']); ?></small>
                            </td>
                            <td>Rp<?php echo number_format($o['final_amount'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d M Y', strtotime($o['order_date'])); ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $o['payment_method']; ?></span>
                            </td>
                            <td>
                                <?php 
                                            $status_class = $statuses_to_monitor[$o['order_status']]['color'] ?? 'secondary';
                                        ?>
                                <span class="badge bg-<?php echo $status_class; ?> order-status-badge">
                                    <?php echo $o['order_status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1 btn-detail-order" data-bs-toggle="modal"
                                    data-bs-target="#detailOrderModal" data-order-id="<?php echo $o['id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-pink dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false" title="Ubah Status">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="dropdown-header">Ubah Status ke:</li>
                                        <?php foreach ($statuses_to_monitor as $status => $details): ?>
                                        <?php if ($status !== $o['order_status']): ?>
                                        <li>
                                            <a class="dropdown-item update-status-btn" href="#"
                                                data-id="<?php echo $o['id']; ?>" data-status="<?php echo $status; ?>"
                                                data-code="<?php echo $o['order_code']; ?>">
                                                <span class="text-<?php echo $details['color']; ?>"><i
                                                        class="<?php echo $details['icon']; ?> me-2"></i><?php echo $status; ?></span>
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pesanan yang ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?php echo count($orders); ?> dari <?php echo $total_rows; ?>
                    total pesanan.</small>

                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($total_pages > 1): ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>&s=<?php echo $search; ?>&status=<?php echo $filter_status; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php 
                            // Tampilkan maksimal 5 link halaman di sekitar halaman saat ini
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            for ($i = $start; $i <= $end; $i++): 
                                $is_active = $i == $page ? 'active' : '';
                                $page_link = "?page=$i&limit=$limit&s=$search&status=$filter_status&sort=$sort&order=$order";
                            ?>
                        <li class="page-item <?php echo $is_active; ?>">
                            <a class="page-link" href="<?php echo $page_link; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>&s=<?php echo $search; ?>&status=<?php echo $filter_status; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

    </div>

    <div class="modal fade" id="detailOrderModal" tabindex="-1" aria-labelledby="detailOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailOrderModalLabel">Detail Pesanan: #<span
                            id="order_code_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal_content_loading" class="text-center p-5">
                        <div class="spinner-border text-pink-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat detail pesanan...</p>
                    </div>
                    <div id="modal_content_detail" style="display: none;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-pink-primary text-white">
                    <h5 class="modal-title" id="updateStatusModalLabel">Konfirmasi Perubahan Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="manajemen-pesanan.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" id="status_order_id">
                        <input type="hidden" name="new_status" id="status_new_status">
                        <p>Yakin ingin mengubah status pesanan <b id="status_order_code"></b> menjadi <b
                                id="status_new_status_text"></b>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-pink">Ya, Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // ====================================================
    // Logic Dark/Light Mode (FIXED)
    // ====================================================
    const body = document.getElementById('body-admin');
    const modeToggle = document.getElementById('mode-toggle');

    function applyMode(isInitialLoad = true) {
        let savedMode = localStorage.getItem('theme') || 'light';

        if (!isInitialLoad && modeToggle) {
            savedMode = body.classList.contains('dark-mode') ? 'light' : 'dark';
            localStorage.setItem('theme', savedMode);
        }

        if (savedMode === 'dark') {
            body.classList.add('dark-mode');
        } else {
            body.classList.remove('dark-mode');
        }

        // Terapkan styling ke modal (agar sesuai dengan mode)
        document.querySelectorAll('.modal-content').forEach(el => {
            if (savedMode === 'dark') {
                el.classList.add('dark-mode');
            } else {
                el.classList.remove('dark-mode');
            }
        });

        if (modeToggle) {
            if (savedMode === 'dark') {
                modeToggle.innerHTML = '<i class="fas fa-moon"></i> Dark Mode';
                modeToggle.classList.remove('text-pink-primary');
            } else {
                modeToggle.innerHTML = '<i class="fas fa-sun"></i> Light Mode';
                modeToggle.classList.add('text-pink-primary');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyMode(true);
    });

    if (modeToggle) {
        modeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            applyMode(false);
        });
    }


    // ====================================================
    // Logic Modals (Detail dan Update Status)
    // ====================================================

    // 1. Logic untuk MODAL DETAIL (Menggunakan AJAX)
    $('#detailOrderModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const orderId = button.data('order-id');
        const modal = $(this);

        // Tampilkan loading, sembunyikan detail
        modal.find('#modal_content_loading').show();
        modal.find('#modal_content_detail').hide().empty();
        modal.find('#order_code_title').text('...'); // Placeholder

        // Lakukan permintaan AJAX
        $.ajax({
            url: 'proses/get_pesanan-baru.php', // Kita akan buat file ini
            method: 'GET',
            data: {
                id: orderId
            },
            dataType: 'json',
            success: function(response) {
                modal.find('#modal_content_loading').hide();

                if (response.success) {
                    const order = response.data.order;
                    const items = response.data.items;

                    modal.find('#order_code_title').text(order.order_code);

                    let html = `
                            <div class="row mb-4 order-detail-header">
                                <div class="col-md-4">
                                    <p class="mb-1 small text-muted">Kode/ID Pesanan:</p>
                                    <h5 class="fw-bold text-pink-primary">${order.order_code} / #${order.id}</h5>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 small text-muted">Status Pesanan:</p>
                                    <span class="badge bg-${response.data.status_color} order-status-badge">${order.order_status}</span>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 small text-muted">Tanggal Pesanan:</p>
                                    <p class="fw-bold">${order.order_date_formatted}</p>
                                </div>
                            </div>

                            <hr class="my-3 detail-divider">

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-user me-2 text-pink-primary"></i> Info Pelanggan</h6>
                                    <p class="mb-1"><strong>Nama:</strong> ${order.user_name}</p>
                                    <p class="mb-1"><strong>Email:</strong> ${order.user_email}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-map-marker-alt me-2 text-pink-primary"></i> Alamat Pengiriman</h6>
                                    <p class="mb-1">${order.shipping_address_line1}</p>
                                    <p class="mb-1">${order.shipping_address_city}, ${order.shipping_address_postcode}</p>
                                </div>
                            </div>
                            
                            <h6><i class="fas fa-box-open me-2 text-pink-primary"></i> Item Pesanan</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${items.map(item => `
                                            <tr>
                                                <td>${item.product_name}</td>
                                                <td class="text-end">Rp${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                                                <td class="text-end">${item.quantity}</td>
                                                <td class="text-end">Rp${new Intl.NumberFormat('id-ID').format(item.price * item.quantity)}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-md-6">
                                    <div class="border p-3 rounded order-summary-box">
                                        <p class="d-flex justify-content-between mb-1">Total Item: <span>Rp${new Intl.NumberFormat('id-ID').format(order.total_amount)}</span></p>
                                        <p class="d-flex justify-content-between mb-1 text-success">Diskon (${order.discount_percentage}%): <span>- Rp${new Intl.NumberFormat('id-ID').format(order.discount_amount)}</span></p>
                                        <p class="d-flex justify-content-between mb-2 text-primary">Biaya Kirim: <span>Rp${new Intl.NumberFormat('id-ID').format(order.shipping_cost)}</span></p>
                                        <h5 class="d-flex justify-content-between border-top pt-2 fw-bold text-pink-primary">Total Akhir: <span>Rp${new Intl.NumberFormat('id-ID').format(order.final_amount)}</span></h5>
                                    </div>
                                </div>
                            </div>
                        `;
                    modal.find('#modal_content_detail').html(html).show();

                } else {
                    modal.find('#modal_content_detail').html(
                        `<p class="alert alert-danger">${response.message}</p>`).show();
                }
            },
            error: function(xhr) {
                modal.find('#modal_content_loading').hide();
                modal.find('#modal_content_detail').html(
                    '<p class="alert alert-danger">Terjadi kesalahan saat mengambil data dari server.</p>'
                ).show();
            }
        });
    });

    // 2. Logic untuk MODAL UPDATE STATUS (Konfirmasi)
    $('.update-status-btn').on('click', function(e) {
        e.preventDefault();
        const orderId = $(this).data('id');
        const newStatus = $(this).data('status');
        const orderCode = $(this).data('code');

        // Isi modal konfirmasi
        $('#status_order_id').val(orderId);
        $('#status_new_status').val(newStatus);
        $('#status_order_code').text(orderCode);
        $('#status_new_status_text').text(newStatus).addClass('text-uppercase');

        // Tampilkan modal konfirmasi
        const updateStatusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        updateStatusModal.show();
    });
    </script>
</body>

</html>