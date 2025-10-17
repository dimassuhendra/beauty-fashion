<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Masuk Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style-login.css">
</head>

<body>

    <div class="login-container">
        <div class="welcome-section">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>

            <div class="content">
                <h1 class="display-4 fw-bold">Selamat Datang!</h1>
                <p class="lead mb-4">Masuk untuk melanjutkan pengalaman belanja terbaik Anda.</p>
                <small class="text-white-50">www.beautyfashion.com</small>
            </div>
        </div>

        <div class="login-form-section">
            <h5 class="text-start mb-1">Halo !</h5>
            <h3 class="text-pink fw-bold mb-4">Selamat Pagi</h3>
            <h4 class="mb-4">Masuk ke Akun Anda</h4>

            <form>
                <div class="mb-3">
                    <label for="emailInput" class="form-label visually-hidden">Email Address</label>
                    <input type="email" class="form-control form-control-lg" id="emailInput" placeholder="Alamat Email"
                        required>
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label visually-hidden">Password</label>
                    <input type="password" class="form-control form-control-lg" id="passwordInput"
                        placeholder="Password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Ingat Saya
                        </label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password text-decoration-none">Lupa Password?</a>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-submit">MASUK</button>
                </div>

                <div class="text-center create-account">
                    Belum punya akun? <a href="signup.php" class="text-pink fw-bold">Daftar di sini</a>
                </div>
                <div class="text-center create-account">
                    <a href="index.php" class="text-pink fw-bold">Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>