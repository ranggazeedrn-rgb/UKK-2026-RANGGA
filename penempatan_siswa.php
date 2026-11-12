<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $ta_id = $_GET['tahun_ajaran_id'] ?? '';
    $k_id = $_GET['kelas_id'] ?? '';
    mysqli_query($koneksi, "DELETE FROM t_kelas_siswa WHERE id = '$id'");
    header("Location: penempatan_siswa.php?tahun_ajaran_id=$ta_id&kelas_id=$k_id");
    exit();
}

// Tahun ajaran aktif otomatis terpilih bila belum ada filter dari URL
$ta_aktif_id = tahun_ajaran_aktif_id($koneksi);

$selected_ta = (int)($_GET['tahun_ajaran_id'] ?? 0);
if ($selected_ta <= 0) {
    $selected_ta = $ta_aktif_id;
}
$selected_kelas = (int)($_GET['kelas_id'] ?? 0);

// Fetch filters secara aman
$ta_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC");
$kelas_list = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");

// Opsi kelas dipakai di filter
$kelas_options = '';
while ($k = mysqli_fetch_assoc($kelas_list)) {
    $kelas_options .= '<option value="' . $k['id'] . '"' . ($selected_kelas == $k['id'] ? ' selected' : '') . '>' . htmlspecialchars($k['nama']) . '</option>';
}

$siswa_in_kelas = null;
if ($selected_kelas) {
    // Kelas dipilih: tampilkan siswa dari kelas tersebut
    $query_siswa = "SELECT ks.id as ks_id, s.nis, s.nama, k.nama AS nama_kelas
                    FROM t_kelas_siswa ks
                    JOIN t_siswa s ON ks.siswa_id = s.id
                    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.kelas_id = '$selected_kelas'
                    ORDER BY s.nama ASC";
} else {
    // Tanpa filter kelas: tampilkan seluruh siswa beserta kelasnya
    $query_siswa = "SELECT ks.id as ks_id, s.nis, s.nama, k.nama AS nama_kelas
                    FROM t_siswa s
                    LEFT JOIN t_kelas_siswa ks ON ks.id = (SELECT ks2.id FROM t_kelas_siswa ks2 WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1)
                    LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                    ORDER BY s.nama ASC";
}
$siswa_in_kelas = mysqli_query($koneksi, $query_siswa);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Penempatan Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Penempatan Siswa (Plotting)</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Atur penempatan siswa ke dalam kelas berdasarkan Tahun Ajaran.</p>
            </div>
            <?php
            $tambah_url = 'tambah_penempatan_siswa.php';
            $tambah_params = [];
            if ($selected_ta) $tambah_params[] = 'tahun_ajaran_id=' . $selected_ta;
            if ($selected_kelas) $tambah_params[] = 'kelas_id=' . $selected_kelas;
            if ($tambah_params) $tambah_url .= '?' . implode('&', $tambah_params);
            ?>
            <a href="<?php echo $tambah_url; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Siswa ke Kelas
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="w-56 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none" onchange="this.form.submit()">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php
                        if ($ta_list) {
                            mysqli_data_seek($ta_list, 0);
                            while ($ta = mysqli_fetch_assoc($ta_list)):
                                $label_ta = nama_tahun_ajaran($ta, 'Ganjil');
                            ?>
                                <option value="<?php echo $ta['id']; ?>"<?php echo ((int)$ta['id'] === (int)$selected_ta) ? ' selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label_ta); ?>
                                </option>
                            <?php
                            endwhile;
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Kelas</label>
                    <select name="kelas_id" class="w-56 border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        <?php echo $kelas_options; ?>
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">
                <?php echo $selected_kelas ? 'Daftar Siswa di Kelas Ini' : 'Seluruh Siswa'; ?>
            </h3>
            <div class="overflow-x-auto">
                <table id="tabelPenempatan" class="w-full border-collapse border border-slate-300 display">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-12">NO</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">NIS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA SISWA</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-40">KELAS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($siswa_in_kelas && mysqli_num_rows($siswa_in_kelas) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($siswa_in_kelas)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-mono font-bold text-blue-600"><?php echo htmlspecialchars($row['nis']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center font-bold text-slate-600"><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php if (!empty($row['ks_id'])): ?>
                                            <a href="penempatan_siswa.php?delete_id=<?php echo $row['ks_id']; ?>&tahun_ajaran_id=<?php echo $selected_ta; ?>&kelas_id=<?php echo $selected_kelas; ?>" onclick="return confirm('Keluarkan siswa ini dari kelas?')" class="text-red-600 font-bold hover:underline">Keluarkan</a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 font-bold">Belum ditempatkan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
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
            $('#tabelPenempatan').DataTable({
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada data siswa ditemukan.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>

</body>
</html>
