<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: kelola_siswa.php"); exit(); }

$query_data = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id = '$id'");
$data = mysqli_fetch_assoc($query_data);
if (!$data) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data tidak ditemukan!'];
    header("Location: kelola_siswa.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if (strlen($nis) < 9 || strlen($nis) > 15 || !ctype_digit($nis)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: NIS harus 9 sampai 15 digit angka!'];
    } elseif (strlen($nisn) > 10 || ($nisn !== '' && !ctype_digit($nisn))) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal update: NISN maksimal 10 digit angka!'];
    } else {
        $query = "UPDATE t_siswa SET nis=NULLIF('$nis', ''), nisn=NULLIF('$nisn', ''), nama='$nama', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', alamat='$alamat', status_aktif='$status_aktif', updated_at=NOW() WHERE id='$id'";

        try {
            if (mysqli_query($koneksi, $query) === false) {
                throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil diperbarui!'];
            header("Location: kelola_siswa.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            // 1062 = unique index dilanggar; nama index menunjukkan kolomnya (nisn dicek dulu, "nis" adalah bagian dari "nisn")
            $kolom = strpos($e->getMessage(), 'uq_t_siswa_nisn') !== false ? 'NISN' : 'NIS';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
                ? "Gagal update: $kolom sudah dipakai siswa lain!"
                : 'Gagal update data: ' . $e->getMessage()];
            // halaman edit tidak menampilkan flash; pesan dilihat di daftar siswa
            header("Location: kelola_siswa.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Edit Data Siswa</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Perbarui profil siswa di sistem.</p>
            </div>
            <a href="kelola_siswa.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="">
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NIS * (Maks. 15 karakter)</label>
                        <input type="text" name="nis" inputmode="numeric" pattern="[0-9]{9,15}" minlength="9" maxlength="15" oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="<?php echo htmlspecialchars($data['nis']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NISN (Maks. 10 karakter)</label>
                        <input type="text" name="nisn" inputmode="numeric" pattern="[0-9]{1,10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="<?php echo htmlspecialchars($data['nisn']); ?>" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP *</label>
                    <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">JENIS KELAMIN *</label>
                        <select name="jenis_kelamin" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="L" <?php echo $data['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                            <option value="P" <?php echo $data['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL LAHIR *</label>
                        <input type="date" name="tanggal_lahir" value="<?php echo $data['tanggal_lahir']; ?>" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">ALAMAT</label>
                    <textarea name="alamat" rows="3" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"><?php echo htmlspecialchars($data['alamat']); ?></textarea>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-2">
                    <input type="checkbox" name="status_aktif" value="1" <?php echo $data['status_aktif'] == 1 ? 'checked' : ''; ?> class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Status Aktif</label>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Perbarui Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
