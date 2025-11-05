<nav class="navbar navbar-expand-lg navbar-pink sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">BeautyFashion Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders.php"><i class="fas fa-box-open"></i> Pesanan Anda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                </li>
            </ul>
            <div class="d-flex">
                <button class="btn btn-logout me-2" onclick="handleLogout()"><i class="fas fa-sign-out-alt"></i>
                    Keluar</button>
            </div>
        </div>
    </div>
</nav>