<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Halaman ini untuk updating tindakanatas pelanggaran yang sudah tercatat.
// Data yang sudah berstatus "Selesai" tidak dapat diedit lagi.
$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: riwayat.php");
    exit();
}

// Ambil data pelanggaran
$query = mysqli_query($koneksi, "
    SELECT ps.*, s.nis, s.nama AS nama_siswa_terbaru,
           k.nama AS nama_kelas_ref,
           p.nama AS nama_pelanggaran, p.poin AS poin_jenis
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_siswa s ON ps.siswa_id = s.id
    LEFT JOIN t_kelas k ON ps.kelas_id = k.id
    LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
    WHERE ps.id = '$id'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data pelanggaran tidak ditemukan!'];
    header("Location: riwayat.php");
    exit();
}

// Batas data:
// - Wali Kelas : seluruh kelas (termasuk kelas lain)
// - Guru biasa : kelas yang diampu + catatan pelanggaran yang dicatat sendiri
if ($role === 'Guru') {
    $scope = scope_data_guru($role, $is_wali_kelas, $wali_kelas_kelas_ids, $guru_id_login, 'ps');
    if ($scope === '1 = 0') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak dapat diakses.'];
        header("Location: riwayat.php");
        exit();
    }
    $cek_data = mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa ps WHERE ps.id = '$id' AND $scope LIMIT 1");
    if (!$cek_data || mysqli_num_rows($cek_data) === 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak dapat diakses.'];
        header("Location: riwayat.php");
        exit();
    }
}

// Status saat ini
$status_sekarang = trim((string)($data['status'] ?? ''));
if ($status_sekarang === '') {
    $status_sekarang = 'Dicatat';
}
$terkunci = (strtolower($status_sekarang) === 'selesai');
// Hanya guru yang mencatat pelanggaran (guru A) yang boleh mengubah tindakannya.
// Guru lain hanya dapat melihat datanya.
$boleh_edit = boleh_tindak($data['guru_id']);

// Proses update
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_tindakan'])) {
    if ($terkunci) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: tindakan sudah berstatus Selesai sehingga data tidak dapat diedit.'];
        header("Location: riwayat.php");
        exit();
    }
    if (!$boleh_edit) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: hanya guru yang mencatat pelanggaran ini yang dapat mengubah status tindakannya.'];
        header("Location: riwayat.php");
        exit();
    }

    $tindakan  = mysqli_real_escape_string($koneksi, $_POST['tindakan'] ?? '');
    $tanggal   = mysqli_real_escape_string($koneksi, $_POST['tanggal'] ?? '');
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan'] ?? '');
    $status    = ($_POST['status'] ?? 'Dicatat') === 'Selesai' ? 'Selesai' : 'Dicatat';

    if (trim($tindakan) === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Tindakan Pembinaan tidak boleh kosong.'];
        header("Location: edit_tindakan.php?id=$id");
        exit();
    }

    $tanggal_sql = $tanggal !== '' ? "'$tanggal'" : 'NULL';
    $update = "UPDATE t_pelanggaran_siswa
               SET tindakan = '$tindakan',
                   keterangan = '$keterangan',
                   tanggal = $tanggal_sql,
                   status = '$status',
                   updated_at = NOW()
               WHERE id = '$id'";

    if (mysqli_query($koneksi, $update)) {
        // Catat penindakan pada tabel t_tindakan agar riwayat menampilkan
        // oleh siapa murid itu ditindak beserta data tindakannya.
        $tt_guru_sql = $guru_id_login ? "'" . (int)$guru_id_login . "'" : "NULL";
        $tt_nama     = mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? ''));
        $tt_tanggal  = $tanggal !== '' ? "'$tanggal'" : 'NULL';
        $tt_status   = $status === 'Selesai' ? 'Selesai' : 'Ditindak';
        mysqli_query($koneksi, "INSERT INTO t_tindakan (pelanggaran_siswa_id, guru_id, nama_guru, tanggal_tindakan, tindakan, catatan, status, created_at, updated_at)
                                VALUES ('$id', $tt_guru_sql, '$tt_nama', $tt_tanggal, '$tindakan', '$keterangan', '$tt_status', NOW(), NOW())");

        $pesan = ($status === 'Selesai')
            ? 'Tindakan diperbarui dan ditandai <b>Selesai</b>. Data tidak dapat diedit lagi.'
            : 'Tindakan berhasil diperbarui.';
        $_SESSION['flash'] = ['type' => 'success', 'message' => $pesan];
        header("Location: riwayat.php");
        exit();
    }
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
}

