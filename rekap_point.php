<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$kelas_id = isset($_GET['kelas_id']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_id']) : '';
$search = $_GET['search'] ?? '';

// Rekap total poin per siswa. Kelas diutamakan dari penempatan (t_kelas_siswa),
// fallback ke kelas saat pelanggaran dicatat (ps.kelas_id), karena penempatan bisa kosong.
// Filter kelas ditaruh di kondisi JOIN agar filter tetap berlaku tanpa menghilangkan siswa lain.
$join_ps = "LEFT JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id";
$conds = [];
if (!empty($kelas_id)) {
    $join_ps = "LEFT JOIN t_pelanggaran_siswa ps ON s.id = ps.siswa_id AND ps.kelas_id = '$kelas_id'";
    $conds[] = "ps.kelas_id = '$kelas_id'";
}
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $conds[] = "s.nama LIKE '%$s%'";
}
$where = count($conds) > 0 ? "WHERE " . implode(' AND ', $conds) : "";

$query = "SELECT s.id, s.nama AS nama_siswa,
                 COALESCE(
                     (SELECT k2.nama FROM t_kelas_siswa ks2 JOIN t_kelas k2 ON ks2.kelas_id = k2.id WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1),
                     (SELECT k3.nama FROM t_pelanggaran_siswa ps3 JOIN t_kelas k3 ON ps3.kelas_id = k3.id WHERE ps3.siswa_id = s.id ORDER BY ps3.id DESC LIMIT 1),
                     '-'
                 ) AS nama_kelas,
                 COALESCE(SUM(p.poin), 0) AS total_poin
          FROM t_siswa s
          $join_ps
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          $where
          GROUP BY s.id, s.nama
          ORDER BY total_poin DESC, s.nama ASC";

$result = @mysqli_query($koneksi, $query);

// User yang membuka halaman (sesuai login di dashboard)
$user_name = $_SESSION['name'] ?? $_SESSION['username'] ?? 'Pengguna';
$open_role = $_SESSION['role'] ?? 'Guru';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Akumulasi Poin Siswa</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            /* Data table: sembunyikan kontrol (paging) saat cetak */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate { display: none !important; }
            body { background-color: white !important; padding: 0 !important; }
        }
        /* Samakan tampilan header tabel DataTables dengan tema halaman */
        table.dataTable thead th { text-align: center; }
        table.dataTable thead .sorting { text-align: center; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex">

    <div class="no-print">
        <?php include 'layouts/sidebar.php'; ?>
    </div>

    <main class="flex-1 p-8 overflow-y-auto">

        <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center no-print">
            <a href="cetak_export.php" class="text-sm font-bold text-slate-600 hover:text-black">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Cetak & Export
            </a>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Rekap Poin
            </button>
        </div>

        <form method="GET" class="max-w-5xl mx-auto mb-4 flex gap-2 no-print">
            <?php if (!empty($kelas_id)): ?>
                <input type="hidden" name="kelas_id" value="<?php echo htmlspecialchars($kelas_id); ?>">
            <?php endif; ?>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Nama Siswa..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-72 focus:outline-none focus:border-blue-600">
            <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
        </form>

        <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <div class="text-center border-b border-slate-300 pb-4 mb-6">
                <h1 class="text-2xl font-bold text-black uppercase tracking-wide">Rekapitulasi Akumulasi Poin Kedisiplinan</h1>
                <p class="text-sm text-slate-500 font-bold mt-1">Laporan Ringkasan Poin Siswa per Kelas</p>
            </div>

            <table id="tabelRekap" class="w-full border-collapse border border-slate-300 text-sm display">
                <thead>
                    <tr class="bg-slate-100 text-slate-800">
                        <th class="border border-slate-300 px-4 py-3 text-center w-12">NO</th>
                        <th class="border border-slate-300 px-4 py-3 text-left">NAMA SISWA</th>
                        <th class="border border-slate-300 px-4 py-3 text-center w-32">KELAS</th>
                        <th class="border border-slate-300 px-4 py-3 text-center w-44">TOTAL POIN PELANGGARAN</th>
                        <th class="border border-slate-300 px-4 py-3 text-center w-40">STATUS / TINDAKAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)):
                            $poin = (int)$row['total_poin'];
                            $status_label = 'Aman';
                            $status_class = 'text-green-600 bg-green-50';
                            if ($poin >= 50) {
                                $status_label = 'Panggilan Ortu';
                                $status_class = 'text-red-600 bg-red-50 font-bold';
                            } elseif ($poin >= 20) {
                                $status_label = 'Peringatan';
                                $status_class = 'text-amber-600 bg-amber-50 font-bold';
                            }
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="border border-slate-300 px-4 py-2.5 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                                <td class="border border-slate-300 px-4 py-2.5 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                <td class="border border-slate-300 px-4 py-2.5 text-center font-bold"><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                                <td class="border border-slate-300 px-4 py-2.5 text-center font-bold text-base <?php echo $poin > 0 ? 'text-red-600' : 'text-slate-600'; ?>"><?php echo $poin; ?> Poin</td>
                                <td class="border border-slate-300 px-4 py-2.5 text-center"><span class="px-2 py-1 rounded text-xs <?php echo $status_class; ?>"><?php echo $status_label; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Tidak ada data siswa ditemukan untuk rekapitulasi poin.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="mt-10 flex justify-between items-end text-sm">
                <div>
                    <p class="text-xs text-slate-500 font-bold">Keterangan Akumulasi Poin:</p>
                    <p class="text-xs text-slate-600 mt-1">• 0 - 19 Poin: Kategori Pembinaan Normal</p>
                    <p class="text-xs text-slate-600">• 20 - 49 Poin: Peringatan Pertama / Wali Kelas</p>
                    <p class="text-xs text-slate-600">• ≥ 50 Poin: Surat Panggilan Orang Tua / BK</p>
                </div>
                <div class="text-center w-52">
                    <p class="text-slate-600">Tanggal Cetak:</p>
                    <p class="font-bold text-black mb-12"><?php echo date('d F Y'); ?></p>
                    <p class="text-xs text-slate-500 font-bold">Dibuka oleh:</p>
                    <p class="font-bold text-black"><?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($open_role); ?>)</p>
                    <p class="font-bold text-black border-b border-black inline-block px-6 mt-4">Wali Kelas / Guru BK</p>
                </div>
            </div>
        </div>

    </main>

    <!-- jQuery & DataTables (Data Table) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelRekap').DataTable({
                "searching": false,          // pencarian pakai form "Cari" di atas, sama seperti halaman lain
                "order": [],                 // urutan tetap dari query (total poin terbesar di atas)
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data yang ditemukan",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>

</body>
</html>
