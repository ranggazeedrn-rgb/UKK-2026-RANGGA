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

// Siswa yang melebihi batas aman poin (≥ 20 poin) untuk Admin & Wali Kelas
$siswa_lewat_batas = [];
if ($role === 'Admin' || ($role === 'Guru' && $is_wali_kelas)) {
    $sub_ks = "SELECT k2.nama FROM t_kelas_siswa ks2 JOIN t_kelas k2 ON ks2.kelas_id = k2.id WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1";
    $sub_ps = "SELECT k3.nama FROM t_pelanggaran_siswa ps3 JOIN t_kelas k3 ON ps3.kelas_id = k3.id WHERE ps3.siswa_id = s.id ORDER BY ps3.id DESC LIMIT 1";
    $sub_ks_id = "SELECT ks4.kelas_id FROM t_kelas_siswa ks4 WHERE ks4.siswa_id = s.id ORDER BY ks4.id DESC LIMIT 1";
    $sub_ps_id = "SELECT ps5.kelas_id FROM t_pelanggaran_siswa ps5 WHERE ps5.siswa_id = s.id ORDER BY ps5.id DESC LIMIT 1";

    $filter_wk_sql = "";
    if ($role === 'Guru' && $is_wali_kelas && !empty($wali_kelas_kelas_ids)) {
        $ids_wk_str = implode(',', array_map('intval', $wali_kelas_kelas_ids));
        $filter_wk_sql = "AND COALESCE(($sub_ks_id), ($sub_ps_id), 0) IN ($ids_wk_str)";
    }

    $q_bahaya = mysqli_query($koneksi, "
        SELECT s.id, s.nis, s.nama AS nama_siswa,
               COALESCE(($sub_ks), ($sub_ps), '-') AS nama_kelas,
               COALESCE(($sub_ks_id), ($sub_ps_id), NULL) AS kelas_id,
               COALESCE(SUM(p.poin), 0) AS total_poin,
               (SELECT id FROM t_surat sr WHERE sr.siswa_id = s.id ORDER BY sr.id DESC LIMIT 1) AS surat_terakhir_id,
               (SELECT jenis_surat FROM t_surat sr WHERE sr.siswa_id = s.id ORDER BY sr.id DESC LIMIT 1) AS surat_terakhir_jenis,
               (SELECT tanggal_surat FROM t_surat sr WHERE sr.siswa_id = s.id ORDER BY sr.id DESC LIMIT 1) AS surat_terakhir_tgl
        FROM t_siswa s
        INNER JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id
        INNER JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
        GROUP BY s.id, s.nis, s.nama
        HAVING total_poin >= 20 $filter_wk_sql
        ORDER BY total_poin DESC, s.nama ASC
    ");

    if ($q_bahaya) {
        while ($row_b = mysqli_fetch_assoc($q_bahaya)) {
            $siswa_lewat_batas[] = $row_b;
        }
    }
}
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

            <!-- Peringatan & Fitur Tambah/Cetak Surat (Admin & Wali Kelas) -->
            <?php if ($role === 'Admin' || ($role === 'Guru' && $is_wali_kelas)): ?>
            <div class="mb-8 bg-white border border-red-200 rounded-[1.5rem] p-6 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-black">Perhatian: Siswa Melebihi Batas Aman Poin</h3>
                            <p class="text-xs font-bold text-slate-500">Siswa dengan akumulasi poin ≥ 20 (Peringatan) atau ≥ 50 (Panggilan Orang Tua). Buat & cetak surat penindakan di sini.</p>
                        </div>
                    </div>
                    <a href="kelola_surat.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                        <i class="fa-solid fa-envelope-open-text"></i> Kelola & Arsip Surat
                    </a>
                </div>

                <?php if (!empty($siswa_lewat_batas)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="py-2.5 px-3">NO</th>
                                    <th class="py-2.5 px-3">NAMA SISWA / NIS</th>
                                    <th class="py-2.5 px-3 text-center">KELAS</th>
                                    <th class="py-2.5 px-3 text-center">TOTAL POIN</th>
                                    <th class="py-2.5 px-3 text-center">STATUS TINDAKAN</th>
                                    <th class="py-2.5 px-3 text-center">ARSIP SURAT</th>
                                    <th class="py-2.5 px-3 text-center">AKSI / CETAK</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $no_b = 1; foreach ($siswa_lewat_batas as $sb):
                                    $poin_b = (int)$sb['total_poin'];
                                    $is_panggilan = $poin_b >= 50;
                                    $has_surat = !empty($sb['surat_terakhir_id']);
                                ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-3 font-bold text-slate-500 text-center"><?php echo $no_b++; ?></td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($sb['nama_siswa']); ?></div>
                                        <div class="text-[11px] font-bold text-slate-400">NIS: <?php echo htmlspecialchars($sb['nis'] ?? '-'); ?></div>
                                    </td>
                                    <td class="py-3 px-3 text-center font-bold"><?php echo htmlspecialchars($sb['nama_kelas']); ?></td>
                                    <td class="py-3 px-3 text-center font-bold text-red-600 text-base">
                                        <?php echo $poin_b; ?> Poin
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo $is_panggilan ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'; ?>">
                                            <i class="fa-solid <?php echo $is_panggilan ? 'fa-phone' : 'fa-triangle-exclamation'; ?> mr-1"></i>
                                            <?php echo $is_panggilan ? 'Panggilan Ortu' : 'Peringatan'; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center text-xs">
                                        <?php if ($has_surat): ?>
                                            <span class="text-green-600 font-bold inline-flex items-center gap-1">
                                                <i class="fa-solid fa-check-circle"></i> Sudah Diterbitkan
                                            </span>
                                            <div class="text-[10px] text-slate-400 font-medium"><?php echo date('d/m/Y', strtotime($sb['surat_terakhir_tgl'])); ?></div>
                                        <?php else: ?>
                                            <span class="text-slate-400 font-bold italic">Belum Ada Surat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="tambah_surat.php?siswa_id=<?php echo (int)$sb['id']; ?>" class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm transition" title="Buat Surat Baru">
                                                <i class="fa-solid fa-plus"></i> Buat Surat
                                            </a>
                                            <?php if ($has_surat): ?>
                                                <a href="cetak_surat.php?id=<?php echo (int)$sb['surat_terakhir_id']; ?>" target="_blank" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3 py-1.5 rounded-lg shadow-sm transition" title="Cetak Surat Terakhir">
                                                    <i class="fa-solid fa-print"></i> Cetak Surat
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-base"></i>
                        <span>Alhamdulillah, seluruh siswa saat ini masih berada dalam batas aman poin kedisiplinan (&lt; 20 poin).</span>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

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

