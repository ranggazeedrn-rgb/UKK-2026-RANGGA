<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_file = basename($_SERVER['PHP_SELF']);

// 1. Pengecekan Login (Memeriksa semua variabel session yang mungkin diset saat login)
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['login']) || isset($_SESSION['loggedin']);

if ($current_file === 'login.php') {
    if ($is_logged_in) {
        header("Location: dashboard.php");
        exit;
    }
    return;
}

if (!$is_logged_in) {
    header("Location: login.php");
    exit;
}

// 2. Normalisasi Role
$raw_role = $_SESSION['role'] ?? $_SESSION['level'] ?? 'Admin';
$role_lower = strtolower($raw_role);

if ($role_lower === 'admin') {
    $role = 'Admin';
} elseif ($role_lower === 'guru') {
    $role = 'Guru';
} elseif ($role_lower === 'wali kelas' || $role_lower === 'walikelas') {
    $role = 'Wali Kelas';
} else {
    $role = 'Admin';
}

// 3. Pengalihan dari index.php
if ($current_file === 'index.php') {
    header("Location: dashboard.php");
    exit;
}

// 4. Hak Akses
$akses_halaman = [
    'catatan_pelanggaran.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'tindakan.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'laporan.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'riwayat.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'logout.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'dashboard.php' => ['Admin', 'Guru', 'Wali Kelas'],
    'kelola_siswa.php' => ['Admin'],
    'kelola_guru.php' => ['Admin'],
    'kelola_kelas.php' => ['Admin'],
    'kelola_tahun_ajaran.php' => ['Admin'],
    'tambah_user.php' => ['Admin'],
    'penempatan_siswa.php' => ['Admin'],
    'walikelas.php' => ['Admin'],
    'kelola_jenis_pelanggaran.php' => ['Admin'],
    'kelola_kategori_pelanggaran.php' => ['Admin'],
    'cetak_export.php' => ['Admin'],
    'rekap_point.php' => ['Admin', 'Guru', 'Wali Kelas']
];

// 5. Validasi Akses Halaman
if (array_key_exists($current_file, $akses_halaman)) {
    if (!in_array($role, $akses_halaman[$current_file])) {
        if ($current_file !== 'dashboard.php') {
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>