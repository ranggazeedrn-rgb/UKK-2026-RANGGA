<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$current_role = $role_tampil;
$user_name = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Pengguna';

// Data kelas yang diampui (jika user ditunjuk sebagai wali kelas melalui Kelola Wali Kelas)
$wali_kelas_kelas = [];
if ($is_wali_kelas && !empty($wali_kelas_kelas_ids)) {
    $ids = implode(',', $wali_kelas_kelas_ids);
    $q_kelas_wk = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas WHERE id IN ($ids) ORDER BY nama ASC");
    if ($q_kelas_wk) {
        while ($k = mysqli_fetch_assoc($q_kelas_wk)) {
            $wali_kelas_kelas[] = $k;
        }
    }
}

// Scope data dashboard:
// - Admin & Wali Kelas : seluruh kelas
// - Guru biasa         : kelas yang diampu + catatan yang dicatat sendiri
$boleh_semua_kelas = ($role === 'Admin') || ($role === 'Guru' && $is_wali_kelas);
$guru_scope = $guru_id_login ? 'ps.guru_id = ' . (int)$guru_id_login : '1 = 0';
$ids_kelas = implode(',', array_map('intval', $wali_kelas_kelas_ids));

if ($boleh_semua_kelas) {
    $total_siswa = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id"))['total'] ?? 0);
    $total_pelanggaran = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa"))['total'] ?? 0);
} else {
    $total_siswa = (int)(mysqli_fetch_assoc($ids_kelas === ''
        ? mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_pelanggaran_siswa ps ON ps.siswa_id = s.id WHERE $guru_scope")
        : mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id WHERE ks.kelas_id IN ($ids_kelas)")
    )['total'] ?? 0);
    $total_pelanggaran = (int)(mysqli_fetch_assoc($ids_kelas === ''
        ? mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa ps WHERE $guru_scope")
        : mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa WHERE kelas_id IN ($ids_kelas)")
    )['total'] ?? 0);
}

