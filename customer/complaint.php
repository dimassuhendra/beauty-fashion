<?php
// ====================================================================
// BAGIAN 1: LOGIKA PHP & KONEKSI DATABASE
// ====================================================================
session_start();
include '../db_connect.php'; 
include 'proses/get_complaint.php'; 
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan & Komplain - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="profile-hero">
        <div class="container">
            <h1><i class="fas fa-headset me-2"></i> Pusat Bantuan & Komplain</h1>
            <p class="lead">Ajukan keluhan Anda dan pantau status penyelesaiannya.</p>
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
                    
                    <button class="nav-link profile-tab <?= $currentTab == 'submit' ? 'active' : ''; ?>"
                        id="submit-tab" data-bs-toggle="pill" data-bs-target="#complaint-submit" type="button"
                        role="tab" aria-controls="complaint-submit"
                        aria-selected="<?= $currentTab == 'submit' ? 'true' : 'false'; ?>"
                        onclick="changeTab('submit')">
                        <i class="fas fa-file-alt me-2"></i> Ajukan Komplain Baru
                    </button>
                    
                    <button class="nav-link profile-tab <?= $currentTab == 'history' ? 'active' : ''; ?>"
                        id="history-tab" data-bs-toggle="pill" data-bs-target="#complaint-history" type="button"
                        role="tab" aria-controls="complaint-history"
                        aria-selected="<?= $currentTab == 'history' ? 'true' : 'false'; ?>"
                        onclick="changeTab('history')">
                        <i class="fas fa-history me-2"></i> Riwayat Komplain
                    </button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="tab-content profile-card" id="v-pills-tabContent">

                    <div class="tab-pane fade <?= $currentTab == 'submit' ? 'show active' : ''; ?>"
                        id="complaint-submit" role="tabpanel" aria-labelledby="submit-tab">
                        <h4 class="mb-4 text-pink"><i class="fas fa-file-alt me-2"></i> Formulir Pengajuan
                            Keluhan</h4>
                        <p class="text-muted">Mohon jelaskan masalah Anda secara detail. Jika terkait pesanan, cantumkan
                            kode pesanan Anda.</p>

                        <form method="POST" action="complaint.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="submit_complaint">
                            <input type="hidden" name="current_tab" value="submit">

                            <div class="mb-3">
                                <label for="order_id" class="form-label">Terkait Pesanan (Opsional)</label>
                                <select class="form-select" id="order_id" name="order_id">
                                    <option value="">-- Pilih Kode Pesanan --</option>
                                    <?php foreach ($orders as $order): ?>
                                    <option value="<?= $order['id']; ?>">
                                        <?= htmlspecialchars($order['order_code']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subjek Komplain</label>
                                <input type="text" class="form-control" id="subject" name="subject" required
                                    maxlength="100" placeholder="Contoh: Barang Rusak / Masalah Pembayaran">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Lengkap Masalah</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required
                                    placeholder="Jelaskan kronologi dan detail masalah Anda..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="attachment" class="form-label">Bukti Pendukung (Foto/Video, Opsional)</label>
                                <input type="file" class="form-control" id="attachment" name="attachment"
                                    accept="image/*,video/*">
                                <div class="form-text">Maksimal 2MB. Format: JPG, PNG, atau MP4.</div>
                            </div>

                            <button type="submit" class="btn btn-update-profile mt-3"><i class="fas fa-paper-plane me-2"></i> Kirim Komplain</button>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $currentTab == 'history' ? 'show active' : ''; ?>"
                        id="complaint-history" role="tabpanel" aria-labelledby="history-tab">
                        <h4 class="mb-4 text-pink"><i class="fas fa-history me-2"></i> Riwayat Komplain</h4>

                        <?php if (empty($complaints)): ?>
                        <div class="alert alert-info text-center">Anda belum pernah mengajukan komplainn.</div>
                        <?php else: ?>
                        <div class="accordion" id="complaintAccordion">
                            <?php foreach ($complaints as $complaint): 
                                $statusClass = '';
                                switch ($complaint['status']) {
                                    case 'Open':
                                        $statusClass = 'bg-primary';
                                        break;
                                    case 'In Progress':
                                        $statusClass = 'bg-warning text-dark';
                                        break;
                                    case 'Resolved':
                                        $statusClass = 'bg-success';
                                        break;
                                    case 'Closed':
                                        $statusClass = 'bg-secondary';
                                        break;
                                }
                            ?>
                            <div class="accordion-item mb-2">
                                <h2 class="accordion-header" id="heading<?= $complaint['id']; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $complaint['id']; ?>" aria-expanded="false"
                                        aria-controls="collapse<?= $complaint['id']; ?>">
                                        <div class="d-flex justify-content-between w-100 pe-3">
                                            <div>
                                                <strong>#<?= $complaint['id']; ?></strong> - 
                                                <?= htmlspecialchars($complaint['subject']); ?> 
                                                <?php if($complaint['order_code'] != '-'): ?>
                                                    (Pesanan: <?= htmlspecialchars($complaint['order_code']); ?>)
                                                <?php endif; ?>
                                            </div>
                                            <span class="badge <?= $statusClass; ?> me-3">
                                                <?= htmlspecialchars($complaint['status']); ?>
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?= $complaint['id']; ?>" class="accordion-collapse collapse"
                                    aria-labelledby="heading<?= $complaint['id']; ?>"
                                    data-bs-parent="#complaintAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-1 text-muted">Tanggal Pengajuan: <?= date('d M Y H:i', strtotime($complaint['created_at'])); ?></p>
                                        <hr>
                                        <p><strong>Deskripsi:</strong></p>
                                        <p><?= nl2br(htmlspecialchars($complaint['description'])); ?></p>
                                        </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk mengaktifkan tab yang benar saat halaman dimuat atau setelah POST
        document.addEventListener('DOMContentLoaded', function () {
            const currentTabId = '<?= $currentTab; ?>-tab';
            const tabElement = document.getElementById(currentTabId);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        });

        // Fungsi untuk menjaga parameter tab di URL saat berpindah tab
        function changeTab(tabName) {
            history.pushState(null, '', `complaint.php?tab=${tabName}`);
        }
        
        // Hapus kode JS editAddressModal karena tidak relevan
    </script>
</body>

</html>