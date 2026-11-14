<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Admin, Guru, dan Wali Kelas dapat mencatat pelanggaran.
// Wali kelas juga dapat melihat kelas lain dan melaporkan siswa kelas lain.

// Proses Simpan Data Pelanggaran Siswa
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['simpan_pelanggaran'])) {
    $siswa_id       = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $pelanggaran_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_id']);
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan     = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tindakan       = mysqli_real_escape_string($koneksi, $_POST['tindakan']);
    $post_kelas_id  = isset($_POST['kelas_id']) ? (int)$_POST['kelas_id'] : 0;

    if (!empty($siswa_id) && !empty($pelanggaran_id) && !empty($tanggal)) {
        // Cari kelas siswa (kelas yang dipilih pada form, jika siswa ada di sana;
        // jika tidak, gunakan penempatan terbaru siswa).
        $info = [];
        if ($post_kelas_id > 0) {
            $q_kelas_siswa = mysqli_query($koneksi, "
                SELECT ks.kelas_id, ks.tahun_ajaran_id, k.nama AS nama_kelas
                FROM t_kelas_siswa ks
                LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                WHERE ks.siswa_id = '$siswa_id' AND ks.kelas_id = '$post_kelas_id'
                ORDER BY ks.id DESC LIMIT 1
            ");
            if ($q_kelas_siswa && mysqli_num_rows($q_kelas_siswa) > 0) {
                $info = mysqli_fetch_assoc($q_kelas_siswa);
            }
        }
        if (empty($info)) {
            $q_kelas_siswa = mysqli_query($koneksi, "
                SELECT ks.kelas_id, ks.tahun_ajaran_id, k.nama AS nama_kelas
                FROM t_kelas_siswa ks
                LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                WHERE ks.siswa_id = '$siswa_id'
                ORDER BY ks.id DESC LIMIT 1
            ");
            if ($q_kelas_siswa && mysqli_num_rows($q_kelas_siswa) > 0) {
                $info = mysqli_fetch_assoc($q_kelas_siswa);
            }
        }

        $q_siswa = mysqli_query($koneksi, "SELECT nama FROM t_siswa WHERE id = '$siswa_id' LIMIT 1");
        $nama_siswa_db = ($q_siswa && mysqli_num_rows($q_siswa) > 0) ? mysqli_fetch_assoc($q_siswa)['nama'] : '';

        $q_pelanggaran = mysqli_query($koneksi, "SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = '$pelanggaran_id' LIMIT 1");
        $data_pelanggaran = ($q_pelanggaran && mysqli_num_rows($q_pelanggaran) > 0) ? mysqli_fetch_assoc($q_pelanggaran) : [];

        $nama_siswa = mysqli_real_escape_string($koneksi, $nama_siswa_db);
        $nama_kelas = mysqli_real_escape_string($koneksi, $info['nama_kelas'] ?? '');
        $poin       = (int)($data_pelanggaran['poin'] ?? 0);
        $kelas_sql  = !empty($info['kelas_id']) ? "'" . (int)$info['kelas_id'] . "'" : "NULL";
        $ta_sql     = !empty($info['tahun_ajaran_id']) ? "'" . (int)$info['tahun_ajaran_id'] . "'" : "NULL";
        $kategori_sql = !empty($data_pelanggaran['pelanggaran_kategori_id']) ? "'" . (int)$data_pelanggaran['pelanggaran_kategori_id'] . "'" : "NULL";
        $guru_sql   = $guru_id_login ? "'" . (int)$guru_id_login . "'" : "NULL";
        // Admin tidak memiliki record t_guru, gunakan nama akun login sebagai pelapor
        $nama_guru  = mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? ''));

        $query = "INSERT INTO t_pelanggaran_siswa (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, tindakan, poin, status, created_at, updated_at)
                  VALUES ($ta_sql, '$siswa_id', '$nama_siswa', $kelas_sql, '$nama_kelas', '$pelanggaran_id', $kategori_sql, $guru_sql, '$nama_guru', '$tanggal', '$keterangan', '$tindakan', '$poin', 'Dicatat', NOW(), NOW())";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data pelanggaran berhasil dicatat!'];
            header("Location: catatan_pelanggaran.php");
            exit();
        }
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Harap isi semua kolom wajib (*)'];
    }
}

