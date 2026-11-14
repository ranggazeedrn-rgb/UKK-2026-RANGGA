<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['simpan_pelanggaran'])) {
    $siswa_id       = (int)$_POST['siswa_id'];
    $pelanggaran_id = mysqli_real_escape_string($koneksi, $_POST['pelanggaran_id']);
    $tanggal        = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan     = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tindakan       = mysqli_real_escape_string($koneksi, $_POST['tindakan']);
    $post_kelas_id  = (int)($_POST['kelas_id'] ?? 0);

    if ($siswa_id && $pelanggaran_id && $tanggal) {
        $info = [];
        if ($post_kelas_id > 0) {
            $q = mysqli_query($koneksi, "
                SELECT ks.kelas_id, ks.tahun_ajaran_id, k.nama AS nama_kelas
                FROM t_kelas_siswa ks
                LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                WHERE ks.siswa_id = '$siswa_id' AND ks.kelas_id = '$post_kelas_id'
                ORDER BY ks.id DESC LIMIT 1
            ");
            if ($q && mysqli_num_rows($q) > 0) $info = mysqli_fetch_assoc($q);
        }
        if (empty($info)) {
            $q = mysqli_query($koneksi, "
                SELECT ks.kelas_id, ks.tahun_ajaran_id, k.nama AS nama_kelas
                FROM t_kelas_siswa ks
                LEFT JOIN t_kelas k ON ks.kelas_id = k.id
                WHERE ks.siswa_id = '$siswa_id'
                ORDER BY ks.id DESC LIMIT 1
            ");
            if ($q && mysqli_num_rows($q) > 0) $info = mysqli_fetch_assoc($q);
        }

        $q_siswa = mysqli_query($koneksi, "SELECT nama FROM t_siswa WHERE id = '$siswa_id' LIMIT 1");
        $nama_siswa_db = $q_siswa && mysqli_num_rows($q_siswa) ? mysqli_fetch_assoc($q_siswa)['nama'] : '';

        $q_pel = mysqli_query($koneksi, "SELECT nama, poin, pelanggaran_kategori_id FROM t_pelanggaran WHERE id = '$pelanggaran_id' LIMIT 1");
        $pel = $q_pel && mysqli_num_rows($q_pel) ? mysqli_fetch_assoc($q_pel) : [];

        $kelas_sql  = !empty($info['kelas_id']) ? "'" . (int)$info['kelas_id'] . "'" : "NULL";
        $ta_sql     = !empty($info['tahun_ajaran_id']) ? "'" . (int)$info['tahun_ajaran_id'] . "'" : "NULL";
        $kategori_sql = !empty($pel['pelanggaran_kategori_id']) ? "'" . (int)$pel['pelanggaran_kategori_id'] . "'" : "NULL";
        $guru_sql   = $guru_id_login ? "'" . (int)$guru_id_login . "'" : "NULL";
        $nama_guru  = mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? ''));

        $query = "INSERT INTO t_pelanggaran_siswa (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, keterangan, tindakan, poin, status, created_at, updated_at)
                  VALUES ($ta_sql, '$siswa_id', '" . mysqli_real_escape_string($koneksi, $nama_siswa_db) . "', $kelas_sql, '" . mysqli_real_escape_string($koneksi, $info['nama_kelas'] ?? '') . "', '$pelanggaran_id', $kategori_sql, $guru_sql, '$nama_guru', '$tanggal', '$keterangan', '$tindakan', '" . (int)($pel['poin'] ?? 0) . "', 'Dicatat', NOW(), NOW())";
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

$selected_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : '';

$kelas_list = @mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY nama ASC");
$pelanggaran_list = @mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif = 1 ORDER BY kode ASC");

if (!empty($selected_kelas)) {
    $query_siswa = "SELECT s.*, ks.kelas_id AS kelas_id_siswa, k.nama AS nama_kelas
                    FROM t_siswa s
                    INNER JOIN t_kelas_siswa ks ON s.id = ks.siswa_id
                    INNER JOIN t_kelas k ON ks.kelas_id = k.id
                    WHERE ks.kelas_id = '$selected_kelas'
                    ORDER BY s.nama ASC";
} else {
    $query_siswa = "SELECT s.*,
                    (SELECT ks2.kelas_id FROM t_kelas_siswa ks2 WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1) AS kelas_id_siswa,
                    (SELECT k2.nama FROM t_kelas_siswa ks2 JOIN t_kelas k2 ON ks2.kelas_id = k2.id WHERE ks2.siswa_id = s.id ORDER BY ks2.id DESC LIMIT 1) AS nama_kelas
                    FROM t_siswa s
                    ORDER BY s.nama ASC";
}
$siswa_list = @mysqli_query($koneksi, $query_siswa);
if (!$siswa_list || mysqli_num_rows($siswa_list) == 0) {
    $siswa_list = @mysqli_query($koneksi, "SELECT s.*, NULL AS kelas_id_siswa, NULL AS nama_kelas FROM t_siswa s ORDER BY s.nama ASC");
}

$where_pencatatan = $guru_id_login
    ? "(ps.guru_id = " . (int)$guru_id_login . " OR ps.nama_guru = '" . mysqli_real_escape_string($koneksi, $guru_nama_login ?: ($_SESSION['name'] ?? '')) . "')"
    : "ps.nama_guru = '" . mysqli_real_escape_string($koneksi, $_SESSION['name'] ?? '') . "'";
$where_pencatatan .= " AND (ps.status <> 'Selesai' OR ps.status IS NULL OR ps.status = '')";

$query_pencatatan_saya = "SELECT ps.*, s.nama AS nama_siswa, s.nis, p.nama AS nama_pelanggaran, p.poin AS poin_jenis, ka.nama AS nama_kelas
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
            $q_k = "SELECT k.nama FROM t_kelas_siswa ks JOIN t_kelas k ON ks.kelas_id = k.id WHERE ks.siswa_id = '$sd' ORDER BY ks.id DESC LIMIT 1";
            $r_k = mysqli_query($koneksi, $q_k);
            if ($r_k && mysqli_num_rows($r_k) > 0) $row['nama_kelas'] = mysqli_fetch_assoc($r_k)['nama'];
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

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 max-w-2xl mx-auto">
            <div>
                <h1 class="text-2xl font-bold text-black">Input Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Catat pelanggaran tata tertib yang dilakukan siswa.</p>
            </div>
            <a href="riwayat.php" class="text-blue-600 font-bold text-sm hover:underline flex items-center gap-1 mt-2 md:mt-0">
                <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="max-w-2xl mx-auto p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <?php if ($role === 'Guru'): ?>
        <div class="max-w-2xl mx-auto mb-6 bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold rounded-[1.25rem] px-5 py-4 flex items-start gap-3">
            <i class="fa-solid fa-circle-info mt-1"></i>
            <span>
                Anda mengakses halaman ini sebagai <b><?php echo $is_wali_kelas ? 'Wali Kelas (' . htmlspecialchars(implode(', ', $wali_kelas_kelas_nama)) . ')' : 'Guru'; ?></b>.
                Anda dapat memilih kelas <b>manapun</b> dan melaporkan pelanggaran siswa dari kelas lain.
            </span>
        </div>
        <?php endif; ?>

        <div class="max-w-2xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
            <form method="POST" action="catatan_pelanggaran.php" onsubmit="return cekSiswaDipilih()">
                <input type="hidden" name="simpan_pelanggaran" value="1">

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Kejadian *</label>
                    <input type="date" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

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
                    <p class="text-[11px] font-bold text-slate-400 mt-1.5">Kelas otomatis terpilih mengikuti siswa yang dipilih.</p>
                </div>

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
                            <p id="panelKosong" class="hidden px-4 py-3 text-[11px] font-bold text-slate-400">Siswa tidak ditemukan.</p>
                        </div>
                    </div>
                    <input type="hidden" name="siswa_id" id="siswaId" value="">
                    <p id="infoKelasSiswa" class="hidden text-[11px] font-bold text-blue-600 mt-1.5"></p>
                    <p id="infoKosong" class="hidden text-[11px] font-bold text-red-500 mt-1.5">Pilih siswa dari daftar terlebih dahulu.</p>
                    <?php if (!$siswa_list || mysqli_num_rows($siswa_list) === 0): ?>
                        <p class="text-[11px] font-bold text-red-500 mt-1.5">Belum ada data siswa di database.</p>
                    <?php endif; ?>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Pelanggaran *</label>
                    <select name="pelanggaran_id" required class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        <option value="">-- Pilih Pelanggaran --</option>
                        <?php if ($pelanggaran_list): while ($p = mysqli_fetch_assoc($pelanggaran_list)): ?>
                            <option value="<?php echo $p['id']; ?>" data-poin="<?php echo (int)$p['poin']; ?>" data-kategori="<?php echo htmlspecialchars($p['pelanggaran_kategori_id'] ?? ''); ?>">
                                [<?php echo htmlspecialchars($p['kode']); ?>] <?php echo htmlspecialchars($p['nama']); ?> (<?php echo (int)$p['poin']; ?> Poin)
                            </option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50"></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tindakan Awal</label>
                    <input type="text" name="tindakan" value="Diberi teguran & pembinaan" placeholder="Tindakan yang diberikan..." class="w-full border border-slate-300 px-4 py-2.5 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl py-3 text-sm transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Pelanggaran
                </button>
            </form>
        </div>

        <?php if (!empty($rows_riwayat)): ?>
        <div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black uppercase tracking-wider mb-4">Catatan Pelanggaran Saya (Belum Selesai)</h3>
            <div class="overflow-x-auto">
                <table id="tabelRiwayatSaya" class="w-full border-collapse border border-slate-300 text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-800">
                            <th class="border border-slate-300 px-3 py-2 text-center w-10">NO</th>
                            <th class="border border-slate-300 px-3 py-2 text-center w-20">TANGGAL</th>
                            <th class="border border-slate-300 px-3 py-2 text-left">NAMA / NIS</th>
                            <th class="border border-slate-300 px-3 py-2 text-center w-24">KELAS</th>
                            <th class="border border-slate-300 px-3 py-2 text-left">PELANGGARAN</th>
                            <th class="border border-slate-300 px-3 py-2 text-center w-16">POIN</th>
                            <th class="border border-slate-300 px-3 py-2 text-center w-24">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($rows_riwayat as $row): ?>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-slate-600"><?php echo $no++; ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-slate-700 whitespace-nowrap"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td class="border border-slate-300 px-3 py-2 font-bold text-black"><?php echo htmlspecialchars($row['nama_siswa']); ?><br><span class="text-[11px] font-bold text-slate-400"><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></span></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold"><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-slate-700"><?php echo htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center font-bold text-red-600">+<?php echo $row['poin'] ?? 0; ?></td>
                            <td class="border border-slate-300 px-3 py-2 text-center">
                                <span class="px-2 py-1 rounded text-xs <?php
                                    $st = $row['status'] ?? 'Dicatat';
                                    echo $st === 'Selesai' ? 'text-green-600 bg-green-50 font-bold' : ($st === 'Ditindak' ? 'text-amber-600 bg-amber-50 font-bold' : 'text-blue-600 bg-blue-50');
                                ?>"><?php echo htmlspecialchars($st); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        function bukaPanelSiswa() { document.getElementById('panelSiswa').classList.remove('hidden'); }
        function tutupPanelSiswa() { document.getElementById('panelSiswa').classList.add('hidden'); }
        function togglePanelSiswa() { document.getElementById('panelSiswa').classList.toggle('hidden'); }
        function pilihItemSiswa(btn) {
            document.getElementById('siswaId').value = btn.dataset.id;
            document.getElementById('cariSiswa').value = btn.dataset.label;
            document.getElementById('infoKelasSiswa').textContent = 'Kelas: ' + (btn.dataset.kelasNama || '-');
            document.getElementById('infoKelasSiswa').classList.remove('hidden');
            document.getElementById('infoKosong').classList.add('hidden');
            tutupPanelSiswa();
        }
        function bersihkanPilihanSiswa() {
            document.getElementById('siswaId').value = '';
            document.getElementById('cariSiswa').value = '';
            document.getElementById('infoKelasSiswa').classList.add('hidden');
            document.getElementById('infoKosong').classList.remove('hidden');
        }
        function ketikSiswa() {
            const input = document.getElementById('cariSiswa').value.toLowerCase();
            const items = document.querySelectorAll('.item-siswa');
            const emptyMsg = document.getElementById('panelKosong');
            let visible = 0;
            items.forEach(item => {
                if (item.dataset.cari.includes(input)) { item.classList.remove('hidden'); visible++; }
                else { item.classList.add('hidden'); }
            });
            emptyMsg.classList.toggle('hidden', visible > 0);
        }
        function navigasiSiswa(e) {
            const items = Array.from(document.querySelectorAll('.item-siswa:not(.hidden)'));
            if (!items.length) return;
            const idx = items.findIndex(i => i === document.activeElement);
            if (e.key === 'ArrowDown') { e.preventDefault(); items[(idx + 1) % items.length].focus(); }
            if (e.key === 'ArrowUp') { e.preventDefault(); items[(idx - 1 + items.length) % items.length].focus(); }
            if (e.key === 'Enter') { e.preventDefault(); pilihItemSiswa(document.activeElement); }
            if (e.key === 'Escape') { tutupPanelSiswa(); }
        }
        function cekSiswaDipilih() {
            const id = document.getElementById('siswaId').value;
            if (!id) { alert('Pilih siswa terlebih dahulu!'); return false; }
            return true;
        }
        document.addEventListener('click', e => {
            if (!e.target.closest('.relative') && !e.target.closest('#panelSiswa')) tutupPanelSiswa();
        });

        $(document).ready(function() {
            $('#tabelRiwayatSaya').DataTable({
                "searching": false,
                "order": [],
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Belum ada catatan pelanggaran.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                }
            });
        });
    </script>
</body>
</html>