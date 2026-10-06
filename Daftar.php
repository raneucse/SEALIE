<?php
include 'process.php';

$status = cek_data_get('status');
$pesan = [
    'kosong'      => 'Semua kolom wajib diisi',
    'user_salah'  => 'Username harus 3-30 karakter (huruf, angka, titik, garis bawah, atau strip)',
    'email_salah' => 'Format email tidak valid',
    'pass_pendek' => 'Password minimal 6 karakter',
    'sudah_ada'   => 'Username atau email sudah terdaftar',
][$status] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SEALIE | Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <main class="login-page">
        <a href="index.php" class="login-logo"><span>Sea</span> Lie</a>

        <div class="login-card">
            <h1 class="login-title">Sign Up</h1>
            <p class="login-subtitle">Buat akun baru untuk mulai menggunakan SEA LIE.</p>

            <?php if ($pesan): ?>
                <div class="login-alert error" role="alert"><?= htmlspecialchars($pesan) ?></div>
                <script>history.replaceState(null, '', location.pathname);</script>
            <?php endif; ?>

            <form action="" method="post">
                <div class="login-field">
                    <label for="user">Username</label>
                    <input type="text" id="user" name="user" autocomplete="username" required autofocus
                           pattern="[A-Za-z0-9_.\-]{3,30}"
                           title="3-30 karakter: huruf, angka, titik, garis bawah, atau strip">
                    <p class="login-hint">3-30 karakter: huruf, angka, titik, garis bawah, atau strip.</p>
                </div>

                <div class="login-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>

                <div class="login-field">
                    <label for="pass">Password</label>
                    <input type="password" id="pass" name="pass" autocomplete="new-password" minlength="6" required>
                    <p class="login-hint">Minimal 6 karakter.</p>
                </div>

                <input type="submit" name="dor" value="Daftar" class="login-submit">
            </form>

            <p class="login-footer">Sudah memiliki akun?<a href="Login.php">Login</a></p>
        </div>

        <a href="index.php" class="login-back">&larr; Kembali ke Home</a>
    </main>
</body>
</html>
