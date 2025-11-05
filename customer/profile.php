<?php
// ====================================================================
// BAGIAN 1: LOGIKA PHP & KONEKSI DATABASE
// ====================================================================
session_start();

// Asumsikan file koneksi ada di direktori sebelumnya
include '../db_connect.php'; 
include 'proses/get_profile.php'; 
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-hero">
        <div class="container">
            <h1><i class="fas fa-user-circle me-2"></i> Pengaturan Akun</h1>
            <p class="lead">Kelola informasi pribadi, alamat, dan keamanan akun Anda.</p>
        </div>
    </div>

    <main class="container mb-5">
        <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars(urldecode($message)); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars(urldecode($error)); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-3">
                <div class="nav flex-column nav-pills me-3 profile-card" id="v-pills-tab" role="tablist"
                    aria-orientation="vertical">
                    <button class="nav-link profile-tab <?= $currentTab == 'profile' ? 'active' : ''; ?>"
                        id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile-info" type="button" role="tab"
                        aria-controls="profile-info" aria-selected="<?= $currentTab == 'profile' ? 'true' : 'false'; ?>"
                        onclick="changeTab('profile')">
                        <i class="fas fa-id-card-alt me-2"></i> Info Akun
                    </button>
                    <button class="nav-link profile-tab <?= $currentTab == 'address' ? 'active' : ''; ?>"
                        id="address-tab" data-bs-toggle="pill" data-bs-target="#profile-address" type="button"
                        role="tab" aria-controls="profile-address"
                        aria-selected="<?= $currentTab == 'address' ? 'true' : 'false'; ?>"
                        onclick="changeTab('address')">
                        <i class="fas fa-map-marked-alt me-2"></i> Alamat Pengiriman
                    </button>
                    <button class="nav-link profile-tab <?= $currentTab == 'security' ? 'active' : ''; ?>"
                        id="security-tab" data-bs-toggle="pill" data-bs-target="#profile-security" type="button"
                        role="tab" aria-controls="profile-security"
                        aria-selected="<?= $currentTab == 'security' ? 'true' : 'false'; ?>"
                        onclick="changeTab('security')">
                        <i class="fas fa-lock me-2"></i> Keamanan
                    </button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="tab-content profile-card" id="v-pills-tabContent">

                    <div class="tab-pane fade <?= $currentTab == 'profile' ? 'show active' : ''; ?>" id="profile-info"
                        role="tabpanel" aria-labelledby="profile-tab">
                        <h4 class="mb-4 text-pink"><i class="fas fa-id-card-alt me-2"></i>
                            Informasi Pribadi</h4>

                        <div class="card p-4 mb-4 border-0 shadow-sm">
                            <h5 class="card-title text-primary-pink fw-bold">Detail Akun
                                Saat Ini</h5>

                            <hr>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="mb-0 text-muted">Nama Lengkap</p>
                                    <p class="fw-bold fs-5">
                                        <?= htmlspecialchars($user['full_name'] ?? '-'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-0 text-muted">Nomor Telepon</p>
                                    <p class="fw-bold fs-5">
                                        <?= htmlspecialchars($user['phone_number'] ?? '-'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-0 text-muted">Email</p>
                                    <p class="fw-bold fs-5">
                                        <?= htmlspecialchars($user['email'] ?? '-'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-0 text-muted">Akun Dibuat</p>
                                    <p class="fw-bold fs-5">
                                        <?= date('d M Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                                </div>
                            </div>
                            <button class="btn btn-outline-pink mt-2" type="button" data-bs-toggle="collapse"
                                data-bs-target="#editProfileForm" aria-expanded="false" aria-controls="editProfileForm">
                                <i class="fas fa-edit me-2"></i> Ubah Informasi Pribadi
                            </button>

                        </div>

                        <div class="collapse" id="editProfileForm">
                            <div class="card p-4 border-0 shadow-sm mt-4">
                                <h5 class="card-title text-pink fw-bold">Formulir Edit
                                    Data</h5>
                                <p class="text-muted">Isi kolom di bawah untuk
                                    memperbarui data Anda.</p>

                                <hr>
                                <form method="POST" action="profile.php">
                                    <input type="hidden" name="action" value="update_profile">
                                    <input type="hidden" name="current_tab" value="profile">

                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                            value="<?= htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email (Tidak dapat diubah)</label>
                                        <input type="email" class="form-control" id="email"
                                            value="<?= htmlspecialchars($user['email'] ?? ''); ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Nomor Telepon</label>

                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            value="<?= htmlspecialchars($user['phone_number'] ?? ''); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-update-profile mt-3">Simpan Perubahan</button>
                                </form>

                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade <?= $currentTab == 'address' ? 'show active' : ''; ?>"
                        id="profile-address" role="tabpanel" aria-labelledby="address-tab">
                        <h4 class="mb-4 text-pink"><i class="fas fa-map-marked-alt me-2"></i>
                            Daftar Alamat Pengiriman
                        </h4>

                        <div class="row g-3 mb-4">
                            <?php if (!empty($addresses)): ?>
                            <?php foreach ($addresses as $addr): ?>
                            <div class="col-12">
                                <div class="card address-card p-3 <?= $addr['is_active'] ? 'active-address' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="fw-bold mb-1">

                                                <?= htmlspecialchars($addr['label']); ?>

                                                <?php if ($addr['is_active']): ?>
                                                <span class="badge badge-active ms-2">Utama</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-1">Penerima:

                                                **<?= htmlspecialchars($addr['recipient_name']); ?>**

                                                (<?= htmlspecialchars($addr['phone_number']); ?>)</p>
                                            <p class="text-muted mb-0">
                                                <?= htmlspecialchars($addr['full_address']); ?>,

                                                <?= htmlspecialchars($addr['city']); ?> -

                                                <?= htmlspecialchars($addr['postal_code']); ?></p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editAddressModal" data-id="<?= $addr['id']; ?>"
                                                data-label="<?= $addr['label']; ?>"
                                                data-recipient="<?= $addr['recipient_name']; ?>"
                                                data-phone="<?= $addr['phone_number']; ?>"
                                                data-address="<?= $addr['full_address']; ?>"
                                                data-city="<?= $addr['city']; ?>"
                                                data-postal="<?= $addr['postal_code']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>


                                            <?php if (!$addr['is_active']): ?>
                                            <form method="POST" action="profile.php"
                                                onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                                <input type="hidden" name="action" value="delete_address">
                                                <input type="hidden" name="address_id" value="<?= $addr['id']; ?>">
                                                <input type="hidden" name="current_tab" value="address">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                                        class="fas fa-trash-alt"></i></button>
                                            </form>

                                            <form method="POST" action="profile.php">
                                                <input type="hidden" name="action" value="set_active_address">
                                                <input type="hidden" name="address_id" value="<?= $addr['id']; ?>">
                                                <input type="hidden" name="current_tab" value="address">
                                                <button type="submit" class="btn btn-sm btn-primary-pink">Pilih
                                                    Utama</button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="alert alert-info text-center">Belum ada alamat
                                tersimpan. Silakan tambahkan!
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($currentAddressCount < $maxAddresses): ?>
                        <button type="button" class="btn btn-outline-pink mt-3" data-bs-toggle="modal"
                            data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i> Tambah Alamat Baru
                            (<?= $currentAddressCount; ?>/<?= $maxAddresses; ?>)
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-secondary mt-3" disabled>
                            Maksimal <?= $maxAddresses; ?> Alamat telah tercapai
                        </button>
                        <?php endif; ?>

                    </div>

                    <div class="tab-pane fade <?= $currentTab == 'security' ? 'show active' : ''; ?>"
                        id="profile-security" role="tabpanel" aria-labelledby="security-tab">
                        <h4 class="mb-4 text-pink"><i class="fas fa-lock me-2"></i> Ganti Kata
                            Sandi</h4>
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="current_tab" value="security">

                            <div class="mb-3">
                                <label for="old_password" class="form-label">Kata Sandi
                                    Lama</label>
                                <input type="password" class="form-control" id="old_password" name="old_password"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Kata Sandi
                                    Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password"
                                    required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-update-profile mt-3">Ubah
                                Kata Sandi</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-pink-light">
                    <h5 class="modal-title" id="addAddressModalLabel">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="profile.php">
                    <input type="hidden" name="action" value="add_address">
                    <input type="hidden" name="current_tab" value="address">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_label" class="form-label">Label Alamat (ex:
                                Rumah, Kantor)</label>
                            <input type="text" class="form-control" id="new_label" name="label" required maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="new_recipient_name" class="form-label">Nama
                                Penerima</label>
                            <input type="text" class="form-control" id="new_recipient_name" name="recipient_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="new_phone_number" class="form-label">Nomor Telepon
                                Penerima</label>
                            <input type="tel" class="form-control" id="new_phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_city" class="form-label">Kota/Kabupaten</label>
                            <input type="text" class="form-control" id="new_city" name="city" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="new_postal_code" name="postal_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_full_address" class="form-label">Alamat
                                Lengkap</label>
                            <textarea class="form-control" id="new_full_address" name="full_address" rows="3"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-pink">Simpan
                            Alamat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-pink-light">
                    <h5 class="modal-title" id="editAddressModalLabel">Edit Alamat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="profile.php">
                    <input type="hidden" name="action" value="edit_address">
                    <input type="hidden" name="current_tab" value="address">
                    <input type="hidden" name="address_id" id="edit_address_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_label" class="form-label">Label Alamat</label>
                            <input type="text" class="form-control" id="edit_label" name="label" required
                                maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label for="edit_recipient_name" class="form-label">Nama
                                Penerima</label>
                            <input type="text" class="form-control" id="edit_recipient_name" name="recipient_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_phone_number" class="form-label">Nomor Telepon
                                Penerima</label>
                            <input type="tel" class="form-control" id="edit_phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_city" class="form-label">Kota/Kabupaten</label>
                            <input type="text" class="form-control" id="edit_city" name="city" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_postal_code" class="form-label">Kode
                                Pos</label>
                            <input type="text" class="form-control" id="edit_postal_code" name="postal_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_full_address" class="form-label">Alamat
                                Lengkap</label>
                            <textarea class="form-control" id="edit_full_address" name="full_address" rows="3"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-pink">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <?php include '../footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Fungsi untuk mengaktifkan tab yang benar saat halaman dimuat atau setelah POST
    document.addEventListener('DOMContentLoaded', function() {
        const currentTabId = '<?= $currentTab; ?>-tab';
        const tabElement = document.getElementById(currentTabId);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    });

    // Fungsi untuk menjaga parameter tab di URL saat berpindah tab
    function changeTab(tabName) {
        history.pushState(null, '', `profile.php?tab=${tabName}`);
    }

    // JS untuk mengisi data ke Modal Edit Alamat saat tombol Edit ditekan
    document.getElementById('editAddressModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const modal = this;

        modal.querySelector('#edit_address_id').value = button.getAttribute('data-id');
        modal.querySelector('#edit_label').value = button.getAttribute('data-label');
        modal.querySelector('#edit_recipient_name').value = button.getAttribute('data-recipient');
        modal.querySelector('#edit_phone_number').value = button.getAttribute('data-phone');
        modal.querySelector('#edit_full_address').value = button.getAttribute('data-address');
        modal.querySelector('#edit_city').value = button.getAttribute('data-city');
        modal.querySelector('#edit_postal_code').value = button.getAttribute('data-postal');
    });
    </script>
</body>

</html>