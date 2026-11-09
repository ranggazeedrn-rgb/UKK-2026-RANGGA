<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$selected_ta = (int)($_GET['tahun_ajaran_id'] ?? 0);
$selected_kelas = (int)($_GET['kelas_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun_ajaran_id = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran_id']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);

    $tanggal_mulai = $tanggal_selesai = date('Y-m-d');
    if ($res_ta = mysqli_query($koneksi, "SELECT tanggal_mulai, tanggal_selesai FROM t_tahun_ajaran WHERE id = '$tahun_ajaran_id'")) {
        if ($ta = mysqli_fetch_assoc($res_ta)) {
            $tanggal_mulai = $ta['tanggal_mulai'];
            $tanggal_selesai = $ta['tanggal_selesai'];
        }
    }

    $query = "INSERT INTO t_kelas_siswa (tahun_ajaran_id, kelas_id, siswa_id, tanggal_mulai, tanggal_selesai, status_aktif)
              VALUES ('$tahun_ajaran_id', '$kelas_id', '$siswa_id', '$tanggal_mulai', '$tanggal_selesai', 1)";
    try {
        if (mysqli_query($koneksi, $query) === false) {
            throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Siswa berhasil ditempatkan ke kelas!'];
        header("Location: penempatan_siswa.php?tahun_ajaran_id=$tahun_ajaran_id&kelas_id=$kelas_id");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
            ? 'Gagal: siswa sudah punya kelas pada tahun ajaran ini!'
            : 'Gagal menyimpan penempatan: ' . $e->getMessage()];
    }
}

$ta_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC");
$kelas_list = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");

if ($selected_ta) {
    $siswa_unallocated = mysqli_query($koneksi, "SELECT s.* FROM t_siswa s
        WHERE NOT EXISTS (SELECT 1 FROM t_kelas_siswa ks WHERE ks.siswa_id = s.id AND ks.tahun_ajaran_id = '$selected_ta')
        ORDER BY s.nama ASC");
} else {
    $siswa_unallocated = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE 0");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Penempatan Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Penempatan Siswa</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Tempatkan siswa ke kelas pada tahun ajaran tertentu.</p>
            </div>
            <a href="penempatan_siswa.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-4xl mx-auto p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">TAHUN AJARAN *</label>
                    <select name="tahun_ajaran_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php if ($ta_list): while ($ta = mysqli_fetch_assoc($ta_list)):
                            $t_name = $ta['tahun_ajaran'] ?? $ta['nama'] ?? $ta['tahun'] ?? '-';
                            $t_sem = isset($ta['semester']) ? ucfirst($ta['semester']) : 'Ganjil';
                        ?>
                            <option value="<?php echo $ta['id']; ?>" <?php echo $selected_ta == $ta['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t_name); ?> (<?php echo htmlspecialchars($t_sem); ?>)
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">KELAS *</label>
                    <select name="kelas_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Kelas --</option>
                        <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama']); ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-2">PILIH SISWA *</label>
                    <select name="siswa_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Siswa --</option>
                        <?php if ($siswa_unallocated): while ($s = mysqli_fetch_assoc($siswa_unallocated)): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nis']); ?> - <?php echo htmlspecialchars($s['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Hanya menampilkan siswa yang belum ditempatkan pada tahun ajaran terpilih.</p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Penempatan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>