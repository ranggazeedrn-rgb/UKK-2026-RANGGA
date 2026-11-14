<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah cache browser: setelah logout, tombol mundur wajib meminta ulang ke server
// sehingga pengecekan login di bawah tetap berlaku (bukan salinan cache).
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

$current_file = basename($_SERVER['PHP_SELF']);

// 1. Cek login
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

// 2. Normalisasi role (wali kelas ditentukan lewat penetapan, bukan role)
$role = strtolower(trim($_SESSION['role'] ?? $_SESSION['level'] ?? '')) === 'admin' ? 'Admin' : 'Guru';

// 3. Status wali kelas dari t_wali_kelas: session user -> t_guru.user_id -> wk.guru_id
$wali_kelas_kelas_ids = [];
$wali_kelas_kelas_nama = [];
if ($role === 'Guru' && isset($_SESSION['user_id']) && isset($koneksi)) {
    $q_wali_kelas = @mysqli_query($koneksi, "
        SELECT wk.kelas_id, k.nama AS nama_kelas
        FROM t_wali_kelas wk
        INNER JOIN t_guru g ON wk.guru_id = g.id
        LEFT JOIN t_kelas k ON wk.kelas_id = k.id
        WHERE g.user_id = '" . (int)$_SESSION['user_id'] . "' AND wk.status_aktif = 1
    ");
    while ($q_wali_kelas && $wk = mysqli_fetch_assoc($q_wali_kelas)) {
        $wali_kelas_kelas_ids[] = (int)$wk['kelas_id'];
        if (!empty($wk['nama_kelas'])) {
            $wali_kelas_kelas_nama[] = $wk['nama_kelas'];
        }
    }
}
$is_wali_kelas = count($wali_kelas_kelas_ids) > 0;

// 4. Label tampilan: guru yang ditunjuk wali kelas ditampilkan sebagai "Wali Kelas"
$role_tampil = $is_wali_kelas ? 'Wali Kelas' : $role;

// 5. Data guru milik user login (dipakai sebagai pencatat pelanggaran)
$guru_id_login = null;
$guru_nama_login = '';
if (isset($_SESSION['user_id']) && isset($koneksi)) {
    $q_guru_login = @mysqli_query($koneksi, "
        SELECT id, nama FROM t_guru WHERE user_id = " . (int)$_SESSION['user_id'] . " LIMIT 1
    ");
    if ($q_guru_login && mysqli_num_rows($q_guru_login) > 0) {
        $row_guru_login = mysqli_fetch_assoc($q_guru_login);
        $guru_id_login = (int)$row_guru_login['id'];
        $guru_nama_login = (string)$row_guru_login['nama'];
    }
}

// 6. Pengalihan index.php
if ($current_file === 'index.php') {
    header("Location: dashboard.php");
    exit;
}

// 7. Hak akses per halaman: kunci = nama file, nilai = role yang boleh mengakses
//    Admin: seluruh fitur. Guru: Dashboard, Catat Pelanggaran, Tindakan, Rekap Poin (+ edit_tindakan).
//    Wali Kelas mewarisi hak akses Guru ditambah Laporan dan Riwayat.
$akses_halaman = [
    'catatan_pelanggaran.php' => ['Guru'],
    'riwayat.php'             => ['Admin', 'Guru'],
    'rekap_point.php'         => ['Admin', 'Guru'],
    'edit_tindakan.php'       => ['Admin', 'Guru'],
    'tindakan.php'            => ['Admin', 'Guru'],
    'laporan.php'             => ['Admin', 'Guru'],
    'dashboard.php'           => ['Admin', 'Guru'],
    'kelola_siswa.php'        => ['Admin'],
    'tambah_siswa.php'        => ['Admin'],
    'edit_siswa.php'          => ['Admin'],
    'kelola_guru.php'         => ['Admin'],
    'tambah_guru.php'         => ['Admin'],
    'edit_guru.php'           => ['Admin'],
    'kelola_kelas.php'        => ['Admin'],
    'tambah_kelas.php'        => ['Admin'],
    'edit_kelas.php'          => ['Admin'],
    'kelola_tahun_ajaran.php' => ['Admin'],
    'tambah_tahun_ajaran.php' => ['Admin'],
    'edit_tahun_ajaran.php'   => ['Admin'],
    'tambah_user.php'         => ['Admin'],
    'kelola_user.php'         => ['Admin'],
    'penempatan_siswa.php'    => ['Admin'],
    'walikelas.php'           => ['Admin'],
    'kelola_jenis_pelanggaran.php' => ['Admin'],
    'kelola_kategori_pelanggaran.php' => ['Admin'],
    'cetak_export.php'        => ['Admin'],
    'aboutme.php'             => ['Admin']
];

// Validasi akses halaman
if (array_key_exists($current_file, $akses_halaman) && !in_array($role, $akses_halaman[$current_file])) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Akses ditolak. Halaman ini hanya dapat diakses oleh ' . implode(' / ', $akses_halaman[$current_file]) . '.'
    ];
    header("Location: dashboard.php");
    exit;
}