// Ambil filter kelas dari URL (wali kelas bebas memilih kelas lain)
$selected_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : '';

// Fetch Data Dropdown Kelas & Jenis Pelanggaran
$sql_kelas = "SELECT * FROM t_kelas";
$sql_kelas .= " ORDER BY nama ASC";
$kelas_list = @mysqli_query($koneksi, $sql_kelas);
$pelanggaran_list = @mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY kode ASC");

// Kueri Pengambilan Data Siswa Presisi
if (!empty($selected_kelas)) {
    // Memakai kelas_id & siswa_id sesuai struktur schema t_kelas_siswa
    $query_siswa = "SELECT s.*, ks.kelas_id AS kelas_id_siswa, k.nama AS nama_kelas
                    FROM t_siswa s
                    INNER JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
                    INNER JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.kelas_id = '$selected_kelas'
                    ORDER BY s.nama ASC";
} else {
    // Menampilkan seluruh siswa jika filter kelas tidak dipilih
    // (kelas diambil dari penempatan terbaru siswa, dipakai untuk terpilih otomatis)
    $query_siswa = "SELECT s.*,
                    (SELECT ks2.kelas_id FROM t_kelas_siswa ks2 WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1) AS kelas_id_siswa,
                    (SELECT k2.nama FROM t_kelas_siswa ks2 JOIN t_kelas k2 ON ks2.kelas_id = k2.id WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1) AS nama_kelas
                    FROM t_siswa s
                    ORDER BY s.nama ASC";
}

$siswa_list = @mysqli_query($koneksi, $query_siswa);

// Fallback jika tidak ada data dari kueri di atas
if (!$siswa_list || mysqli_num_rows($siswa_list) == 0) {
    $query_fallback = "SELECT s.*, NULL AS kelas_id_siswa, NULL AS nama_kelas FROM t_siswa s ORDER BY s.nama ASC";
    $siswa_list = @mysqli_query($koneksi, $query_fallback);
}

// Table pencatatan di bawah form: hanya catatan milik user yang login,
// dan otomatis tidak lagi tampil di sini setelah penindakan berstatus Selesai
// (data tindakannya tampil di halaman Riwayat).
if ($guru_id_login) {
    $where_pencatatan = "(ps.guru_id = '" . (int)$guru_id_login . "' OR ps.nama_guru = '" . mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? '')) . "')";
} else {
    // Admin tidak punya record t_guru, identifikasi lewat nama akun login
    $where_pencatatan = "ps.nama_guru = '" . mysqli_real_escape_string($koneksi, $_SESSION['name'] ?? '') . "'";
}
$where_pencatatan .= " AND (ps.status <> 'Selesai' OR ps.status IS NULL OR ps.status = '')";

$query_pencatatan_saya = "SELECT ps.*,
                               s.nama AS nama_siswa, s.nis,
                               p.nama AS nama_pelanggaran, p.poin AS poin_jenis,
                               ka.nama AS nama_kelas
                        FROM t_pelanggaran_siswa ps
                        LEFT JOIN t_siswa s ON ps.siswa_id = s.id
                        LEFT JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
                        LEFT JOIN t_kelas ka ON ps.kelas_id = ka.id
                        WHERE $where_pencatatan
                        ORDER BY ps.tanggal DESC, ps.id DESC";
