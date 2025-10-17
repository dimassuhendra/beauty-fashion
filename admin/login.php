<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion Admin - Masuk Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/beauty-fashion/css/style-login.css">
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
                <h1 class="display-4 fw-bold">Area Administrator</h1>
                <small class="text-white-50">Masuk ke pengelolaan sistem Beauty Fashion</small>
            </div>
        </div>

        <div class="login-form-section">
            <h5 class="text-start mb-1">Selamat Datang,</h5>
            <h3 class="text-pink fw-bold mb-4">Admin! 👋</h3>
            <h4 class="mb-4">Masuk ke Akun Anda</h4>

            <form action="admin_dashboard.php" method="POST">
                <div class="mb-3">
                    <label for="usernameInput" class="form-label visually-hidden">Username Admin</label>
                    <input type="text" class="form-control form-control-lg" id="usernameInput"
                        placeholder="Username Admin" required name="username">
                </div>
                <div class="mb-3">
                    <label for="passwordInput" class="form-label visually-hidden">Password</label>
                    <input type="password" class="form-control form-control-lg" id="passwordInput"
                        placeholder="Password" required name="password">
                </div>

                <div class="d-flex justify-content-start align-items-center mb-5">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Ingat Username Saya
                        </label>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-submit">MASUK DASHBOARD</button>
                </div>

                <div class="text-center create-account">
                    <a href="/beauty-fashion/index.php" class="text-pink fw-bold">Kunjungi Halaman Pengunjung</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>