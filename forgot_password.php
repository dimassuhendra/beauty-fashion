<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Fashion - Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    /* CSS Kustom untuk nuansa Pink - Sama seperti halaman akun lainnya */
    :root {
        --bs-pink-primary: #ff69b4;
        /* Hot Pink */
        --bs-pink-secondary: #fce4ec;
        /* Light Pink */
        --bs-gradient-start: #ff69b4;
        /* Pink */
        --bs-gradient-end: #8a2be2;
        /* Blue Violet */
        --bs-text-dark: #343a40;
        --bs-input-focus-border: #ff69b4;
        --bs-link-color: #ff69b4;
    }

    body {
        font-family: Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fce4ec;
        background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ff69b4' fill-opacity='0.25' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .fp-container {
        display: flex;
        width: 100%;
        max-width: 900px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    /* --- Bagian Kiri (Sama seperti halaman Login/Signup) --- */
    .welcome-section {
        flex: 1;
        background: linear-gradient(135deg, var(--bs-gradient-start), var(--bs-gradient-end));
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        text-align: center;
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 150px;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path fill="%23fff" fill-opacity="1" d="M0,192L48,176C96,160,192,128,288,101.3C384,75,480,53,576,80C672,107,768,181,864,192C960,203,1056,149,1152,144C1248,139,1344,181,1392,202.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        background-position: bottom;
        transform: translateY(20px);
        z-index: 1;
    }

    .welcome-section .content {
        position: relative;
        z-index: 2;
    }

    .circle {
        position: absolute;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        pointer-events: none;
    }

    .circle:nth-child(1) {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 15%;
    }

    .circle:nth-child(2) {
        width: 60px;
        height: 60px;
        top: 30%;
        right: 10%;
    }

    .circle:nth-child(3) {
        width: 80px;
        height: 80px;
        bottom: 20%;
        left: 25%;
    }

    .circle:nth-child(4) {
        width: 120px;
        height: 120px;
        top: 50%;
        left: -10%;
    }

    .circle:nth-child(5) {
        width: 70px;
        height: 70px;
        bottom: 5%;
        right: 20%;
    }

    /* --- Bagian Kanan (Form Lupa Password) --- */
    .fp-form-section {
        flex: 1;
        background-color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .fp-form-section .text-pink {
        color: var(--bs-pink-primary) !important;
    }

    .form-control:focus {
        border-color: var(--bs-input-focus-border);
        box-shadow: 0 0 0 0.25rem rgba(255, 105, 180, 0.25);
    }

    .btn-submit {
        background: linear-gradient(to right, var(--bs-pink-primary), var(--bs-gradient-end));
        border: none;
        color: white;
        padding: 12px 0;
        font-size: 1.1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        opacity: 0.9;
        color: white;
    }

    a {
        color: var(--bs-link-color);
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
        color: var(--bs-pink-primary);
    }

    .back-link {
        font-size: 0.9rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fp-container {
            flex-direction: column;
            max-width: 500px;
        }

        .welcome-section {
            padding: 30px;
            min-height: 200px;
            border-bottom-left-radius: 0;
            border-top-right-radius: 15px;
        }

        .welcome-section::before {
            display: none;
        }

        .fp-form-section {
            padding: 30px;
        }
    }
    </style>
</head>

<body>

    <div class="fp-container">
        <div class="welcome-section">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>

            <div class="content">
                <h1 class="display-4 fw-bold">Tenang, Kami Bantu !</h1>
                <p class="lead mb-4">Kami akan segera mengirimkan tautan pemulihan ke alamat email Anda.</p>
                <small class="text-white-50">Amankan Akun Anda</small>
            </div>
        </div>

        <div class="fp-form-section">
            <h5 class="text-start mb-1">Butuh Bantuan?</h5>
            <h3 class="text-pink fw-bold mb-4">Reset Password</h3>

            <p class="mb-4 text-muted">Masukkan alamat email yang terdaftar untuk menerima tautan pemulihan.</p>

            <form>
                <div class="mb-5">
                    <label for="emailInput" class="form-label visually-hidden">Alamat Email</label>
                    <input type="email" class="form-control form-control-lg" id="emailInput"
                        placeholder="Alamat Email Anda" required>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-submit">KIRIM TAUTAN RESET</button>
                </div>

                <div class="text-center back-link">
                    <a href="login.php" class="text-pink fw-bold">← Kembali ke Halaman Login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>