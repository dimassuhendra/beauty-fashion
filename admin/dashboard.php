<?php
include '../db_connect.php';
include './proses/get_dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Asumsi style-admin.css mendefinisikan .text-pink-primary dan .card-pink -->
    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
    <style>
    /* Gaya tambahan sementara jika style-admin.css tidak tersedia */
    .text-pink-primary {
        color: #d63384;
    }

    .card-pink {
        border-left: 5px solid #d63384;
    }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php';?>
        <div id="content" class="p-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <h2 class="text-pink-primary fw-bold mb-0">Selamat Datang, Admin!</h2>
                    <div class="d-flex">
                        <span class="navbar-text me-3 d-none d-sm-inline">
                            <?php echo date('l, d-m-Y'); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Bagian Metrik/Card -->
            <div class="row g-4 mb-5">
                <!-- Card 1: Total Penjualan (Bulan Ini) -->
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-money-check-alt fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Total Penjualan (Bulan Ini)</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">
                                        <?php echo formatRupiah($total_sales_this_month); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pesanan Baru (Pending) -->
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Pesanan Baru (Pending Pembayaran)</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">
                                        <?php echo number_format($count_pending_orders); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Total Produk Terdaftar -->
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-cubes fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Total Produk Aktif</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">
                                        <?php echo number_format($count_total_products); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Pelanggan Baru (Hari Ini) -->
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-user-plus fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Pelanggan Baru (Hari Ini)</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">
                                        <?php echo number_format($count_new_users_today); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Tabel Pesanan Terbaru -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary, #d63384);">Pesanan Terbaru (Pending &
                        Diproses)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_orders->num_rows > 0): ?>
                                <?php while($row = $recent_orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['order_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['order_date'])); ?></td>
                                    <td><?php echo formatRupiah($row['final_amount']); ?></td>
                                    <td><?php echo getStatusBadge($row['order_status']); ?></td>
                                    <td><button class="btn btn-sm btn-outline-secondary">Detail</button></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pesanan terbaru yang perlu diproses.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>