$total_guru = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru"))['total'] ?? 0);
$total_kelas = (int)(mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_kelas"))['total'] ?? 0);

// Daftar kelas sesuai scope dashboard
if ($boleh_semua_kelas) {
    $q_daftar_kelas = mysqli_query($koneksi, "SELECT nama FROM t_kelas ORDER BY nama ASC");
} elseif ($ids_kelas !== '') {
    $q_daftar_kelas = mysqli_query($koneksi, "SELECT nama FROM t_kelas WHERE id IN ($ids_kelas) ORDER BY nama ASC");
} else {
    $q_daftar_kelas = false;
}

// Kelas dari catatan pelanggaran Guru biasa
$guru_kelas = [];
if ($role === 'Guru' && !$is_wali_kelas && $guru_id_login) {
    $q_guru_kelas = mysqli_query($koneksi, "
        SELECT DISTINCT k.nama 
        FROM t_pelanggaran_siswa ps 
        INNER JOIN t_kelas k ON ps.kelas_id = k.id 
        WHERE ps.guru_id = " . (int)$guru_id_login . " 
        ORDER BY k.nama ASC
    ");
    while ($q_guru_kelas && $k = mysqli_fetch_assoc($q_guru_kelas)) {
        $guru_kelas[] = $k['nama'];
    }
}

// Scope pelanggaran terbaru
if ($boleh_semua_kelas) {
    $where_recent = '';
} elseif ($ids_kelas !== '') {
    $where_recent = "WHERE ps.kelas_id IN ($ids_kelas)";
} else {
    $where_recent = "WHERE $guru_scope";
}

$query_recent = mysqli_query($koneksi, "
    SELECT ps.tanggal, ps.poin, s.nama AS nama_siswa, k.nama AS nama_kelas
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s ON ps.siswa_id = s.id
    LEFT JOIN t_kelas k ON ps.kelas_id = k.id
    $where_recent
    ORDER BY ps.tanggal DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard <?php echo htmlspecialchars($current_role); ?> - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-white text-slate-800 font-sans">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <?php include 'layouts/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-white overflow-y-auto">

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-black">Dashboard Utama</h1>
                    <p class="text-sm font-bold text-slate-500 mt-1">Selamat datang kembali, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
            </div>

            <!-- Notifikasi flash (mis. pesan akses ditolak dari cek_akses.php) -->
            <?php if (isset($_SESSION['flash'])): ?>
                <div class="p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($_SESSION['flash']['message']); unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-slate-100 p-6 rounded-2xl flex items-center gap-4">
                    <i class="fa-solid fa-user text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Siswa</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_siswa; ?></p>
                    </div>
                </div>

                <div class="bg-slate-100 p-6 rounded-2xl flex items-center gap-4">
                    <i class="fa-solid fa-list-check text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Catatan</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_pelanggaran; ?></p>
                    </div>
                </div>

                <div class="bg-slate-100 p-6 rounded-2xl flex items-center gap-4">
                    <i class="fa-solid fa-user-tie text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Guru</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_guru; ?></p>
                    </div>
                </div>

                <div class="bg-slate-100 p-6 rounded-2xl flex items-center gap-4">
                    <i class="fa-solid fa-school text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Kelas</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_kelas; ?></p>
                    </div>
                </div>
            </div>

            <!-- Daftar Kelas -->
            <?php if ($q_daftar_kelas && mysqli_num_rows($q_daftar_kelas) > 0): ?>
            <div class="bg-slate-100 p-6 rounded-[1.5rem] mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-chalkboard text-xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Daftar Kelas</h3>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php while ($k_dk = mysqli_fetch_assoc($q_daftar_kelas)): ?>
                        <span class="bg-white border border-slate-200 px-3 py-1.5 rounded-xl text-sm font-bold text-slate-600">
                            <?php echo htmlspecialchars($k_dk['nama']); ?>
                        </span>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Kelas yang Diampu (Wali Kelas) -->
            <?php if ($is_wali_kelas): ?>
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-[1.5rem] mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-user-tie text-xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Kelas yang Anda Ampu</h3>
                        <p class="text-xs font-bold text-slate-500">Anda ditetapkan sebagai wali kelas dari kelas berikut.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($wali_kelas_kelas as $k): ?>
                        <span class="bg-white border border-blue-200 px-4 py-2 rounded-xl text-sm font-bold text-blue-600 shadow-sm">
                            <i class="fa-solid fa-school mr-1"></i> <?php echo htmlspecialchars($k['nama']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
 
            <!-- Kelas dari Catatan Pelanggaran (Guru Biasa) -->
            <?php if ($role === 'Guru' && !$is_wali_kelas && !empty($guru_kelas)): ?>
            <div class="bg-green-50 border border-green-200 p-6 rounded-[1.5rem] mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-chalkboard text-xl text-green-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Kelas pada Catatan Pelanggaran</h3>
                        <p class="text-xs font-bold text-slate-500">Kelas yang memiliki catatan pelanggaran dari Anda.</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($guru_kelas as $nama_kelas): ?>
                        <span class="bg-white border border-green-200 px-4 py-2 rounded-xl text-sm font-bold text-green-600 shadow-sm">
                            <i class="fa-solid fa-school mr-1"></i> <?php echo htmlspecialchars($nama_kelas); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
 
            <!-- Table -->
            <div class="bg-slate-100 p-6 rounded-[1.5rem]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-slate-300 bg-slate-100">
                        <thead>
                            <tr>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-16">NO</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">KELAS</th>
                                <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">CATATAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($query_recent) > 0):
                                while ($row = mysqli_fetch_assoc($query_recent)):
                            ?>
                                <tr>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo $no++; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm">
                                        <span class="text-blue-600 font-bold">+<?php echo $row['poin']; ?> Poin</span> - <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>
                                    </td>
                                </tr>
                            <?php
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="border border-slate-300 px-4 py-8 text-center text-slate-500">Belum ada pelanggaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