$rows_riwayat = [];
$result_riwayat = @mysqli_query($koneksi, $query_pencatatan_saya);
if ($result_riwayat) {
    while ($row = mysqli_fetch_assoc($result_riwayat)) {
        if (empty($row['nama_kelas']) && !empty($row['siswa_id'])) {
            $sd = (int)$row['siswa_id'];
            $q_k = "SELECT k.nama FROM t_kelas_siswa ks
                    JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.siswa_id = '$sd'
                    ORDER BY ks.id DESC LIMIT 1";
            $r_k = mysqli_query($koneksi, $q_k);
            if ($r_k && mysqli_num_rows($r_k) > 0) {
                $row['nama_kelas'] = mysqli_fetch_assoc($r_k)['nama'];
            }
        }
        $rows_riwayat[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Input Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 max-w-2xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Input Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Catat pelanggaran tata tertib yang dilakukan siswa.</p>
            </div>
            <a href="riwayat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat
            </a>
        </div>

        <!-- Flash Alert -->
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-2xl mx-auto p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Info untuk role Guru -->
        <?php if ($role === 'Guru'): ?>
            <div class="max-w-2xl mx-auto mb-6 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-[1.25rem] px-5 py-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-1"></i>
                <span>
                    Anda mengakses halaman ini sebagai <b><?php echo $is_wali_kelas ? 'Wali Kelas (' . htmlspecialchars(implode(', ', $wali_kelas_kelas_nama)) . ')' : 'Guru'; ?></b>.
                    Anda dapat memilih kelas <b>manapun</b> dan melaporkan pelanggaran siswa dari kelas lain.
                </span>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="catatan_pelanggaran.php" onsubmit="return cekSiswaDipilih()">
                <input type="hidden" name="simpan_pelanggaran" value="1">

                <!-- Tanggal Kejadian -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Kejadian *</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <!-- Filter Pilih Kelas -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Kelas (Filter Siswa)</label>
                    <select name="kelas_id" id="selectKelas" onchange="location.href='catatan_pelanggaran.php?kelas_id=' + this.value" class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">Semua Kelas</option>
                        <?php if ($kelas_list): while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo $selected_kelas == $k['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['nama']); ?>
                                <?php if (in_array((int)$k['id'], $wali_kelas_kelas_ids, true)): ?> [MURID KELAS ANDA] <?php endif; ?>
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                    <p class="text-[11px] font-bold text-slate-400 mt-1.5">
                        Kelas otomatis terpilih mengikuti siswa yang dipilih.
                    </p>
                </div>

                <!-- Pilih Siswa: pencarian dan pemilihan dalam satu kolom -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Siswa *</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                <input type="text" id="cariSiswa" autocomplete="off"
                                       placeholder="Ketik nama atau NIS siswa..."
                                       onclick="bukaPanelSiswa()"
                                       oninput="ketikSiswa()"
                                       onkeydown="navigasiSiswa(event)"
                                       aria-controls="panelSiswa" aria-expanded="false"
                                       class="w-full border border-slate-300 pl-10 pr-10 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                                <button type="button" onclick="bersihkanPilihanSiswa()" title="Bersihkan pilihan siswa"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <button type="button" id="tombolSiswa" onclick="togglePanelSiswa()"
                                    aria-controls="panelSiswa" aria-expanded="false" aria-label="Buka daftar siswa"
                                    class="w-12 shrink-0 rounded-xl border border-slate-300 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-blue-600 flex items-center justify-center transition">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>

                        <div id="panelSiswa" class="hidden absolute left-0 right-0 z-20 mt-2 max-h-64 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg">
                            <?php if ($siswa_list && mysqli_num_rows($siswa_list) > 0): ?>
                                <?php while ($s = mysqli_fetch_assoc($siswa_list)):
                                    $nis = $s['nis'] ?? $s['nisn'] ?? '-';
                                    $nama = $s['nama'] ?? 'Siswa Tanpa Nama';
                                    $kelas = $s['nama_kelas'] ?? '';
                                    $label = $nis . ' - ' . $nama . ($kelas !== '' ? " ($kelas)" : '');
                                ?>
                                    <button type="button"
                                            class="item-siswa w-full text-left px-4 py-2.5 text-sm border-b border-slate-100 last:border-0 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
                                            data-id="<?php echo (int)$s['id']; ?>"
                                            data-kelas-id="<?php echo (int)($s['kelas_id_siswa'] ?? 0); ?>"
                                            data-kelas-nama="<?php echo htmlspecialchars($kelas); ?>"
                                            data-label="<?php echo htmlspecialchars($label); ?>"
                                            data-cari="<?php echo htmlspecialchars(strtolower($nis . ' ' . $nama . ' ' . $kelas)); ?>"
                                            onclick="pilihItemSiswa(this)">
                                        <span class="block font-bold text-slate-700"><?php echo htmlspecialchars($nama); ?></span>
                                        <span class="block text-[11px] font-bold text-slate-400"><?php echo htmlspecialchars($nis); ?><?php echo $kelas !== '' ? ' &bull; ' . htmlspecialchars($kelas) : ''; ?></span>
                                    </button>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <p id="panelKosong" class="hidden px-4 py-3 text-[11px] font-bold text-slate-400">
                                Siswa tidak ditemukan.
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="siswa_id" id="siswaId" value="">
                    <p id="infoKelasSiswa" class="hidden text-[11px] font-bold text-blue-600 mt-1.5"></p>
                    <p id="infoKosong" class="hidden text-[11px] font-bold text-red-500 mt-1.5">
                        Pilih siswa dari daftar terlebih dahulu.
                    </p>
                    <?php if (!$siswa_list || mysqli_num_rows($siswa_list) === 0): ?>
                        <p class="text-[11px] font-bold text-red-500 mt-1.5">Belum ada siswa yang ditempatkan di kelas ini.</p>
                    <?php endif; ?>
                </div>

                <!-- Jenis Pelanggaran: pencarian dan pemilihan dalam satu kolom -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Pelanggaran *</label>
                    <div class="relative">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                <input type="text" id="cariPelanggaran" autocomplete="off"
                                       placeholder="Ketik kode atau nama jenis pelanggaran..."
                                       onclick="bukaPanelPelanggaran()"
                                       oninput="ketikPelanggaran()"
                                       onkeydown="navigasiPelanggaran(event)"
                                       aria-controls="panelPelanggaran" aria-expanded="false"
                                       class="w-full border border-slate-300 pl-10 pr-10 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                                <button type="button" onclick="bersihkanPilihanPelanggaran()" title="Bersihkan pilihan jenis pelanggaran"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <button type="button" id="tombolPelanggaran" onclick="togglePanelPelanggaran()"
                                    aria-controls="panelPelanggaran" aria-expanded="false" aria-label="Buka daftar jenis pelanggaran"
                                    class="w-12 shrink-0 rounded-xl border border-slate-300 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-blue-600 flex items-center justify-center transition">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>

                        <div id="panelPelanggaran" class="hidden absolute left-0 right-0 z-20 mt-2 max-h-64 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg">
                            <?php if ($pelanggaran_list): while ($p = mysqli_fetch_assoc($pelanggaran_list)):
                                $kode = $p['kode'] ?? '';
                                $nama = $p['nama'] ?? 'Jenis Pelanggaran';
                                $poin = (int)($p['poin'] ?? 0);
                                $label = '[' . $kode . '] ' . $nama . ' (+' . $poin . ' Poin)';
                            ?>
                                <button type="button"
                                        class="item-pelanggaran w-full text-left px-4 py-2.5 text-sm border-b border-slate-100 last:border-0 hover:bg-blue-50 focus:bg-blue-50 focus:outline-none"
                                        data-id="<?php echo (int)$p['id']; ?>"
                                        data-label="<?php echo htmlspecialchars($label); ?>"
                                        data-cari="<?php echo htmlspecialchars(strtolower($kode . ' ' . $nama . ' ' . $poin)); ?>"
                                        onclick="pilihItemPelanggaran(this)">
                                        <span class="block font-bold text-slate-700"><?php echo htmlspecialchars($nama); ?></span>
                                        <span class="block text-[11px] font-bold text-slate-400">[<?php echo htmlspecialchars($kode); ?>] &bull; +<?php echo $poin; ?> Poin</span>
                                </button>
                            <?php endwhile; endif; ?>
                            <p id="panelPelanggaranKosong" class="hidden px-4 py-3 text-[11px] font-bold text-slate-400">
                                Jenis pelanggaran tidak ditemukan.
                            </p>
                        </div>
                    </div>
                    <input type="hidden" name="pelanggaran_id" id="pelanggaranId" value="">
                    <p id="infoKosongPelanggaran" class="hidden text-[11px] font-bold text-red-500 mt-1.5">
                        Pilih jenis pelanggaran dari daftar terlebih dahulu.
                    </p>
                </div>

                <!-- Deskripsi / Keterangan Kejadian -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Deskripsi / Keterangan Kejadian</label>
                    <textarea name="keterangan" rows="3" placeholder="Rincian kronologi atau detail pelanggaran..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                </div>

                <!-- Tindakan -->
                <div class="mb-8">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tindakan Pembinaan</label>
                    <input type="text" name="tindakan" value="Diberi teguran & pembinaan" placeholder="Tindakan yang diberikan..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                    <p class="text-[11px] font-bold text-slate-400 mt-1.5">
                        Tindakan dapat diperbarui pada menu <b>Riwayat</b> (Update Tindakan) selama belum berstatus Selesai.
                    </p>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition">
                    SIMPAN PELANGGARAN
                </button>
            </form>
        </div>

        <!-- Table Riwayat Pencatatan (Semua Guru) -->
        <div class="mt-8 bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
                <div>
                    <h2 class="text-sm font-bold text-black uppercase tracking-wider">Pencatatan Saya</h2>
                    <p class="text-xs font-bold text-slate-400 mt-1">Data pencatatan yang Anda buat tetap berada di table ini. Setelah siswa ditindak dan penindakan berstatus <b>Selesai</b>, riwayat penindakannya otomatis pindah ke halaman <a href="riwayat.php" class="text-blue-600 hover:underline">Riwayat</a>.</p>
                </div>
                <a href="riwayat.php" class="text-blue-600 font-bold text-xs hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-clock-rotate-left"></i> Buka Riwayat
                </a>
            </div>
            <div class="overflow-x-auto">
                <table id="tabelPencatatan" class="w-full text-left border-collapse display">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">TANGGAL</th>
                            <th class="py-3 px-4">SISWA & KELAS</th>
                            <th class="py-3 px-4">PELANGGARAN</th>
                            <th class="py-3 px-4 text-center">POIN</th>
                            <th class="py-3 px-4">DICATAT OLEH</th>
                            <th class="py-3 px-4">TINDAKAN</th>
                            <th class="py-3 px-4 text-center">STATUS TINDAKAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (!empty($rows_riwayat)): ?>
                            <?php foreach ($rows_riwayat as $rrow):
                                $r_nama_siswa = !empty($rrow['nama_siswa']) ? $rrow['nama_siswa'] : 'Siswa Tidak Ditemukan';
                                $r_nama_kelas = !empty($rrow['nama_kelas']) ? $rrow['nama_kelas'] : '-';
                                $r_poin = !empty($rrow['poin']) ? (int)$rrow['poin'] : (int)($rrow['poin_jenis'] ?? 0);
                                $r_tanggal = !empty($rrow['tanggal']) ? date('d/m/Y', strtotime($rrow['tanggal'])) : '-';
                                $r_status = trim((string)($rrow['status'] ?? ''));
                                if ($r_status === '') { $r_status = 'Dicatat'; }
                                $r_selesai = (strtolower($r_status) === 'selesai');
                            ?>
                                <tr class="hover:bg-slate-50 transition <?php echo $r_selesai ? 'bg-green-50/40' : ''; ?>">
                                    <td class="py-4 px-4 font-bold text-slate-700 whitespace-nowrap">
                                        <?php echo htmlspecialchars($r_tanggal); ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($r_nama_siswa); ?></div>
                                        <div class="text-xs text-slate-400 font-bold">Kelas: <?php echo htmlspecialchars($r_nama_kelas); ?></div>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-800">
                                        <?php echo htmlspecialchars($rrow['nama_pelanggaran'] ?? 'Jenis Pelanggaran Tidak Ada'); ?>
                                    </td>
                                    <td class="py-4 px-4 text-center font-bold text-red-600">
                                        +<?php echo $r_poin; ?>
                                    </td>
                                    <td class="py-4 px-4 text-slate-600 font-medium">
                                        <?php echo !empty($rrow['nama_guru']) ? htmlspecialchars($rrow['nama_guru']) : 'Administrator'; ?>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-black"><?php echo htmlspecialchars($rrow['tindakan'] ?? '-'); ?></div>
                                        <div class="text-xs text-slate-400 font-medium italic mt-0.5"><?php echo htmlspecialchars($rrow['keterangan'] ?? '-'); ?></div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <?php if ($r_selesai): ?>
                                            <span class="px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">
                                                <i class="fa-solid fa-check-circle mr-1"></i> Selesai
                                            </span>
                                        <?php elseif (strtolower($r_status) === 'ditindak'): ?>
                                            <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700">
                                                <i class="fa-solid fa-gavel mr-1"></i> Ditindak
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700">
                                                <i class="fa-solid fa-clock mr-1"></i> Belum Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- Baris kosong dibiarkan tanpa colspan agar DataTables tidak menampilkan
                             peringatan "Incorrect column count"; pesan data kosong muncul dari DataTables. -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- jQuery & DataTables (Data Table) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelPencatatan').DataTable({
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Belum ada pencatatan dari Anda. Catatan yang sudah ditindak (Selesai) tampil di halaman Riwayat.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>

    <script>
        // Panel siswa tertutup secara default dan hanya terbuka saat diklik/diketik.
        // siswa_id dikirim lewat input hidden.
        var indeksAktif = -1;

        function itemSiswaTerlihat() {
            var panel = document.getElementById('panelSiswa');
            if (!panel) return [];
            return Array.prototype.filter.call(panel.querySelectorAll('.item-siswa'), function (el) {
                return !el.classList.contains('hidden');
            });
        }

        function bukaPanelSiswa() {
            var panel = document.getElementById('panelSiswa');
            if (!panel) return;
            panel.classList.remove('hidden');
            var tombol = document.getElementById('tombolSiswa');
            var kolom = document.getElementById('cariSiswa');
            var hidden = document.getElementById('siswaId');
            if (tombol) tombol.setAttribute('aria-expanded', 'true');
            if (kolom) kolom.setAttribute('aria-expanded', 'true');
            // Bila sudah ada siswa terpilih, tampilkan semua agar mudah mengganti.
            filterItemSiswa(hidden && hidden.value ? '' : undefined);
        }

        // Mengetik membatalkan pilihan sebelumnya sampai siswa dipilih lagi dari daftar.
        function ketikSiswa() {
            var hidden = document.getElementById('siswaId');
            var info = document.getElementById('infoKelasSiswa');
            if (hidden) hidden.value = '';
            if (info) { info.textContent = ''; info.classList.add('hidden'); }
            bukaPanelSiswa();
            filterItemSiswa();
        }

        function tutupPanelSiswa() {
            var panel = document.getElementById('panelSiswa');
            if (!panel) return;
            panel.classList.add('hidden');
            var tombol = document.getElementById('tombolSiswa');
            var kolom = document.getElementById('cariSiswa');
            if (tombol) tombol.setAttribute('aria-expanded', 'false');
            if (kolom) kolom.setAttribute('aria-expanded', 'false');
            indeksAktif = -1;
        }

        function togglePanelSiswa() {
            var panel = document.getElementById('panelSiswa');
            if (!panel) return;
            if (panel.classList.contains('hidden')) {
                bukaPanelSiswa();
                var kolom = document.getElementById('cariSiswa');
                if (kolom) kolom.focus();
            } else {
                tutupPanelSiswa();
            }
        }

        function filterItemSiswa(kunci) {
            var panel = document.getElementById('panelSiswa');
            var kolom = document.getElementById('cariSiswa');
            if (!panel || !kolom) return;
            if (kunci === undefined) kunci = kolom.value.trim().toLowerCase();
            var jumlah = 0;
            Array.prototype.forEach.call(panel.querySelectorAll('.item-siswa'), function (el) {
                var cocok = kunci === '' || (el.getAttribute('data-cari') || '').indexOf(kunci) !== -1;
                el.classList.toggle('hidden', !cocok);
                el.classList.remove('bg-blue-50');
                if (cocok) jumlah++;
            });
            var kosong = document.getElementById('panelKosong');
            if (kosong) kosong.classList.toggle('hidden', jumlah !== 0);
            indeksAktif = -1;
        }

        function pilihItemSiswa(el) {
            var kolom = document.getElementById('cariSiswa');
            var hidden = document.getElementById('siswaId');
            if (kolom) kolom.value = el.getAttribute('data-label') || '';
            if (hidden) hidden.value = el.getAttribute('data-id') || '';
            sinkronKelasSiswa(el.getAttribute('data-kelas-id') || '', el.getAttribute('data-kelas-nama') || '');
            var kosong = document.getElementById('infoKosong');
            if (kosong) kosong.classList.add('hidden');
            tutupPanelSiswa();
        }

        // Kelas otomatis terpilih mengikuti kelas asal siswa.
        // Mengubah nilai select secara programatis tidak memicu onchange (tanpa reload).
        function sinkronKelasSiswa(kelasId, kelasNama) {
            var selectKelas = document.getElementById('selectKelas');
            var info = document.getElementById('infoKelasSiswa');
            if (kelasId && selectKelas) {
                for (var i = 0; i < selectKelas.options.length; i++) {
                    if (selectKelas.options[i].value === kelasId) { selectKelas.value = kelasId; break; }
                }
            }
            if (info) {
                info.textContent = kelasNama
                    ? 'Siswa ini berasal dari kelas ' + kelasNama + '.'
                    : 'Siswa ini belum ditempatkan pada kelas manapun.';
                info.classList.remove('hidden');
            }
        }

        function bersihkanPilihanSiswa() {
            var kolom = document.getElementById('cariSiswa');
            var hidden = document.getElementById('siswaId');
            var info = document.getElementById('infoKelasSiswa');
            var kosong = document.getElementById('infoKosong');
            if (kolom) { kolom.value = ''; kolom.focus(); }
            if (hidden) hidden.value = '';
            if (info) { info.textContent = ''; info.classList.add('hidden'); }
            if (kosong) kosong.classList.add('hidden');
            tutupPanelSiswa();
        }

        // Panah atas/bawah untuk memilih, Enter untuk mengambil, Escape untuk menutup.
        function navigasiSiswa(e) {
            var panel = document.getElementById('panelSiswa');
            if (!panel) return;

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (panel.classList.contains('hidden')) bukaPanelSiswa();
                var items = itemSiswaTerlihat();
                if (items.length === 0) return;
                indeksAktif = e.key === 'ArrowDown'
                    ? (indeksAktif + 1) % items.length
                    : (indeksAktif - 1 + items.length) % items.length;
                items.forEach(function (el, i) {
                    el.classList.toggle('bg-blue-50', i === indeksAktif);
                });
                items[indeksAktif].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (panel.classList.contains('hidden')) { bukaPanelSiswa(); return; }
                var items = itemSiswaTerlihat();
                if (items.length === 0) return;
                // Tanpa panah, Enter mengambil hasil pertama yang cocok.
                pilihItemSiswa(items[indeksAktif >= 0 ? indeksAktif : 0]);
            } else if (e.key === 'Escape') {
                tutupPanelSiswa();
            }
        }

        // Klik di luar panel menutup panel.
        document.addEventListener('click', function (e) {
            var panel = document.getElementById('panelSiswa');
            if (!panel || panel.classList.contains('hidden')) return;
            var tombol = document.getElementById('tombolSiswa');
            var kolom = document.getElementById('cariSiswa');
            if (panel.contains(e.target) || (tombol && tombol.contains(e.target)) || (kolom && kolom.contains(e.target))) return;
            tutupPanelSiswa();
        });

        // Cegah submit jika belum ada siswa yang dipilih dari daftar.
        function cekSiswaDipilih() {
            var hidden = document.getElementById('siswaId');
            if (!(hidden && hidden.value)) {
                var kolom = document.getElementById('cariSiswa');
                var kosong = document.getElementById('infoKosong');
                if (kosong) kosong.classList.remove('hidden');
                if (kolom) { kolom.focus(); bukaPanelSiswa(); }
                return false;
            }
            // Jenis pelanggaran juga wajib dipilih dari daftar.
            var hiddenP = document.getElementById('pelanggaranId');
            if (!(hiddenP && hiddenP.value)) {
                var kolomP = document.getElementById('cariPelanggaran');
                var kosongP = document.getElementById('infoKosongPelanggaran');
                if (kosongP) kosongP.classList.remove('hidden');
                if (kolomP) { kolomP.focus(); bukaPanelPelanggaran(); }
                return false;
            }
            return true;
        }

        // ===== Panel Jenis Pelanggaran (pola sama dengan panel siswa) =====
        var indeksPelanggaran = -1;

        function itemPelanggaranTerlihat() {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel) return [];
            return Array.prototype.filter.call(panel.querySelectorAll('.item-pelanggaran'), function (el) {
                return !el.classList.contains('hidden');
            });
        }

        function bukaPanelPelanggaran() {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel) return;
            panel.classList.remove('hidden');
            var tombol = document.getElementById('tombolPelanggaran');
            var kolom = document.getElementById('cariPelanggaran');
            var hidden = document.getElementById('pelanggaranId');
            if (tombol) tombol.setAttribute('aria-expanded', 'true');
            if (kolom) kolom.setAttribute('aria-expanded', 'true');
            filterItemPelanggaran(hidden && hidden.value ? '' : undefined);
        }

        // Mengetik membatalkan pilihan sebelumnya sampai jenis dipilih lagi dari daftar.
        function ketikPelanggaran() {
            var hidden = document.getElementById('pelanggaranId');
            if (hidden) hidden.value = '';
            bukaPanelPelanggaran();
            filterItemPelanggaran();
        }

        function tutupPanelPelanggaran() {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel) return;
            panel.classList.add('hidden');
            var tombol = document.getElementById('tombolPelanggaran');
            var kolom = document.getElementById('cariPelanggaran');
            if (tombol) tombol.setAttribute('aria-expanded', 'false');
            if (kolom) kolom.setAttribute('aria-expanded', 'false');
            indeksPelanggaran = -1;
        }

        function togglePanelPelanggaran() {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel) return;
            if (panel.classList.contains('hidden')) {
                bukaPanelPelanggaran();
                var kolom = document.getElementById('cariPelanggaran');
                if (kolom) kolom.focus();
            } else {
                tutupPanelPelanggaran();
            }
        }

        function filterItemPelanggaran(kunci) {
            var panel = document.getElementById('panelPelanggaran');
            var kolom = document.getElementById('cariPelanggaran');
            if (!panel || !kolom) return;
            if (kunci === undefined) kunci = kolom.value.trim().toLowerCase();
            var jumlah = 0;
            Array.prototype.forEach.call(panel.querySelectorAll('.item-pelanggaran'), function (el) {
                var cocok = kunci === '' || (el.getAttribute('data-cari') || '').indexOf(kunci) !== -1;
                el.classList.toggle('hidden', !cocok);
                el.classList.remove('bg-blue-50');
                if (cocok) jumlah++;
            });
            var kosong = document.getElementById('panelPelanggaranKosong');
            if (kosong) kosong.classList.toggle('hidden', jumlah !== 0);
            indeksPelanggaran = -1;
        }

        function pilihItemPelanggaran(el) {
            var kolom = document.getElementById('cariPelanggaran');
            var hidden = document.getElementById('pelanggaranId');
            if (kolom) kolom.value = el.getAttribute('data-label') || '';
            if (hidden) hidden.value = el.getAttribute('data-id') || '';
            var kosong = document.getElementById('infoKosongPelanggaran');
            if (kosong) kosong.classList.add('hidden');
            tutupPanelPelanggaran();
        }

        function bersihkanPilihanPelanggaran() {
            var kolom = document.getElementById('cariPelanggaran');
            var hidden = document.getElementById('pelanggaranId');
            var kosong = document.getElementById('infoKosongPelanggaran');
            if (kolom) { kolom.value = ''; kolom.focus(); }
            if (hidden) hidden.value = '';
            if (kosong) kosong.classList.add('hidden');
            tutupPanelPelanggaran();
        }

        // Panah atas/bawah untuk memilih, Enter untuk mengambil, Escape untuk menutup.
        function navigasiPelanggaran(e) {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel) return;

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (panel.classList.contains('hidden')) bukaPanelPelanggaran();
                var items = itemPelanggaranTerlihat();
                if (items.length === 0) return;
                indeksPelanggaran = e.key === 'ArrowDown'
                    ? (indeksPelanggaran + 1) % items.length
                    : (indeksPelanggaran - 1 + items.length) % items.length;
                items.forEach(function (el, i) {
                    el.classList.toggle('bg-blue-50', i === indeksPelanggaran);
                });
                items[indeksPelanggaran].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (panel.classList.contains('hidden')) { bukaPanelPelanggaran(); return; }
                var items = itemPelanggaranTerlihat();
                if (items.length === 0) return;
                pilihItemPelanggaran(items[indeksPelanggaran >= 0 ? indeksPelanggaran : 0]);
            } else if (e.key === 'Escape') {
                tutupPanelPelanggaran();
            }
        }

        // Klik di luar panel jenis pelanggaran menutup panel.
        document.addEventListener('click', function (e) {
            var panel = document.getElementById('panelPelanggaran');
            if (!panel || panel.classList.contains('hidden')) return;
            var tombol = document.getElementById('tombolPelanggaran');
            var kolom = document.getElementById('cariPelanggaran');
            if (panel.contains(e.target) || (tombol && tombol.contains(e.target)) || (kolom && kolom.contains(e.target))) return;
            tutupPanelPelanggaran();
        });
    </script>

</body>
</html>
