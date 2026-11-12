<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';


$where = ($role === 'Guru') ? "WHERE " . scope_data_guru($role, $is_wali_kelas, $wali_kelas_kelas_ids, $guru_id_login, 'ps') : "WHERE 1 = 1";
$where .= " AND (tt.id IS NOT NULL OR ps.status = 'Selesai')";
// t_tindakan: mencatat siapa yang menindak murid. Ambil penindakan terakhir untuk setiap pelanggaran.
$query = "SELECT ps.*,
                 s.nama AS nama_siswa, s.nis,
                 p.nama AS nama_pelanggaran, p.poin,
                 ka.nama AS nama_kelas,
                 tt.nama_guru AS nama_petugas_tindakan,
                 tt.tanggal_tindakan,
                 tt.tindakan AS tindakan_dilakukan,
                 tt.catatan AS catatan_tindakan,
                 tt.status AS status_tindakan
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          LEFT JOIN t_kelas ka ON ps.kelas_id = ka.id
          LEFT JOIN (SELECT t1.* FROM t_tindakan t1
                     JOIN (SELECT pelanggaran_siswa_id, MAX(id) AS max_id FROM t_tindakan GROUP BY pelanggaran_siswa_id) t2
                       ON t1.id = t2.max_id) tt ON tt.pelanggaran_siswa_id = ps.id
          $where
          ORDER BY ps.tanggal DESC, ps.id DESC";

$rows = [];
$result = @mysqli_query($koneksi, $query);

