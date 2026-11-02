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

// 2. Normalisasi Role (hanya Admin & Guru, wali kelas ditentukan lewat penetapan)
$raw_role = $_SESSION['role'] ?? $_SESSION['level'] ?? '';
$role_lower = strtolower(trim($raw_role));

if ($role_lower === 'admin') {
    $role = 'Admin';
} else {
    $role = 'Guru';
}

// 3. Status Wali Kelas ditentukan oleh penetapan di t_wali_kelas, bukan role
$is_wali_kelas = false;
$wali_kelas_kelas_ids = [];

if ($role === 'Guru' && isset($_SESSION['user_id']) && isset($koneksi)) {
    $uid = (int)$_SESSION['user_id'];
    $q_wali_kelas = @mysqli_query($koneksi, "
        SELECT wk.kelas_id
        FROM t_wali_kelas wk
        INNER JOIN t_guru g ON wk.guru_id = g.id
        WHERE g.user_id = '$uid' AND wk.status_aktif = 1
    ");
    if ($q_wali_kelas) {
        while ($wk = mysqli_fetch_assoc($q_wali_kelas)) {
            $wali_kelas_kelas_ids[] = (int)$wk['kelas_id'];
        }
        $is_wali_kelas = count($wali_kelas_kelas_ids) > 0;
    }
}

// 4. Pengalihan dari index.php
if ($current_file === 'index.php') {
    header("Location: dashboard.php");
    exit;
}

// 5. Hak Akses
$akses_halaman = [
    'catatan_pelanggaran.php' => ['Admin', 'Guru'],
    'laporan.php' => ['Admin', 'Guru'],
    'riwayat.php' => ['Admin', 'Guru'],
    'dashboard.php' => ['Admin', 'Guru'],
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
    'rekap_point.php' => ['Admin', 'Guru']
];

// 6. Validasi Akses Halaman
if (array_key_exists($current_file, $akses_halaman)) {
    if (!in_array($role, $akses_halaman[$current_file])) {
        if ($current_file !== 'dashboard.php') {
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>