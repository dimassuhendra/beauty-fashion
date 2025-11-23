<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Menggunakan file koneksi database
include '../db_connect.php';
include 'proses/get_manajemen-diskon.php';
include 'proses/proses_manajemen-diskon.php';

// Fungsi format_date_indo (asumsi ada di salah satu file yang di-include)
if (!function_exists('format_date_indo')) {
    function format_date_indo($date_str)
    {
        // Implementasi sederhana: mengubah YYYY-MM-DD menjadi DD Bulan YYYY
        $months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];
        $date = new DateTime($date_str);
        return $date->format('d') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y');
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Diskon | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Kupon Diskon</h1>
            <p class="lead">Kelola dan atur kode kupon untuk promosi penjualan.</p>
            <?php
            // Menampilkan pesan sukses/error dari proses CRUD
            if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php
                unset($_SESSION['message']);
                unset($_SESSION['msg_type']);
            endif;
            ?>
        </header>

        <div class="card shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title-dark-mode mb-0">Daftar Kupon Diskon</h5>
                <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#addEditCouponModal"
                    data-action="add">
                    <i class="fas fa-plus me-1"></i> Tambah Kupon
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 15%;">Kode Kupon</th>
                            <th scope="col">Tipe Diskon</th>
                            <th scope="col">Nilai Diskon</th>
                            <th scope="col">Min. Beli (Rp)</th>
                            <th scope="col">Masa Berlaku</th>
                            <th scope="col">Batasan & Digunakan</th>
                            <th scope="col">Status</th>
                            <th scope="col" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_coupons->num_rows > 0): ?>
                            <?php $no = 1;
                            while ($coupon = $result_coupons->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td class="fw-bold text-pink-primary">
                                        <?php echo htmlspecialchars($coupon['coupon_code']); ?></td>
                                    <td>
                                        <?php
                                        $type = $coupon['discount_type'];
                                        echo $type === 'fixed' ? 'Nominal Tetap (Rp)' : 'Persentase (%)';
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                        if ($type === 'fixed') {
                                            echo 'Rp' . number_format($coupon['discount_value'], 0, ',', '.');
                                        } else {
                                            echo number_format($coupon['discount_value'], 0) . '%';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        Rp<?php echo number_format($coupon['minimum_purchase'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-center small">
                                        <?php
                                        echo format_date_indo($coupon['valid_from']) . ' - ' . format_date_indo($coupon['valid_until']);
                                        ?>
                                    </td>
                                    <td class="text-center small">
                                        <?php echo $coupon['used_count']; ?>x /
                                        <?php echo $coupon['usage_limit'] ?? 'Tidak Terbatas'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $is_active = $coupon['is_active'];
                                        $badge_class = $is_active ? 'bg-success' : 'bg-danger';
                                        $status_text = $is_active ? 'Aktif' : 'Nonaktif';
                                        echo "<span class='badge $badge_class'>$status_text</span>";
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                            data-bs-target="#addEditCouponModal" data-action="edit"
                                            data-id="<?php echo $coupon['id']; ?>"
                                            data-code="<?php echo htmlspecialchars($coupon['coupon_code']); ?>"
                                            data-type="<?php echo $coupon['discount_type']; ?>"
                                            data-value="<?php echo $coupon['discount_value']; ?>"
                                            data-min-purchase="<?php echo $coupon['minimum_purchase']; ?>"
                                            data-from="<?php echo $coupon['valid_from']; ?>"
                                            data-until="<?php echo $coupon['valid_until']; ?>"
                                            data-limit="<?php echo $coupon['usage_limit']; ?>"
                                            data-active="<?php echo $coupon['is_active']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="manajemen-diskon.php?toggle_status=<?php echo $coupon['id']; ?>&current_status=<?php echo $coupon['is_active']; ?>"
                                            class="btn btn-sm btn-outline-<?php echo $is_active ? 'danger' : 'success'; ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin <?php echo $is_active ? 'menonaktifkan' : 'mengaktifkan'; ?> kupon ini?');">
                                            <i class="fas fa-toggle-<?php echo $is_active ? 'on' : 'off'; ?>"></i>
                                        </a>
                                        <a href="manajemen-diskon.php?delete_id=<?php echo $coupon['id']; ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kupon <?php echo htmlspecialchars($coupon['coupon_code']); ?>? Aksi ini tidak dapat dibatalkan.');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada kupon diskon yang ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addEditCouponModal" tabindex="-1" aria-labelledby="addEditCouponModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="manajemen-diskon.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEditCouponModalLabel">Tambah Kupon Diskon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="coupon_id" id="coupon_id">

                        <div class="mb-3">
                            <label for="coupon_code" class="form-label">Kode Kupon <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" required
                                maxlength="50" placeholder="Contoh: BIGSALE20">
                        </div>

                        <div class="mb-3">
                            <label for="discount_type" class="form-label">Tipe Diskon <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="discount_type" name="discount_type" required>
                                <option value="percent">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="discount_value" class="form-label">Nilai Diskon <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="discount_value" name="discount_value" required
                                min="1">
                            <small class="form-text text-muted" id="discount_value_help">Masukkan nilai diskon (e.g., 10
                                untuk 10% atau 10000 untuk Rp10.000).</small>
                        </div>

                        <div class="mb-3">
                            <label for="minimum_purchase" class="form-label">Minimum Pembelian (Rp)</label>
                            <input type="number" class="form-control" id="minimum_purchase" name="minimum_purchase"
                                value="0.00" min="0">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="valid_from" class="form-label">Berlaku Dari <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="valid_from" name="valid_from" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="valid_until" class="form-label">Berlaku Sampai <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="valid_until" name="valid_until" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="usage_limit" class="form-label">Batasan Penggunaan (Total)</label>
                            <input type="number" class="form-control" id="usage_limit" name="usage_limit"
                                placeholder="Kosongkan untuk tanpa batas" min="1">
                            <small class="form-text text-muted">Berapa kali kupon ini bisa digunakan secara
                                total.</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                                name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Aktifkan Kupon</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="add_coupon" id="modal_submit_btn"
                            class="btn btn-pink">Simpan Kupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ====================================================
        // Logic Dark/Light Mode (Dari file sebelumnya)
        // ====================================================
        const body = document.getElementById('body-admin');
        // Asumsi modeToggle ada di sidebar.php
        // const modeToggle = document.getElementById('mode-toggle'); 

        function applyMode(isInitialLoad = true) {
            let savedMode = localStorage.getItem('theme') || 'light';

            // Tidak perlu toggle logic di sini karena logic toggle ada di sidebar.php

            if (savedMode === 'dark') {
                body.classList.add('dark-mode');
            } else {
                body.classList.remove('dark-mode');
            }

            // Terapkan styling ke komponen modal
            document.querySelectorAll('.modal-content').forEach(el => {
                if (savedMode === 'dark') {
                    el.classList.add('dark-mode');
                } else {
                    el.classList.remove('dark-mode');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyMode(true);
        });

        // ====================================================
        // Logic Modal Edit dan Update Info Diskon
        // ====================================================
        const addEditCouponModal = document.getElementById('addEditCouponModal');
        addEditCouponModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang memicu modal
            const action = button.getAttribute('data-action');

            const modalTitle = addEditCouponModal.querySelector('.modal-title');
            const submitButton = addEditCouponModal.querySelector('#modal_submit_btn');
            const form = addEditCouponModal.querySelector('form');
            const typeSelect = addEditCouponModal.querySelector('#discount_type');
            const valueHelp = addEditCouponModal.querySelector('#discount_value_help');

            if (action === 'add') {
                modalTitle.textContent = 'Tambah Kupon Diskon Baru';
                submitButton.textContent = 'Simpan Kupon';
                submitButton.value = 'add_coupon';
                form.reset(); // Reset form untuk mode tambah
                document.getElementById('coupon_id').value = '';
            } else if (action === 'edit') {
                modalTitle.textContent = 'Edit Kupon Diskon';
                submitButton.textContent = 'Update Kupon';
                submitButton.value = 'edit_coupon';

                // Ambil data dari tombol
                const id = button.getAttribute('data-id');
                const code = button.getAttribute('data-code');
                const type = button.getAttribute('data-type');
                const value = button.getAttribute('data-value');
                const minPurchase = button.getAttribute('data-min-purchase');
                const fromDate = button.getAttribute('data-from');
                const untilDate = button.getAttribute('data-until');
                const limit = button.getAttribute('data-limit');
                const active = button.getAttribute('data-active');

                // Isi form
                document.getElementById('coupon_id').value = id;
                document.getElementById('coupon_code').value = code;
                document.getElementById('discount_type').value = type;
                document.getElementById('discount_value').value = value;
                document.getElementById('minimum_purchase').value = minPurchase;
                document.getElementById('valid_from').value = fromDate;
                document.getElementById('valid_until').value = untilDate;
                document.getElementById('usage_limit').value = limit === 'null' ? '' : limit;
                document.getElementById('is_active').checked = active === '1';

                updateDiscountHelp(type, valueHelp);
            }
        });

        // Fungsi untuk memperbarui teks bantuan berdasarkan tipe diskon
        function updateDiscountHelp(type, element) {
            if (type === 'percent') {
                element.textContent = 'Masukkan nilai persentase diskon (e.g., 10 untuk 10%).';
            } else {
                element.textContent = 'Masukkan nilai nominal diskon (e.g., 10000 untuk Rp10.000).';
            }
        }

        // Event listener untuk perubahan tipe diskon
        document.getElementById('discount_type').addEventListener('change', function () {
            const type = this.value;
            const valueHelp = document.getElementById('discount_value_help');
            updateDiscountHelp(type, valueHelp);
        });
    </script>
</body>

</html>