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
// - Admin & Wali Kelas : seluruh kelas (wali kelas juga melihat kelas lain)
// - Guru biasa         : kelas yang diampu + catatan yang dicatat sendiri
$boleh_semua_kelas = ($role === 'Admin') || ($role === 'Guru' && $is_wali_kelas);
$guru_scope = $guru_id_login ? 'ps.guru_id = ' . (int)$guru_id_login : '1 = 0';
$ids_kelas = implode(',', array_map('intval', $wali_kelas_kelas_ids));

if ($boleh_semua_kelas) {
    $query_siswa = mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id");
    $query_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
} else {
    $query_siswa = $ids_kelas === ''
        ? mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_pelanggaran_siswa ps ON ps.siswa_id = s.id WHERE $guru_scope")
        : mysqli_query($koneksi, "SELECT COUNT(DISTINCT s.id) as total FROM t_siswa s INNER JOIN t_kelas_siswa ks ON ks.siswa_id = s.id WHERE ks.kelas_id IN ($ids_kelas)");
    $query_pelanggaran = $ids_kelas === ''
        ? mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa ps WHERE $guru_scope")
        : mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa WHERE kelas_id IN ($ids_kelas)");
}
$total_siswa = mysqli_fetch_assoc($query_siswa)['total'] ?? 0;

$query_guru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru");
$total_guru = mysqli_fetch_assoc($query_guru)['total'] ?? 0;

$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'] ?? 0;

$query_kelas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_kelas");
$total_kelas = mysqli_fetch_assoc($query_kelas)['total'] ?? 0;

// Tahun ajaran aktif (dipakai juga sebagai pilihan default pada semua
// kolom "Pilih Tahun Ajaran" di seluruh aplikasi)
$ta_aktif = tahun_ajaran_aktif($koneksi);
$nama_ta_aktif = nama_tahun_ajaran($ta_aktif);

if ($boleh_semua_kelas) {
    $where_recent = '';
} elseif ($ids_kelas !== '') {
    $where_recent = "WHERE ps.kelas_id IN ($ids_kelas)";
} else {
    $where_recent = "WHERE $guru_scope";
}

$query_recent = mysqli_query($koneksi, "
    SELECT
        ps.tanggal,
        ps.poin,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas
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

            <!-- Info untuk Guru yang belum menjadi Wali Kelas -->
            <?php if ($role === 'Guru' && !$is_wali_kelas): ?>
            <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold rounded-[1.5rem] px-6 py-4 mb-6 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-1"></i>
                <span>
                    Anda adalah <b>Guru</b> (belum ditunjuk sebagai Wali Kelas).
                    Anda tetap dapat mencatat pelanggaran serta memperbarui <b>tindakan</b>.
                    Halaman <b>Laporan</b> hanya dapat diakses oleh Admin dan Guru yang ditunjuk sebagai <b>Wali Kelas</b>.
                </span>
            </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                    <i class="fa-solid fa-triangle-exclamation text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Pelanggaran</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_pelanggaran; ?></p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid Tambahan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

                <div class="bg-slate-100 p-6 rounded-2xl flex items-center gap-4">
                    <i class="fa-solid fa-calendar-days text-4xl text-blue-600"></i>
                    <div>
                        <h3 class="text-sm font-bold text-black uppercase tracking-wider">Tahun Ajaran Aktif</h3>
                        <p class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($nama_ta_aktif); ?></p>
                        <p class="text-[11px] font-bold text-slate-400"><?php echo $ta_aktif ? 'Otomatis dipilih pada kolom tahun ajaran' : 'Belum ada tahun ajaran aktif'; ?></p>
                    </div>
                </div>
            </div>

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
