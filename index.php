<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['login']) || isset($_SESSION['loggedin']);
if ($is_logged_in) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sistem Informasi Pelanggaran Siswa - UKK 2026</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-2">
            <i class=" fa-solid fa-school text-3xl text-blue-600"></i>
            <span class="font-bold text-black text-lg tracking-wide">SISTEM KEDISIPLINAN</span>
        </div>
        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-bold text-sm shadow-sm transition">
            Masuk
        </a>
    </nav>

    <!-- Hero Section -->
    <section class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white border-[3px] border-blue-500 rounded-[2.5rem] p-10 max-w-2xl w-full text-center shadow-lg">
            <div class="mb-6 flex justify-center">
                 <i class=" fa-solid fa-school text-6xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-black mb-4">Sistem Informasi Kedisiplinan Siswa</h1>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                Platform pencatatan, pemantauan, dan rekapitulasi kedisiplinan siswa secara real-time. Memudahkan Guru, Wali Kelas, dan Administrator dalam mengelola tata tertib sekolah secara transparan dan terintegrasi.
            </p>
            <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg px-8 py-3 text-sm transition shadow-sm">
                Masuk ke Aplikasi
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 text-center py-4 text-xs font-bold text-slate-500">
        &copy; 2026 Sistem Pelanggaran Siswa - UKK RPL 2026. All rights reserved.
    </footer>

</body>
</html>
