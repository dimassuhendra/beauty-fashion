<?php
require_once '../db_connect.php';
include 'proses/get_manajemen-promo.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo & Kupon | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Kelola Promo & Kupon Diskon</h1>
            <p class="lead">Buat, atur, dan pantau efektivitas kode promosi Anda.</p>
        </header>

        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm promo-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-ticket-alt fa-3x text-pink-primary me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Kupon Dibuat</p>
                                <h4 class="card-title mb-0"><?php echo number_format($total_coupons, 0, ',', '.'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm promo-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-3x text-success me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Kupon Aktif Saat Ini</p>
                                <h4 class="card-title mb-0"><?php echo number_format($active_coupons, 0, ',', '.'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm promo-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history fa-3x text-warning me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Kupon Kedaluwarsa</p>
                                <h4 class="card-title mb-0"><?php echo number_format($expired_coupons, 0, ',', '.'); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card shadow-sm promo-summary-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shopping-basket fa-3x text-info me-3"></i>
                            <div>
                                <p class="card-text small text-muted mb-0">Total Digunakan</p>
                                <h4 class="card-title mb-0"><?php echo number_format($total_used, 0, ',', '.'); ?>x</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h5 class="mb-0 card-title-dark-mode">Daftar Kode Promo</h5>

                <button class="btn btn-pink-outline btn-tambah-promo" data-bs-toggle="modal"
                    data-bs-target="#promoModal">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Promo Baru
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped promo-table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 10%;">Kode</th>
                            <th scope="col" style="width: 15%;">Tipe Diskon</th>
                            <th scope="col" style="width: 25%;">Periode Berlaku</th>
                            <th scope="col" style="width: 15%;">Min. Belanja</th>
                            <th scope="col" style="width: 10%;" class="text-center">Sisa Kuota</th>
                            <th scope="col" style="width: 10%;" class="text-center">Status</th>
                            <th scope="col" style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($coupons) > 0): ?>
                        <?php foreach ($coupons as $c): 
                                $status_class = match ($c['status_label']) {
                                    'Aktif' => 'success',
                                    'Nonaktif' => 'secondary',
                                    'Kedaluwarsa' => 'danger',
                                    'Belum Aktif' => 'info',
                                    default => 'secondary',
                                };
                                $discount_display = $c['discount_type'] === 'percentage' 
                                                    ? $c['discount_value'] . '%' 
                                                    : 'Rp' . number_format($c['discount_value'], 0, ',', '.');
                            ?>
                        <tr>
                            <td><span
                                    class="fw-bold promo-code-text"><?php echo htmlspecialchars($c['coupon_code']); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-pink-primary border border-pink-primary">
                                    <?php echo ucfirst(str_replace('_', ' ', $c['discount_type'])); ?>:
                                </span>
                                <span class="fw-bold"><?php echo $discount_display; ?></span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt me-1 text-muted"></i>
                                <?php echo date('d M y', strtotime($c['valid_from'])); ?> s/d
                                <?php echo date('d M y', strtotime($c['valid_until'])); ?>
                            </td>
                            <td>Rp<?php echo number_format($c['minimum_purchase'] ?? 0, 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php 
                                            $remaining = ($c['usage_limit'] ?? 0) - ($c['used_count'] ?? 0);
                                            echo $c['usage_limit'] === null ? 'Unlimited' : $remaining . 'x';
                                        ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo $status_class; ?> status-badge-promo">
                                    <?php echo $c['status_label']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1 btn-edit-promo" data-bs-toggle="modal"
                                    data-bs-target="#promoModal" data-coupon-id="<?php echo $c['id']; ?>"
                                    data-json='<?php echo json_encode($c); ?>'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-<?php echo $c['is_active'] ? 'danger' : 'success'; ?> btn-toggle-status"
                                    data-id="<?php echo $c['id']; ?>"
                                    data-status="<?php echo $c['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data kode promo yang dibuat.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="promoForm" method="POST" action="proses/get_manajemen-promo.php">
                    <div class="modal-header bg-pink-primary text-white">
                        <h5 class="modal-title" id="promoModalLabel">Tambah Kupon Diskon Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="add">
                        <input type="hidden" name="id" id="couponId">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="coupon_code" class="form-label">Kode Promo <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="coupon_code" name="coupon_code" required
                                    maxlength="20" placeholder="Mis: SALEAKHIRTAHUN">
                            </div>
                            <div class="col-md-6">
                                <label for="discount_type" class="form-label">Tipe Diskon <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="discount_type" name="discount_type" required>
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed_amount">Jumlah Tetap (Rp)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="discount_value" class="form-label">Nilai Diskon <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="discount_prefix">%</span>
                                    <input type="number" class="form-control" id="discount_value" name="discount_value"
                                        required min="1" max="100">
                                </div>
                                <div class="form-text">Untuk persentase, maksimal 100%.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="minimum_purchase" class="form-label">Min. Belanja (Syarat)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="minimum_purchase"
                                        name="minimum_purchase" min="0" value="0">
                                </div>
                                <div class="form-text">Total belanja minimum sebelum diskon diterapkan.</div>
                            </div>

                            <hr class="mt-4 mb-3 detail-divider">

                            <div class="col-md-6">
                                <label for="valid_from" class="form-label">Tanggal Mulai Berlaku <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="valid_from" name="valid_from" required>
                            </div>
                            <div class="col-md-6">
                                <label for="valid_until" class="form-label">Tanggal Akhir Berlaku <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="valid_until" name="valid_until" required>
                            </div>

                            <div class="col-md-6">
                                <label for="usage_limit" class="form-label">Batas Penggunaan (Total Kuota)</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" min="0"
                                    placeholder="0 = Tak Terbatas">
                                <div class="form-text">Jumlah maksimum kupon ini dapat digunakan di seluruh transaksi.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status Awal <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="is_active" name="is_active" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif (Draft)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-pink" id="submitButton">Simpan Promo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="proses/get_manajemen-promo.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="confirmAction" value="">
                        <input type="hidden" name="id" id="confirmId">
                        <p id="confirmationMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="confirmSubmit">Ya, Lakukan</button>
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
    // Logic Modal Promo (Tambah/Edit)
    // ====================================================

    const $promoModal = $('#promoModal');
    const $formAction = $('#formAction');
    const $couponId = $('#couponId');
    const $modalLabel = $('#promoModalLabel');
    const $discountPrefix = $('#discount_prefix');
    const $discountValue = $('#discount_value');
    const $discountType = $('#discount_type');

    // Fungsi Reset Form
    function resetForm() {
        $('#promoForm')[0].reset();
        $formAction.val('add');
        $couponId.val('');
        $modalLabel.text('Tambah Kupon Diskon Baru');
        $('#submitButton').text('Simpan Promo').removeClass('btn-warning').addClass('btn-pink');
        $('#coupon_code').prop('disabled', false);
        // Panggil fungsi perubahan tipe diskon untuk reset prefix
        updateDiscountPrefix();
    }

    // Update Prefix Input (%, Rp)
    function updateDiscountPrefix() {
        if ($discountType.val() === 'percentage') {
            $discountPrefix.text('%');
            $discountValue.attr('max', 100);
        } else {
            $discountPrefix.text('Rp');
            $discountValue.attr('max', 999999999); // Angka besar
        }
    }
    $discountType.on('change', updateDiscountPrefix);

    // Listener saat Modal dibuka
    $promoModal.on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);

        // Mode Tambah Baru
        if (button.hasClass('btn-tambah-promo')) {
            resetForm();
        }
        // Mode Edit
        else if (button.hasClass('btn-edit-promo')) {
            resetForm();
            const couponData = button.data('json');

            $formAction.val('edit');
            $couponId.val(couponData.id);
            $modalLabel.text('Edit Kupon Diskon: ' + couponData.coupon_code);
            $('#submitButton').text('Perbarui Promo').removeClass('btn-pink').addClass('btn-warning');

            // Isi Form dengan data kupon
            $('#coupon_code').val(couponData.coupon_code).prop('disabled', true); // Kode tidak bisa diubah
            $('#discount_type').val(couponData.discount_type);
            $('#discount_value').val(couponData.discount_value);
            $('#minimum_purchase').val(couponData.minimum_purchase);
            $('#valid_from').val(couponData.valid_from);
            $('#valid_until').val(couponData.valid_until);
            $('#usage_limit').val(couponData.usage_limit);
            $('#is_active').val(couponData.is_active);

            // Update prefix setelah mengisi tipe diskon
            updateDiscountPrefix();
        }
    });

    // ====================================================
    // Logic Konfirmasi (Delete & Toggle Status)
    // ====================================================
    const $confirmModal = $('#confirmationModal');
    const $confirmAction = $('#confirmAction');
    const $confirmId = $('#confirmId');
    const $confirmMessage = $('#confirmationMessage');
    const $confirmSubmit = $('#confirmSubmit');

    // Delete button listener
    $('.btn-toggle-status, .btn-delete-promo').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        if ($(this).hasClass('btn-toggle-status')) {
            // Toggle Status
            const newStatus = $(this).data('status');
            $confirmAction.val('toggle_status');
            $confirmMessage.html(`Yakin ingin **${newStatus}** kupon ini?`);
            $confirmSubmit.removeClass('btn-danger').addClass('btn-info').text(`Ya, ${newStatus}`);
        } else {
            // Delete
            $confirmAction.val('delete');
            $confirmMessage.html(
                'Anda yakin ingin **menghapus** promo ini secara permanen? Aksi ini tidak dapat dibatalkan.'
            );
            $confirmSubmit.removeClass('btn-info').addClass('btn-danger').text('Ya, Hapus');
        }

        $confirmId.val(id);
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        confirmationModal.show();
    });


    // ====================================================
    // Validasi Form
    // ====================================================
    $('#promoForm').on('submit', function(e) {
        const start = $('#valid_from').val();
        const end = $('#valid_until').val();
        const value = parseFloat($('#discount_value').val());
        const type = $('#discount_type').val();

        if (new Date(start) > new Date(end)) {
            e.preventDefault();
            alert('Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir.');
            return;
        }

        if (type === 'percentage' && value > 100) {
            e.preventDefault();
            alert('Nilai diskon persentase maksimal 100.');
            return;
        }

        // Lanjutkan submit (PHP logic akan menangani INSERT/UPDATE)
    });
    </script>
</body>

</html>