<?php
// PASTIKAN BARIS INI ADA DI PALING ATAS
session_start(); 

// --- Bagian 1: Koneksi dan Logika READ, FILTER, SORTING ---

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "beauty"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Logika Filtering dan Sorting
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'ASC';

$sql = "SELECT p.*, c.name as category_name, c.id as category_id 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1=1"; 

if ($category_filter !== 'all' && is_numeric($category_filter)) {
    $sql .= " AND p.category_id = " . (int)$category_filter;
}

if ($status_filter !== 'all') {
    $status_value = ($status_filter === 'active') ? 1 : 0;
    $sql .= " AND p.is_active = " . $status_value;
}

$valid_columns = ['id', 'name', 'sku', 'price', 'stock', 'category_name', 'is_active'];
if (in_array($sort_column, $valid_columns)) {
    $sort_field = ($sort_column === 'category_name') ? 'c.name' : 'p.' . $sort_column;
    $sql .= " ORDER BY $sort_field $sort_order";
} else {
    $sql .= " ORDER BY p.id ASC"; 
}

$result = $conn->query($sql);
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");

function get_sort_link($column, $current_sort, $current_order, $category_filter, $status_filter) {
    $new_order = 'ASC';
    if ($current_sort === $column && $current_order === 'ASC') {
        $new_order = 'DESC';
    }
    $filter_params = "&category=" . $category_filter . "&status=" . $status_filter;
    return "?sort=" . $column . "&order=" . $new_order . $filter_params;
}

$categories_array = [];
if ($categories_result && $categories_result->num_rows > 0) {
    $categories_result->data_seek(0);
    while ($cat = $categories_result->fetch_assoc()) {
        $categories_array[] = $cat;
    }
}

// --- Bagian 2: Ambil pesan Feedback dari Session ---
$feedback_message = '';
$feedback_type = '';

