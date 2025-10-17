<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Daftar Akun Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style-login.css">
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

            <form>
                <div class="mb-3">
                    <label for="nameInput" class="form-label visually-hidden">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-lg" id="nameInput" placeholder="Nama Lengkap"
                        required>
                </div>
                <div class="mb-3">
                    <label for="nameInput" class="form-label visually-hidden">Alamat Pengiriman</label>
                    <input type="text" class="form-control form-control-lg" id="nameInput" placeholder="Alamat Lengkap"
                        required>
                </div>
                <div class="mb-3">
                    <label for="emailInput" class="form-label visually-hidden">Email Address</label>
                    <input type="email" class="form-control form-control-lg" id="emailInput" placeholder="Alamat Email"
                        required>
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label visually-hidden">Password</label>
                    <input type="password" class="form-control form-control-lg" id="passwordInput"
                        placeholder="Buat Password" required>
                </div>
                <div class="mb-4">
                    <label for="confirmPasswordInput" class="form-label visually-hidden">Konfirmasi Password</label>
                    <input type="password" class="form-control form-control-lg" id="confirmPasswordInput"
                        placeholder="Konfirmasi Password" required>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-submit">DAFTAR SEKARANG</button>
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