<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE id = '$id'");
    header("Location: walikelas.php");
    exit();
}

// Fitur Filter / Search
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE g.nama LIKE '%$s%' OR k.nama LIKE '%$s%' OR ta.nama LIKE '%$s%'";
}

// Alias kolom eksplisit: ta.* punya kolom id/status_aktif yang menimpa wk.id
$query_data = "SELECT wk.id, wk.tahun_ajaran_id, wk.kelas_id, wk.guru_id, wk.status_aktif,
                      g.nama as nama_guru, k.nama as nama_kelas, ta.nama as nama_ta
               FROM t_wali_kelas wk
               LEFT JOIN t_guru g ON wk.guru_id = g.id
               LEFT JOIN t_kelas k ON wk.kelas_id = k.id
               LEFT JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id
               $where
               ORDER BY wk.id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Wali Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Wali Kelas</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Penetapan guru sebagai wali kelas per tahun ajaran.</p>
            </div>
            <a href="tambah_walikelas.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tetapkan Wali Kelas
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="GET" class="mb-6 flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Nama Guru, Kelas, atau Tahun Ajaran..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-80 focus:outline-none focus:border-blue-600">
            <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
        </form>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Wali Kelas</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">TAHUN AJARAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">KELAS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA GURU (WALI KELAS)</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)):
                                $thn = $data['nama_ta'] ?? '-';
                                $sem = isset($data['semester']) ? ucfirst($data['semester']) : 'Ganjil';
                            ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700">
                                        <?php echo htmlspecialchars($thn); ?> (<?php echo htmlspecialchars($sem); ?>)
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black text-center"><?php echo htmlspecialchars($data['nama_kelas'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-slate-800"><?php echo htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo (isset($data['status_aktif']) && $data['status_aktif'] == 1) ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <a href="edit_walikelas.php?id=<?php echo $data['id']; ?>" class="text-blue-600 font-bold mr-2 hover:underline">Edit</a>
                                        <a href="walikelas.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus penetapan wali kelas ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Belum ada data wali kelas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
