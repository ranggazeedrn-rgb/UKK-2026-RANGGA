<?php
// login.php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_input = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email_input) && !empty($password_input)) {
        $email = mysqli_real_escape_string($koneksi, $email_input);
        $query = "SELECT * FROM t_users WHERE LOWER(email) = LOWER('$email') LIMIT 1";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($password_input === $user['password'] || password_verify($password_input, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar dalam sistem!";
        }
    } else {
        $error = "Silakan isi email dan password terlebih dahulu!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Sistem Kedisiplinan Siswa</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-white min-h-screen flex items-center justify-center font-sans text-slate-800">

    <div class="w-full max-w-sm border-[3px] rounded-[2.5rem] p-10 bg-white">
        <div class="text-center mb-8">
        <i class="fa-solid fa-school text-6xl text-blue-600"></i>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 border border-red-200 p-3 rounded-lg text-xs mb-5 text-center font-bold">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Email</label>
                <input type="email" name="email" required placeholder="Masukan Email" class="w-full border border-slate-400 rounded-lg px-4 py-2 text-sm text-slate-800 focus:outline-none focus:border-blue-600">
            </div>
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" id="inputPassword" name="password" required placeholder="**********" class="w-full border border-slate-400 rounded-lg px-4 py-2 pr-11 text-sm text-slate-800 focus:outline-none focus:border-blue-600">
                    <button type="button" id="tombolLihatPassword" onclick="toggleLihatPassword()"
                            title="Lihat / sembunyikan password"
                            aria-label="Lihat password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 text-xs">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg py-2.5 text-sm transition">
                LOGIN
            </button>
        </form>
    </div>

    <script>
        // Tampilkan / sembunyikan password
        function toggleLihatPassword() {
            var kolom = document.getElementById('inputPassword');
            var tombol = document.getElementById('tombolLihatPassword');
            if (!kolom) return;
            var tampil = kolom.type === 'password';
            kolom.type = tampil ? 'text' : 'password';
            if (tombol) {
                var ikon = tombol.querySelector('i');
                if (ikon) ikon.className = tampil ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
                tombol.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Lihat password');
            }
        }
    </script>

</body>
</html>