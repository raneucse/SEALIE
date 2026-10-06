<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'sealie';

const HALAMAN_LOGIN  = 'Login.php';
const HALAMAN_DAFTAR = 'Daftar.php';

// Tujuan setelah login berhasil, berdasarkan role
const TUJUAN = [
    'admin' => 'admin/index.php',
    'user'  => 'user/index.php',
];
// ====================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function connect(): mysqli
{
    static $conn = null;
    if ($conn !== null) {
        return $conn;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        error_log('Koneksi database gagal: ' . $e->getMessage());
        die('Koneksi database gagal.');
    }
    return $conn;
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Dipertahankan agar kompatibel dengan Login.php (memakai cek_data_get)
function cek_data_post($jenis)
{
    return $_POST[$jenis] ?? 0;
}

function cek_data_get($jenis)
{
    return $_GET[$jenis] ?? 0;
}

// Ambil input POST sebagai string yang sudah di-trim ('' jika tidak ada)
function input_post(string $nama): string
{
    return trim((string) ($_POST[$nama] ?? ''));
}

function register(string $user, string $email, string $pass): void
{
    $kembali = HALAMAN_DAFTAR . '?status=';

    if ($user === '' || $email === '' || $pass === '') {
        redirect($kembali . 'kosong');
    }
    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $user)) {
        redirect($kembali . 'user_salah');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect($kembali . 'email_salah');
    }
    if (strlen($pass) < 6) {
        redirect($kembali . 'pass_pendek');
    }

    $db = connect();

    // username atau email sudah dipakai?
    $cek = $db->prepare('SELECT id FROM pengguna WHERE username = ? OR email = ? LIMIT 1');
    $cek->bind_param('ss', $user, $email);
    $cek->execute();
    if ($cek->get_result()->num_rows > 0) {
        redirect($kembali . 'sudah_ada');
    }
    $cek->close();

    // role selalu 'user' untuk pendaftaran baru (tidak diambil dari form)
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
        $ins = $db->prepare("INSERT INTO pengguna (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $ins->bind_param('sss', $user, $email, $hash);
        $ins->execute();
    } catch (mysqli_sql_exception $e) {
        // 1062 = duplikat (terjadi jika kolom sudah diberi UNIQUE dan ada pendaftaran bersamaan)
        if ((int) $e->getCode() === 1062) {
            redirect($kembali . 'sudah_ada');
        }
        throw $e;
    }

    redirect(HALAMAN_LOGIN . '?status=daftar_ok');
}

function login(string $user, string $pass): void
{
    if ($user === '' || $pass === '') {
        redirect(HALAMAN_LOGIN . '?status=salah');
    }

    $db = connect();
    $stmt = $db->prepare('SELECT id, username, password, role FROM pengguna WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        redirect(HALAMAN_LOGIN . '?status=no_akun');
    }
    if (!password_verify($pass, $row['password'])) {
        redirect(HALAMAN_LOGIN . '?status=salah');
    }

    // perbarui hash jika algoritma default PHP sudah berubah
    if (password_needs_rehash($row['password'], PASSWORD_DEFAULT)) {
        $baru = password_hash($pass, PASSWORD_DEFAULT);
        $upd = $db->prepare('UPDATE pengguna SET password = ? WHERE id = ?');
        $upd->bind_param('si', $baru, $row['id']);
        $upd->execute();
    }

    session_regenerate_id(true);
    $_SESSION['sesi']     = (int) $row['id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['role']     = $row['role'];

    redirect(TUJUAN[$row['role']] ?? 'index.php');
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    redirect(HALAMAN_LOGIN . '?status=kelar');
}

// ====== Penentu aksi ======
if (cek_data_get('aksi') === 'logout') {
    logout();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = input_post('dor');

    if ($aksi === 'Log In') {
        login(input_post('username'), (string) ($_POST['password'] ?? ''));
    } elseif ($aksi === 'Daftar') {
        register(input_post('user'), input_post('email'), (string) ($_POST['pass'] ?? ''));
    }
}
