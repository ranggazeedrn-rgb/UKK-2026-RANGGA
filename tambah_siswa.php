<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

    if (strlen($nis) < 9 || strlen($nis) > 15 || !ctype_digit($nis)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan: NIS harus 9 sampai 15 digit angka!'];
    } elseif (strlen($nisn) > 10 || ($nisn !== '' && !ctype_digit($nisn))) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal simpan: NISN maksimal 10 digit angka!'];
    } else {
        $query = "INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif, created_at, updated_at)
                  VALUES (NULLIF('$nis', ''), NULLIF('$nisn', ''), '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', '$status_aktif', NOW(), NOW())";

        try {
            if (mysqli_query($koneksi, $query) === false) {
                throw new mysqli_sql_exception(mysqli_error($koneksi), mysqli_errno($koneksi));
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data Siswa berhasil ditambahkan!'];
            header("Location: kelola_siswa.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            // 1062 = unique index dilanggar; nama index menunjukkan kolomnya (nisn dicek dulu, "nis" adalah bagian dari "nisn")
            $kolom = strpos($e->getMessage(), 'uq_t_siswa_nisn') !== false ? 'NISN' : 'NIS';
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getCode() === 1062
                ? "Gagal simpan: $kolom sudah dipakai siswa lain!"
                : 'Gagal simpan data: ' . $e->getMessage()];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Siswa - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    <?php include 'layouts/sidebar.php'; ?>
    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Tambah Data Siswa</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Daftarkan profil siswa baru pada sistem.</p>
            </div>
            <a href="kelola_siswa.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
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
                        <label class="block text-xs font-bold text-slate-700 mb-2">NIS * (Maks. 15 karakter)</label>
                        <input type="text" name="nis" inputmode="numeric" pattern="[0-9]{9,15}" minlength="9" maxlength="15" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">NISN (Maks. 10 karakter)</label>
                        <input type="text" name="nisn" inputmode="numeric" pattern="[0-9]{1,10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP *</label>
                    <input type="text" name="nama" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">JENIS KELAMIN *</label>
                        <select name="jenis_kelamin" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL LAHIR *</label>
                        <input type="date" name="tanggal_lahir" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">ALAMAT</label>
                    <textarea name="alamat" rows="3" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                </div>

                <div class="flex items-center gap-2 mb-6 mt-2">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500">
                    <label class="text-sm font-bold text-slate-700">Status Aktif</label>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Data Siswa
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
