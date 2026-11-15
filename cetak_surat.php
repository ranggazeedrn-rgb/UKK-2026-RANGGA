<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID surat tidak valid.'];
    header("Location: kelola_surat.php");
    exit();
}

$q = mysqli_query($koneksi, "
    SELECT sr.*, s.nis, s.nama AS nama_siswa, s.alamat, k.nama AS nama_kelas
    FROM t_surat sr
    LEFT JOIN t_siswa s ON sr.siswa_id = s.id
    LEFT JOIN t_kelas k ON sr.kelas_id = k.id
    WHERE sr.id = $id
    LIMIT 1
");

if (!$q || mysqli_num_rows($q) == 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Surat tidak ditemukan.'];
    header("Location: kelola_surat.php");
    exit();
}

$surat = mysqli_fetch_assoc($q);

// Ambil daftar pelanggaran siswa
$daftar_pelanggaran = [];
$q_pel = mysqli_query($koneksi, "
    SELECT ps.tanggal, p.nama AS nama_pelanggaran, p.poin, ps.keterangan
    FROM t_pelanggaran_siswa ps
    LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
    WHERE ps.siswa_id = " . (int)$surat['siswa_id'] . "
    ORDER BY ps.tanggal ASC
");
if ($q_pel) {
    while ($r = mysqli_fetch_assoc($q_pel)) {
        $daftar_pelanggaran[] = $r;
    }
}

// 1. Ambil nama & NIP guru yang menindak (prioritas dari t_tindakan, fallback ke t_pelanggaran_siswa)
$nama_guru_penindak = '-';
$nip_guru_penindak = '';
$q_penindak = mysqli_query($koneksi, "
    SELECT tt.nama_guru, g.nip
    FROM t_tindakan tt
    LEFT JOIN t_guru g ON tt.guru_id = g.id
    JOIN t_pelanggaran_siswa ps ON tt.pelanggaran_siswa_id = ps.id
    WHERE ps.siswa_id = " . (int)$surat['siswa_id'] . " AND tt.nama_guru IS NOT NULL AND tt.nama_guru != ''
    ORDER BY tt.id DESC LIMIT 1
");

if ($q_penindak && mysqli_num_rows($q_penindak) > 0) {
    $row_p = mysqli_fetch_assoc($q_penindak);
    $nama_guru_penindak = $row_p['nama_guru'];
    $nip_guru_penindak = $row_p['nip'] ?? '';
} else {
    // Fallback ke guru pencatat pelanggaran
    $q_pencatat = mysqli_query($koneksi, "
        SELECT ps.nama_guru, g.nip
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_guru g ON ps.guru_id = g.id
        WHERE ps.siswa_id = " . (int)$surat['siswa_id'] . " AND ps.nama_guru IS NOT NULL AND ps.nama_guru != ''
        ORDER BY ps.id DESC LIMIT 1
    ");
    if ($q_pencatat && mysqli_num_rows($q_pencatat) > 0) {
        $row_pc = mysqli_fetch_assoc($q_pencatat);
        $nama_guru_penindak = $row_pc['nama_guru'];
        $nip_guru_penindak = $row_pc['nip'] ?? '';
    }
}

// Fallback jika belum pernah ada guru penindak tercatat
if (empty($nama_guru_penindak) || $nama_guru_penindak === '-') {
    if (!empty($surat['nama_pembuat']) && strtolower($surat['nama_pembuat']) !== 'administrator') {
        $nama_guru_penindak = $surat['nama_pembuat'];
    } else {
        $q_fb_guru = mysqli_query($koneksi, "SELECT nama, nip FROM t_guru WHERE status_aktif = 1 ORDER BY id ASC LIMIT 1");
        if ($q_fb_guru && mysqli_num_rows($q_fb_guru) > 0) {
            $row_fb = mysqli_fetch_assoc($q_fb_guru);
            $nama_guru_penindak = $row_fb['nama'];
            $nip_guru_penindak = $row_fb['nip'] ?? '';
        }
    }
}
if (empty($nip_guru_penindak) && $nama_guru_penindak !== '-') {
    $q_nip_cari = mysqli_query($koneksi, "SELECT nip FROM t_guru WHERE nama = '" . mysqli_real_escape_string($koneksi, $nama_guru_penindak) . "' LIMIT 1");
    if ($q_nip_cari && mysqli_num_rows($q_nip_cari) > 0) {
        $nip_guru_penindak = mysqli_fetch_assoc($q_nip_cari)['nip'] ?? '';
    }
}

// 2. Ambil data wali kelas siswa dari t_wali_kelas
$nama_wali_kelas = '-';
$nip_wali_kelas = '';
if (!empty($surat['kelas_id'])) {
    $q_wk = mysqli_query($koneksi, "
        SELECT g.nama, g.nip FROM t_wali_kelas wk
        JOIN t_guru g ON wk.guru_id = g.id
        WHERE wk.kelas_id = " . (int)$surat['kelas_id'] . " AND wk.status_aktif = 1
        LIMIT 1
    ");
    if ($q_wk && mysqli_num_rows($q_wk) > 0) {
        $row_wk = mysqli_fetch_assoc($q_wk);
        $nama_wali_kelas = $row_wk['nama'];
        $nip_wali_kelas = $row_wk['nip'] ?? '';
    }
}

// Fallback cari wali kelas dari t_kelas_siswa jika kelas_id pada surat belum ada
if (empty($nama_wali_kelas) || $nama_wali_kelas === '-') {
    $q_wk_fallback = mysqli_query($koneksi, "
        SELECT g.nama, g.nip, k.nama AS nama_kelas_fb
        FROM t_kelas_siswa ks
        JOIN t_wali_kelas wk ON ks.kelas_id = wk.kelas_id
        JOIN t_guru g ON wk.guru_id = g.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        WHERE ks.siswa_id = " . (int)$surat['siswa_id'] . " AND wk.status_aktif = 1
        ORDER BY ks.id DESC LIMIT 1
    ");
    if ($q_wk_fallback && mysqli_num_rows($q_wk_fallback) > 0) {
        $row_wkb = mysqli_fetch_assoc($q_wk_fallback);
        $nama_wali_kelas = $row_wkb['nama'];
        $nip_wali_kelas = $row_wkb['nip'] ?? '';
        if (empty($surat['nama_kelas']) || $surat['nama_kelas'] === '-') {
            $surat['nama_kelas'] = $row_wkb['nama_kelas_fb'];
        }
    }
}

// Jika dibuka oleh Wali Kelas dan user yang login adalah wali kelas yang bersangkutan
if ($role === 'Guru' && $is_wali_kelas && !empty($guru_nama_login)) {
    if (in_array((int)$surat['kelas_id'], $wali_kelas_kelas_ids, true) || $nama_wali_kelas === '-') {
        $nama_wali_kelas = $guru_nama_login;
        if ($guru_id_login) {
            $q_nip_login = mysqli_query($koneksi, "SELECT nip FROM t_guru WHERE id = " . (int)$guru_id_login . " LIMIT 1");
            if ($q_nip_login && mysqli_num_rows($q_nip_login) > 0) {
                $nip_wali_kelas = mysqli_fetch_assoc($q_nip_login)['nip'] ?? $nip_wali_kelas;
            }
        }
    }
}

// 3. Data Admin jika dibuka oleh Admin
$nama_admin = $_SESSION['name'] ?? 'Administrator';

// 4. Mode Tanda Tangan:
// Default: ikuti role user yang login ('admin' atau 'walikelas')
// Admin dapat melihat simulasi mode wali kelas via parameter ?mode=walikelas
$mode_ttd = ($role === 'Admin') ? 'admin' : 'walikelas';
if (isset($_GET['mode']) && in_array($_GET['mode'], ['admin', 'walikelas'], true)) {
    if ($role === 'Admin') {
        $mode_ttd = $_GET['mode'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat - <?php echo htmlspecialchars($surat['jenis_surat']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Standar Ukuran Kertas A4 (210mm x 297mm) */
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }

        .surat-container {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 0 auto;
            background: #ffffff;
            box-sizing: border-box;
        }

        .no-split {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media print {
            .no-print { display: none !important; }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: auto !important;
                height: auto !important;
                min-height: auto !important;
            }
            .surat-container {
                width: 100% !important;
                min-height: auto !important;
                padding: 10mm 15mm !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-200 text-slate-800 py-8">

    <div class="w-[210mm] max-w-full mx-auto mb-6 flex flex-wrap justify-between items-center gap-3 no-print px-4 md:px-0">
        <a href="kelola_surat.php" class="text-sm font-bold text-slate-600 hover:text-black">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Kelola Surat
        </a>
        <div class="flex items-center gap-2">
            <?php if ($role === 'Admin'): ?>
                <div class="inline-flex rounded-lg border border-slate-300 bg-white p-1 text-xs font-bold shadow-sm">
                    <span class="px-2 py-1 text-slate-400">Mode TTD:</span>
                    <a href="cetak_surat.php?id=<?php echo $id; ?>&mode=admin" class="px-2.5 py-1 rounded-md transition <?php echo $mode_ttd === 'admin' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'; ?>">
                        <i class="fa-solid fa-user-shield mr-1"></i> Admin
                    </a>
                    <a href="cetak_surat.php?id=<?php echo $id; ?>&mode=walikelas" class="px-2.5 py-1 rounded-md transition <?php echo $mode_ttd === 'walikelas' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100'; ?>">
                        <i class="fa-solid fa-chalkboard-user mr-1"></i> Wali Kelas
                    </a>
                </div>
            <?php else: ?>
                <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-white text-slate-600 border border-slate-300 shadow-sm">
                    <i class="fa-solid fa-signature mr-1 text-blue-600"></i> Mode TTD: <b class="text-blue-600">Wali Kelas</b>
                </span>
            <?php endif; ?>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Surat (A4)
            </button>
        </div>
    </div>

    <div class="surat-container rounded-2xl shadow-md border border-slate-300" style="font-family: 'Times New Roman', Times, serif;">

        <!-- Kop Surat -->
        <div class="text-center border-b-4 border-double border-black pb-4 mb-6">
            <h3 class="text-sm font-bold uppercase tracking-widest text-slate-700">Majelis Pendidikan Dasar Menengah dan Pendidikan Nonformal</h3>
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800">Pimpinan Daerah Muhammadiyah Kota Tasikmalaya</h4>
            <h2 class="text-xl font-bold uppercase tracking-wider text-black mt-1">SMK MUHAMMADIYAH KOTA TASIKMALAYA</h2>
            <p class="text-xs mt-1">Jl. Rumah Sakit No. 29, Kel. Empangsari, Kec. Tawang, Kota Tasikmalaya, Jawa Barat 46113</p>
            <p class="text-xs">Telp. (0265) 327380 &bull; Email: info@smkmtsm.sch.id &bull; Website: www.smkmtsm.sch.id</p>
        </div>

        <!-- Judul Surat -->
        <div class="text-center mb-6">
            <h1 class="text-base font-bold uppercase underline tracking-wide"><?php echo htmlspecialchars($surat['jenis_surat']); ?></h1>
            <p class="text-sm mt-1">Nomor: <?php echo htmlspecialchars($surat['nomor_surat']); ?></p>
        </div>

        <!-- Informasi Tujuan -->
        <div class="mb-4 text-sm leading-relaxed">
            <table class="text-sm">
                <tr><td class="pr-4 align-top">Kepada Yth.</td><td>: Orang Tua / Wali Murid</td></tr>
                <tr><td></td><td class="font-bold"><?php echo htmlspecialchars($surat['nama_siswa']); ?></td></tr>
                <tr><td class="pr-4">Perihal</td><td>: <b><?php echo htmlspecialchars($surat['perihal']); ?></b></td></tr>
            </table>
        </div>

        <!-- Isi Surat -->
        <div class="text-sm leading-relaxed mb-6 whitespace-pre-line"><?php echo htmlspecialchars($surat['isi_surat']); ?></div>

        <!-- Tabel Pelanggaran -->
        <?php if (!empty($daftar_pelanggaran)): ?>
        <div class="mb-6 no-split">
            <p class="text-sm font-bold mb-2">Rincian Pelanggaran:</p>
            <table class="w-full border-collapse border border-slate-400 text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border border-slate-400 px-3 py-1.5 text-center w-10">No</th>
                        <th class="border border-slate-400 px-3 py-1.5 text-center w-24">Tanggal</th>
                        <th class="border border-slate-400 px-3 py-1.5 text-left">Pelanggaran</th>
                        <th class="border border-slate-400 px-3 py-1.5 text-center w-16">Poin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; $total = 0; foreach ($daftar_pelanggaran as $dp):
                        $total += (int)$dp['poin'];
                    ?>
                    <tr>
                        <td class="border border-slate-400 px-3 py-1.5 text-center"><?php echo $no++; ?></td>
                        <td class="border border-slate-400 px-3 py-1.5 text-center"><?php echo date('d/m/Y', strtotime($dp['tanggal'])); ?></td>
                        <td class="border border-slate-400 px-3 py-1.5"><?php echo htmlspecialchars($dp['nama_pelanggaran'] ?? '-'); ?></td>
                        <td class="border border-slate-400 px-3 py-1.5 text-center"><?php echo (int)$dp['poin']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-slate-50 font-bold">
                        <td colspan="3" class="border border-slate-400 px-3 py-1.5 text-right">Total Poin Akumulasi</td>
                        <td class="border border-slate-400 px-3 py-1.5 text-center"><?php echo $total; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="no-split" style="margin-top: 35px; width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                
                <!-- Tanda Tangan Sisi Kiri: Guru yang Menindak -->
                <div style="width: 260px; text-align: center;">
                    <p style="margin: 0; font-size: 13px;">Mengetahui,</p>
                    <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 14px;">Guru yang Menindak</p>
                    
                    <!-- Paraf / Visual Tanda Tangan -->
                    <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 4px 0;">
                        <svg width="140" height="50" viewBox="0 0 140 50" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                            <path d="M12 36C26 32 42 16 54 20C64 24 56 44 72 32C86 22 102 12 114 24C120 32 126 40 134 32" stroke="#1d4ed8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M36 28C48 28 84 24 118 30" stroke="#1d4ed8" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span style="font-size: 9px; color: #1e40af; font-family: sans-serif; font-weight: bold; letter-spacing: 0.3px; margin-top: -4px;">
                            &#10003; Ditandatangani Elektronik
                        </span>
                    </div>

                    <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 14px; color: #000000;">
                        <?php echo htmlspecialchars($nama_guru_penindak); ?>
                    </p>
                    <?php if (!empty($nip_guru_penindak)): ?>
                        <p style="margin: 2px 0 0 0; font-size: 12px; color: #334155;">NIP. <?php echo htmlspecialchars($nip_guru_penindak); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Tanda Tangan Sisi Kanan: Admin atau Wali Kelas (Sesuai Role / Mode) -->
                <div style="width: 260px; text-align: center;">
                    <p style="margin: 0; font-size: 13px;">Tasikmalaya, <?php echo date('d F Y', strtotime($surat['tanggal_surat'])); ?></p>
                    
                    <?php if ($mode_ttd === 'admin'): ?>
                        <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 14px;">Administrator Sekolah</p>
                        
                        <!-- Paraf / Visual Tanda Tangan Admin -->
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 4px 0;">
                            <svg width="140" height="50" viewBox="0 0 140 50" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                                <path d="M18 40C30 18 42 12 55 22C65 30 58 46 78 34C94 24 110 16 120 26C126 34 132 40 138 34" stroke="#1e40af" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M28 32C48 30 80 28 114 34" stroke="#1e40af" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <span style="font-size: 9px; color: #1e40af; font-family: sans-serif; font-weight: bold; letter-spacing: 0.3px; margin-top: -4px;">
                                &#10003; Ditandatangani Elektronik
                            </span>
                        </div>

                        <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 14px; color: #000000;">
                            <?php echo htmlspecialchars($nama_admin); ?>
                        </p>
                        <p style="margin: 2px 0 0 0; font-size: 12px; color: #334155;">Petugas Administrator</p>

                    <?php else: ?>
                        <p style="margin: 2px 0 0 0; font-weight: bold; font-size: 14px;">Wali Kelas <?php echo htmlspecialchars($surat['nama_kelas']); ?></p>
                        
                        <!-- Paraf / Visual Tanda Tangan Wali Kelas -->
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 4px 0;">
                            <svg width="140" height="50" viewBox="0 0 140 50" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block;">
                                <path d="M15 38C28 22 45 14 58 24C68 32 60 48 80 36C95 26 112 18 122 28C128 36 134 42 140 36" stroke="#047857" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M32 30C52 28 85 26 120 32" stroke="#047857" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <span style="font-size: 9px; color: #047857; font-family: sans-serif; font-weight: bold; letter-spacing: 0.3px; margin-top: -4px;">
                                &#10003; Ditandatangani Elektronik
                            </span>
                        </div>

                        <p style="margin: 0; font-weight: bold; text-decoration: underline; font-size: 14px; color: #000000;">
                            <?php echo htmlspecialchars($nama_wali_kelas); ?>
                        </p>
                        <?php if (!empty($nip_wali_kelas)): ?>
                            <p style="margin: 2px 0 0 0; font-size: 12px; color: #334155;">NIP. <?php echo htmlspecialchars($nip_wali_kelas); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
