<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Fetch data dropdown secara aman
$kelas_list = @mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");
$ta_list = tahun_ajaran_pilihan($koneksi);

// Tahun ajaran aktif otomatis terpilih pada kolom "Pilih Tahun Ajaran"
$ta_aktif_id = tahun_ajaran_aktif_id($koneksi);
$selected_ta = $ta_aktif_id;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Cetak & Export Laporan - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-black">Cetak & Export Laporan</h1>
            <p class="text-sm font-bold text-slate-500 mt-1">Generate dan cetak laporan kedisiplinan siswa dalam berbagai format.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card Export Pelanggaran -->
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                        <i class="fa-solid fa-file-pdf text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-black">Rekap Pelanggaran Siswa</h3>
                        <p class="text-xs text-slate-400 font-bold">Laporan detail poin dan riwayat pelanggaran.</p>
                    </div>
                </div>
                <!-- Menghapus target="_blank" agar membuka di tab yang sama -->
                <form action="laporan.php" method="GET" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">TAHUN AJARAN</label>
                        <select name="tahun_ajaran_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                            <option value="">-- Semua Tahun Ajaran --</option>
                            <?php if ($ta_list): while ($ta = mysqli_fetch_assoc($ta_list)):
                                $label_ta = nama_tahun_ajaran($ta, 'Ganjil');
                            ?>
                                <option value="<?php echo $ta['id']; ?>"<?php echo ((int)$ta['id'] === (int)$selected_ta) ? ' selected' : ''; ?>><?php echo htmlspecialchars($label_ta); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">KELAS</label>
                        <select name="kelas_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                            <option value="">-- Semua Kelas --</option>
                            <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button type="submit" name="export" value="pdf" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-print"></i> Cetak PDF
                        </button>
                        <button type="submit" name="export" value="excel" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold shadow-sm transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card Rekap Poin (Admin, Guru & Wali Kelas) -->
            <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                        <i class="fa-solid fa-calculator text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-black">Rekap Akumulasi Poin</h3>
                        <p class="text-xs text-slate-400 font-bold">Ringkasan total poin per siswa.</p>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-6 text-center">
                    <i class="fa-solid fa-calculator text-2xl text-blue-600 mb-3"></i>
                    <p class="text-sm font-bold text-slate-700">Rekap Poin &amp; Cetak Poin</p>
                    <p class="text-xs font-bold text-slate-500 mt-2 leading-relaxed">
                        Lihat akumulasi poin seluruh siswa, lalu cetak langsung dari halaman rekap.
                    </p>
                    <a href="rekap_point.php" class="inline-flex items-center gap-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-lg text-sm shadow-sm transition">
                        <i class="fa-solid fa-arrow-right"></i> Buka Rekap Poin
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