if (isset($_SESSION['feedback_msg'])) {
    $feedback_message = $_SESSION['feedback_msg'];
    $feedback_type = $_SESSION['feedback_type'] ?? 'info';
    
    // HAPUS pesan dari session agar tidak muncul lagi setelah refresh
    unset($_SESSION['feedback_msg']);
    unset($_SESSION['feedback_type']);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    .table th a {
        color: inherit;
        text-decoration: none;
    }

    .table th a:hover {
        text-decoration: underline;
    }

    .product-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">📋 Manajemen Produk</h2>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-success" onclick="openProductModal('add')">
                ➕ Tambah Produk
            </button>
        </div>

        <form method="GET" class="form-inline mb-4">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_column) ?>">
            <input type="hidden" name="order" value="<?= htmlspecialchars($sort_order) ?>">

            <label for="category_filter" class="mr-2">Filter Kategori:</label>
            <select name="category" id="category_filter" class="form-control mr-3">
                <option value="all">Semua Kategori</option>
                <?php 
                foreach ($categories_array as $cat): 
                ?>
                <option value="<?= $cat['id'] ?>" <?= ($category_filter == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="status_filter" class="mr-2">Filter Status:</label>
            <select name="status" id="status_filter" class="form-control mr-3">
                <option value="all" <?= ($status_filter == 'all') ? 'selected' : '' ?>>Semua Data</option>
                <option value="active" <?= ($status_filter == 'active') ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= ($status_filter == 'inactive') ? 'selected' : '' ?>>Non-aktif</option>
            </select>

            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            <a href="products.php" class="btn btn-secondary ml-2">Reset Filter</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="product-table">
                <thead class="thead-dark">
                    <tr>
                        <th><a
                                href="<?= get_sort_link('id', $sort_column, $sort_order, $category_filter, $status_filter) ?>">ID
                                <?= ($sort_column === 'id') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a></th>
                        <th>Gambar</th>
                        <th><a
                                href="<?= get_sort_link('sku', $sort_column, $sort_order, $category_filter, $status_filter) ?>">SKU
                                <?= ($sort_column === 'sku') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a></th>
                        <th><a
                                href="<?= get_sort_link('name', $sort_column, $sort_order, $category_filter, $status_filter) ?>">Nama
                                Produk <?= ($sort_column === 'name') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a>
                        </th>
                        <th><a
                                href="<?= get_sort_link('category_name', $sort_column, $sort_order, $category_filter, $status_filter) ?>">Kategori
                                <?= ($sort_column === 'category_name') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a>
                        </th>
                        <th><a
                                href="<?= get_sort_link('price', $sort_column, $sort_order, $category_filter, $status_filter) ?>">Harga
                                <?= ($sort_column === 'price') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a></th>
                        <th><a
                                href="<?= get_sort_link('stock', $sort_column, $sort_order, $category_filter, $status_filter) ?>">Stok
                                <?= ($sort_column === 'stock') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a></th>
                        <th><a
                                href="<?= get_sort_link('is_active', $sort_column, $sort_order, $category_filter, $status_filter) ?>">Status
                                <?= ($sort_column === 'is_active') ? (($sort_order === 'ASC') ? '▲' : '▼') : '' ?></a>
                        </th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php 
                                    $image_path = 'uploads/product/' . htmlspecialchars($row['image_url']);
                                    // Pengecekan path relatif dari products.php (admin/uploads/product/...)
                                    if (!empty($row['image_url']) && file_exists($image_path)) { 
                                        echo '<img src="' . $image_path . '" alt="' . htmlspecialchars($row['name']) . '" class="product-img">';
                                    } else {
                                        echo '[No Image]';
                                    }
                                    ?>
                        </td>
                        <td><?= htmlspecialchars($row['sku']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['category_name']) ?></td>
                        <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td><?= $row['stock'] ?></td>
                        <td>
                            <span class="badge badge-<?= $row['is_active'] ? 'success' : 'danger' ?>">
                                <?= $row['is_active'] ? 'Aktif' : 'Non-aktif' ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info btn-edit-product"
                                data-product='<?= json_encode($row) ?>' onclick="openProductModal('edit', this)">
                                Edit
                            </button>
                            <a href="proses/proses_manajemen-produk.php?action=delete&id=<?= $row['id'] ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Yakin ingin menghapus produk <?= htmlspecialchars($row['name']) ?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data produk yang ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php $conn->close(); ?>

    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="productForm" action="proses/proses_manajemen-produk.php" method="POST"
                    enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Tambah Produk Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" id="modal_action" value="add">
                        <input type="hidden" name="id" id="modal_id" value="">

                        <div class="form-group">
                            <label for="modal_name">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_name" name="name" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="modal_category_id">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories_array as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="modal_sku">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_sku" name="sku" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="modal_price">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="modal_price" name="price" step="any"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="modal_stock">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="modal_stock" name="stock" min="0"
                                    required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="modal_product_image">Gambar Produk</label>
                                <input type="file" class="form-control-file" id="modal_product_image"
                                    name="product_image">
                                <small class="form-text text-muted" id="current_image_info">Abaikan jika tidak ingin
                                    mengganti gambar.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="modal_description">Deskripsi</label>
                            <textarea class="form-control" id="modal_description" name="description"
                                rows="3"></textarea>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="modal_is_active" name="is_active"
                                value="1">
                            <label class="form-check-label" for="modal_is_active">Produk Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="modalSubmitButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Pemberitahuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="feedback-content" class="alert" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Fungsi JS Modal Tambah/Edit
    function openProductModal(mode, button) {
        const modalTitle = $('#productModalLabel');
        const form = $('#productForm');
        const submitButton = $('#modalSubmitButton');
        const currentImageInfo = $('#current_image_info');

        // 1. Reset Form
        form[0].reset();
        $('#modal_id').val('');
        $('#modal_is_active').prop('checked', true);
        currentImageInfo.text('Pilih file baru untuk diupload.').removeClass('text-warning');

        if (mode === 'add') {
            // Konfigurasi Tambah
            modalTitle.text('➕ Tambah Produk Baru');
            $('#modal_action').val('add');
            submitButton.text('Simpan Produk');
            currentImageInfo.hide();
            $('#modal_product_image').prop('required', true);

        } else if (mode === 'edit' && button) {
            // Konfigurasi Edit
            modalTitle.text('✏️ Edit Produk');
            $('#modal_action').val('edit');
            submitButton.text('Perbarui Produk');
            currentImageInfo.show();
            $('#modal_product_image').prop('required', false);

            const productData = $(button).data('product');

            // 2. Isi Form dengan Data Produk
            $('#modal_id').val(productData.id);
            $('#modal_name').val(productData.name);
            $('#modal_category_id').val(productData.category_id);
            $('#modal_sku').val(productData.sku);
            $('#modal_price').val(parseFloat(productData.price));
            $('#modal_stock').val(productData.stock);
            $('#modal_description').val(productData.description);

            // Set Status Aktif
            $('#modal_is_active').prop('checked', productData.is_active == 1);

            // Tampilkan info gambar lama
            if (productData.image_url) {
                currentImageInfo.html('Gambar saat ini: <strong>' + productData.image_url +
                    '</strong>. Pilih file baru jika ingin mengganti.').addClass('text-warning');
            } else {
                currentImageInfo.text('Produk ini belum memiliki gambar.').addClass('text-warning');
            }
        }

        $('#productModal').modal('show');
    }

    // FUNGSI UNTUK MENAMPILKAN MODAL FEEDBACK SETELAH REDIRECT
    $(document).ready(function() {
        // Mengambil pesan feedback dari variabel PHP yang berasal dari Session
        const feedbackMessage = "<?= addslashes($feedback_message) ?>";
        const feedbackType = "<?= addslashes($feedback_type) ?>";

        if (feedbackMessage) {
            // Konfigurasi tampilan alert
            $('#feedback-content').removeClass().addClass('alert alert-' + feedbackType);
            $('#feedback-content').html(feedbackMessage);

            // Tampilkan Modal Feedback
            $('#feedbackModal').modal('show');
        }
    });
    </script>
</body>

</html>