<?php 
require_once 'db_connect.php'; 

// Cek apakah ada notifikasi error/sukses dari proses_register.php (jika menggunakan redirect)
$message = '';
$message_type = '';

if (isset($_GET['status']) && isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $message_type = ($_GET['status'] == 'success') ? 'success' : 'danger';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Daftar Akun Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style-login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="signup-container">
        <div class="welcome-section">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>

            <div class="content">
                <h1 class="display-4 fw-bold">Jadilah Bagian Kami !</h1>
                <p class="lead mb-4">Daftar sekarang dan nikmati penawaran eksklusif khusus member baru.</p>
                <small class="text-white-50">#BeautyFashionMember</small>
            </div>
        </div>

        <div class="signup-form-section">
            <h5 class="text-start mb-1">Daftar Akun</h5>
            <h3 class="text-pink fw-bold mb-4">Buat Akun Baru</h3>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <form action="proses/proses_signup.php" method="POST">
                <div class="mb-3">
                    <label for="fullName" class="form-label visually-hidden">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-lg" id="fullName" name="full_name"
                        placeholder="Nama Lengkap" required>
                </div>
                <div class="mb-3">
                    <label for="phoneNumber" class="form-label visually-hidden">Nomor Telepon</label>
                    <input type="text" class="form-control form-control-lg" id="phoneNumber" name="phone_number"
                        placeholder="Nomor Telepon (Opsional)">
                </div>
                <div class="mb-3">
                    <label for="emailInput" class="form-label visually-hidden">Alamat Email</label>
                    <input type="email" class="form-control form-control-lg" id="emailInput" name="email"
                        placeholder="Alamat Email" required>
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label visually-hidden">Password</label>
                    <input type="password" class="form-control form-control-lg" id="passwordInput" name="password"
                        placeholder="Buat Password" required>
                </div>
                <div class="mb-4">
                    <label for="confirmPasswordInput" class="form-label visually-hidden">Konfirmasi Password</label>
                    <input type="password" class="form-control form-control-lg" id="confirmPasswordInput"
                        name="confirm_password" placeholder="Konfirmasi Password" required>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" name="register_submit" class="btn btn-submit">DAFTAR SEKARANG</button>
                </div>

                <div class="text-center login-link">
                    Sudah punya akun? <a href="login.php" class="text-pink fw-bold">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>