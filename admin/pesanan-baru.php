<?php
require_once '../db_connect.php'; 
require_once './proses/get_pesanan-baru.php'; 

$is_status_redirect = isset($_GET['status']) && isset($_GET['message']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan | Beauty Fashion Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
    <style>
    /* Gaya kustom untuk badge status pesanan */
    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }

    .badge-processing {
        background-color: #0d6efd;
    }

    .badge-shipped {
        background-color: #198754;
    }

    .badge-completed {
        background-color: #6c757d;
    }

    .badge-cancelled {
        background-color: #dc3545;
    }

    /* DataTables Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: var(--bs-pink-primary) !important;
        border-color: var(--bs-pink-primary) !important;
        color: white !important;
    }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php';?>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <h2 class="text-pink-primary fw-bold mb-0">Manajemen Pesanan</h2>
                    <div class="d-flex">
                        <span class="navbar-text me-3 d-none d-sm-inline">
                            <?php echo date('l, d-m-Y'); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary);">Daftar Semua Pesanan</h6>
                </div>
                <div class="card-body">
                    <div id="alertPlaceholder">
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <label for="statusFilter" class="form-label fw-semibold me-2 mb-0">Filter Status:</label>
                        <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Status</option>
                            <?php 
                            // Pastikan data status tersedia
                            if (isset($result_statuses) && mysqli_num_rows($result_statuses) > 0) {
                                mysqli_data_seek($result_statuses, 0); 
                                while ($status = mysqli_fetch_assoc($result_statuses)): 
                            ?>
                            <option value="<?php echo htmlspecialchars($status['order_status']); ?>">
                                <?php echo htmlspecialchars($status['order_status']); ?>
                            </option>
                            <?php endwhile; } ?>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="orderTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Kode Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Total Akhir</th>
                                    <th>Metode Bayar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($result_orders) && mysqli_num_rows($result_orders) > 0): ?>
                                <?php mysqli_data_seek($result_orders, 0); 
                                while ($order = mysqli_fetch_assoc($result_orders)): 
                                    // Tentukan class badge berdasarkan status
                                    $status_class = match ($order['order_status']) {
                                        'Pending Payment' => 'badge-pending',
                                        'Processing' => 'badge-processing',
                                        'Shipped' => 'badge-shipped',
                                        'Completed' => 'badge-completed',
                                        'Cancelled' => 'badge-cancelled',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_code']); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?>
                                        (<?php echo htmlspecialchars($order['shipping_city']); ?>)</td>
                                    <td data-order="<?php echo $order['final_amount']; ?>">
                                        Rp <?php echo number_format($order['final_amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $status_class; ?> py-2 px-3 rounded-pill">
                                            <?php echo htmlspecialchars($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td class="btn-action-group">
                                        <a href="detail-pesanan.php?id=<?php echo $order['order_id']; ?>"
                                            class="btn btn-info me-1 rounded-lg text-white" title="Lihat Detail"><i
                                                class="fas fa-eye"></i></a>

                                        <div class="dropdown d-inline">
                                            <button class="btn btn-primary rounded-lg dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false" title="Ubah Status">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="./proses/proses_pesanan-baru.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=Processing">Processing</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="./proses/proses_pesanan-baru.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=Shipped">Shipped</a>
                                                </li>
                                                <li><a class="dropdown-item"
                                                        href="./proses/proses_pesanan-baru.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=Completed">Completed</a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item text-danger"
                                                        href="./proses/proses_pesanan-baru.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=Cancelled">Cancelled</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada pesanan yang tercatat
                                        di
                                        database.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

    <script>
    $(document).ready(function() {

        // =========================================================
        // === 1. INISIALISASI DATATABLES ===
        // =========================================================
        var orderTable = $('#orderTable').DataTable({
            "order": [
                [2, "desc"]
            ], // Urutkan default berdasarkan kolom Tanggal (indeks 2), urutan menurun (desc)
            "language": {
                // FIX ERROR I18N: Menggunakan URL penuh
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
            },
            "columnDefs": [{
                    "orderable": false,
                    "searchable": false,
                    "targets": [7] // Kolom 'Aksi'
                },
                {
                    "type": "num-fmt",
                    "targets": 4 // Kolom 'Total Akhir'
                },
                {
                    "targets": [0], // Kolom 'ID'
                    "visible": false, // Sembunyikan ID agar tidak mengganggu, tapi tetap bisa disorting
                    "searchable": false
                }
            ]
        });

        // =========================================================
        // === 2. LOGIKA FILTER STATUS KUSTOM ===
        // =========================================================
        $('#statusFilter').on('change', function() {
            var statusName = $(this).val();
            // Kolom 'Status' adalah kolom ke-6 (indeks 6)
            orderTable.column(6).search(statusName ? '^' + statusName + '$' : '', true, false).draw();
        });


        // =========================================================
        // === 3. LOGIKA NOTIFIKASI (Merespon Redirect PHP) ===
        // =========================================================
        <?php if ($is_status_redirect): ?>
        Swal.fire({
            icon: '<?php echo $_GET['status']; ?>',
            title: '<?php echo ($_GET['status'] == 'success' ? 'Berhasil!' : 'Gagal!'); ?>',
            text: '<?php echo htmlspecialchars($_GET['message']); ?>',
            timer: 4000,
            showConfirmButton: false
        }).then(() => {
            if (history.replaceState) {
                let url = window.location.href;
                url = url.replace(/[?&]status=[^&]*/g, '').replace(/[?&]message=[^&]*/g, '');
                url = url.replace(/([?])\&/g, '$1');
                history.replaceState({}, document.title, url);
            }
        });
        <?php endif; ?>

        // 4. Tutup koneksi database
        <?php mysqli_close($conn); ?>
    });
    </script>
</body>

</html>