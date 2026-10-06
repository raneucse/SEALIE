<?php
include 'process.php';

$status = cek_data_get('status');
$pesan = [
    'salah'     => ['error',   'Username atau password salah'],
    'no_akun'   => ['error',   'Akun tidak ditemukan, silakan buat akun terlebih dahulu'],
    'kelar'     => ['success', 'Anda telah logout'],
    'daftar_ok' => ['success', 'Akun berhasil dibuat, silakan login'],
][$status] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SEALIE | Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <main class="login-page">
        <a href="index.php" class="login-logo"><span>Sea</span> Lie</a>

        <div class="login-card">
            <h1 class="login-title">Login</h1>
            <p class="login-subtitle">Masuk untuk melanjutkan ke akun Anda.</p>

            <?php if ($pesan): ?>
                <div class="login-alert <?= $pesan[0] ?>" role="alert"><?= htmlspecialchars($pesan[1]) ?></div>
                <script>history.replaceState(null, '', location.pathname);</script>
            <?php endif; ?>

            <form action="" method="post">
                <div class="login-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username" required autofocus>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>

                <input type="submit" name="dor" value="Log In" class="login-submit">
            </form>

            <p class="login-footer">Belum memiliki akun?<a href="daftar.php">Daftar</a></p>
        </div>

        <a href="index.php" class="login-back">&larr; Kembali ke Home</a>
    </main>
</body>
</html>
