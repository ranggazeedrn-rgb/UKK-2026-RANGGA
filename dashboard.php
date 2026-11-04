<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$current_role = $_SESSION['role'] ?? 'Admin';
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

$query_siswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_siswa");
$total_siswa = mysqli_fetch_assoc($query_siswa)['total'] ?? 0;

$query_guru = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_guru");
$total_guru = mysqli_fetch_assoc($query_guru)['total'] ?? 0;

$query_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_pelanggaran_siswa");
$total_pelanggaran = mysqli_fetch_assoc($query_pelanggaran)['total'] ?? 0;

$query_kelas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_kelas");
$total_kelas = mysqli_fetch_assoc($query_kelas)['total'] ?? 0;

$query_ta_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM t_tahun_ajaran WHERE status_aktif = 1");
$total_ta_aktif = mysqli_fetch_assoc($query_ta_aktif)['total'] ?? 0;

$query_recent = mysqli_query($koneksi, "
    SELECT
        ps.tanggal,
        ps.poin,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s ON ps.siswa_id = s.id
    LEFT JOIN t_kelas k ON ps.kelas_id = k.id
    " . ($role === 'Guru'
        ? (!empty($wali_kelas_kelas_ids)
            ? "WHERE ps.kelas_id IN (" . implode(',', $wali_kelas_kelas_ids) . ")"
            : "WHERE 1 = 0")
        : "") . "
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
                        <p class="text-2xl font-bold text-blue-600"><?php echo $total_ta_aktif; ?></p>
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