// 8. Halaman khusus Admin & Wali Kelas: Guru biasa (non-wali kelas) hanya boleh
//    mengakses Dashboard, Catat Pelanggaran, Tindakan, dan Rekap Poin.
//    Laporan & Riwayat dibatasi untuk Admin & Wali Kelas.
if (in_array($current_file, ['laporan.php', 'riwayat.php'], true) && $role === 'Guru' && !$is_wali_kelas) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Akses ditolak. Halaman ini hanya dapat diakses oleh Guru yang ditunjuk sebagai Wali Kelas.'
    ];
    header("Location: dashboard.php");
    exit;
}

// 9. Scope data laporan: Admin seluruh kelas, Wali Kelas hanya siswa kelasnya,
//    Guru biasa tidak ada data. Mengembalikan string kondisi SQL (alias tabel: ps).
if (!function_exists('scope_kelas_laporan')) {
    function scope_kelas_laporan($role, array $kelas_ids)
    {
        if ($role !== 'Guru') {
            return '1 = 1';
        }
        if (count($kelas_ids) > 0) {
            // ps.kelas_id bisa kosong; fallback ke penempatan terakhir di t_kelas_siswa
            $ids = implode(',', array_map('intval', $kelas_ids));
            return "COALESCE(ps.kelas_id, (SELECT ks.kelas_id FROM t_kelas_siswa ks WHERE ks.siswa_id = ps.siswa_id ORDER BY ks.id DESC LIMIT 1)) IN ($ids)";
        }
        return '1 = 0';
    }
}

// 10. Scope data guru (catatan/tindakan/riwayat/rekap): semua guru melihat semua
//     catatan; pembatasan perubahan status ditangani boleh_tindak().
if (!function_exists('scope_data_guru')) {
    function scope_data_guru($role, $is_wali_kelas, array $kelas_ids, $guru_id_login = 0, $alias = 'ps')
    {
        return '1 = 1';
    }
}

// 11. Hak mengubah status tindakan: hanya guru yang mencatat pelanggarannya.
//     Catatan tanpa guru_id dicatat Admin, jadi hanya Admin yang boleh.
if (!function_exists('boleh_tindak')) {
    function boleh_tindak($ps_guru_id)
    {
        global $role, $guru_id_login;
        if ($role === 'Admin') {
            return true;
        }
        return $guru_id_login !== null && (int)$ps_guru_id === (int)$guru_id_login;
    }
}

// 12. Helper tahun ajaran (dipakai dashboard, laporan, penempatan, dll.)
if (!function_exists('tahun_ajaran_aktif')) {
    function tahun_ajaran_aktif($koneksi)
    {
        try {
            $q = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC LIMIT 1");
            return $q ? mysqli_fetch_assoc($q) : null;
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }
}

if (!function_exists('tahun_ajaran_aktif_id')) {
    function tahun_ajaran_aktif_id($koneksi)
    {
        $ta = tahun_ajaran_aktif($koneksi);
        return $ta ? (int)$ta['id'] : 0;
    }
}

if (!function_exists('nama_tahun_ajaran')) {
    function nama_tahun_ajaran($ta, $semester_default = null)
    {
        if (empty($ta)) {
            return '-';
        }
        $nama = $ta['nama'] ?? $ta['tahun_ajaran'] ?? $ta['tahun'] ?? '-';
        $semester = $ta['semester'] ?? $semester_default;
        if (!empty($semester)) {
            $nama .= ' (' . ucfirst($semester) . ')';
        }
        return $nama;
    }
}

if (!function_exists('tahun_ajaran_pilihan')) {
    function tahun_ajaran_pilihan($koneksi)
    {
        return mysqli_query($koneksi, "SELECT id, nama, semester FROM t_tahun_ajaran ORDER BY id DESC");
    }
}

// 13. Validasi format tanggal Y-m-d
if (!function_exists('valid_tanggal_filter')) {
    function valid_tanggal_filter($tanggal)
    {
        if (!is_string($tanggal) || $tanggal === '') {
            return '';
        }
        $d = DateTime::createFromFormat('Y-m-d', $tanggal);
        return ($d && $d->format('Y-m-d') === $tanggal) ? $tanggal : '';
    }
}
?>
