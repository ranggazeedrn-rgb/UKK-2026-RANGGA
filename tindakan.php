<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Halaman Tindakan: guru/admin menindak pelanggaran yang sudah dicatat.
// Setiap penindakan dicatat pada tabel t_tindakan (siapa yang menindak murid),
// lalu tampil pada halaman Riwayat. Jika tindakan berstatus Selesai,
// data tindakan ditampilkan (terkunci) di Riwayat.

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['pelanggaran_siswa_id'] ?? 0);

// Proses simpan tindakan
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['simpan_tindakan'])) {
    $pelanggaran_siswa_id = (int)($_POST['pelanggaran_siswa_id'] ?? 0);
    $tindakan_dilakukan   = mysqli_real_escape_string($koneksi, $_POST['tindakan_dilakukan'] ?? '');
    $catatan_tindakan     = mysqli_real_escape_string($koneksi, $_POST['catatan_tindakan'] ?? '');
    $tanggal_tindakan     = mysqli_real_escape_string($koneksi, $_POST['tanggal_tindakan'] ?? date('Y-m-d'));
    $status_tindakan      = ($_POST['status_tindakan'] ?? 'Ditindak') === 'Selesai' ? 'Selesai' : 'Ditindak';

    if ($pelanggaran_siswa_id <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data pelanggaran tidak valid.'];
        header("Location: tindakan.php");
        exit();
    }

    // Ambil data pelanggaran + cek scope akses
    $q_ps = mysqli_query($koneksi, "
        SELECT ps.*
        FROM t_pelanggaran_siswa ps
        WHERE ps.id = '$pelanggaran_siswa_id'
        LIMIT 1
    ");
    $ps = $q_ps ? mysqli_fetch_assoc($q_ps) : null;

    if (!$ps) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data pelanggaran tidak ditemukan!'];
        header("Location: tindakan.php");
        exit();
    }

    // Batas data guru biasa: kelas yang diampu + catatan yang dicatat sendiri
    if ($role === 'Guru') {
        $scope = scope_data_guru($role, $is_wali_kelas, $wali_kelas_kelas_ids, $guru_id_login, 'ps');
        $cek = mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa ps WHERE ps.id = '$pelanggaran_siswa_id' AND $scope LIMIT 1");
        if (!$cek || mysqli_num_rows($cek) === 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak dapat diakses.'];
            header("Location: tindakan.php");
            exit();
        }
    }

    if (strtolower(trim((string)$ps['status'])) === 'selesai') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: tindakan sudah berstatus Selesai.'];
        header("Location: tindakan.php");
        exit();
    }

    // Hanya guru yang mencatat pelanggaran (guru A) yang dapat menindak siswa ini.
    // Guru lain hanya dapat melihat datanya.
    if (!boleh_tindak($ps['guru_id'])) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: hanya guru yang mencatat pelanggaran ini yang dapat menindak siswa tersebut.'];
        header("Location: tindakan.php");
        exit();
    }

    if (trim($tindakan_dilakukan) === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tindakan yang dilakukan tidak boleh kosong.'];
        header("Location: tindakan.php?id=$pelanggaran_siswa_id");
        exit();
    }

    // Petugas penindak: data guru login (Admin tidak punya record t_guru)
    $tindak_guru_sql = $guru_id_login ? "'" . (int)$guru_id_login . "'" : "NULL";
    $tindak_nama     = mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? ''));
    $tanggal_sql     = $tanggal_tindakan !== '' ? "'$tanggal_tindakan'" : 'NULL';

    mysqli_begin_transaction($koneksi);
    $ok = true;

    // 1. Catat siapa yang menindak pada tabel t_tindakan
    $insert = "INSERT INTO t_tindakan (pelanggaran_siswa_id, guru_id, nama_guru, tanggal_tindakan, tindakan, catatan, status, created_at, updated_at)
               VALUES ('$pelanggaran_siswa_id', $tindak_guru_sql, '$tindak_nama', $tanggal_sql, '$tindakan_dilakukan', '$catatan_tindakan', '$status_tindakan', NOW(), NOW())";
    if (!mysqli_query($koneksi, $insert)) { $ok = false; }

    // 2. Perbarui data pelanggaran: tindakan & status
    if ($ok) {
        $update = "UPDATE t_pelanggaran_siswa
                   SET tindakan = '$tindakan_dilakukan',
                       status = '$status_tindakan',
                       updated_at = NOW()
                   WHERE id = '$pelanggaran_siswa_id'";
        if (!mysqli_query($koneksi, $update)) { $ok = false; }
    }

    if ($ok) {
        mysqli_commit($koneksi);
        $pesan = ($status_tindakan === 'Selesai')
            ? 'Tindakan berhasil disimpan dan berstatus <b>Selesai</b>. Data tindakan tampil di Riwayat.'
            : 'Tindakan berhasil disimpan. Status pelanggaran kini <b>Ditindak</b>.';
        $_SESSION['flash'] = ['type' => 'success', 'message' => $pesan];
        header("Location: tindakan.php");
        exit();
    }
    mysqli_rollback($koneksi);
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan tindakan: ' . mysqli_error($koneksi)];
}

