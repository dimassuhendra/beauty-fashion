<?php
require_once '../db_connect.php'; 
require_once './proses/get_manajemen-produk.php'; 
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | Beauty Fashion Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
    <style>
    .product-image-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .btn-action-group .btn {
        padding: 0.375rem 0.5rem;
    }

    /* Agar DataTables menyesuaikan dengan tema Bootstrap 5 */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: var(--bs-pink-primary) !important;
        border-color: var(--bs-pink-primary) !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #f7a6cf !important;
        border-color: #f7a6cf !important;
    }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php';?>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <h2 class="text-pink-primary fw-bold mb-0">Manajemen Produk</h2>
                    <div class="d-flex">
                        <span class="navbar-text me-3 d-none d-sm-inline">
                            <?php echo date('l, d-m-Y'); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary);">Daftar Semua Produk</h6>
                    <button class="btn btn-pink-primary rounded-lg" data-bs-toggle="modal"
                        data-bs-target="#productFormModal" id="btnAddProduct">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Produk Baru
                    </button>
                </div>
                <div class="card-body">
                    <div id="alertPlaceholder">
                    </div>

                    <div class="mb-3 d-flex align-items-center">
                        <label for="categoryFilter" class="form-label fw-semibold me-2 mb-0">Filter Kategori:</label>
                        <select id="categoryFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Semua Kategori</option>
                            <?php 
                            // Asumsi $result_categories tersedia dari proses/get_manajemen-produk.php
                            if (isset($result_categories) && mysqli_num_rows($result_categories) > 0) {
                                mysqli_data_seek($result_categories, 0); // Reset pointer
                                while ($category = mysqli_fetch_assoc($result_categories)): 
                            ?>
                            <option value="<?php echo htmlspecialchars($category['name']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endwhile; } ?>
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="productTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result_products) > 0): ?>
                                <?php mysqli_data_seek($result_products, 0); 
                                while ($product = mysqli_fetch_assoc($result_products)): ?>
                                <tr>
                                    <td class="product-id"><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td>
                                        <?php
                                        $image_url = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://placehold.co/50x50/cccccc/333333?text=N/A';
                                        $product_initial = substr(htmlspecialchars($product['name']), 0, 1);
                                        if (empty($product['image_url'])) {
                                            $image_url = "https://placehold.co/50x50/e83e8c/ffffff?text={$product_initial}";
                                        }
                                        ?>
                                        <img src="<?php echo $image_url; ?>" class="product-image-thumb"
                                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            onerror="this.onerror=null; this.src='https://placehold.co/50x50/e83e8c/ffffff?text=X'">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                    <td>
                                        <?php
                                        $stock = (int)$product['stock'];
                                        $is_active = (int)$product['is_active'];
                                        $status_text = 'Nonaktif';
                                        $status_class = 'bg-secondary';
                                        
                                        if ($is_active == 1) {
                                            if ($stock > 50) {
                                                $status_text = 'Tersedia';
                                                $status_class = 'bg-success';
                                            } elseif ($stock > 0 && $stock <= 50) {
                                                $status_text = 'Stok Rendah';
                                                $status_class = 'bg-warning text-dark';
                                            } else {
                                                $status_text = 'Habis';
                                                $status_class = 'bg-danger';
                                            }
                                        }
                                        ?>
                                        <span
                                            class="badge <?php echo $status_class; ?> py-2 px-3 rounded-pill"><?php echo $status_text; ?></span>
                                    </td>
                                    <td class="btn-action-group">
                                        <a href="manajemen-produk.php?edit_id=<?php echo $product['id']; ?>"
                                            class="btn btn-warning me-1 rounded-lg" title="Edit Produk"><i
                                                class="fas fa-edit"></i></a>

                                        <a href="./proses/proses_manajemen-produk.php?action=delete&id=<?php echo $product['id']; ?>"
                                            class="btn btn-danger rounded-lg"
                                            onclick="return confirm('Yakin ingin menghapus produk <?php echo htmlspecialchars($product['name']); ?>? Tindakan ini tidak dapat dibatalkan.');"
                                            title="Hapus Produk"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada data produk di
                                        database.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="productFormModal" tabindex="-1" aria-labelledby="productFormModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-xl">
                        <div class="modal-header modal-header-pink">
                            <h5 class="modal-title" id="productFormModalLabel">
                                <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus-circle'; ?> me-2"></i>
                                <?php echo $is_edit ? 'Edit Produk: ' . htmlspecialchars($product_data['name']) : 'Tambah Produk Baru'; ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="productForm" method="POST" action="./proses/proses_manajemen-produk.php">
                                <input type="hidden" name="productId" value="<?php echo $product_data['id'] ?? ''; ?>">
                                <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="productName" class="form-label fw-semibold">Nama Produk</label>
                                        <input type="text" name="productName" class="form-control rounded-lg"
                                            id="productName" placeholder="Contoh: Lipstik Cair Ajaib" required
                                            value="<?php echo htmlspecialchars($product_data['name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="productSKU" class="form-label fw-semibold">Kode SKU / ID
                                            Produk</label>
                                        <input type="text" name="productSKU" class="form-control rounded-lg"
                                            id="productSKU" placeholder="Contoh: BFLC-RED01" required
                                            value="<?php echo htmlspecialchars($product_data['sku'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productCategory" class="form-label fw-semibold">Kategori</label>
                                        <select name="productCategory" id="productCategory"
                                            class="form-select rounded-lg" required>
                                            <option value="" disabled
                                                <?php echo empty($product_data) ? 'selected' : ''; ?>>Pilih Kategori...
                                            </option>
                                            <?php 
                                            // Reset pointer categories untuk digunakan di sini (jika sudah digunakan di filter)
                                            if (isset($result_categories)) mysqli_data_seek($result_categories, 0); 
                                            while ($category = mysqli_fetch_assoc($result_categories)): 
                                            ?>
                                            <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                                <?php echo (isset($product_data['category_id']) && $product_data['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productPrice" class="form-label fw-semibold">Harga (Rp)</label>
                                        <input type="number" name="productPrice" class="form-control rounded-lg"
                                            id="productPrice" placeholder="Contoh: 85000" min="0" step="1" required
                                            value="<?php echo htmlspecialchars($product_data['price'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productStock" class="form-label fw-semibold">Stok</label>
                                        <input type="number" name="productStock" class="form-control rounded-lg"
                                            id="productStock" placeholder="Jumlah stok" min="0" required
                                            value="<?php echo htmlspecialchars($product_data['stock'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <label for="productDescription" class="form-label fw-semibold">Deskripsi
                                            Produk</label>
                                        <textarea class="form-control rounded-lg" name="productDescription"
                                            id="productDescription" rows="3"
                                            placeholder="Jelaskan fitur dan manfaat produk..."><?php echo htmlspecialchars($product_data['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="productImage" class="form-label fw-semibold">Gambar Produk
                                            (image_url)</label>
                                        <input class="form-control rounded-lg" type="text" name="productImage"
                                            id="productImage" placeholder="Masukkan URL Gambar Produk"
                                            value="<?php echo htmlspecialchars($product_data['image_url'] ?? ''); ?>">
                                        <small class="form-text text-muted">Contoh:
                                            https://placehold.co/600x400</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productStatus" class="form-label fw-semibold">Status Produk
                                            (is_active)</label>
                                        <select name="productStatus" id="productStatus" class="form-select rounded-lg"
                                            required>
                                            <option value="1"
                                                <?php echo (isset($product_data['is_active']) && $product_data['is_active'] == 1) ? 'selected' : ''; ?>>
                                                Aktif / Tersedia</option>
                                            <option value="0"
                                                <?php echo (isset($product_data['is_active']) && $product_data['is_active'] == 0) ? 'selected' : ''; ?>>
                                                Nonaktif / Tersembunyi</option>
                                        </select>
                                    </div>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-lg"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" form="productForm" id="btnSaveProduct"
                                class="btn btn-pink-primary rounded-lg">
                                <i class="fas fa-save me-1"></i> Simpan Produk
                            </button>
                            </form>
                        </div>
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
        // === 1. INISIALISASI DATATABLES (FITUR FILTER & SORT) ===
        // =========================================================
        var productTable = $('#productTable').DataTable({
            "order": [
                [0, "desc"]
            ], // Urutkan default berdasarkan kolom ID (indeks 0), urutan menurun (desc)
            "language": {
                // Menggunakan terjemahan Bahasa Indonesia
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
            },
            "columnDefs": [
                // Kolom 'Foto' (indeks 1) dan 'Aksi' (indeks 8) tidak dapat diurutkan/dicari
                {
                    "orderable": false,
                    "searchable": false,
                    "targets": [1, 8]
                },
                // Kolom 'Harga' (indeks 5) menggunakan tipe 'num-fmt' agar mengurutkan angka yang diformat
                // Catatan: Ini opsional, tapi membantu jika Anda memformat angka di PHP.
                // Jika DataTables masih salah, pastikan input Harga di PHP tidak mengandung karakter selain angka.
                {
                    "type": "num-fmt",
                    "targets": 5
                }
            ]
        });

        // =========================================================
        // === 2. LOGIKA FILTER KATEGORI KUSTOM ===
        // =========================================================
        $('#categoryFilter').on('change', function() {
            var categoryName = $(this).val();
            // Kolom 'Kategori' adalah kolom ke-4 (indeks 4)
            // .search(string, regex, smart_filter).draw()
            // Menggunakan regex '^(...)$' agar match penuh, bukan substring.
            productTable.column(4).search(categoryName ? '^' + categoryName + '$' : '', true, false)
                .draw();
        });


        // =========================================================
        // === 3. LOGIKA NOTIFIKASI (Merespon Redirect PHP) ===
        // =========================================================
        <?php if (isset($_GET['status']) && isset($_GET['message'])): ?>
        Swal.fire({
            icon: '<?php echo $_GET['status']; ?>',
            title: '<?php echo ($_GET['status'] == 'success' ? 'Berhasil!' : 'Gagal!'); ?>',
            text: '<?php echo htmlspecialchars($_GET['message']); ?>',
            timer: 4000,
            showConfirmButton: false
        }).then(() => {
            if (history.replaceState) {
                let url = window.location.href;
                url = url.replace(/[?&]status=[^&]*/g, '').replace(/[?&]message=[^&]*/g, '').replace(
                    /[?&]edit_id=[^&]*/g, ''); // Hapus semua parameter notif/edit
                url = url.replace(/([?])\&/g, '$1');
                history.replaceState({}, document.title, url);
            }
        });
        <?php endif; ?>

        // =========================================================
        // === 4. LOGIKA UNTUK MEMBUKA MODAL EDIT JIKA ADA DATA EDIT ===
        // =========================================================
        <?php if ($is_edit): ?>
        $('#productFormModal').modal('show');
        <?php endif; ?>

        // =========================================================
        // === 5. LOGIKA MERESET FORM KETIKA MODAL DITUTUP ===
        // =========================================================
        $('#productFormModal').on('hidden.bs.modal', function() {
            if (<?php echo $is_edit ? 'true' : 'false'; ?>) {
                // Refresh halaman untuk menghilangkan parameter edit_id di URL
                window.location.href = 'manajemen-produk.php';
            }
        });

        // 6. Tutup koneksi database (di akhir skrip PHP)
        <?php mysqli_close($conn); ?>
    });
    </script>
</body>

</html>