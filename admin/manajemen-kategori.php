<?php
// ====================================================================
// 1. SETUP KONEKSI DAN PENGATURAN DATA
// ====================================================================

// Menggunakan file koneksi database
include '../db_connect.php';

// Asumsikan ada file proses untuk mengambil data kategori
// File ini akan berisi logika SQL untuk SELECT, INSERT, UPDATE, DELETE kategori
include 'proses/get_manajemen-kategori.php';

// Variabel contoh untuk data kategori (nanti diisi dari database)
/*
$categories = [
    ['id' => 1, 'name' => 'Skincare', 'description' => 'Produk perawatan kulit', 'product_count' => 45],
    ['id' => 2, 'name' => 'Makeup', 'description' => 'Produk rias wajah', 'product_count' => 62],
    ['id' => 3, 'name' => 'Haircare', 'description' => 'Produk perawatan rambut', 'product_count' => 18],
];
*/

// Anda harus memastikan file get_manajemen-kategori.php mengisi variabel $categories
// dan menangani proses form (INSERT/UPDATE/DELETE)
$categories = $categories ?? []; // Fallback jika variabel belum terdefinisi
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori | Beauty Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Kategori</h1>
            <p class="lead">Kelola dan atur kategori produk Anda.</p>
        </header>

        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-pink" data-bs-toggle="modal" data-bs-target="#categoryModal"
                id="btn-add-category">
                <i class="fas fa-plus me-1"></i> Tambah Kategori Baru
            </button>
        </div>

        <div class="card shadow-sm p-4">
            <h5 class="mb-4 card-title-dark-mode">Daftar Kategori Produk</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th scope="col" style="width: 5%;">ID</th>
                            <th scope="col" style="width: 25%;">Nama Kategori</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" style="width: 15%;">Jumlah Produk</th>
                            <th scope="col" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="text-center"><?php echo $cat['id']; ?></td>
                                    <td class="fw-bold">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info">

                                            <?php echo number_format($cat['product_count'] ?? 0, 0); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning edit-btn"
                                            data-id="<?php echo $cat['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($cat['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($cat['description']); ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="proses/proses_manajemen-kategori.php?action=delete&id=<?php echo $cat['id']; ?>"
                                            class="btn btn-sm btn-danger delete-btn"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus kategori <?php echo htmlspecialchars($cat['name']); ?>?');">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada kategori yang
                                    ditambahkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm" action="proses/proses_manajemen-kategori.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category_id">

                        <div class="mb-3">
                            <label for="category_name" class="form-label">Nama Kategori
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_description" class="form-label">Deskripsi
                                Kategori</label>
                            <textarea class="form-control" id="category_description" name="category_description"
                                rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink" id="saveCategoryBtn">Simpan</button>
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

            // Terapkan styling ke komponen modal
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
        // Logic JavaScript untuk Manajemen Kategori
        // ====================================================
        $(document).ready(function () {
            // 1. Saat tombol 'Tambah Kategori Baru' diklik
            $('#btn-add-category').on('click', function () {
                $('#categoryModalLabel').text('Tambah Kategori Baru');
                $('#categoryForm')[0].reset(); // Kosongkan formulir
                $('#category_id').val(''); // Pastikan ID kosong (untuk mode tambah)
                $('#saveCategoryBtn').text('Simpan');
            });

            // 2. Saat tombol 'Edit' diklik
            $('.edit-btn').on('click', function () {
                // Ambil data dari atribut data-
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                // Isi formulir modal dengan data kategori
                $('#categoryModalLabel').text('Edit Kategori: ' + name);
                $('#category_id').val(id);
                $('#category_name').val(name);
                $('#category_description').val(description);
                $('#saveCategoryBtn').text('Update');

                // Tampilkan modal
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            });
        });
    </script>
</body>

</html>