// Mode form penindakan (tindakan.php?id=X)
$data = null;
if ($id > 0 && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    $q = mysqli_query($koneksi, "
        SELECT ps.*, s.nis, s.nama AS nama_siswa_ref,
               k.nama AS nama_kelas_ref,
               p.nama AS nama_pelanggaran
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k ON ps.kelas_id = k.id
        LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
        WHERE ps.id = '$id'
        LIMIT 1
    ");
    $data = $q ? mysqli_fetch_assoc($q) : null;

    if ($data && $role === 'Guru') {
        $scope = scope_data_guru($role, $is_wali_kelas, $wali_kelas_kelas_ids, $guru_id_login, 'ps');
        $cek = mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa ps WHERE ps.id = '$id' AND $scope LIMIT 1");
        if (!$cek || mysqli_num_rows($cek) === 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak dapat diakses.'];
            header("Location: tindakan.php");
            exit();
        }
    }

    if (!$data) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data pelanggaran tidak ditemukan!'];
        header("Location: tindakan.php");
        exit();
    }
}

// Daftar pelanggaran yang belum selesai ditindak (status Dicatat/Ditindak)
$where = ($role === 'Guru') ? "WHERE " . scope_data_guru($role, $is_wali_kelas, $wali_kelas_kelas_ids, $guru_id_login, 'ps') . " AND (ps.status <> 'Selesai' OR ps.status IS NULL OR ps.status = '')"
                            : "WHERE (ps.status <> 'Selesai' OR ps.status IS NULL OR ps.status = '')";
