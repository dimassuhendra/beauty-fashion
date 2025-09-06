<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?php echo time(); ?>">
</head>

<body>

    <div class="form-container">
        <h2>Login Admin</h2>
        <form action="login_process.php" method="POST">
            <label for="username">Username atau Email</label>
            <input type="text" id="username" name="username_email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Masuk</button>
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </form>
    </div>

</body>

</html>