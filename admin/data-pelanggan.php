<?php
// Ambil koneksi database
require_once '../db_connect.php'; 

// Cek apakah ada notifikasi dari aksi sebelumnya
$is_status_redirect = isset($_GET['status']) && isset($_GET['message']);

// --- LOGIKA PENGAMBILAN DATA PELANGGAN ---
$sql_users = "
    SELECT 
        id, 
        full_name, 
        email, 
        phone_number, 
        created_at
    FROM users
    ORDER BY created_at DESC
";

$result_users = mysqli_query($conn, $sql_users);

// Handle error
if (!$result_users) {
    // Di lingkungan produksi, ini diganti dengan logging yang aman.
    // die("Query Error: " . mysqli_error($conn)); 
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan | Beauty Fashion Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">

    <link rel="stylesheet" href="/beauty-fashion/css/style-admin.css">
    <style>
    /* DataTables Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background-color: var(--bs-pink-primary) !important;
        border-color: var(--bs-pink-primary) !important;
        color: white !important;
    }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php';?>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded-3 mb-4 shadow-sm">
                <div class="container-fluid">
                    <h2 class="text-pink-primary fw-bold mb-0">Data Pelanggan</h2>
                    <div class="d-flex">
                        <span class="navbar-text me-3 d-none d-sm-inline">
                            <?php echo date('l, d-m-Y'); ?>
                        </span>
                    </div>
                </div>
            </nav>

            <div class="card shadow mb-4 border-0 rounded-lg">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold" style="color: var(--bs-pink-primary);">Daftar Pelanggan Terdaftar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="userTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Nomor Telepon</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($result_users) && mysqli_num_rows($result_users) > 0): ?>
                                <?php while ($user = mysqli_fetch_assoc($result_users)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone_number'] ?? '-'); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($user['created_at'])); ?></td>
                                    <td class="btn-action-group">
                                        <a href="detail-pelanggan.php?id=<?php echo $user['id']; ?>"
                                            class="btn btn-info me-1 rounded-lg text-white" title="Lihat Detail"><i
                                                class="fas fa-eye"></i></a>

                                        <button class="btn btn-danger rounded-lg" title="Nonaktifkan Akun"
                                            onclick="confirmNonaktif(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada pelanggan yang
                                        terdaftar.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

    <script>
    // Fungsi untuk konfirmasi nonaktifkan akun
    function confirmNonaktif(userId, userName) {
        Swal.fire({
            title: 'Yakin Nonaktifkan?',
            text: "Anda akan menonaktifkan akun pelanggan: " + userName,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Nonaktifkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Di sini Anda akan mengarahkan ke file proses untuk nonaktifkan akun
                // Misalnya: window.location.href = './proses/proses_pelanggan.php?action=deactivate&id=' + userId;

                // Karena file prosesnya belum dibuat, kita buat notifikasi berhasil saja dulu:
                Swal.fire(
                    'Berhasil!',
                    'Akun ' + userName + ' telah dinonaktifkan (simulasi).',
                    'success'
                )
            }
        })
    }

    $(document).ready(function() {

        // =========================================================
        // === 1. INISIALISASI DATATABLES ===
        // =========================================================
        $('#userTable').DataTable({
            "order": [
                [4, "desc"]
            ], // Urutkan default berdasarkan Tanggal Daftar (indeks 4)
            "language": {
                // FIX ERROR I18N
                "url": "https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
            },
            "columnDefs": [{
                    "orderable": false,
                    "searchable": false,
                    "targets": [5] // Kolom 'Aksi'
                },
                {
                    "targets": [0], // Kolom 'ID'
                    "visible": false,
                    "searchable": false
                }
            ]
        });

        // =========================================================
        // === 2. LOGIKA NOTIFIKASI (Merespon Redirect PHP) ===
        // =========================================================
        <?php if ($is_status_redirect): ?>
        Swal.fire({
            icon: '<?php echo $_GET['status']; ?>',
            title: '<?php echo ($_GET['status'] == 'success' ? 'Berhasil!' : 'Gagal!'); ?>',
            text: '<?php echo htmlspecialchars($_GET['message']); ?>',
            timer: 4000,
            showConfirmButton: false
        }).then(() => {
            if (history.replaceState) {
                let url = window.location.href;
                url = url.replace(/[?&]status=[^&]*/g, '').replace(/[?&]message=[^&]*/g, '');
                url = url.replace(/([?])\&/g, '$1');
                history.replaceState({}, document.title, url);
            }
        });
        <?php endif; ?>

        // 3. Tutup koneksi database
        <?php mysqli_close($conn); ?>
    });
    </script>
</body>

</html>