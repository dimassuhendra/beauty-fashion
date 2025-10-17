<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | Beauty Fashion Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
    <style>
    /* Gaya untuk tombol Aksi di tabel */
    /* Modal Header agar sesuai tema */
    </style>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar (Menggantikan include 'sidebar.php') -->
        <?php include 'sidebar.php'; ?>
        <!-- End Sidebar -->

        <div id="content">
            <!-- Navbar Header -->
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

            <!-- Konten Utama: Tabel Produk -->
            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary);">Daftar Semua Produk</h6>
                    <!-- Tombol Modal Tambah Produk -->
                    <button class="btn btn-pink-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Produk Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="productTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th> <!-- Kolom Baru: Foto Produk -->
                                    <th>SKU</th> <!-- Kolom Baru: SKU -->
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Contoh Data Produk -->
                                <tr>
                                    <td>1</td>
                                    <td><img src="https://placehold.co/50x50/ff69b4/ffffff?text=L"
                                            class="product-image-thumb" alt="Lipstik"></td>
                                    <td>BFLM-001</td>
                                    <td>Lipstik Matte Glamour</td>
                                    <td>Makeup</td>
                                    <td>Rp 85.000</td>
                                    <td>120</td>
                                    <td><span class="badge bg-success py-2 px-3 rounded-pill">Tersedia</span></td>
                                    <td class="btn-action-group">
                                        <button class="btn btn-info text-white me-1" title="Lihat Detail"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-warning me-1" title="Edit Produk"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger" title="Hapus Produk"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><img src="https://placehold.co/50x50/4b0082/ffffff?text=T"
                                            class="product-image-thumb" alt="Tas"></td>
                                    <td>BFAT-002</td>
                                    <td>Tas Tangan Kulit Elegance</td>
                                    <td>Aksesoris</td>
                                    <td>Rp 599.000</td>
                                    <td>35</td>
                                    <td><span class="badge bg-warning py-2 px-3 rounded-pill">Stok Rendah</span>
                                    </td>
                                    <td class="btn-action-group">
                                        <button class="btn btn-info text-white me-1" title="Lihat Detail"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-warning me-1" title="Edit Produk"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger" title="Hapus Produk"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><img src="https://placehold.co/50x50/00bcd4/ffffff?text=S"
                                            class="product-image-thumb" alt="Skincare"></td>
                                    <td>BFSK-003</td>
                                    <td>Set Perawatan Wajah Hydrating</td>
                                    <td>Skincare</td>
                                    <td>Rp 250.000</td>
                                    <td>0</td>
                                    <td><span class="badge bg-danger py-2 px-3 rounded-pill">Habis</span></td>
                                    <td class="btn-action-group">
                                        <button class="btn btn-info text-white me-1" title="Lihat Detail"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-warning me-1" title="Edit Produk"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger" title="Hapus Produk"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Produk Baru -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-xl">
                        <div class="modal-header modal-header-pink">
                            <h5 class="modal-title" id="addProductModalLabel"><i class="fas fa-plus-circle me-2"></i>
                                Tambah Produk Baru</h5>
                            <!-- Mengubah class btn-close agar lebih terlihat -->
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addProductForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="productName" class="form-label fw-semibold">Nama Produk</label>
                                        <input type="text" class="form-control rounded-lg" id="productName"
                                            placeholder="Contoh: Lipstik Cair Ajaib" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="productSKU" class="form-label fw-semibold">Kode SKU / ID
                                            Produk</label>
                                        <input type="text" class="form-control rounded-lg" id="productSKU"
                                            placeholder="Contoh: BFLC-RED01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productCategory" class="form-label fw-semibold">Kategori</label>
                                        <select id="productCategory" class="form-select rounded-lg" required>
                                            <option value="" disabled selected>Pilih Kategori...</option>
                                            <!-- Pilihan ini harusnya diisi dinamis dari tabel categories -->
                                            <option value="1">Makeup</option>
                                            <option value="2">Skincare</option>
                                            <option value="3">Aksesoris</option>
                                            <option value="4">Pakaian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productPrice" class="form-label fw-semibold">Harga (Rp)</label>
                                        <input type="number" class="form-control rounded-lg" id="productPrice"
                                            placeholder="Contoh: 85000" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productStock" class="form-label fw-semibold">Stok</label>
                                        <input type="number" class="form-control rounded-lg" id="productStock"
                                            placeholder="Jumlah stok" min="0" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="productDescription" class="form-label fw-semibold">Deskripsi
                                            Produk</label>
                                        <textarea class="form-control rounded-lg" id="productDescription" rows="3"
                                            placeholder="Jelaskan fitur dan manfaat produk..."></textarea>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="productImage" class="form-label fw-semibold">Gambar Produk
                                            (image_url)</label>
                                        <input class="form-control rounded-lg" type="text" id="productImage"
                                            placeholder="Masukkan URL Gambar Produk">
                                        <small class="form-text text-muted">Contoh:
                                            https://placehold.co/600x400</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="productStatus" class="form-label fw-semibold">Status Produk
                                            (is_active)</label>
                                        <select id="productStatus" class="form-select rounded-lg" required>
                                            <option value="1" selected>Aktif / Tersedia</option>
                                            <option value="0">Nonaktif / Tersembunyi</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-lg"
                                data-bs-dismiss="modal">Batal</button>
                            <!-- Tombol ini harus berada di dalam form jika ingin submit data -->
                            <button type="submit" form="addProductForm" class="btn btn-pink-primary rounded-lg">
                                <i class="fas fa-save me-1"></i> Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Tambah Produk -->

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>