<?php
session_start();
include 'config/koneksi.php';
include 'cek_akses.php';

// Cek hak akses khusus Admin
$role_session = strtolower($_SESSION['role'] ?? $_SESSION['level'] ?? '');
if ($role_session !== 'admin') {
    header("Location: index.php");
    exit();
}

// Proses Simpan User Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_user'])) {
    $name           = mysqli_real_escape_string($koneksi, trim($_POST['name'] ?? ''));
    $email          = mysqli_real_escape_string($koneksi, trim($_POST['email'] ?? ''));
    $password       = $_POST['password'] ?? '';
    $confirm_pass   = $_POST['confirm_password'] ?? '';
    $role_input     = mysqli_real_escape_string($koneksi, $_POST['role'] ?? '');

    // Validasi Kelengkapan dan Konfirmasi Password
    if (empty($name) || empty($password) || empty($confirm_pass) || empty($role_input)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Harap isi semua kolom wajib (*)'];
    } elseif ($password !== $confirm_pass) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Konfirmasi password tidak cocok. Silakan periksa kembali!'];
    } else {
        // Cek ketersediaan nama pengguna
        $cek_user = @mysqli_query($koneksi, "SELECT id FROM t_users WHERE name = '$name'");
        if ($cek_user && mysqli_num_rows($cek_user) > 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Nama pengguna sudah terdaftar, gunakan nama lain.'];
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_email = !empty($email) ? "'$email'" : "NULL";
            $remember_token = ''; 

            $query = "INSERT INTO t_users (name, email, password, remember_token, role, created_at, updated_at) 
                      VALUES ('$name', $sql_email, '$hashed_password', '$remember_token', '$role_input', NOW(), NOW())";
            
            $simpan = @mysqli_query($koneksi, $query);

            if ($simpan) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'User baru berhasil ditambahkan!'];
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menyimpan user: ' . mysqli_error($koneksi)];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah User Baru - Sistem Kedisiplinan</title>
    <?php include 'layouts/head.php'; ?>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <?php include 'layouts/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <main class="p-8 overflow-y-auto flex-1">
            
            <div class="flex justify-between items-center mb-8 max-w-4xl mx-auto">
                <div>
                    <h1 class="text-2xl font-bold text-black">Tambah User Baru</h1>
                    <p class="text-sm font-bold text-slate-500 mt-1">Buat akun pengguna baru, tentukan hak akses, dan verifikasi password.</p>
                </div>
                <a href="dashboard.php" class="text-slate-600 hover:text-black font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="max-w-4xl mx-auto p-4 mb-6 rounded-xl text-sm font-bold <?php echo $_SESSION['flash']['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo $_SESSION['flash']['message']; unset($_SESSION['flash']); ?>
                </div>
            <?php endif; ?>

            <div class="max-w-4xl mx-auto bg-white p-8 rounded-[1.5rem] shadow-sm border border-slate-200">
                <form method="POST" action="tambah_user.php" class="space-y-5" onsubmit="return validatePassword()">
                    <input type="hidden" name="simpan_user" value="1">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">NAMA PENGGUNA *</label>
                            <input type="text" name="name" placeholder="Masukkan nama pengguna" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">EMAIL (Opsional)</label>
                            <input type="email" name="email" placeholder="Masukkan email aktif" class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">PASSWORD *</label>
                            <input type="password" name="password" id="password" placeholder="Masukkan password" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">KONFIRMASI PASSWORD *</label>
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Masukkan ulang password" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">PILIH ROLE / HAK AKSES *</label>
                        <select name="role" required class="w-full border border-slate-300 px-4 py-3 rounded-xl text-sm focus:border-blue-600 focus:outline-none bg-slate-50">
                            <option value="">-- Pilih Role --</option>
                            <option value="Admin">Admin</option>
                            <option value="Guru">Guru</option>
                        </select>
                    </div>

                    <div id="error-msg" class="text-red-600 text-xs font-bold hidden"></div>

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-plus"></i> Simpan User Baru
                        </button>
                    </div>
                </form>
            </div>

        </main>
    </div>

    <script>
        function validatePassword() {
            var pass = document.getElementById("password").value;
            var confirmPass = document.getElementById("confirm_password").value;
            var errorDiv = document.getElementById("error-msg");

            if (pass !== confirmPass) {
                errorDiv.innerText = "Konfirmasi password tidak cocok. Mohon pastikan kedua password sama.";
                errorDiv.classList.remove("hidden");
                return false;
            }
            errorDiv.classList.add("hidden");
            return true;
        }
    </script>
</body>
</html>