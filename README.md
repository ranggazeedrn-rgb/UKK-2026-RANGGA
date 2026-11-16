# Sistem Informasi Kedisiplinan Siswa — UKK 2026

Aplikasi web untuk mencatat, memantau, dan merekap pelanggaran siswa secara terpusat. Dibuat untuk UKK RPL 2026 oleh **Rangga Ramadiyanto Nurhakim** (SMK Muhammadiyah Tasikmalaya).

## Fitur

**Admin**
- CRUD siswa, guru, kelas, tahun ajaran, user, jenis & kategori pelanggaran
- Penempatan siswa per kelas per tahun ajaran
- Penetapan wali kelas
- Lihat seluruh laporan, riwayat, rekap poin, dan arsip surat
- Cetak/export laporan (PDF via print browser, Excel `.xls`)

**Guru**
- Catat pelanggaran siswa
- Isi tindakan atas pelanggaran yang ia catat sendiri
- Rekap poin & riwayat pelanggaran

**Wali Kelas (guru yang ditunjuk di menu Wali Kelas)**
- Semua hak Guru, ditambah laporan siswa di kelasnya, riwayat, dan cetak surat peringatan / panggilan orang tua

**Umum**
- Login berbasis role, proteksi halaman terpusat di `cek_akses.php`
- Dashboard statistik + daftar siswa dengan poin ≥ 20 (peringatan) dan ≥ 50 (panggilan orang tua)
- Surat peringatan/panggilan siap cetak
- Halaman profil developer

## Teknologi

- PHP native (prosedural, `mysqli`) — tanpa framework dan tanpa Composer
- MySQL / MariaDB
- Tailwind CSS + Font Awesome via CDN
- Diagram database: `draw io/UKK_DB.drawio`

## Kebutuhan

- PHP **7.4+** (kode memakai arrow function)
- MySQL / MariaDB (XAMPP/Laragon sudah cukup)
- Web server Apache

## Instalasi

1. Clone repo ke folder web server:
   ```bash
   git clone https://github.com/ranggazeedrn-rgb/UKK-2026-RANGGA.git
   ```
   Untuk XAMPP letakkan di `C:\xampp\htdocs\UKK-2026-RANGGA`.

2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.

3. Import database `database/if0_43368906_db_ukk_rangga.sql` lewat phpMyAdmin.
   Nama database di dump adalah `if0_43368906_db_ukk_rangga`, sedangkan aplikasi memakai **`db_ukk_2026`** (`config/koneksi.php`). Pilih salah satu:
   - Rename database menjadi `db_ukk_2026` setelah import, atau
   - Ubah `$db` di `config/koneksi.php` sesuai nama database hasil import.

4. Sesuaikan kredensial database di `config/koneksi.php` bila perlu:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "db_ukk_2026";
   ```

5. Buka `http://localhost/UKK-2026-RANGGA/`.

## Akun Demo (dari data seed)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@ukk2026.com` | `admin123` |
| Guru | `guru@ukk2026.com` | `guru123` |
| Wali Kelas | `walikelas@ukk2026.com` | `walikelas123` |

Akun guru seed lainnya (`*@sekolah.sch.id`) memakai password `guru123`. Segera ganti password setelah aplikasi dipakai.

## Struktur Folder

```
UKK-2026-RANGGA/
├── config/koneksi.php        # Koneksi database
├── layouts/head.php          # Meta, Tailwind, Font Awesome, helper UI
├── layouts/sidebar.php       # Menu sidebar per role
├── database/                 # Dump SQL database
├── draw io/                  # ERD database
├── cek_akses.php             # Guard login, role, scope data, helper bersama
├── login.php / logout.php
├── dashboard.php
├── kelola_*.php / tambah_*.php / edit_*.php   # CRUD data master
├── catatan_pelanggaran.php   # Input pelanggaran
├── tindakan.php / edit_tindakan.php
├── rekap_point.php / riwayat.php / laporan.php
├── kelola_surat.php / tambah_surat.php / cetak_surat.php
├── cetak_export.php
├── walikelas.php / penempatan_siswa.php
└── aboutme.php
```

## Alur Singkat

1. Admin menyiapkan data master: tahun ajaran (aktifkan satu), kelas, guru, user, jenis & kategori pelanggaran.
2. Admin menempatkan siswa ke kelas pada tahun ajaran berjalan dan menetapkan wali kelas.
3. Guru mencatat pelanggaran; poin otomatis diakumulasi.
4. Guru yang mencatat (atau Admin) mengisi tindakan.
5. Siswa dengan poin ≥ 20 muncul di dashboard; Admin/Wali Kelas membuat dan mencetak surat.
6. Laporan dapat difilter per tahun ajaran/kelas lalu dicetak atau diexport.

## Catatan

- Tidak ada proses build: semua aset UI dari CDN, cukup buka lewat Apache.
- Login menerima password hash (`password_hash`) atau plaintext lama; data seed memakai hash bcrypt.
- `config/koneksi.php` berisi kredensial default XAMPP (`root`, tanpa password) — jangan dipakai apa adanya di server publik.
