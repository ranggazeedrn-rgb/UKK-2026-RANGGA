<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Halaman Laporan dapat dibuka oleh Admin, Guru, dan Wali Kelas.
// Namun ISI data laporan hanya ditampilkan bagi Wali Kelas (Admin & Wali Kelas),
// sesuai ketentuan: isi data laporan hanya untuk Guru berstatus Wali Kelas.
if ($role === 'Guru' && !$is_wali_kelas) {
    $_SESSION['flash'] = [
        'type' => 'danger',
        'message' => 'Isi data Laporan hanya dapat dilihat oleh Guru yang telah ditunjuk sebagai Wali Kelas.'
    ];
}

// Tahun ajaran aktif otomatis terpilih pada kolom "Pilih Tahun Ajaran"
$ta_aktif_id = tahun_ajaran_aktif_id($koneksi);

$tahun_ajaran_id = isset($_GET['tahun_ajaran_id']) ? (int)$_GET['tahun_ajaran_id'] : $ta_aktif_id;
$kelas_id       = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : '';
$export         = isset($_GET['export']) ? $_GET['export'] : 'preview';
$export         = in_array($export, ['pdf', 'excel'], true) ? $export : 'preview';

// Filter tanggal: hanya format Y-m-d yang valid dipakai (validasi input user)
function valid_tanggal_filter($tanggal)
{
    if (!is_string($tanggal) || $tanggal === '') {
        return '';
    }
    $d = DateTime::createFromFormat('Y-m-d', $tanggal);
    return ($d && $d->format('Y-m-d') === $tanggal) ? $tanggal : '';
}
$tanggal_dari   = valid_tanggal_filter($_GET['tanggal_dari'] ?? '');
$tanggal_sampai = valid_tanggal_filter($_GET['tanggal_sampai'] ?? '');

$where = [];
if (!empty($tahun_ajaran_id)) {
    $where[] = "COALESCE(ps.tahun_ajaran_id, (SELECT ks.tahun_ajaran_id FROM t_kelas_siswa ks WHERE ks.siswa_id = ps.siswa_id ORDER BY ks.id DESC LIMIT 1)) = '$tahun_ajaran_id'";
}
if (!empty($kelas_id)) {
    $where[] = "COALESCE(ps.kelas_id, (SELECT ks.kelas_id FROM t_kelas_siswa ks WHERE ks.siswa_id = ps.siswa_id ORDER BY ks.id DESC LIMIT 1)) = '$kelas_id'";
}
if ($tanggal_dari !== '') {
    $where[] = "ps.tanggal >= '$tanggal_dari'";
}
if ($tanggal_sampai !== '') {
    $where[] = "ps.tanggal <= '$tanggal_sampai'";
}

// Scope data: Admin & Wali Kelas boleh melihat seluruh kelas (termasuk kelas lain)
$where[] = scope_kelas_laporan($role, $wali_kelas_kelas_ids);

$where_sql = "WHERE " . implode(' AND ', $where);

$query = "SELECT ps.id, ps.siswa_id, ps.nama_siswa AS nama_siswa_ps, ps.kelas_id,
                 ps.nama_kelas AS nama_kelas_ps, ps.pelanggaran_id, ps.tahun_ajaran_id,
                 ps.guru_id, ps.nama_guru AS nama_guru_ps, ps.tanggal, ps.keterangan,
                 ps.tindakan, ps.poin, ps.status,
                 s.nis, s.nama AS nama_siswa,
                 k.nama AS nama_kelas,
                 p.nama AS nama_pelanggaran,
                 g.nama AS nama_guru
          FROM t_pelanggaran_siswa ps
          LEFT JOIN t_siswa s ON ps.siswa_id = s.id
          LEFT JOIN t_kelas k ON ps.kelas_id = k.id
          LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
          LEFT JOIN t_guru g ON ps.guru_id = g.id
          $where_sql
          ORDER BY ps.tanggal DESC, ps.id DESC";

$result = @mysqli_query($koneksi, $query);
$total_data = $result ? mysqli_num_rows($result) : 0;

