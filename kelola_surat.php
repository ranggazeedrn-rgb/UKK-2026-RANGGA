<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Hapus surat jika diminta
if (isset($_GET['hapus']) && (int)$_GET['hapus'] > 0) {
    $hapus_id = (int)$_GET['hapus'];
    // Validasi hak akses hapus: Admin atau pembuat surat
    $q_cek = mysqli_query($koneksi, "SELECT * FROM t_surat WHERE id = $hapus_id");
    if ($q_cek && mysqli_num_rows($q_cek) > 0) {
        $surat_row = mysqli_fetch_assoc($q_cek);
        if ($role === 'Admin' || (int)$surat_row['dibuat_oleh'] === (int)$_SESSION['user_id']) {
            mysqli_query($koneksi, "DELETE FROM t_surat WHERE id = $hapus_id");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Surat berhasil dihapus.'];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Anda tidak memiliki hak untuk menghapus surat ini.'];
        }
    }
    header("Location: kelola_surat.php");
    exit();
}

// Scope query
$where = "WHERE 1 = 1";
if ($role === 'Guru' && $is_wali_kelas && !empty($wali_kelas_kelas_ids)) {
    $ids_wk = implode(',', array_map('intval', $wali_kelas_kelas_ids));
    $where .= " AND sr.kelas_id IN ($ids_wk)";
}

$query_surat = "SELECT sr.*, s.nama AS nama_siswa, s.nis, k.nama AS nama_kelas
                FROM t_surat sr
                LEFT JOIN t_siswa s ON sr.siswa_id = s.id
                LEFT JOIN t_kelas k ON sr.kelas_id = k.id
                $where
                ORDER BY sr.id DESC";
$result_surat = @mysqli_query($koneksi, $query_surat);
$daftar_surat = [];
if ($result_surat) {
    while ($row = mysqli_fetch_assoc($result_surat)) {
        $daftar_surat[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Surat Peringatan & Panggilan - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola & Cetak Surat</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Daftar arsip surat peringatan dan surat panggilan orang tua yang telah diterbitkan.</p>
            </div>
            <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 mt-4 md:mt-0">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo htmlspecialchars($_SESSION['flash']['message']); unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Table Card -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table id="tabelSurat" class="w-full text-left border-collapse display">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4 text-center">NO</th>
                            <th class="py-3 px-4">NOMOR & TGL SURAT</th>
                            <th class="py-3 px-4">SISWA & KELAS</th>
                            <th class="py-3 px-4">JENIS SURAT</th>
                            <th class="py-3 px-4 text-center">POIN SAAT DIBUAT</th>
                            <th class="py-3 px-4">DITERBITKAN OLEH</th>
                            <th class="py-3 px-4 text-center">AKSI / CETAK</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($daftar_surat)): ?>
                            <?php $no = 1; foreach ($daftar_surat as $row):
                                $is_panggilan = strpos($row['jenis_surat'], 'Panggilan') !== false;
                            ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-4 px-4 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-blue-600"><?php echo htmlspecialchars($row['nomor_surat']); ?></div>
                                        <div class="text-xs text-slate-400 font-bold"><?php echo date('d/m/Y', strtotime($row['tanggal_surat'])); ?></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa'] ?? 'Siswa Tidak Ditemukan'); ?></div>
                                        <div class="text-xs text-slate-400 font-bold">Kelas: <?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo $is_panggilan ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'; ?>">
                                            <i class="fa-solid <?php echo $is_panggilan ? 'fa-phone' : 'fa-triangle-exclamation'; ?> mr-1"></i>
                                            <?php echo htmlspecialchars($row['jenis_surat']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center font-bold text-red-600 text-base">
                                        <?php echo (int)$row['total_poin']; ?> Poin
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 font-medium">
                                        <?php echo htmlspecialchars($row['nama_pembuat']); ?>
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <a href="cetak_surat.php?id=<?php echo (int)$row['id']; ?>" target="_blank" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3.5 py-2 rounded-lg shadow-sm transition">
                                            <i class="fa-solid fa-print"></i> Cetak Surat
                                        </a>
                                        <a href="kelola_surat.php?hapus=<?php echo (int)$row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus arsip surat ini?')" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-bold text-xs ml-2 p-2 hover:bg-red-50 rounded-lg transition" title="Hapus Surat">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelSurat').DataTable({
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Belum ada surat yang diterbitkan.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>
</body>
</html>

