<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Mulai session untuk menangani notifikasi
session_start();

// Menggunakan file koneksi database
include '../db_connect.php';
// File untuk mengambil data pengaturan
include 'proses/get_pengaturan.php';

// Cek notifikasi dari session
$notification = $_SESSION['notification'] ?? null;
unset($_SESSION['notification']); // Hapus notifikasi setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Situs | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Pengaturan Situs</h1>
            <p class="lead">Kelola informasi dasar dan konfigurasi utama website.</p>
        </header>

        <?php if ($notification): ?>
            <div class="alert alert-<?php echo $notification['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $notification['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm p-4">
            <h5 class="mb-4 card-title-dark-mode"><i class="fas fa-cogs me-2"></i> Pengaturan Umum</h5>
            <form action="proses/proses_pengaturan.php" method="POST">

                <div class="mb-3">
                    <label for="site_name" class="form-label">Nama Situs/Toko</label>
                    <input type="text" class="form-control" id="site_name" name="site_name"
                        value="<?php echo htmlspecialchars($site_name); ?>" required>
                    <div class="form-text">Nama ini akan muncul di judul halaman, footer, dan email.</div>
                </div>

                <div class="mb-3">
                    <label for="site_tagline" class="form-label">Slogan (Tagline)</label>
                    <input type="text" class="form-control" id="site_tagline" name="site_tagline"
                        value="<?php echo htmlspecialchars($site_tagline); ?>">
                    <div class="form-text">Deskripsi singkat toko Anda.</div>
                </div>

                <div class="mb-3">
                    <label for="email_contact" class="form-label">Email Kontak Utama</label>
                    <input type="email" class="form-control" id="email_contact" name="email_contact"
                        value="<?php echo htmlspecialchars($email_contact); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="phone_contact" class="form-label">Nomor Telepon Kontak</label>
                    <input type="text" class="form-control" id="phone_contact" name="phone_contact"
                        value="<?php echo htmlspecialchars($phone_contact); ?>">
                </div>

                <div class="mb-4">
                    <label for="shipping_cost" class="form-label">Biaya Pengiriman Default (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="shipping_cost" name="shipping_cost"
                            value="<?php echo htmlspecialchars($shipping_cost); ?>" min="0" required>
                    </div>
                    <div class="form-text">Biaya pengiriman standar jika tidak ada perhitungan khusus.</div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-pink-primary"><i class="fas fa-save me-2"></i> Simpan
                        Pengaturan</button>
                </div>
            </form>
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

            // Terapkan styling ke komponen modal (jika ada)
            document.querySelectorAll('.modal-content').forEach(el => {
                if (savedMode === 'dark') {
                    el.classList.add('dark-mode');
                } else {
                    el.classList.remove('dark-mode');
                }
            });

            if (modeToggle) {
                if (savedMode === 'dark') {
                    // Asumsi #mode-toggle berada di sidebar
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
    </script>
</body>

</html>