<?php
// koneksi.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ukk_2026";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Pilihan tahun ajaran dibatasi 5 tahun ke belakang dari tahun berjalan
if (!function_exists('tahun_ajaran_pilihan')) {
    function tahun_ajaran_pilihan($koneksi)
    {
        return mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE YEAR(tanggal_mulai) >= YEAR(CURDATE()) - 5 ORDER BY id DESC");
    }
}
?>
