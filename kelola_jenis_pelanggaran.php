<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Proses Hapus Data
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if (mysqli_query($koneksi, "DELETE FROM t_pelanggaran WHERE id = '$delete_id'")) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data pelanggaran berhasil dihapus!'];
    }
    header("Location: kelola_jenis_pelanggaran.php");
    exit();
}

// Filter Pencarian
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori_id'] ?? '';

$where_clauses = [];
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where_clauses[] = "(p.kode LIKE '%$s%' OR p.nama LIKE '%$s%' OR p.deskripsi LIKE '%$s%')";
}
if (!empty($kategori_filter)) {
    $where_clauses[] = "p.pelanggaran_kategori_id = '$kategori_filter'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

$query_data = "SELECT p.*, k.nama AS nama_kategori
               FROM t_pelanggaran p
               LEFT JOIN t_pelanggaran_kategori k ON p.pelanggaran_kategori_id = k.id
               $where_sql
               ORDER BY p.id DESC";
$result = mysqli_query($koneksi, $query_data);

$kategori_res = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE status_aktif = 1 ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Jenis Pelanggaran - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>

    <!-- CSS DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 0.25rem;
            outline: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="flex-1 p-8 bg-slate-50 overflow-y-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Kelola Jenis Pelanggaran</h1>
                <p class="text-sm font-bold text-slate-500 mt-1">Kelola data master kode, nama, dan bobot poin pelanggaran.</p>
            </div>
            <a href="tambah_jenis_pelanggaran.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Pelanggaran
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Cari Pelanggaran</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari kode, nama, deskripsi..." class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                </div>
                <div class="w-60">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="kategori_id" class="w-full border border-slate-300 px-3 py-2 rounded-lg text-sm focus:border-blue-600 focus:outline-none">
                        <option value="">-- Semua Kategori --</option>
                        <?php if ($kategori_res): while ($kat = mysqli_fetch_assoc($kategori_res)): ?>
                            <option value="<?php echo $kat['id']; ?>" <?php echo $kategori_filter == $kat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($kat['nama']); ?></option>
                        <?php endwhile; endif; ?>
                    </select>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar Jenis Pelanggaran</h3>
            <div class="overflow-x-auto">
                <!-- Tambahkan id="tabelData" di sini -->
                <table id="tabelData" class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-24">KODE</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA PELANGGARAN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">KATEGORI</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-24">POIN</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-28">STATUS</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center w-32">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-mono font-bold text-blue-600 text-center"><?php echo htmlspecialchars($data['kode']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-slate-600 font-bold"><?php echo htmlspecialchars($data['nama_kategori'] ?? '-'); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-red-600 text-center">+<?php echo $data['poin']; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php echo $data['status_aktif'] == 1 ? '<span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Aktif</span>' : '<span class="text-slate-500 font-bold bg-slate-100 px-2 py-1 rounded text-xs">Non-Aktif</span>'; ?>
                                    </td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <a href="edit_jenis_pelanggaran.php?id=<?php echo $data['id']; ?>" class="text-blue-600 font-bold mr-2 hover:underline">Edit</a>
                                        <a href="kelola_jenis_pelanggaran.php?delete_id=<?php echo $data['id']; ?>" onclick="return confirm('Hapus jenis pelanggaran ini?')" class="text-red-600 font-bold hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- JS jQuery dan DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelData').DataTable({
                "searching": false, // Menyembunyikan form pencarian bawaan DataTables
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Belum ada data jenis pelanggaran.",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
</body>
</html>
