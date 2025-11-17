<?php
// manajemen-komplain.php
// Sesuaikan dengan struktur include Anda
include '../db_connect.php';
include 'proses/get_manajemen-komplain.php'; // Ganti ke file logika komplain
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Komplain | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .status-badge {
            font-weight: bold;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }
    </style>
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Komplain</h1>
            <p class="lead">Kelola dan tanggapi komplain/masukan dari pelanggan.</p>
        </header>

        <?php 
        $alert_message = '';
        $alert_type = '';

        if (!empty($message)) {
            $alert_message = $message;
            $alert_type = 'success';
        } elseif (!empty($error)) {
            $alert_message = $error;
            $alert_type = 'danger';
        }
        ?>

        <?php if (!empty($alert_message)): ?>
        <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $alert_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <?php if ($open_complaints_count > 0): ?>
                <span class="badge bg-danger text-white mb-2 mb-md-0 fs-6 p-2">
                    <i class="fas fa-headset me-1"></i> **<?= $open_complaints_count; ?>** Komplain Baru
                </span>
                <?php else: ?>
                <span class="badge bg-success text-white mb-2 mb-md-0 fs-6 p-2">
                    <i class="fas fa-check-circle me-1"></i> Semua Komplain Teratasi
                </span>
                <?php endif; ?>

                <form method="GET" class="d-flex flex-wrap align-items-center gap-3">
                    <div class="input-group input-group-sm" style="width: auto;">
                        <label class="input-group-text" for="status">Filter Status</label>
                        <select name="status" id="status" class="form-select form-select-sm"
                            onchange="this.form.submit()">
                            <option value="all" <?= $filter_status == 'all' ? 'selected' : ''; ?>>Semua</option>
                            <?php foreach ($valid_statuses as $status_option): ?>
                            <option value="<?= $status_option; ?>"
                                <?= $filter_status == $status_option ? 'selected' : ''; ?>>
                                <?= $status_option; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari Subjek/Pelanggan/Pesanan..."
                            value="<?php echo htmlspecialchars($search_query); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search_query) || $filter_status != 'all'): ?>
                        <a href="manajemen-komplain.php" class="btn btn-outline-danger" title="Reset Filter"><i
                                class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Subjek</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Pesanan</th>
                            <th scope="col">Diajukan</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($complaints) > 0): ?>
                        <?php foreach ($complaints as $complaint): 
                            $status_badge_class = '';
                            switch ($complaint['status']) {
                                case 'Open': $status_badge_class = 'bg-danger'; break;
                                case 'In Progress': $status_badge_class = 'bg-warning text-dark'; break;
                                case 'Resolved': $status_badge_class = 'bg-info text-white'; break; 
                                case 'Closed': $status_badge_class = 'bg-secondary'; break;
                            }
                        ?>
                        <tr>
                            <td><?php echo $complaint['id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['order_code'] ?? '-'); ?></td>
                            <td><?php echo date('d M Y', strtotime($complaint['created_at'])); ?></td>
                            <td>
                                <span class="badge status-badge <?= $status_badge_class; ?>">
                                    <?= $complaint['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info text-white view-detail-btn" data-bs-toggle="modal"
                                    data-bs-target="#detailComplaintModal" data-id="<?= $complaint['id']; ?>"
                                    data-subject="<?= htmlspecialchars($complaint['subject']); ?>"
                                    data-customer="<?= htmlspecialchars($complaint['customer_name'] . ' (' . $complaint['customer_email'] . ')'); ?>"
                                    data-order-code="<?= htmlspecialchars($complaint['order_code'] ?? '-'); ?>"
                                    data-desc="<?= htmlspecialchars($complaint['description']); ?>"
                                    data-current-status="<?= htmlspecialchars($complaint['status']); ?>"
                                    data-admin-response="<?= htmlspecialchars($complaint['admin_response'] ?? ''); ?>">
                                    <i class="fas fa-eye me-1"></i> Kelola
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data komplain yang ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?php echo count($complaints); ?> dari
                    <?php echo $total_results; ?>
                    total komplain.</small>

                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?php if ($total_pages > 1): ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?p=<?php echo $page - 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_query); ?>">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            for ($i = $start_page; $i <= $end_page; $i++): 
                                $is_active = $i == $page ? 'active' : '';
                                $page_link = "?p=$i&status=$filter_status&search=" . urlencode($search_query);
                            ?>
                        <li class="page-item <?php echo $is_active; ?>">
                            <a class="page-link" href="<?php echo $page_link; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link"
                                href="?p=<?php echo $page + 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_query); ?>">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailComplaintModal" tabindex="-1" aria-labelledby="detailComplaintModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="detailComplaintModalLabel">Kelola Komplain #<span
                            id="modal-complaint-id-title"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="manajemen-komplain.php">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="complaint_id" id="modal-complaint-id">
                    <input type="hidden" name="current_tab" value="<?= $filter_status; ?>">

                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Subjek:</strong> <span id="modal-subject"></span></p>
                                <p><strong>Pelanggan:</strong> <span id="modal-customer"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Kode Pesanan:</strong> <span id="modal-order-code"></span></p>
                                <p><strong>Status Saat Ini:</strong> <span id="modal-current-status"
                                        class="badge status-badge"></span></p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6>Deskripsi Komplain Pelanggan:</h6>
                            <div id="modal-description" class="p-3 border rounded bg-light text-break"></div>
                        </div>

                        <h5 class="mt-4 mb-3 text-color"><i class="fas fa-reply me-1"></i> Tanggapan & Update Status
                            Admin</h5>
                        <div class="mb-3">
                            <label for="modal-admin-response" class="form-label">Tanggapan Admin (Tampil ke
                                Pelanggan):</label>
                            <textarea class="form-control" id="modal-admin-response" name="admin_response" rows="4"
                                placeholder="Tuliskan tanggapan atau tindakan yang sudah dilakukan."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="modal-new-status" class="form-label">Ubah Status Komplain:</label>
                            <select class="form-select" id="modal-new-status" name="new_status" required>
                                <?php foreach ($valid_statuses as $status_option): ?>
                                <option value="<?= $status_option; ?>"><?= $status_option; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink"><i class="fas fa-sync-alt me-1"></i> Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ====================================================
        // Logic Dark/Light Mode (Diambil dari template user)
        // ====================================================
        const body = document.getElementById('body-admin');
        const modeToggle = document.getElementById('mode-toggle'); // Asumsi ada di sidebar

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
        // Logic Modal Detail/Update Komplain
        // ====================================================

        const detailComplaintModal = document.getElementById('detailComplaintModal');

        detailComplaintModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            // Ambil data dari data-* atribut
            const id = button.getAttribute('data-id');
            const subject = button.getAttribute('data-subject');
            const customer = button.getAttribute('data-customer');
            const orderCode = button.getAttribute('data-order-code');
            const description = button.getAttribute('data-desc');
            const currentStatus = button.getAttribute('data-current-status');
            const adminResponse = button.getAttribute('data-admin-response');

            const modal = this;

            // --- 1. Isi ID di Judul dan Hidden Input ---
            modal.querySelector('#modal-complaint-id-title').textContent = id;
            modal.querySelector('#modal-complaint-id').value = id;

            // --- 2. Isi Detail Komplain ---
            modal.querySelector('#modal-subject').textContent = subject;
            modal.querySelector('#modal-customer').textContent = customer;
            modal.querySelector('#modal-order-code').textContent = orderCode;

            // --- 3. Ganti Status Badge ---
            const statusBadge = modal.querySelector('#modal-current-status');
            statusBadge.textContent = currentStatus;
            statusBadge.className = 'badge status-badge'; // Reset classes
            let statusClass = '';
            switch (currentStatus) {
                case 'Open':
                    statusClass = 'bg-danger';
                    break;
                case 'In Progress':
                    statusClass = 'bg-warning text-dark';
                    break;
                case 'Resolved':
                    statusClass = 'bg-info text-white';
                    break;
                case 'Closed':
                    statusClass = 'bg-secondary';
                    break;
            }
            statusBadge.classList.add(statusClass);

            // --- 4. Isi deskripsi dan konversi \n ke <br> ---
            // Gunakan innerHTML untuk menampilkan baris baru
            modal.querySelector('#modal-description').innerHTML = description.replace(/\n/g, '<br>');

            // --- 5. Isi form update ---
            modal.querySelector('#modal-admin-response').value = adminResponse;
            modal.querySelector('#modal-new-status').value = currentStatus;
        });
    </script>
</body>

</html>