// Fallback: baris lama tanpa kelas_id diisi dari penempatan siswa terbaru
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (empty($row['nama_kelas']) && !empty($row['siswa_id'])) {
            $sd = (int)$row['siswa_id'];
            $q_k = "SELECT k.nama FROM t_kelas_siswa ks
                    JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.siswa_id = '$sd'
                    ORDER BY ks.id DESC LIMIT 1";
            $r_k = mysqli_query($koneksi, $q_k);
            if ($r_k && mysqli_num_rows($r_k) > 0) {
                $row['nama_kelas'] = mysqli_fetch_assoc($r_k)['nama'];
            }
        }
        $rows[] = $row;
    }
}
?><!DOCTYPE html>
<html lang="id">
<head>
    <title>Riwayat Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black">Riwayat Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Daftar rekam jejak pelanggaran tata tertib siswa.</p>
            </div>
            <a href="catatan_pelanggaran.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Input Pelanggaran
            </a>
        </div>

        <!-- Table Card -->
        <?php if ($role === 'Guru'): ?>
            <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-[1.25rem] px-5 py-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-1"></i>
                <span>
                    <?php if ($is_wali_kelas): ?>
                        Anda adalah <b>Wali Kelas</b> (<?php echo htmlspecialchars(implode(', ', $wali_kelas_kelas_nama)); ?>).
                        Riwayat di bawah menampilkan catatan tindakan dari <b>semua guru</b>. Mengubah status tindakan hanya dapat dilakukan oleh guru yang mencatat pelanggarannya.
                    <?php else: ?>
                        Anda adalah <b>Guru</b>. Riwayat di bawah menampilkan catatan tindakan dari <b>semua guru</b>.
                        Anda hanya dapat mengubah status tindakan pada catatan <b>yang Anda buat sendiri</b>; catatan lain dapat dilihat saja.
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <div class="overflow-x-auto">
                <table id="tabelRiwayat" class="w-full text-left border-collapse display">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">TANGGAL</th>
                            <th class="py-3 px-4">SISWA & KELAS</th>
                            <th class="py-3 px-4">PELANGGARAN</th>
                            <th class="py-3 px-4 text-center">POIN</th>
                            <th class="py-3 px-4">PETUGAS PENCATAT</th>
                            <th class="py-3 px-4">TINDAKAN & DESKRIPSI</th>
                            <th class="py-3 px-4">DITINDAK OLEH</th>
                            <th class="py-3 px-4 text-center">STATUS TINDAKAN</th>
                            <th class="py-3 px-4 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row):
                                $nama_siswa = !empty($row['nama_siswa']) ? $row['nama_siswa'] : 'Siswa Tidak Ditemukan';
                                $nama_kelas = !empty($row['nama_kelas']) ? $row['nama_kelas'] : '-';
                                $nama_pelanggaran = !empty($row['nama_pelanggaran']) ? $row['nama_pelanggaran'] : 'Jenis Pelanggaran Tidak Ada';
                                $poin = $row['poin'] ?? 0;
                                $tanggal = !empty($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-';
                                $status = trim((string)($row['status'] ?? ''));
                                if ($status === '') { $status = 'Dicatat'; }
                                $tindakan_selesai = (strtolower($status) === 'selesai');
                                $ditindak_oleh = trim((string)($row['nama_petugas_tindakan'] ?? ''));
                                $tgl_tindakan = !empty($row['tanggal_tindakan']) ? date('d/m/Y', strtotime($row['tanggal_tindakan'])) : '';
                                $ditindak = ($ditindak_oleh !== '') || (strtolower($status) === 'ditindak');
                            ?>
                                <tr class="hover:bg-slate-50 transition <?php echo $tindakan_selesai ? 'bg-green-50/40' : ''; ?>">
                                    <td class="py-4 px-4 font-bold text-slate-700 whitespace-nowrap">
                                        <?php echo htmlspecialchars($tanggal); ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($nama_siswa); ?></div>
                                        <div class="text-xs text-slate-400 font-bold">Kelas: <?php echo htmlspecialchars($nama_kelas); ?></div>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-800">
                                        <?php echo htmlspecialchars($nama_pelanggaran); ?>
                                    </td>
                                    <td class="py-4 px-4 text-center font-bold text-red-600">
                                        +<?php echo $poin; ?>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 font-medium">
                                        <?php echo !empty($row['nama_guru']) ? htmlspecialchars($row['nama_guru']) : 'Administrator'; ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($row['tindakan'] ?? '-'); ?></div>
                                        <div class="text-xs text-slate-400 font-medium italic mt-0.5"><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></div>
                                        <?php if ($tindakan_selesai): ?>
                                            <!-- Data tindakan ketika proses tindakan sudah selesai -->
                                            <div class="mt-2 bg-green-50 border border-green-200 rounded-lg px-3 py-2 text-xs">
                                                <p class="font-bold text-green-700 uppercase tracking-wider mb-1"><i class="fa-solid fa-check-circle mr-1"></i> Data Tindakan Selesai</p>
                                                <p class="text-slate-700"><span class="font-bold">Tindakan:</span> <?php echo htmlspecialchars(!empty($row['tindakan_dilakukan']) ? $row['tindakan_dilakukan'] : ($row['tindakan'] ?? '-')); ?></p>
                                                <?php if ($tgl_tindakan !== ''): ?>
                                                    <p class="text-slate-700"><span class="font-bold">Tanggal Tindakan:</span> <?php echo $tgl_tindakan; ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($row['catatan_tindakan'])): ?>
                                                    <p class="text-slate-700"><span class="font-bold">Catatan:</span> <?php echo htmlspecialchars($row['catatan_tindakan']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <?php if ($ditindak_oleh !== ''): ?>
                                            <div class="font-bold text-black"><?php echo htmlspecialchars($ditindak_oleh); ?></div>
                                            <?php if ($tgl_tindakan !== ''): ?>
                                                <div class="text-xs text-slate-400 font-bold">Ditindak: <?php echo $tgl_tindakan; ?></div>
                                            <?php endif; ?>
                                        <?php elseif ($ditindak): ?>
                                            <div class="text-xs text-slate-400 font-bold italic">Sudah ditindak (petugas tidak tercatat)</div>
                                        <?php else: ?>
                                            <div class="text-xs text-slate-400 font-bold italic">Belum ditindak</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if ($tindakan_selesai): ?>
                                            <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">
                                                <i class="fa-solid fa-check-circle mr-1"></i> Selesai
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700">
                                                <i class="fa-solid fa-clock mr-1"></i> Belum Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if ($tindakan_selesai): ?>
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 cursor-not-allowed" title="Tindakan sudah selesai sehingga data tidak dapat diedit">
                                                <i class="fa-solid fa-lock"></i> Terkunci
                                            </span>
                                        <?php elseif (boleh_tindak($row['guru_id'])): ?>
                                            <a href="edit_tindakan.php?id=<?php echo (int)$row['id']; ?>" class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline">
                                                <i class="fa-solid fa-pen"></i> Update Tindakan
                                            </a>
                                        <?php else: ?>
                                            <a href="edit_tindakan.php?id=<?php echo (int)$row['id']; ?>" class="inline-flex items-center gap-1 text-slate-500 font-bold hover:underline" title="Hanya guru yang mencatat yang dapat mengubah status tindakan">
                                                <i class="fa-solid fa-eye"></i> Lihat Detail
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- Baris kosong dibiarkan tanpa colspan agar DataTables tidak menampilkan
                             peringatan "Incorrect column count"; pesan data kosong muncul dari DataTables. -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- jQuery & DataTables (Data Table) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelRiwayat').DataTable({
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Belum ada riwayat tindakan. Siswa baru tampil di sini setelah ditindak (status Ditindak/Selesai).",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>

</body>
</html>
