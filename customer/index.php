<?php
session_start();

// --- PANGGIL FILE KONEKSI DATABASE ---
// Asumsi: File ini akan membuat variabel koneksi, misalnya $conn
include '../db_connect.php';
include 'proses/get_index.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer - Beauty Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* |--------------------------------------------------------------------------
| A. CSS ROOT, GLOBAL & UTILITY
|--------------------------------------------------------------------------
*/

        /* --- 1. CSS ROOT VARIABLES (Soft Minimalist Pink) --- */
        :root {
            --pink-brand: #ff69b4;
            /* Hot Pink (Aksen Utama) */
            --pink-subtle: #ffc2d1;
            /* Pink Lembut untuk Background dan Outline */
            --pink-lightest: #fff0f5;
            /* Background Utama Super Ringan */
            --text-primary: #4a4a4a;
            /* Teks Abu-abu Tua */
            --text-heading: #c2185b;
            /* Dark Pink untuk Judul */
            --shadow-soft: 0 6px 20px rgba(0, 0, 0, 0.05);
            /* Bayangan Sangat Halus */
            --transition-fast: all 0.3s ease;
        }

        /* --- 2. GLOBAL & UTILITY --- */
        html {
            height: 100%;
        }

        body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            padding-top: 70px;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            background-color: var(--pink-lightest);
            /* Background paling ringan */
            color: var(--text-primary);
        }

        main {
            flex: 1 0 auto;
        }

        /* --- C. HALAMAN DASHBOARD (INDEX.PHP) - Kartu Statistik Premium --- */
        .dashboard-header {
            margin-bottom: 4rem;
        }

        .welcome-message {
            color: var(--text-heading);
            font-weight: 700;
        }

        /* --- 1. Statistik Cards (Gaya 'Crystal' / Premium) --- */
        .stat-card {
            background-color: white;
            border: 1px solid var(--pink-subtle);
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            padding: 1.5rem;
            transition: var(--transition-fast);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card:hover {
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.1);
            transform: translateY(-4px);
        }

        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--pink-subtle);
            /* Background lingkaran ikon */
            color: var(--pink-brand);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--pink-brand);
            line-height: 1;
        }

        .stat-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            opacity: 0.8;
            margin-top: 0.5rem;
        }

        /* --- 2. Activity & Promo Cards --- */
        .panel-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow-soft);
            padding: 2rem;
            height: 100%;
        }

        .panel-title {
            color: var(--text-heading);
            font-weight: 700;
            font-size: 1.5rem;
        }

        /* List Pesanan */
        .list-group-flush>.list-group-item {
            border-color: #f7e0e5;
            padding: 1rem 0;
        }

        /* Badge Status (Custom Pink) */
        .badge-custom-pink {
            background-color: var(--pink-brand);
            color: white;
            font-weight: 500;
            padding: 0.5em 1em;
            border-radius: 50rem;
        }

        /* Tombol Aksi */
        .btn-pink-primary {
            background-color: var(--pink-brand);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: var(--transition-fast);
        }

        .btn-pink-primary:hover {
            background-color: var(--text-heading);
            color: white;
        }

        /* Promo Card Style */
        .promo-card {
            background: linear-gradient(135deg, var(--pink-subtle) 0%, var(--pink-lightest) 100%);
            text-align: center;
            border: none;
        }

        .promo-card .promo-tagline {
            font-weight: 600;
            color: var(--text-heading);
            font-size: 1.25rem;
        }

        .promo-card p {
            color: var(--text-primary);
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>

    <main class="py-5">
        <div class="container">
            <header class="dashboard-header text-center">
                <h1 class="welcome-message display-4">
                    Selamat Datang, <?= htmlspecialchars($customerData['full_name']); ?>!
                </h1>
                <p class="text-muted lead">Ringkasan cepat akun dan aktivitas terkini Anda.</p>
            </header>

            <div class="row g-4 mb-5">
                <?php foreach ($stats as $stat): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="stat-icon-wrapper">
                                    <i class="fas <?= $stat['icon']; ?>"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="stat-value"><?= $stat['value']; ?></div>
                                <div class="stat-title"><?= $stat['title']; ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="panel-card">
                        <h2 class="panel-title mb-4">
                            <i class="fas fa-box-open me-2"></i> Pesanan Terbaru
                        </h2>

                        <ul class="list-group list-group-flush">
                            <?php if (!empty($recentOrders)): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <?php
                                    // Menentukan warna badge khusus untuk status
                                    $badgeClass = 'badge-custom-pink'; // Default custom pink
                                    switch ($order['order_status']) {
                                        case 'Completed':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'Shipped':
                                            $badgeClass = 'bg-primary';
                                            break;
                                        case 'Processing':
                                            $badgeClass = 'bg-info text-dark';
                                            break;
                                        case 'Pending Payment':
                                            $badgeClass = 'bg-warning text-dark';
                                            break;
                                        case 'Cancelled':
                                            $badgeClass = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold me-2">#<?= htmlspecialchars($order['order_code']); ?></span>
                                            <small class="text-muted">
                                                <i
                                                    class="far fa-clock me-1"></i><?= date('d M Y', strtotime($order['order_date'])); ?>
                                            </small>
                                        </div>
                                        <span
                                            class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($order['order_status']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-center text-muted p-4">
                                    <i class="fas fa-info-circle me-1"></i> Tidak ada pesanan terbaru.
                                </li>
                            <?php endif; ?>
                        </ul>

                        <a href="orders.php" class="btn btn-pink-primary mt-4 align-self-start">
                            Lihat Riwayat Lengkap <i class="fas fa-chevron-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="panel-card promo-card">
                        <h3 class="promo-tagline mb-3">
                            <i class="fas fa-gem me-1"></i> Eksklusif untuk Anda!
                        </h3>
                        <p class="mb-4">
                            Gunakan kode **PINKLOVE** untuk Diskon 20% khusus kategori Skincare Pink. Promo terbatas!
                        </p>
                        <a href="products.php" class="btn btn-pink-primary w-100">
                            Ambil Promonya Sekarang
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <?php include '../footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulasikan JavaScript untuk tombol logout
        function handleLogout() {
            if (confirm("Anda yakin ingin keluar dari sesi ini?")) {
                // Di sini Anda akan mengarahkan user ke halaman logout:
                window.location.href = 'proses/proses_logout.php';
            }
        }
    </script>
</body>

</html>