# Sistem Informasi Kedisiplinan Siswa — UKK 2026

Aplikasi web untuk mencatat, memantau, dan merekap pelanggaran siswa secara terpusat. Project ini dibuat untuk kebutuhan UKK RPL 2026 oleh **Rangga Ramadiyanto Nurhakim** dari **SMK Muhammadiyah Tasikmalaya**.

## Deskripsi

Sistem ini membantu sekolah dalam mengelola data kedisiplinan siswa dengan alur yang sederhana namun lengkap, mulai dari input data siswa, guru, kelas, tahun ajaran, hingga proses pencatatan pelanggaran dan pembuatan surat peringatan.

Dengan sistem ini, pihak sekolah dapat:
- mengelola data master secara terstruktur,
- memantau poin pelanggaran siswa secara real-time,
- menilai tingkat kedisiplinan berdasarkan rekapan poin,
- membuat surat peringatan atau panggilan orang tua dengan cepat,
- menampilkan dashboard yang memudahkan monitoring.

## Fitur Utama

### Admin
- CRUD siswa, guru, kelas, tahun ajaran, user, jenis pelanggaran, dan kategori pelanggaran
- Penempatan siswa ke kelas per tahun ajaran
- Penetapan wali kelas
- Melihat seluruh laporan, riwayat, rekap poin, dan arsip surat
- Cetak/export laporan dalam format PDF dan Excel `.xls`

### Guru
- Mencatat pelanggaran siswa
- Mengisi tindakan atas pelanggaran yang dibuat sendiri
- Melihat rekap poin dan riwayat pelanggaran

### Wali Kelas
- Memiliki akses seperti Guru, ditambah:
- melihat laporan siswa di kelasnya,
- memantau riwayat pelanggaran,
- mencetak surat peringatan atau panggilan orang tua

### Umum
- Sistem login berbasis role
- Proteksi akses halaman terpusat melalui `cek_akses.php`
- Dashboard statistik dan daftar siswa dengan poin ≥ 20 serta ≥ 50
- Surat peringatan dan panggilan siap dicetak
- Halaman profil pengembang

## Teknologi yang Digunakan

- PHP native (prosedural, `mysqli`)
- MySQL / MariaDB
- Tailwind CSS
- Font Awesome via CDN
- Diagram database: `draw io/UKK_DB.drawio`

## Persyaratan Sistem

- PHP 7.4+
- MySQL / MariaDB
- Web server Apache
- XAMPP / Laragon (direkomendasikan untuk pengembangan lokal)

## Instalasi

### 1) Clone repository Melalui Github Cli

```bash
gh repo clone ranggazeedrn-rgb/UKK-2026-RANGGA
```

Atau Clone Melalui Github Desktop

```bash
https://github.com/ranggazeedrn-rgb/UKK-2026-RANGGA.git
```

### 2) Letakkan project di folder web server

Untuk XAMPP:

```bash
C:\xampp\htdocs\UKK-2026-RANGGA
```

### 3) Jalankan Apache dan MySQL

Aktifkan Apache dan MySQL pada XAMPP Control Panel.

### 4) Import database

Import file SQL berikut:

```bash
database/if0_43368906_db_ukk_rangga.sql
```

Catatan penting:
- Nama database asli pada dump adalah `if0_43368906_db_ukk_rangga`
- Aplikasi ini pada konfigurasi default memakai database `db_ukk_2026`

Pilih salah satu cara berikut:
- rename database hasil import menjadi `db_ukk_2026`, atau
- ubah nilai `$db` di `config/koneksi.php` sesuai nama database yang Anda gunakan

### 5) Konfigurasi koneksi database

Buka file `config/koneksi.php` lalu sesuaikan konfigurasi berikut:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ukk_2026";
```

### 6) Akses aplikasi

Buka browser dan masuk ke:

```bash
http://localhost/UKK-2026-RANGGA/
```

## Akun Demo

Akun demo berikut tersedia dari data seed:

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@ukk2026.com` | `admin123` |
| Guru | `guru@ukk2026.com` | `guru123` |

Catatan:
- Akun guru lain dengan domain `*@sekolah.sch.id` juga menggunakan password `guru123`
- Segera ganti password setelah aplikasi dipakai di lingkungan nyata

## Struktur Folder

```text
UKK-2026-RANGGA/
├── config/
│   └── koneksi.php          # konfigurasi database
├── layouts/
│   ├── head.php             # meta tag, Tailwind, Font Awesome, helper UI
│   └── sidebar.php          # menu sidebar per role
├── database/
│   └── *.sql                # dump database
├── draw io/
│   └── UKK_DB.drawio        # diagram ERD database
├── cek_akses.php            # guard login, role, dan akses data
├── login.php
├── logout.php
├── dashboard.php
├── kelola_*.php
├── tambah_*.php
├── edit_*.php
├── catatan_pelanggaran.php
├── tindakan.php
├── edit_tindakan.php
├── rekap_point.php
├── riwayat.php
├── laporan.php
├── kelola_surat.php
├── tambah_surat.php
├── cetak_surat.php
├── cetak_export.php
├── walikelas.php
├── penempatan_siswa.php
├── aboutme.php
└── README.md
```

## Alur Sistem

1. Admin menyiapkan data master seperti tahun ajaran, kelas, guru, user, serta jenis dan kategori pelanggaran.
2. Admin menempatkan siswa ke kelas pada tahun ajaran aktif dan menetapkan wali kelas.
3. Guru mencatat pelanggaran siswa dan sistem akan melakukan akumulasi poin otomatis.
4. Guru yang mencatat pelanggaran, atau Admin, dapat mengisi tindakan atas pelanggaran tersebut.
5. Siswa dengan poin ≥ 20 akan muncul di dashboard sebagai peringatan; siswa dengan poin ≥ 50 akan masuk kategori panggilan orang tua.
6. Admin atau Wali Kelas dapat membuat dan mencetak surat peringatan/panggilan orang tua.
7. Laporan dapat difilter berdasarkan tahun ajaran dan kelas lalu dicetak atau di-export.

## Catatan Penting

- Aplikasi tidak membutuhkan proses build. Semua asset UI diambil dari CDN.
- Login mendukung password yang sudah di-hash (`password_hash`) maupun password plain text versi lama.
- File `config/koneksi.php` berisi kredensial default XAMPP (`root`, tanpa password). Jangan digunakan begitu saja untuk server publik.
- Project ini dibuat untuk keperluan pembelajaran dan ujian praktik, sehingga perlu disesuaikan terlebih dahulu sebelum digunakan di lingkungan produksi.

## Lisensi

Project ini dibuat untuk kebutuhan pembelajaran UKK RPL 2026. Gunakan dengan bijak dan sesuaikan kebutuhan sekolah/instansi Anda.

## Kontak

Dibuat oleh: **Rangga Ramadiyanto Nurhakim**

GitHub: https://github.com/ranggazeedrn-rgb

---

Semoga README ini lebih rapi, informatif, dan siap digunakan untuk proyek maupun portofolio.