$q_daftar = @mysqli_query($koneksi, "
    SELECT ps.id, ps.nama_siswa, ps.nama_kelas, ps.kelas_id, ps.siswa_id,
           ps.tanggal, ps.tindakan, ps.keterangan, ps.poin, ps.status, ps.nama_guru, ps.guru_id,
           p.nama AS nama_pelanggaran
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
    $where
    ORDER BY ps.tanggal DESC, ps.id DESC
");
$daftar = [];
if ($q_daftar) {
    while ($r = mysqli_fetch_assoc($q_daftar)) {
        if (empty($r['nama_kelas']) && !empty($r['siswa_id'])) {
            $sd = (int)$r['siswa_id'];
            $rk = mysqli_query($koneksi, "SELECT k.nama FROM t_kelas_siswa ks JOIN t_kelas k ON ks.kelas_id = k.id WHERE ks.siswa_id = '$sd' ORDER BY ks.id DESC LIMIT 1");
            if ($rk && mysqli_num_rows($rk) > 0) { $r['nama_kelas'] = mysqli_fetch_assoc($rk)['nama']; }
        }
        $daftar[] = $r;
    }
}

// Riwayat tindakan (siapa menindak murid mana) tampil di halaman Riwayat.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tindakan - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-black">Tindakan</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Tindak lanjuti pelanggaran siswa yang telah dicatat oleh guru.</p>
            </div>
            <a href="riwayat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat
            </a>
        </div>

        <!-- Flash Alert -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <?php if ($data): ?>
            <!-- ===== Form Penindakan ===== -->
            <?php
                $ps_status = trim((string)($data['status'] ?? ''));
                if ($ps_status === '') { $ps_status = 'Dicatat'; }
                $terkunci = (strtolower($ps_status) === 'selesai');
                // Hanya guru pencatat (guru A) yang boleh menindak; guru lain lihat saja
                $boleh_tindak_siswa = boleh_tindak($data['guru_id']);
                $nama_siswa = !empty($data['nama_siswa_ref']) ? $data['nama_siswa_ref'] : ($data['nama_siswa'] ?? '-');
                $nama_kelas = !empty($data['nama_kelas_ref']) ? $data['nama_kelas_ref'] : ($data['nama_kelas'] ?? '-');
            ?>
            <div class="max-w-3xl mx-auto bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-6">
                <h3 class="text-sm font-bold text-black uppercase tracking-wider mb-4">Data Pelanggaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Nama Siswa</p>
                        <p class="font-bold text-black"><?php echo htmlspecialchars($nama_siswa); ?>
                            <?php if (!empty($data['nis'])): ?>
                                <span class="text-blue-600 font-mono text-xs ml-1"><?php echo htmlspecialchars($data['nis']); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Kelas</p>
                        <p class="font-bold text-black"><?php echo htmlspecialchars($nama_kelas); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Jenis Pelanggaran</p>
                        <p class="font-bold text-black"><?php echo htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Poin</p>
                        <p class="font-bold text-red-600">+<?php echo (int)($data['poin'] ?? 0); ?> Poin</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Tanggal Kejadian</p>
                        <p class="font-bold text-black"><?php echo !empty($data['tanggal']) ? date('d/m/Y', strtotime($data['tanggal'])) : '-'; ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Dicatat Oleh</p>
                        <p class="font-bold text-black"><?php echo !empty($data['nama_guru']) ? htmlspecialchars($data['nama_guru']) : 'Administrator'; ?></p>
                    </div>
                </div>
                <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Keterangan Kejadian</p>
                    <p class="text-slate-700 font-medium"><?php echo htmlspecialchars($data['keterangan'] ?? '-'); ?></p>
                </div>
            </div>

            <?php if ($terkunci || !$boleh_tindak_siswa): ?>
                <!-- Tindakan selesai (terkunci) atau bukan guru pencatat: hanya lihat -->
                <div class="max-w-3xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border <?php echo $terkunci ? 'border-green-200' : 'border-amber-200'; ?>">
                    <div class="flex items-start gap-4">
                        <div class="p-3 <?php echo $terkunci ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'; ?> rounded-xl">
                            <i class="fa-solid fa-<?php echo $terkunci ? 'lock' : 'eye'; ?> text-xl"></i>
                        </div>
                        <div>
                            <?php if ($terkunci): ?>
                                <h2 class="text-base font-bold text-black">Tindakan Sudah Selesai</h2>
                                <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                    Tindakan atas pelanggaran ini berstatus <b>Selesai</b>, sehingga tidak dapat ditindak kembali.
                                </p>
                            <?php else: ?>
                                <h2 class="text-base font-bold text-black">Mode Lihat Saja</h2>
                                <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                    Anda dapat <b>melihat datanya saja</b>. Siswa ini hanya dapat ditindak oleh guru yang
                                    mencatat pelanggarannya:
                                    <b><?php echo htmlspecialchars(!empty($data['nama_guru']) ? $data['nama_guru'] : 'Administrator'); ?></b>.
                                </p>
                            <?php endif; ?>
                            <div class="mt-5 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm">
                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Tindakan Pembinaan</p>
                                <p class="font-bold text-black"><?php echo htmlspecialchars($data['tindakan'] ?? '-'); ?></p>
                            </div>
                            <a href="tindakan.php" class="inline-flex items-center gap-2 mt-5 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition">
                                <i class="fa-solid fa-arrow-left"></i> Kembali ke Tindakan
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="max-w-3xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
                    <form method="POST" action="tindakan.php">
                        <input type="hidden" name="simpan_tindakan" value="1">
                        <input type="hidden" name="pelanggaran_siswa_id" value="<?php echo (int)$data['id']; ?>">

                        <h3 class="text-sm font-bold text-black uppercase tracking-wider mb-5">Form Tindakan</h3>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 mb-2">TINDAKAN YANG DILAKUKAN *</label>
                            <textarea name="tindakan_dilakukan" rows="3" required placeholder="Contoh: Pemanggilan orang tua / wali, pembinaan oleh guru BK..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"><?php echo htmlspecialchars($data['tindakan'] ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL TINDAKAN</label>
                                <input type="date" name="tanggal_tindakan" value="<?php echo date('Y-m-d'); ?>" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">STATUS TINDAKAN</label>
                                <select name="status_tindakan" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                                    <option value="Ditindak">Ditindak (Masih Proses)</option>
                                    <option value="Selesai">Selesai (Tindakan Sudah Selesai)</option>
                                </select>
                                <p class="text-[11px] font-bold text-amber-600 mt-1.5">
                                    Jika <b>Selesai</b>, data tindakan tampil di Riwayat dan tidak dapat diubah lagi.
                                </p>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-bold text-slate-700 mb-2">CATATAN TINDAKAN</label>
                            <textarea name="catatan_tindakan" rows="2" placeholder="Hasil tindakan, tanggapan wali murid, dll..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition">
                                <i class="fa-solid fa-gavel mr-2"></i> SIMPAN TINDAKAN
                            </button>
                            <a href="tindakan.php" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold py-3 px-6 rounded-xl text-sm text-center transition">
                                BATAL
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- ===== Daftar Pelanggaran Belum Ditindak ===== -->
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-8">
                <h2 class="text-sm font-bold text-black uppercase tracking-wider mb-4">Pelanggaran Belum Selesai Ditindak</h2>
                <div class="overflow-x-auto">
                    <table id="tabelTindakan" class="w-full text-left border-collapse display">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">TANGGAL</th>
                                <th class="py-3 px-4">SISWA & KELAS</th>
                                <th class="py-3 px-4">PELANGGARAN</th>
                                <th class="py-3 px-4 text-center">POIN</th>
                                <th class="py-3 px-4">DICATAT OLEH</th>
                                <th class="py-3 px-4 text-center">STATUS</th>
                                <th class="py-3 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (!empty($daftar)): ?>
                                <?php foreach ($daftar as $row): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4 px-4 font-bold text-slate-700 whitespace-nowrap">
                                            <?php echo !empty($row['tanggal']) ? date('d/m/Y', strtotime($row['tanggal'])) : '-'; ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa'] ?? 'Siswa Tidak Ditemukan'); ?></div>
                                            <div class="text-xs text-slate-400 font-bold">Kelas: <?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></div>
                                        </td>
                                        <td class="py-4 px-4 font-bold text-slate-800">
                                            <?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>
                                        <td class="py-4 px-4 text-center font-bold text-red-600">+<?php echo (int)($row['poin'] ?? 0); ?></td>
                                        <td class="py-4 px-4 text-slate-600 font-medium">
                                            <?php echo !empty($row['nama_guru']) ? htmlspecialchars($row['nama_guru']) : 'Administrator'; ?>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <?php $st = strtolower(trim((string)$row['status'])); if ($st === '' || $st === 'dicatat'): ?>
                                                <span class="px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700"><i class="fa-solid fa-clock mr-1"></i> Belum Ditindak</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700"><i class="fa-solid fa-gavel mr-1"></i> Ditindak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <?php if (boleh_tindak($row['guru_id'])): ?>
                                                <a href="tindakan.php?id=<?php echo (int)$row['id']; ?>" class="inline-flex items-center gap-1 text-blue-600 font-bold hover:underline">
                                                    <i class="fa-solid fa-gavel"></i> Tindak
                                                </a>
                                            <?php else: ?>
                                                <a href="tindakan.php?id=<?php echo (int)$row['id']; ?>" class="inline-flex items-center gap-1 text-slate-500 font-bold hover:underline" title="Hanya guru yang mencatat yang dapat menindak">
                                                    <i class="fa-solid fa-eye"></i> Lihat
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
        <?php endif; ?>

    </main>

    <!-- jQuery & DataTables (Data Table) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelTindakan').DataTable({
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Tidak ada pelanggaran yang belum selesai ditindak.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>

</body>
</html>
