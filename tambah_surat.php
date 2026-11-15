<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Ambil data siswa yang poinnya melewati batas aman
$siswa_id = isset($_GET['siswa_id']) ? (int)$_GET['siswa_id'] : 0;

// Data siswa & total poin
$siswa_data = null;
if ($siswa_id > 0) {
    $q = mysqli_query($koneksi, "
        SELECT s.id, s.nis, s.nama, s.alamat,
               COALESCE(
                   (SELECT k.nama FROM t_kelas_siswa ks JOIN t_kelas k ON ks.kelas_id = k.id WHERE ks.siswa_id = s.id ORDER BY ks.id DESC LIMIT 1),
                   '-'
               ) AS nama_kelas,
               COALESCE(
                   (SELECT ks.kelas_id FROM t_kelas_siswa ks WHERE ks.siswa_id = s.id ORDER BY ks.id DESC LIMIT 1),
                   NULL
               ) AS kelas_id,
               COALESCE(
                   (SELECT SUM(p.poin) FROM t_pelanggaran_siswa ps JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id WHERE ps.siswa_id = s.id),
                   0
               ) AS total_poin
        FROM t_siswa s
        WHERE s.id = $siswa_id
        LIMIT 1
    ");
    if ($q && mysqli_num_rows($q) > 0) {
        $siswa_data = mysqli_fetch_assoc($q);
    }
}

// Tentukan jenis surat berdasarkan poin
$jenis_surat = 'Surat Peringatan';
if ($siswa_data && (int)$siswa_data['total_poin'] >= 50) {
    $jenis_surat = 'Surat Panggilan Orang Tua';
}

// Ambil daftar pelanggaran siswa ini
$daftar_pelanggaran = [];
if ($siswa_id > 0) {
    $q_pel = mysqli_query($koneksi, "
        SELECT ps.tanggal, p.nama AS nama_pelanggaran, p.poin, ps.keterangan
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
        WHERE ps.siswa_id = $siswa_id
        ORDER BY ps.tanggal ASC
    ");
    if ($q_pel) {
        while ($r = mysqli_fetch_assoc($q_pel)) {
            $daftar_pelanggaran[] = $r;
        }
    }
}

// Nomor surat otomatis
$tahun = date('Y');
$bulan_romawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
$bulan = (int)date('n');
$q_count = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM t_surat WHERE YEAR(tanggal_surat) = $tahun");
$total_surat = $q_count ? (int)mysqli_fetch_assoc($q_count)['total'] : 0;
$nomor_urut = str_pad($total_surat + 1, 3, '0', STR_PAD_LEFT);
$nomor_surat = "$nomor_urut/SURAT-KES/$bulan_romawi[$bulan]/$tahun";

// Proses simpan surat
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['simpan_surat'])) {
    $post_siswa_id     = (int)$_POST['siswa_id'];
    $post_kelas_id     = !empty($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : 'NULL';
    $post_nomor        = mysqli_real_escape_string($koneksi, $_POST['nomor_surat']);
    $post_jenis        = mysqli_real_escape_string($koneksi, $_POST['jenis_surat']);
    $post_perihal      = mysqli_real_escape_string($koneksi, $_POST['perihal']);
    $post_isi          = mysqli_real_escape_string($koneksi, $_POST['isi_surat']);
    $post_tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat']);
    $post_total_poin   = (int)($_POST['total_poin'] ?? 0);
    $post_dibuat_oleh  = (int)$_SESSION['user_id'];
    $post_nama_pembuat = mysqli_real_escape_string($koneksi, $_SESSION['name'] ?? 'Pengguna');

    $kelas_val = is_int($post_kelas_id) ? "'$post_kelas_id'" : $post_kelas_id;

    $query_insert = "INSERT INTO t_surat (nomor_surat, jenis_surat, siswa_id, kelas_id, total_poin, perihal, isi_surat, tanggal_surat, dibuat_oleh, nama_pembuat, status, created_at, updated_at)
                     VALUES ('$post_nomor', '$post_jenis', '$post_siswa_id', $kelas_val, '$post_total_poin', '$post_perihal', '$post_isi', '$post_tanggal', '$post_dibuat_oleh', '$post_nama_pembuat', 'Diterbitkan', NOW(), NOW())";

    if (mysqli_query($koneksi, $query_insert)) {
        $new_id = mysqli_insert_id($koneksi);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Surat berhasil dibuat! Anda dapat mencetak surat dari halaman Kelola Surat.'];
        header("Location: cetak_surat.php?id=$new_id");
        exit();
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan surat: ' . mysqli_error($koneksi)];
    }
}

// Isi surat otomatis
$isi_default = '';
if ($siswa_data) {
    $nama = htmlspecialchars($siswa_data['nama']);
    $kelas = htmlspecialchars($siswa_data['nama_kelas']);
    $poin = (int)$siswa_data['total_poin'];

    if ($poin >= 50) {
        $isi_default = "Dengan hormat,\n\nMelalui surat ini kami sampaikan bahwa putra/putri Bapak/Ibu:\n\nNama  : $nama\nKelas : $kelas\nTotal Poin Pelanggaran : $poin Poin\n\nTelah melampaui batas akumulasi poin kedisiplinan yang ditetapkan sekolah (≥ 50 Poin). Berdasarkan peraturan tata tertib sekolah, kami mengundang Bapak/Ibu untuk hadir ke sekolah guna membahas langkah pembinaan lebih lanjut bagi putra/putri Bapak/Ibu.\n\nWaktu : (akan dikonfirmasi oleh Wali Kelas / BK)\nTempat : Ruang BK / Ruang Kepala Sekolah\n\nKehadiran Bapak/Ibu sangat kami harapkan demi perbaikan perilaku dan kedisiplinan siswa.\n\nAtas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.";
    } else {
        $isi_default = "Dengan hormat,\n\nMelalui surat ini kami sampaikan bahwa siswa:\n\nNama  : $nama\nKelas : $kelas\nTotal Poin Pelanggaran : $poin Poin\n\nTelah melampaui batas aman akumulasi poin kedisiplinan yang ditetapkan sekolah (≥ 20 Poin - Peringatan). Surat ini merupakan peringatan resmi agar siswa yang bersangkutan segera memperbaiki perilaku dan mematuhi tata tertib sekolah.\n\nApabila pelanggaran terus berlanjut dan akumulasi poin mencapai ≥ 50 Poin, pihak sekolah akan mengirimkan Surat Panggilan Orang Tua.\n\nDemikian surat peringatan ini dibuat untuk dapat diperhatikan sebagaimana mestinya.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Buat Surat - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 max-w-3xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Buat Surat</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Buat surat peringatan atau panggilan orang tua untuk siswa yang melebihi batas poin.</p>
            </div>
            <a href="kelola_surat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-list"></i> Kelola Surat
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-3xl mx-auto p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <?php if (!$siswa_data): ?>
            <div class="max-w-3xl mx-auto bg-amber-50 border border-amber-200 text-amber-800 p-6 rounded-2xl text-sm font-bold">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                Siswa tidak ditemukan atau parameter <code>siswa_id</code> belum diberikan.
                Silahkan buat surat dari halaman <a href="dashboard.php" class="underline">Dashboard</a> dengan menekan tombol "Buat Surat" pada siswa yang melebihi batas poin.
            </div>
        <?php else: ?>

            <!-- Info Siswa -->
            <div class="max-w-3xl mx-auto mb-6 bg-red-50 border border-red-200 text-red-800 p-5 rounded-2xl">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-user-exclamation mt-1 text-lg"></i>
                    <div class="flex-1">
                        <p class="font-bold text-base"><?php echo htmlspecialchars($siswa_data['nama']); ?></p>
                        <p class="text-sm mt-0.5">NIS: <?php echo htmlspecialchars($siswa_data['nis'] ?? '-'); ?> &bull; Kelas: <?php echo htmlspecialchars($siswa_data['nama_kelas']); ?></p>
                        <p class="text-sm font-bold mt-1">Total Poin: <span class="text-red-600 text-lg"><?php echo (int)$siswa_data['total_poin']; ?> Poin</span></p>
                    </div>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold <?php echo (int)$siswa_data['total_poin'] >= 50 ? 'bg-red-200 text-red-800' : 'bg-amber-200 text-amber-800'; ?>">
                        <?php echo (int)$siswa_data['total_poin'] >= 50 ? 'Panggilan Ortu' : 'Peringatan'; ?>
                    </span>
                </div>
            </div>

            <!-- Riwayat Pelanggaran -->
            <?php if (!empty($daftar_pelanggaran)): ?>
            <div class="max-w-3xl mx-auto mb-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3"><i class="fa-solid fa-list-check mr-1"></i> Riwayat Pelanggaran Siswa</h3>
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="py-2 px-3 text-left">Tanggal</th>
                            <th class="py-2 px-3 text-left">Pelanggaran</th>
                            <th class="py-2 px-3 text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar_pelanggaran as $dp): ?>
                        <tr class="border-b border-slate-100">
                            <td class="py-2 px-3 font-bold text-slate-600 whitespace-nowrap"><?php echo date('d/m/Y', strtotime($dp['tanggal'])); ?></td>
                            <td class="py-2 px-3 text-slate-700"><?php echo htmlspecialchars($dp['nama_pelanggaran'] ?? '-'); ?></td>
                            <td class="py-2 px-3 text-center font-bold text-red-600">+<?php echo (int)$dp['poin']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Form Buat Surat -->
            <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                <form method="POST" action="tambah_surat.php?siswa_id=<?php echo $siswa_id; ?>">
                    <input type="hidden" name="simpan_surat" value="1">
                    <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
                    <input type="hidden" name="kelas_id" value="<?php echo $siswa_data['kelas_id'] ?? ''; ?>">
                    <input type="hidden" name="total_poin" value="<?php echo (int)$siswa_data['total_poin']; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Nomor Surat</label>
                            <input type="text" name="nomor_surat" value="<?php echo htmlspecialchars($nomor_surat); ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" value="<?php echo date('Y-m-d'); ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Surat</label>
                        <select name="jenis_surat" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="Surat Peringatan" <?php echo $jenis_surat === 'Surat Peringatan' ? 'selected' : ''; ?>>Surat Peringatan (Poin ≥ 20)</option>
                            <option value="Surat Panggilan Orang Tua" <?php echo $jenis_surat === 'Surat Panggilan Orang Tua' ? 'selected' : ''; ?>>Surat Panggilan Orang Tua (Poin ≥ 50)</option>
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Perihal</label>
                        <input type="text" name="perihal" value="<?php echo $jenis_surat === 'Surat Panggilan Orang Tua' ? 'Panggilan Orang Tua / Wali Murid' : 'Peringatan Kedisiplinan Siswa'; ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Isi Surat</label>
                        <textarea name="isi_surat" rows="12" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50 leading-relaxed"><?php echo htmlspecialchars($isi_default); ?></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl py-3 text-sm transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-envelope"></i> Terbitkan Surat
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