$nama_siswa = !empty($data['nama_siswa_terbaru']) ? $data['nama_siswa_terbaru'] : ($data['nama_siswa'] ?? '-');
$nama_kelas = !empty($data['nama_kelas_ref']) ? $data['nama_kelas_ref'] : ($data['nama_kelas'] ?? '-');
$poin = (int)($data['poin'] ?? 0);
$tanggal_kejadian = !empty($data['tanggal']) ? date('d/m/Y', strtotime($data['tanggal'])) : '-';
$nama_pelapor = !empty($data['nama_guru']) ? $data['nama_guru'] : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Update Tindakan - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 max-w-3xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Update Tindakan</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Perbarui tindakan pembinaan atas pelanggaran siswa.</p>
            </div>
            <a href="riwayat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-3xl mx-auto p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Ringkasan Data Pelanggaran -->
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
                    <p class="font-bold text-red-600">+<?php echo $poin; ?> Poin</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Tanggal Kejadian</p>
                    <p class="font-bold text-black"><?php echo htmlspecialchars($tanggal_kejadian); ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Dicatat Oleh</p>
                    <p class="font-bold text-black"><?php echo htmlspecialchars($nama_pelapor); ?></p>
                </div>
            </div>
        </div>

        <?php if ($terkunci || !$boleh_edit): ?>
            <!-- Data terkunci (sudah selesai) atau petugas lain tidak boleh mengubah -->
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border <?php echo $terkunci ? 'border-green-200' : 'border-amber-200'; ?>">
                <div class="flex items-start gap-4">
                    <div class="p-3 <?php echo $terkunci ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600'; ?> rounded-xl">
                        <i class="fa-solid fa-<?php echo $terkunci ? 'lock' : 'eye'; ?> text-xl"></i>
                    </div>
                    <div>
                        <?php if ($terkunci): ?>
                            <h2 class="text-base font-bold text-black">Data Tindakan Sudah Selesai</h2>
                            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                Tindakan atas pelanggaran ini berstatus <b>Selesai</b>, sehingga data
                                <b>tidak dapat diedit</b> kembali.
                            </p>
                        <?php else: ?>
                            <h2 class="text-base font-bold text-black">Mode Lihat Saja</h2>
                            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                                Anda dapat <b>melihat datanya saja</b>. Status tindakan hanya dapat diubah oleh
                                guru yang mencatat pelanggaran ini:
                                <b><?php echo htmlspecialchars(!empty($data['nama_guru']) ? $data['nama_guru'] : 'Administrator'); ?></b>.
                            </p>
                        <?php endif; ?>
                        <div class="mt-5 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Tindakan Pembinaan</p>
                            <p class="font-bold text-black"><?php echo htmlspecialchars($data['tindakan'] ?? '-'); ?></p>
                        </div>
                        <a href="riwayat.php" class="inline-flex items-center gap-2 mt-5 bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Form Update Tindakan -->
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
                <form method="POST" action="edit_tindakan.php">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="update_tindakan" value="1">

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-2">TINDAKAN PEMBINAAN *</label>
                        <textarea name="tindakan" rows="3" required placeholder="Contoh: Diberi teguran lisan oleh wali kelas..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"><?php echo htmlspecialchars($data['tindakan'] ?? ''); ?></textarea>
                        <p class="text-[11px] font-bold text-slate-400 mt-1.5">
                            Tindakan dapat diperbarui selama belum berstatus Selesai.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL KEJADIAN</label>
                            <input type="date" name="tanggal" value="<?php echo !empty($data['tanggal']) ? date('Y-m-d', strtotime($data['tanggal'])) : date('Y-m-d'); ?>" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">STATUS TINDAKAN</label>
                            <select name="status" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                                <option value="Dicatat" <?php echo $status_sekarang === 'Selesai' ? '' : 'selected'; ?>>Belum Selesai (Tindakan Berlangsung)</option>
                                <option value="Selesai" <?php echo $status_sekarang === 'Selesai' ? 'selected' : ''; ?>>Selesai (Tindakan Sudah Dilakukan)</option>
                            </select>
                            <p class="text-[11px] font-bold text-amber-600 mt-1.5">
                                Jika dipilih <b>Selesai</b>, data ini tidak dapat diedit lagi.
                            </p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-xs font-bold text-slate-700 mb-2">KETERANGAN / DESKRIPSI</label>
                        <textarea name="keterangan" rows="3" placeholder="Rincian kronologi atau detail pelanggaran..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"><?php echo htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition">
                            <i class="fa-solid fa-pen mr-2"></i> SIMPAN PERUBAHAN
                        </button>
                        <a href="riwayat.php" class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold py-3 px-6 rounded-xl text-sm text-center transition">
                            BATAL
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>
