<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php' ?>

        <div id="content">
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

            <div class="row g-4 mb-5">
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
                                    <div class="h5 mb-0 fw-bold text-gray-800">Rp 45.670.000</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Pesanan Baru (Pending)</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">25</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-pink shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-cubes fa-2x text-pink-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-uppercase text-muted fw-bold mb-1">
                                        Total Produk Terdaftar</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">150</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <div class="h5 mb-0 fw-bold text-gray-800">12</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary);">Pesanan Terbaru</h6>
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
                                <tr>
                                    <td>#BF2025001</td>
                                    <td>Ani Nurlela</td>
                                    <td>15/10/2025</td>
                                    <td>Rp 350.000</td>
                                    <td><span class="badge bg-warning">Menunggu Pembayaran</span></td>
                                    <td><button class="btn btn-sm btn-outline-secondary">Detail</button></td>
                                </tr>
                                <tr>
                                    <td>#BF2025002</td>
                                    <td>Budi Santoso</td>
                                    <td>14/10/2025</td>
                                    <td>Rp 580.000</td>
                                    <td><span class="badge bg-success">Dikirim</span></td>
                                    <td><button class="btn btn-sm btn-outline-secondary">Detail</button></td>
                                </tr>
                                <tr>
                                    <td>#BF2025003</td>
                                    <td>Citra Dewi</td>
                                    <td>14/10/2025</td>
                                    <td>Rp 120.000</td>
                                    <td><span class="badge bg-info">Sedang Diproses</span></td>
                                    <td><button class="btn btn-sm btn-outline-secondary">Detail</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
    // Saat ini, sidebar tidak bisa di-toggle, tapi bisa ditambahkan jika dibutuhkan
    </script>
</body>

</html>