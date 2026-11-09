<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun_ajaran_id = mysqli_real_escape_string($koneksi, $_POST['tahun_ajaran_id']);
    $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
    $guru_id = mysqli_real_escape_string($koneksi, $_POST['guru_id']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    $cek_guru = mysqli_query($koneksi, "SELECT id FROM t_wali_kelas WHERE guru_id = '$guru_id' AND tahun_ajaran_id = '$tahun_ajaran_id' LIMIT 1");
    if ($cek_guru && mysqli_num_rows($cek_guru) > 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal: guru ini sudah menjadi wali kelas pada tahun ajaran tersebut!'];
    } else {
        $query = "INSERT INTO t_wali_kelas (tahun_ajaran_id, kelas_id, guru_id, status_aktif, created_at, updated_at)
                  VALUES ('$tahun_ajaran_id', '$kelas_id', '$guru_id', '$status_aktif', NOW(), NOW())";
        try {
            if (mysqli_query($koneksi, $query) === false) {
                throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Wali kelas berhasil ditetapkan!'];
            header("Location: walikelas.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
                ? 'Gagal: kelas ini sudah punya wali kelas pada tahun ajaran tersebut!'
                : 'Gagal menyimpan wali kelas: ' . $e->getMessage()];
        }
    }
}

$guru_list = mysqli_query($koneksi, "SELECT g.* FROM t_guru g
    WHERE NOT EXISTS (SELECT 1 FROM t_wali_kelas wk WHERE wk.guru_id = g.id)
    ORDER BY g.nama ASC");
$kelas_list = mysqli_query($koneksi, "SELECT k.* FROM t_kelas k
    WHERE NOT EXISTS (SELECT 1 FROM t_wali_kelas wk WHERE wk.kelas_id = k.id)
    ORDER BY k.nama ASC");
$ta_list = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif = 1 ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Wali Kelas - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tetapkan Wali Kelas</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Tentukan guru sebagai wali kelas per tahun ajaran.</p>
            </div>
            <a href="walikelas.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
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
                            <option value="<?php echo $ta['id']; ?>">
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
                            <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Hanya menampilkan kelas yang belum punya wali kelas.</p>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">GURU (WALI KELAS) *</label>
                    <select name="guru_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Guru --</option>
                        <?php if ($guru_list): while ($g = mysqli_fetch_assoc($guru_list)): ?>
                            <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Hanya menampilkan guru yang belum menjadi wali kelas.</p>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-2">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Sebagai Wali Kelas Aktif</label>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Wali Kelas
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>