<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: kelola_jenis_pelanggaran.php"); exit(); }

$query_data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE id = '$id'");
$data = mysqli_fetch_assoc($query_data);
if (!$data) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan!'];
    header("Location: kelola_jenis_pelanggaran.php"); exit();
}

$kategori_list = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1 ORDER BY nama ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pelanggaran_kategori_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_kategori_id']);
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $poin = (int)$_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    $query = "UPDATE t_pelanggaran SET
              pelanggaran_kategori_id = '$pelanggaran_kategori_id',
              kode = '$kode',
              nama = '$nama',
              poin = '$poin',
              deskripsi = '$deskripsi',
              status_aktif = '$status_aktif',
              updated_at = NOW()
              WHERE id = '$id'";
    try {
        if (mysqli_query($koneksi, $query) === false) {
            throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
        }
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data jenis pelanggaran berhasil diperbarui!'];
        header("Location: kelola_jenis_pelanggaran.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
            ? 'Gagal menyimpan: kode pelanggaran sudah dipakai!'
            : 'Gagal menyimpan jenis pelanggaran: ' . $e->getMessage()];
        header("Location: kelola_jenis_pelanggaran.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Jenis Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Edit Jenis Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Perbarui data master kode, nama, dan bobot poin pelanggaran.</p>
            </div>
            <a href="kelola_jenis_pelanggaran.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">KODE *</label>
                        <input type="text" name="kode" required value="<?php echo htmlspecialchars($data['kode']); ?>" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50" maxlength="30">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">BOBOT POIN *</label>
                        <input type="number" name="poin" min="1" required value="<?php echo $data['poin']; ?>" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">KATEGORI *</label>
                    <select name="pelanggaran_kategori_id" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Kategori --</option>
                        <?php if ($kategori_list): while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo $data['pelanggaran_kategori_id'] == $kat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($kat['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA PELANGGARAN *</label>
                    <input type="text" name="nama" required value="<?php echo htmlspecialchars($data['nama']); ?>" placeholder="Terlambat Masuk Sekolah" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50" maxlength="30">
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">DESKRIPSI</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-2">
                    <input type="checkbox" name="status_aktif" value="1" <?php echo $data['status_aktif'] == 1 ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Set Pelanggaran Aktif</label>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Perbarui Jenis Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>