<?php
// kelola_user.php
// Tabel daftar user (t_users) — khusus Admin
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Pencarian
$search = $_GET['search'] ?? '';
$where = "";
if (!empty($search)) {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where = "WHERE u.name LIKE '%$s%' OR u.email LIKE '%$s%' OR u.role LIKE '%$s%'";
}

$query_data = "SELECT u.id, u.name, u.email, u.role,
                      EXISTS(SELECT 1 FROM t_guru g JOIN t_wali_kelas wk ON wk.guru_id = g.id AND wk.status_aktif = 1 WHERE g.user_id = u.id) AS is_wk
               FROM t_users u $where ORDER BY u.id DESC";
$result = mysqli_query($koneksi, $query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola User - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-black">Kelola User</h1>
            <a href="tambah_user.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah User
            </a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="p-4 mb-6 rounded-lg text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                <?php echo $_SESSION['flash']['message']; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="GET" class="mb-6 flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama, email, atau role..." class="px-4 py-2 border border-slate-300 rounded-lg text-sm w-72 focus:outline-none focus:border-blue-600">
            <button type="submit" class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-4 py-2 rounded-lg text-sm font-bold">Cari</button>
        </form>

        <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-200">
            <h3 class="text-sm font-bold text-black mb-4">Daftar User</h3>
            <div class="overflow-x-auto">
                <table id="tabelData" class="w-full border-collapse border border-slate-300">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">NO</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">NAMA</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-left">EMAIL</th>
                            <th class="border border-slate-300 px-4 py-3 text-xs font-bold text-black uppercase text-center">ROLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($data = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center"><?php echo $no++; ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm font-bold text-black"><?php echo htmlspecialchars($data['name']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm"><?php echo htmlspecialchars($data['email']); ?></td>
                                    <td class="border border-slate-300 px-4 py-3 text-sm text-center">
                                        <?php if (strtolower($data['role']) === 'admin'): ?>
                                            <span class="text-blue-600 font-bold bg-blue-100 px-2 py-1 rounded text-xs">Admin</span>
                                        <?php elseif (!empty($data['is_wk'])): ?>
                                            <span class="text-purple-600 font-bold bg-purple-100 px-2 py-1 rounded text-xs">Wali Kelas</span>
                                        <?php else: ?>
                                            <span class="text-green-600 font-bold bg-green-100 px-2 py-1 rounded text-xs">Guru</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </main>
</body>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelData').DataTable({
            "searching": false,
            "language": {
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Belum ada data user.",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data tersedia",
                "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
            }
        });
    });
</script>
</html>
