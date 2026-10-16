# EduSync Attendance

Sistem absensi dan administrasi akademik sekolah berbasis CodeIgniter 4.
Aplikasi ini menggabungkan kios absensi RFID, pencatatan absensi kelas,
penilaian akademik, penerimaan siswa baru (SPMB), dan website sekolah publik
dalam satu basis kode.

- Framework: CodeIgniter 4 (4.7.5)
- PHP: 8.1 atau lebih baru (dikembangkan pada PHP 8.2)
- Database: MySQL / MariaDB (nama database: `edusync`)
- Antarmuka panel: tema Vuexy, jQuery, dan DataTables. Seluruh aset sudah
  dibangun dan disertakan di `public/assets/`, jadi tidak perlu langkah build
  untuk menjalankan aplikasi.

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Peran Pengguna](#peran-pengguna)
- [Instalasi](#instalasi)
- [Konfigurasi `.env`](#konfigurasi-env)
- [Menyiapkan Basis Data](#menyiapkan-basis-data)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Peta Rute](#peta-rute)
- [Struktur Proyek](#struktur-proyek)
- [Notifikasi WhatsApp](#notifikasi-whatsapp)
- [Berkas SQL Pendukung](#berkas-sql-pendukung)
- [Dokumentasi Tambahan](#dokumentasi-tambahan)

## Fitur Utama

### Absensi

- **Kios absensi RFID** di `/attendance` dan `/attendance/kiosk` (publik, tanpa
  login). Siswa menempelkan kartu RFID; sistem mencatat jam masuk/pulang dan
  mengirim notifikasi WhatsApp ke orang tua. Halaman `/` sendiri adalah beranda
  website sekolah.
- **Absensi kelas** di `/class-attendance`. Menyimpan kehadiran per kelas,
  mata pelajaran, guru, jam pelajaran, tahun ajaran, dan tanggal. Status:
  hadir, sakit, izin, alpa. Satu kombinasi kelas/mapel/jam/tahun hanya boleh
  dicatat sekali per tanggal.
- **Absensi ekstrakurikuler** di `/extracurricular-attendance`.
- **Laporan** absensi harian (`/report/attendance`) dan rekap bulanan per kelas
  (`/class-attendance-report`), keduanya dapat diunduh sebagai PDF.

### Akademik

- **Master data**: mata pelajaran, guru, orang tua/wali, jam pelajaran,
  ekstrakurikuler, kokurikuler, tahun ajaran, kelas, dan jurusan.
- **Penugasan guru–mapel** di `/teacher-subjects`, prasyarat untuk input nilai.
- **Input nilai** di `/grades` dengan empat komponen (UTS, UAS, Pengetahuan,
  Praktik), masing-masing berskala 0–100. Komponen boleh dikosongkan sampai
  nilainya tersedia. Laporan nilai ada di `/report/grades`.
- **Asesmen siswa** di `/academic-assessments/{jenis}` dengan tiga jenis:
  `personality` (aspek kepribadian), `extracurricular` (ekstrakurikuler), dan
  `kokurikuler`. Penilaian memakai skala A–D.

### Penerimaan Siswa Baru (SPMB)

- Formulir publik di `/news/spmb`. Mengumpulkan data siswa, kontak, alamat,
  sekolah asal, dan data orang tua/wali.
- Dokumen wajib: Kartu Keluarga, akta kelahiran/surat keterangan lahir, KTP
  orang tua/wali, ijazah/SKL jenjang sebelumnya, dan pas foto terbaru. Setiap
  berkas maksimal 2 MB; pas foto harus berupa gambar.
- Berkas disimpan di `writable/uploads/spmb`; pendaftar menerima bukti
  pendaftaran PDF.
- Super Admin meninjau pendaftar di `/spmb/applications`.

### Website Sekolah Publik (SekolahKu)

Beranda publik dilayani di `/` dan `/news`. Halaman publik lainnya berada di
bawah prefiks `/news/`:

- `/news` — beranda, `/news/news` — daftar berita, `/news/news/search` —
  pencarian berita, `/news/downloads` — unduhan.
- `/news/contact` — kontak dan pesan dari pengunjung.
- Kelola konten dari panel: **Kelola News**, **Pengaturan Kontak**,
  **Pengaturan Beranda**, **Pengaturan Profil**, **Pengaturan Program**, dan
  **Pesan Masuk**.

### Administrasi Sistem

- **Data User** (`/users`) untuk penetapan peran, serta `/user-passwords` untuk
  reset kata sandi.
- **Pengaturan Sidebar** (`/sidebar-settings`) — sembunyikan/tampilkan menu.
- **Pengaturan Akses Tombol** (`/button-settings`) — izin aksi operasional per
  peran atau per akun.
- **Pengaturan Menu** (`/menu-settings`) — menu sidebar kustom beserta ikon,
  urutan, URL, dan induk menu.
- **Profil** (`/profile`) — data diri, foto, dan ganti kata sandi.

## Peran Pengguna

Peran didefinisikan di `app/Libraries/RoleAccess.php`:

| Peran | Cakupan bawaan |
| --- | --- |
| `super_admin` | Kelola data sekolah, pengguna, dan seluruh pencatatan absensi. |
| `user` | Akses dasbor dan profil. |
| `kepala_sekolah` | Pantau ringkasan kehadiran dan laporan sekolah. |
| `bendahara` | Pantau ringkasan kehadiran sekolah (modul keuangan belum tersedia). |
| `kurikulum` | Kelola dan pantau informasi akademik sesuai akses yang diberikan. |
| `operator` | Kelola operasional sekolah sesuai akses yang diberikan. |

Aturan penting:

- Super Admin selalu memegang seluruh izin operasional dan merupakan satu-satunya
  peran yang dapat mengelola pengguna serta konfigurasi akses sistem.
- Pengaturan per akun pada `/button-settings` menimpa pengaturan perannya untuk
  aksi yang dikonfigurasi secara eksplisit; aksi yang tidak diatur mewarisi izin
  peran.
- Izin tombol mengatur visibilitas tombol **dan** endpoint aksinya, dan peran
  tetap harus memiliki akses ke menu terkait.
- Perubahan peran berlaku pada permintaan terlindungi berikutnya dari akun
  tersebut.
- Menambah menu sidebar tidak membuat halaman baru maupun memberikan akses;
  rute tujuan harus sudah ada dan tetap menegakkan otorisasinya sendiri.

## Instalasi

```bash
composer install
```

Perintah ini memasang CodeIgniter 4 beserta Dompdf (untuk ekspor PDF).
Aset tampilan sudah tersedia di `public/assets/`, sehingga tidak diperlukan
langkah build tambahan. `package.json` hanya memuat jQuery dan DataTables
untuk keperluan pengembangan.

## Konfigurasi `.env`

Salin berkas `env` menjadi `.env`, lalu sesuaikan nilainya. Jangan pernah
meng-commit `.env` karena berisi kredensial.

### Aplikasi

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
```

### Basis data

```ini
database.default.hostname = 127.0.0.1
database.default.database = edusync
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### Super Admin pertama

Alamat email pada nilai ini akan dijadikan Super Admin saat migrasi
autentikasi dijalankan. Email lain yang mendaftar menerima peran `user`.

```ini
auth.super_admin_email = admin@example.com
```

### SMTP pengiriman kode verifikasi

Gunakan Google App Password (dengan Verifikasi 2 Langkah aktif), bukan kata
sandi akun:

```ini
email.protocol = smtp
email.fromEmail = admin@example.com
email.fromName = EduSync
email.SMTPHost = smtp.gmail.com
email.SMTPUser = admin@example.com
email.SMTPPass = your-google-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

### WhatsApp (opsional)

Lihat [Notifikasi WhatsApp](#notifikasi-whatsapp).

## Menyiapkan Basis Data

Buat database `edusync`, lalu jalankan migrasi dengan akun yang berwenang
membuat dan mengubah tabel:

```bash
php spark migrate
```

Akun basis data yang dipakai aplikasi saat berjalan memerlukan `SELECT`,
`INSERT`, `UPDATE`, dan `DELETE` pada tabel yang digunakannya, termasuk
`custom_sidebar_items`, `role_action_settings`, dan `user_action_settings`.
Server web juga harus diizinkan menulis ke `writable/uploads/spmb`.

Alternatif tanpa migrasi: impor `edusync.sql`, lalu terapkan berkas SQL
pendukung yang relevan (lihat [Berkas SQL Pendukung](#berkas-sql-pendukung)).

### Alur pendaftaran dan masuk

Pendaftaran meminta nama, nama pengguna, kata sandi, dan email. Pengguna
memverifikasi email satu kali dengan kode sekali pakai, lalu masuk memakai
nama pengguna dan kata sandi.

- Kode verifikasi berlaku 10 menit dan hanya dapat dipakai sekali.
- Kata sandi minimal 8 karakter.
- Nama pengguna 3–32 karakter, hanya huruf, angka, titik, garis bawah, atau
  tanda hubung.

## Menjalankan Aplikasi

Cara tercepat, memakai server bawaan CodeIgniter:

```bash
php spark serve
```

Aplikasi tersedia di <http://localhost:8080>. Port dapat diganti dengan
`php spark serve --port 8081`.

Untuk XAMPP/Apache, arahkan document root virtual host ke folder `public/`,
bukan ke akar proyek. `index.php` sengaja tidak berada di akar proyek agar
kode aplikasi dan framework tidak terekspos. Menaruh `index.php` di akar dan
mengaksesnya lewat `public/...` adalah praktik yang tidak disarankan.

## Peta Rute

Rute lengkap ada di `app/Config/Routes.php`. Ringkasan area utama:

| Area | Contoh rute |
| --- | --- |
| Kios absensi | `/`, `/attendance/kiosk`, `POST /attendance/tap` |
| Manajemen absensi | `/attendance/manage`, `/report/attendance` |
| Absensi kelas | `/class-attendance`, `/class-attendance/create`, `/class-attendance-report` |
| Absensi ekstrakurikuler | `/extracurricular-attendance`, `/report/extracurricular-attendance` |
| Nilai | `/grades`, `/grades/student/{id}`, `/report/grades` |
| Asesmen | `/academic-assessments/{jenis}` |
| Master data | `/subjects`, `/teachers`, `/parents`, `/lesson-periods`, `/extracurriculars`, `/kokurikuler`, `/academic-years` |
| Kelas & jurusan | `/class`, `/major`, `/student`, `/timesetting`, `/attendance-status` |
| Website sekolah | `/website-news`, `/website-messages`, `/website-settings/*`, `/website-content/*`, `/school-contact-settings` |
| SPMB | `/news/spmb` (publik), `/spmb/applications` |
| Autentikasi | `/login`, `/register`, `/auth/*` |
| Pengaturan sistem | `/users`, `/user-passwords`, `/sidebar-settings`, `/button-settings`, `/menu-settings`, `/teacher-subjects` |

Semua permintaan `POST` yang mengubah data dilindungi filter `csrf`. Rute
panel dilindungi filter `roleAccess` atau `actionAccess`; rute konfigurasi
sistem memakai `adminAuth`.

## Struktur Proyek

```
app/
  Config/         Konfigurasi aplikasi, rute, basis data, filter
  Controllers/    28 controller (panel, absensi, akademik, website, SPMB)
  Database/
    Migrations/   30 migrasi skema
  Libraries/      ActionAccess, RoleAccess, SpmbProofPdf,
                  StudentSpreadsheet, WhatsAppAttendanceNotifier
  Models/         16 model
  Views/          Tampilan panel, laporan, dan website SekolahKu
docs/             Dokumentasi pendukung
public/           Document root; berisi index.php
writable/         Log dan unggahan (uploads/spmb)
```

## Notifikasi WhatsApp

Absensi kelas dan tap RFID mengirim notifikasi ke orang tua/wali melalui API
Fonnte. Absensi tetap tersimpan meskipun WhatsApp belum dikonfigurasi atau
pengiriman gagal.

```ini
WHATSAPP_API_URL = "https://api.fonnte.com/send"
WHATSAPP_API_TOKEN = your_fonnte_device_token
```

Nomor yang diawali `08` dikonversi menjadi `62` saat pengiriman. Satu data
orang tua dapat ditautkan ke beberapa siswa. Untuk absensi kelas yang sudah
tersimpan, gunakan **Kirim Ulang WA** pada daftar absensi.

Panduan lengkap: [docs/whatsapp-attendance-setup.md](docs/whatsapp-attendance-setup.md).

## Berkas SQL Pendukung

Berkas berikut ada di akar proyek untuk instalasi atau perbaikan manual:

| Berkas | Kegunaan |
| --- | --- |
| `edusync.sql` | Dump skema/data keseluruhan. |
| `create_master_kokurikuler.sql` | Tabel master kokurikuler. |
| `create_kokurikuler_assessments.sql` | Tabel asesmen kokurikuler. |
| `create_role_action_settings.sql` | Tabel izin aksi per peran. |
| `setup_sidebar_menu_icon_overrides.sql` | Timpaan ikon menu sidebar. |
| `fix_master_siswa_status.sql` | Perbaikan kolom status siswa. |
| `grant_attendance_read_access.sql` | Hak baca untuk akun pelaporan. |

## Dokumentasi Tambahan

- [docs/whatsapp-attendance-setup.md](docs/whatsapp-attendance-setup.md) —
  penyiapan Fonnte dan penautan kontak orang tua.
- [note.md](note.md) — catatan pekerjaan yang belum selesai.

## Kebutuhan Server

PHP 8.1 atau lebih baru dengan ekstensi:

- [intl](https://www.php.net/manual/en/intl.requirements.php)
- [mbstring](https://www.php.net/manual/en/mbstring.installation.php)
- json
- [mysqlnd](https://www.php.net/manual/en/mysqlnd.install.php) untuk MySQL
- [libcurl](https://www.php.net/manual/en/curl.requirements.php) untuk
  `HTTP\CURLRequest`

## Lisensi

Lihat [LICENSE](LICENSE). Komponen CodeIgniter 4 berlisensi MIT.