// Filter dropdown: seluruh kelas (wali kelas boleh melihat kelas lain)
$kelas_list = @mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");
$ta_list    = tahun_ajaran_pilihan($koneksi);

if ($export === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Pelanggaran_Siswa_" . date('Y-m-d') . ".xls");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pelanggaran Siswa</title>
    <?php if ($export !== 'excel'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?php endif; ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-8">

    <?php if ($export !== 'excel'): ?>
        <div class="max-w-5xl mx-auto mb-6 flex justify-between items-center no-print">
            <?php if ($role === 'Admin'): ?>
                <a href="cetak_export.php" class="text-sm font-bold text-slate-600 hover:text-black">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Cetak & Export
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="text-sm font-bold text-slate-600 hover:text-black">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Dashboard
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
        </div>

        <!-- Filter Laporan -->
        <form method="GET" class="max-w-5xl mx-auto mb-6 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 no-print">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">TAHUN AJARAN</label>
                    <select name="tahun_ajaran_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        <?php if ($ta_list): while ($ta = mysqli_fetch_assoc($ta_list)):
                            $label_ta = nama_tahun_ajaran($ta, 'Ganjil');
                        ?>
                            <option value="<?php echo $ta['id']; ?>"<?php echo ((int)$ta['id'] === (int)$tahun_ajaran_id) ? ' selected' : ''; ?>>
                                <?php echo htmlspecialchars($label_ta); ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">KELAS</label>
                    <select name="kelas_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Semua Kelas --</option>
                        <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $kelas_id == $k['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama']); ?>
                                <?php if (in_array((int)$k['id'], $wali_kelas_kelas_ids, true)): ?> (Wali Kelas Anda)<?php endif; ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">DARI TANGGAL</label>
                    <input type="date" name="tanggal_dari" value="<?php echo htmlspecialchars($tanggal_dari); ?>" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">SAMPAI TANGGAL</label>
                    <input type="date" name="tanggal_sampai" value="<?php echo htmlspecialchars($tanggal_sampai); ?>" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-sm shadow-sm transition">
                        <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                    </button>
                    <a href="laporan.php?export=excel<?php echo !empty($tahun_ajaran_id) ? '&tahun_ajaran_id=' . (int)$tahun_ajaran_id : ''; ?><?php echo !empty($kelas_id) ? '&kelas_id=' . (int)$kelas_id : ''; ?><?php echo $tanggal_dari !== '' ? '&tanggal_dari=' . $tanggal_dari : ''; ?><?php echo $tanggal_sampai !== '' ? '&tanggal_sampai=' . $tanggal_sampai : ''; ?>" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2 rounded-lg text-sm shadow-sm transition text-center">
                        <i class="fa-solid fa-file-excel mr-1"></i> Excel
                    </a>
                </div>
            </div>

            <?php if ($role === 'Guru' && $is_wali_kelas): ?>
                <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold rounded-xl px-4 py-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <span>
                        Anda mengakses laporan sebagai <b>Wali Kelas</b> (<?php echo htmlspecialchars(implode(', ', $wali_kelas_kelas_nama)); ?>).
                        Laporan ini mencakup <b>seluruh kelas</b>, termasuk pelanggaran yang dilakukan siswa dari kelas lain.
                    </span>
                </div>
            <?php elseif ($role === 'Guru'): ?>
                <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold rounded-xl px-4 py-3 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <span>
                        Anda dapat membuka halaman Laporan, namun <b>isi data laporan</b> hanya ditampilkan bagi Guru yang
                        telah ditunjuk sebagai <b>Wali Kelas</b>. Gunakan menu <b>Riwayat</b> untuk melihat catatan
                        pelanggaran yang Anda buat sendiri.
                    </span>
                </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center border-b border-slate-300 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-black uppercase tracking-wide">Laporan Kedisiplinan & Pelanggaran Siswa</h1>
            <p class="text-sm text-slate-500 font-bold mt-1">Sistem Informasi Kedisiplinan Sekolah UKK 2026</p>
            <?php if (!empty($kelas_id) || !empty($tahun_ajaran_id) || $tanggal_dari !== '' || $tanggal_sampai !== ''): ?>
                <p class="text-xs text-slate-600 font-bold mt-2">
                    <?php
                    $filter_info = [];
                    if (!empty($kelas_id)) {
                        $q_nama_kelas = @mysqli_query($koneksi, "SELECT nama FROM t_kelas WHERE id = '" . (int)$kelas_id . "' LIMIT 1");
                        $r_nama_kelas = $q_nama_kelas ? mysqli_fetch_assoc($q_nama_kelas) : null;
                        $filter_info[] = 'Kelas: ' . ($r_nama_kelas['nama'] ?? '-');
                    }
                    if (!empty($tahun_ajaran_id)) {
                        $q_nama_ta = @mysqli_query($koneksi, "SELECT nama FROM t_tahun_ajaran WHERE id = '" . (int)$tahun_ajaran_id . "' LIMIT 1");
                        $r_nama_ta = $q_nama_ta ? mysqli_fetch_assoc($q_nama_ta) : null;
                        $filter_info[] = 'Tahun Ajaran: ' . ($r_nama_ta['nama'] ?? '-');
                    }
                    if ($tanggal_dari !== '' || $tanggal_sampai !== '') {
                        $filter_info[] = 'Tanggal: ' . ($tanggal_dari !== '' ? date('d/m/Y', strtotime($tanggal_dari)) : 'awal') . ' - ' . ($tanggal_sampai !== '' ? date('d/m/Y', strtotime($tanggal_sampai)) : 'akhir');
                    }
                    echo htmlspecialchars(implode(' | ', $filter_info));
                    ?>
                    <span class="text-slate-400">•</span> Total Data: <?php echo $total_data; ?>
                </p>
            <?php endif; ?>
        </div>

        <table class="w-full border-collapse border border-slate-300 text-sm">
            <thead>
                <tr class="bg-slate-100 text-slate-800">
                    <th class="border border-slate-300 px-3 py-2 text-center w-12">NO</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-28">TANGGAL</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-28">NIS</th>
                    <th class="border border-slate-300 px-3 py-2 text-left">NAMA SISWA</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-32">KELAS</th>
                    <th class="border border-slate-300 px-3 py-2 text-left">PELANGGARAN</th>
                    <th class="border border-slate-300 px-3 py-2 text-center w-20">POIN</th>
                    <th class="border border-slate-300 px-3 py-2 text-left w-40">PELAPOR</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)):
                        $nama_kelas = !empty($row['nama_kelas']) ? $row['nama_kelas'] : ($row['nama_kelas_ps'] ?? '-');
                        $tanggal = !empty($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-';
                        $nama_pelapor = !empty($row['nama_guru']) ? $row['nama_guru'] : (!empty($row['nama_guru_ps']) ? $row['nama_guru_ps'] : '-');
                    ?>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-slate-700 whitespace-nowrap"><?php echo htmlspecialchars($tanggal); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-mono font-bold text-blue-600"><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold"><?php echo htmlspecialchars($nama_kelas); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-slate-700"><?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-red-600">+<?php echo $row['poin'] ?? 0; ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-slate-600 font-medium"><?php echo htmlspecialchars($nama_pelapor); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="border border-slate-300 px-4 py-8 text-center text-slate-500 font-bold">Tidak ada data pelanggaran ditemukan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-10 flex justify-end">
            <div class="text-center w-52 text-sm">
                <p class="text-slate-600">Tanggal Cetak:</p>
                <p class="font-bold text-black mb-12"><?php echo date('d F Y'); ?></p>
                <p class="font-bold text-black border-b border-black inline-block px-4">Petugas Kesiswaan</p>
            </div>
        </div>
    </div>

    <?php if ($export === 'pdf'): ?>
    <script>
        // Memunculkan dialog cetak secara otomatis saat halaman selesai dimuat
        window.onload = function() {
            window.print();
        };
    </script>
    <?php endif; ?>

</body>
</html>
