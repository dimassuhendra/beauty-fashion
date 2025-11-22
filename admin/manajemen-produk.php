<?php
include '../db_connect.php';
include 'proses/get_manajemen-produk.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body id="body-admin">
    <?php include 'sidebar.php' ?>

    <div class="main-content">
        <header class="mb-4">
            <h1 class="text-color">Manajemen Produk</h1>
            <p class="lead">Kelola daftar produk, kategori, stok, dan harga.</p>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <button class="btn btn-pink me-2 mb-2 mb-md-0" data-bs-toggle="modal"
                        data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-1"></i> Tambah Produk
                    </button>
                    <button class="btn btn-outline-pink mb-2 mb-md-0" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">
                        <i class="fas fa-tags me-1"></i> Tambah Kategori
                    </button>
                </div>

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
                        <input type="text" name="s" class="form-control" placeholder="Cari SKU atau Nama Produk..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        <?php if (!empty($search)): ?>
                            <a href="manajemen-produk.php?limit=<?php echo $limit; ?>" class="btn btn-outline-danger"
                                title="Reset Pencarian"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
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

                            <th scope="col">
                                <a href="<?php echo get_sort_link('average_rating', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'average_rating' ? 'active-sort' : ''; ?>">
                                    Rating
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'average_rating' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('sku', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'sku' ? 'active-sort' : ''; ?>">
                                    SKU
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'sku' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" style="width: 20%;">
                                <a href="<?php echo get_sort_link('name', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'name' ? 'active-sort' : ''; ?>">
                                    Nama Produk
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'name' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('total_sold', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'total_sold' ? 'active-sort' : ''; ?>">
                                    Terjual
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'total_sold' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('category_name', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'category_name' ? 'active-sort' : ''; ?>">
                                    Kategori
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'category_name' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('price', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'price' ? 'active-sort' : ''; ?>">
                                    Harga
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'price' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('stock', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'stock' ? 'active-sort' : ''; ?>">
                                    Stok
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'stock' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col">
                                <a href="<?php echo get_sort_link('is_active', $sort, $order, $limit, $search); ?>"
                                    class="sortable-header <?php echo $sort == 'is_active' ? 'active-sort' : ''; ?>">
                                    Status
                                    <i
                                        class="fas fa-sort sort-icon <?php echo $sort == 'is_active' ? ($order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : ''; ?>"></i>
                                </a>
                            </th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td>
                                        <span class="badge bg-info text-white">
                                            <?php echo number_format($p['average_rating'], 2); ?>
                                            <i class="fas fa-star fa-sm"></i>
                                        </span>
                                    </td>
                                    <td><?php echo $p['sku']; ?></td>
                                    <td>
                                        <?php if (!empty($p['image_url'])): ?>
                                            <img src="../uploads/product/<?php echo $p['image_url']; ?>"
                                                alt="<?php echo $p['name']; ?>" class="product-image-thumb me-2">
                                        <?php else: ?>
                                            <i class="fas fa-image me-2 text-muted"></i>
                                        <?php endif; ?>
                                        <?php echo $p['name']; ?>
                                    </td>
                                    <td><?php echo number_format($p['total_sold']); ?></td>
                                    <td><?php echo $p['category_name']; ?></td>
                                    <td>Rp<?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span
                                            class="badge bg-<?php echo $p['stock'] > 20 ? 'success' : ($p['stock'] > 10 ? 'warning' : 'danger'); ?>">
                                            <?php echo $p['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $p['is_active'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $p['is_active'] ? 'Aktif' : 'Non-aktif'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning me-1 btn-edit-product"
                                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                                            data-id="<?php echo $p['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($p['name']); ?>"
                                            data-sku="<?php echo $p['sku']; ?>"
                                            data-category-id="<?php echo $p['category_id']; ?>"
                                            data-price="<?php echo $p['price']; ?>" data-stock="<?php echo $p['stock']; ?>"
                                            data-is-active="<?php echo $p['is_active']; ?>"
                                            data-image-url="<?php echo $p['image_url']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-product" data-bs-toggle="modal"
                                            data-bs-target="#deleteProductModal" data-id="<?php echo $p['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($p['name']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data produk ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?php echo count($products); ?> dari
                    <?php echo $total_rows; ?>
                    total produk.</small>

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

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="manajemen-produk.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="add_name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_sku" class="form-label">SKU/Kode Produk</label>
                            <input type="text" class="form-control" id="add_sku" name="sku" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="add_category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" id="add_price" name="price" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="add_stock" name="stock" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="add_image" class="form-label">Gambar Produk</label>
                            <input type="file" class="form-control" id="add_image" name="image" accept="image/*">
                            <small class="form-text text-muted">Akan disimpan di `uploads/product/`</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Produk: <span
                            id="edit_product_name_title"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProductForm" method="POST" action="manajemen-produk.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="product_id" id="edit_product_id">

                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_price" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" id="edit_price" name="price" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="edit_stock" name="stock" min="0" required>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                checked>
                            <label class="form-check-label" for="edit_is_active">Produk Aktif/Tampil</label>
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Ganti Gambar Produk</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <div id="current_image_preview" class="mt-2"></div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteProductModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="manajemen-produk.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" id="delete_product_id">
                        <p class="text-danger">Anda yakin ingin menghapus produk <b id="delete_product_name"></b>?
                            Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="manajemen-produk.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_category">

                        <div class="mb-3">
                            <label for="add_category_name" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="add_category_name" name="name" required>
                            <small class="form-text text-muted">Contoh: Skincare, Make Up, Aksesori. Slug akan
                                dibuat
                                secara otomatis.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-pink">Simpan Kategori</button>
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
        // Asumsi 'mode-toggle' adalah ID button/link di sidebar.php yang mengontrol mode
        const modeToggle = document.getElementById('mode-toggle');

        function applyMode(isInitialLoad = true) {
            let savedMode = localStorage.getItem('theme') || 'light';

            // Jika bukan initial load (dipicu oleh klik), kita harus toggle mode
            if (!isInitialLoad && modeToggle) {
                // Tentukan mode baru
                savedMode = body.classList.contains('dark-mode') ? 'light' : 'dark';
                localStorage.setItem('theme', savedMode);
            }

            // Terapkan class ke body
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

            // Optional: Update button text/icon (Asumsi ada di sidebar)
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

        // 1. Panggil saat load pertama
        document.addEventListener('DOMContentLoaded', () => {
            applyMode(true);
        });

        // 2. Pasang event listener untuk toggle
        if (modeToggle) {
            modeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                applyMode(false); // Panggil dengan flag false agar logic toggle dijalankan
            });
        }


        // ====================================================
        // Logic Modals (Edit dan Delete)
        // ====================================================

        // 1. Logic untuk MODAL EDIT
        const editProductModal = document.getElementById('editProductModal');
        editProductModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ambil data dari data-* attributes
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const categoryId = button.getAttribute('data-category-id');
            const price = button.getAttribute('data-price');
            const stock = button.getAttribute('data-stock');
            const isActive = button.getAttribute('data-is-active');
            const imageUrl = button.getAttribute('data-image-url');

            // Isi elemen modal
            const modalTitle = editProductModal.querySelector('#editProductModalLabel span');

            // Isi form fields
            editProductModal.querySelector('#edit_product_id').value = id;
            editProductModal.querySelector('#edit_name').value = name;
            editProductModal.querySelector('#edit_price').value = price;
            editProductModal.querySelector('#edit_stock').value = stock;
            editProductModal.querySelector('#edit_category_id').value = categoryId;
            modalTitle.textContent = name;

            // Handle checkbox is_active
            const checkboxActive = editProductModal.querySelector('#edit_is_active');
            checkboxActive.checked = isActive === '1';

            // Handle image preview
            const previewContainer = editProductModal.querySelector('#current_image_preview');
            previewContainer.innerHTML = '';
            if (imageUrl) {
                // Asumsi jalur gambar sama dengan di PHP
                previewContainer.innerHTML = `
                    <p class="mb-1 small text-muted">Gambar Saat Ini:</p>
                    <img src="../uploads/product/${imageUrl}" alt="${name}" class="product-image-thumb" style="width: 80px; height: 80px;">
                `;
            }
        });


        // 2. Logic untuk MODAL HAPUS
        const deleteProductModal = document.getElementById('deleteProductModal');
        deleteProductModal.addEventListener('show.bs.modal', function (event) {
            // Tombol yang memicu modal
            const button = event.relatedTarget;

            // Ambil data dari data-* attributes
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            // Isi elemen modal
            const modalIdField = deleteProductModal.querySelector('#delete_product_id');
            const modalNameText = deleteProductModal.querySelector('#delete_product_name');

            modalIdField.value = id;
            modalNameText.textContent = name;
        });
    </script>
</body>

</html>