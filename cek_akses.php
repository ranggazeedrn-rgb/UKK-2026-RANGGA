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
$wali_kelas_kelas_nama = [];

if ($role === 'Guru' && isset($_SESSION['user_id']) && isset($koneksi)) {
    $uid = (int)$_SESSION['user_id'];
    $q_wali_kelas = @mysqli_query($koneksi, "
        SELECT wk.kelas_id, k.nama AS nama_kelas
        FROM t_wali_kelas wk
        INNER JOIN t_guru g ON wk.guru_id = g.id
        LEFT JOIN t_kelas k ON wk.kelas_id = k.id
        WHERE g.user_id = '$uid' AND wk.status_aktif = 1
    ");
    if ($q_wali_kelas) {
        while ($wk = mysqli_fetch_assoc($q_wali_kelas)) {
            $wali_kelas_kelas_ids[] = (int)$wk['kelas_id'];
            if (!empty($wk['nama_kelas'])) {
                $wali_kelas_kelas_nama[] = $wk['nama_kelas'];
            }
        }
        $is_wali_kelas = count($wali_kelas_kelas_ids) > 0;
    }
}

// 4. Data guru (t_guru) milik user yang sedang login
//    Dipakai untuk mencatat siapa pelapor pelanggaran (nama_guru / guru_id).
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

// 5. Pengalihan dari index.php
if ($current_file === 'index.php') {
    header("Location: dashboard.php");
    exit;
}

// 6. Hak Akses
//    Mengikuti use case diagram (ROLE.jpg):
//    - Admin    : seluruh fitur (Data Master, Plotting, Aktivitas, Laporan, Cetak/Export)
//    - Guru     : Catatan Pelanggaran, Tindakan, Riwayat, Rekap Poin, Laporan, Dashboard
//    - Wali Kelas : mewarisi seluruh hak akses Guru
$akses_halaman = [
    // Aktivitas: dapat diakses oleh Admin, Guru, dan Wali Kelas
    'catatan_pelanggaran.php' => ['Admin', 'Guru'],
    'riwayat.php'             => ['Admin', 'Guru'],
    'rekap_point.php'         => ['Admin', 'Guru'],
    'edit_tindakan.php'       => ['Admin', 'Guru'],
    'tindakan.php'            => ['Admin', 'Guru'],
    // Laporan: Admin & Wali Kelas (Guru biasa dibatasi oleh isi data laporan)
    'laporan.php'             => ['Admin', 'Guru'],
    // Dashboard untuk semua role
    'dashboard.php'           => ['Admin', 'Guru'],
    // Data Master (khusus Admin)
    'kelola_siswa.php' => ['Admin'],
    'tambah_siswa.php' => ['Admin'],
    'edit_siswa.php' => ['Admin'],
    'kelola_guru.php' => ['Admin'],
    'tambah_guru.php' => ['Admin'],
    'edit_guru.php' => ['Admin'],
    'kelola_kelas.php' => ['Admin'],
    'tambah_kelas.php' => ['Admin'],
    'edit_kelas.php' => ['Admin'],
    'kelola_tahun_ajaran.php' => ['Admin'],
    'tambah_tahun_ajaran.php' => ['Admin'],
    'edit_tahun_ajaran.php' => ['Admin'],
    'tambah_user.php' => ['Admin'],
    'penempatan_siswa.php' => ['Admin'],
    'walikelas.php' => ['Admin'],
    'kelola_jenis_pelanggaran.php' => ['Admin'],
    'kelola_kategori_pelanggaran.php' => ['Admin'],
    // Cetak / Export (khusus Admin)
    'cetak_export.php' => ['Admin'],
    // About Me (khusus Admin)
    'aboutme.php' => ['Admin']
];

// 7. Validasi Akses Halaman
if (array_key_exists($current_file, $akses_halaman)) {
    if (!in_array($role, $akses_halaman[$current_file])) {
        if ($current_file !== 'dashboard.php') {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Akses ditolak. Halaman ini hanya dapat diakses oleh ' . implode(' / ', $akses_halaman[$current_file]) . '.'
            ];
            header("Location: dashboard.php");
            exit;
        }
    }
}

// 8. Halaman Laporan untuk role Guru
//    Halaman tetap dapat dibuka oleh Guru (Guru biasa maupun Wali Kelas).
//    Pembatasan hanya berlaku pada ISI data laporan (lihat laporan.php):
//    isi laporan hanya ditampilkan bagi Wali Kelas.
//    (Catatan, Tindakan, Laporan, Riwayat, Rekap Poin = hak akses Admin, Guru & Wali Kelas)

// 9. Scope data laporan/riwayat/rekap poin
//     - Admin      : seluruh kelas
//     - Wali Kelas : seluruh kelas (boleh melihat kelas lain beserta
//                    pelanggaran yang dilakukan siswa kelas lain)
//     - Guru biasa : hanya kelas yang diampu (biasanya tidak ada kelas)
if (!function_exists('scope_kelas_laporan')) {
    function scope_kelas_laporan($role, array $kelas_ids)
    {
        if ($role !== 'Guru') {
            return '1 = 1';
        }
        if (count($kelas_ids) > 0) {
            // Wali kelas: bebas melihat kelas lain
            return '1 = 1';
        }
        return '1 = 0';
    }
}

// 10. Scope data untuk role Guru (catatan, tindakan, riwayat, rekap)
//     Semua guru (termasuk Wali Kelas) dapat MELIHAT seluruh catatan dari guru mana pun.
//     Pembatasan tetap pada TINDAKAN: mengubah status hanya boleh oleh guru
//     yang mencatat pelanggarannya (dicek lewat helper boleh_tindak()).
if (!function_exists('scope_data_guru')) {
    function scope_data_guru($role, $is_wali_kelas, array $kelas_ids, $guru_id_login = 0, $alias = 'ps')
    {
        return '1 = 1';
    }
}

// 11b. Hak menindak / mengubah status tindakan:
//      Hanya guru yang MENCATAT pelanggaran (guru A) yang boleh mengubah status tindakannya.
//      Guru lain hanya dapat melihat datanya. Admin bebas.
//      Catatan tanpa guru_id dicatat oleh Admin, jadi hanya Admin yang boleh mengubahnya.
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

// 11. Helper tahun ajaran
//     Dipakai dashboard.php, laporan.php, penempatan_siswa.php, dll.
if (!function_exists('tahun_ajaran_aktif')) {
    function tahun_ajaran_aktif($koneksi)
    {
        try {
            $q = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC LIMIT 1");
            return $q ? mysqli_fetch_assoc($q) : null;
        } catch (mysqli_sql_exception $e) {
            // Dashboard tetap tampil walau data tahun ajaran bermasalah
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
?>
