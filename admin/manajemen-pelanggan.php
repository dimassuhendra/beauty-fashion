<?php
include '../db_connect.php';
include 'proses/proses_manajemen-pelanggan.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Pelanggan</h1>
            <p class="lead">Data lengkap pelanggan, riwayat belanja, dan statistik akun.</p>
        </header>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm customer-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fa-3x text-pink-primary me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Pelanggan</p>
                                <h4 class="card-title mb-0">
                                    <?php echo number_format($summary['total_users'], 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm customer-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shopping-bag fa-3x text-info me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Pernah Belanja</p>
                                <h4 class="card-title mb-0">
                                    <?php echo number_format($summary['users_with_orders'], 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm customer-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-receipt fa-3x text-warning me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Pesanan Dibuat</p>
                                <h4 class="card-title mb-0">
                                    <?php echo number_format($summary['total_orders_placed'], 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm customer-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-dollar-sign fa-3x text-success me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Revenue (Completed)</p>
                                <h4 class="card-title mb-0">
                                    Rp<?php echo number_format($summary['total_revenue'], 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h5 class="mb-0 card-title-dark-mode">Tabel Data Pelanggan</h5>

                <form method="GET" class="d-flex flex-wrap align-items-center gap-3">

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
                        <input type="text" name="s" class="form-control" placeholder="Cari Nama/Email Pelanggan..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search)): ?>
                        <a href="manajemen-pelanggan.php?limit=<?php echo $limit; ?>" class="btn btn-outline-danger"
                            title="Reset Pencarian"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead>
                        <tr>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('id', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'id' ? 'active-sort' : ''; ?>">
                                    #
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'id' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" style="width: 25%;">
                                <a href="<?php echo get_sort_link('name', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'name' ? 'active-sort' : ''; ?>">
                                    Nama & Kontak
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'name' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-center">
                                <a href="<?php echo get_sort_link('order_count', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'order_count' ? 'active-sort' : ''; ?>">
                                    Jml. Order
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'order_count' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('total_spent', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'total_spent' ? 'active-sort' : ''; ?>">
                                    Total Belanja
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'total_spent' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('registration_date', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'registration_date' ? 'active-sort' : ''; ?>">
                                    Tgl. Daftar
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'registration_date' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <span class="fw-bold"><?php echo htmlspecialchars($u['full_name']); ?></span>
                                <small class="d-block text-muted"><i
                                        class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($u['email']); ?></small>
                                <?php if (!empty($u['phone'])): ?>
                                <small class="d-block text-muted"><i
                                        class="fas fa-phone me-1"></i><?php echo htmlspecialchars($u['phone_number']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-pink-light fw-bold">
                                    <?php echo number_format($u['order_count'], 0); ?>
                                </span>
                            </td>
                            <td>
                                <span
                                    class="fw-bold text-success">Rp<?php echo number_format($u['total_spent'], 0, ',', '.'); ?></span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($u['registration_date'])); ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1 btn-detail-customer"
                                    data-bs-toggle="modal" data-bs-target="#detailCustomerModal"
                                    data-user-id="<?php echo $u['id']; ?>">
                                    <i class="fas fa-user-circle me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data pelanggan ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?php echo count($users); ?> dari <?php echo $total_rows; ?> total
                    pelanggan.</small>

                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($total_pages > 1): ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>&s=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php 
                            // Tampilkan maksimal 5 link halaman di sekitar halaman saat ini
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            for ($i = $start; $i <= $end; $i++): 
                                $is_active = $i == $page ? 'active' : '';
                                $page_link = "?page=$i&limit=$limit&s=$search&sort=$sort&order=$order";
                            ?>
                        <li class="page-item <?php echo $is_active; ?>">
                            <a class="page-link" href="<?php echo $page_link; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>&s=<?php echo $search; ?>&sort=<?php echo $sort; ?>&order=<?php echo $order; ?>">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

    </div>

    <div class="modal fade" id="detailCustomerModal" tabindex="-1" aria-labelledby="detailCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailCustomerModalLabel">Detail Pelanggan: <span
                            id="customer_name_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal_content_loading" class="text-center p-5">
                        <div class="spinner-border text-pink-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat detail pelanggan...</p>
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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // ====================================================
    // Logic Dark/Light Mode (FIXED - Diambil dari file sebelumnya)
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
    // Logic Modal Detail Pelanggan (Menggunakan AJAX)
    // ====================================================

    $('#detailCustomerModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const userId = button.data('user-id');
        const modal = $(this);

        // Tampilkan loading, sembunyikan detail
        modal.find('#modal_content_loading').show();
        modal.find('#modal_content_detail').hide().empty();
        modal.find('#customer_name_title').text('...'); // Placeholder

        // Lakukan permintaan AJAX
        $.ajax({
            url: 'proses/get_manajemen-pelanggan.php', // File AJAX baru yang akan kita buat
            method: 'GET',
            data: {
                id: userId
            },
            dataType: 'json',
            success: function(response) {
                modal.find('#modal_content_loading').hide();

                if (response.success) {
                    const user = response.data.user;
                    const orders = response.data.orders;

                    modal.find('#customer_name_title').text(user.full_name);

                    // --- Susun HTML Detail Pelanggan ---
                    let html = `
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-pink-primary"><i class="fas fa-id-badge me-2"></i> Info Akun</h6>
                                    <p class="mb-1"><strong>ID Pelanggan:</strong> #${user.id}</p>
                                    <p class="mb-1"><strong>Tgl. Daftar:</strong> ${user.registration_date_formatted}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-pink-primary"><i class="fas fa-address-card me-2"></i> Kontak</h6>
                                    <p class="mb-1"><strong>Email:</strong> ${user.email}</p>
                                    <p class="mb-1"><strong>Telepon:</strong> ${user.phone_number || '-'}</p>
                                </div>
                            </div>
                            
                            <hr class="my-3 detail-divider">
                            
                            <h6 class="text-pink-primary"><i class="fas fa-chart-line me-2"></i> Statistik Belanja</h6>
                            <div class="row text-center mb-4">
                                <div class="col-4">
                                    <div class="p-3 border rounded">
                                        <p class="text-muted small mb-0">Total Order</p>
                                        <h5 class="fw-bold text-pink-primary">${user.order_count}</h5>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 border rounded">
                                        <p class="text-muted small mb-0">Total Revenue</p>
                                        <h5 class="fw-bold text-success">Rp${new Intl.NumberFormat('id-ID').format(user.total_spent)}</h5>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 border rounded">
                                        <p class="text-muted small mb-0">Rata-Rata Order</p>
                                        <h5 class="fw-bold">${user.avg_spent}</h5>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-pink-primary"><i class="fas fa-history me-2"></i> Riwayat 5 Pesanan Terakhir</h6>
                        `;

                    if (orders.length > 0) {
                        html += `
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Kode Pesanan</th>
                                                <th>Tanggal</th>
                                                <th class="text-end">Total Akhir</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${orders.map(order => `
                                                <tr>
                                                    <td class="fw-bold">${order.order_code}</td>
                                                    <td>${order.order_date_formatted}</td>
                                                    <td class="text-end">Rp${new Intl.NumberFormat('id-ID').format(order.final_amount)}</td>
                                                    <td><span class="badge bg-${order.status_color}">${order.order_status}</span></td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            `;
                    } else {
                        html +=
                            `<p class="alert alert-info small">Pelanggan ini belum memiliki riwayat pesanan.</p>`;
                    }

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
    </script>
</body>

</html>