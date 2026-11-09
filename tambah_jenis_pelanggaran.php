<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pelanggaran_kategori_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_kategori_id']);
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $poin = (int)$_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    $query = "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif, created_at, updated_at)
              VALUES ('$pelanggaran_kategori_id', '$kode', '$nama', '$poin', '$deskripsi', '$status_aktif', NOW(), NOW())";
    try {
        if (mysqli_query($koneksi, $query) === false) {
            throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil ditambahkan!'];
        header("Location: kelola_jenis_pelanggaran.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
            ? 'Gagal menyimpan: kode pelanggaran sudah dipakai!'
            : 'Gagal menyimpan jenis pelanggaran: ' . $e->getMessage()];
    }
}

$kategori_list = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1 ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Jenis Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Jenis Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Tambah data master kode, nama, dan bobot poin pelanggaran.</p>
            </div>
            <a href="kelola_jenis_pelanggaran.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
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
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">KODE *</label>
                        <input type="text" name="kode" required placeholder="P01" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50" maxlength="30">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">BOBOT POIN *</label>
                        <input type="number" name="poin" min="1" required placeholder="10" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">KATEGORI *</label>
                    <select name="pelanggaran_kategori_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Kategori --</option>
                        <?php if ($kategori_list): while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                            <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA PELANGGARAN *</label>
                    <input type="text" name="nama" required placeholder="Terlambat Masuk Sekolah" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50" maxlength="30">
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">DESKRIPSI</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-2">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Pelanggaran Aktif</label>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Jenis Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>