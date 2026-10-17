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

| Peran            | Cakupan bawaan                                                      |
| ---------------- | ------------------------------------------------------------------- |
| `super_admin`    | Kelola data sekolah, pengguna, dan seluruh pencatatan absensi.      |
| `user`           | Akses dasbor dan profil.                                            |
| `kepala_sekolah` | Pantau ringkasan kehadiran dan laporan sekolah.                     |
| `bendahara`      | Pantau ringkasan kehadiran sekolah (modul keuangan belum tersedia). |
| `kurikulum`      | Kelola dan pantau informasi akademik sesuai akses yang diberikan.   |
| `operator`       | Kelola operasional sekolah sesuai akses yang diberikan.             |

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

Keep `.env` private and do not commit SMTP credentials. Run `php spark migrate`
with a database account that can create tables. The account used by the web
application needs `SELECT`, `INSERT`, and `UPDATE` access to `auth_users`.
The migration seeds the configured email as the Super Admin; other registered
email addresses receive the regular `user` role. Registration verification
codes expire after 10 minutes and can only be used once. Passwords must be at
least 8 characters; usernames must be 3–32 letters, numbers, dots, underscores,
or hyphens.

Super Admin assigns the `user`, `kepala_sekolah`, and `bendahara` roles from
the Data User page. The User role has dashboard and profile access by default;
Kepala Sekolah and Bendahara can view attendance reports and class-attendance
details by default. Super Admin can grant operational button permissions to
any role from Pengaturan Akses Tombol. Role changes take effect on the
account's next protected request. Bendahara currently sees attendance
summaries only; the application does not yet contain financial records or a
finance module.

Super Admin can show or hide items in the Super Admin sidebar from
`/sidebar-settings`. This changes sidebar visibility only; existing role
authorization still protects each page.

Super Admin can grant permissions for operational buttons per role or
individual account from `/button-settings`, including attendance
entry/edit/delete, student management, academic master data, grades, and
assessments. Per-user settings override that account's role setting for actions
that are explicitly configured; unset actions inherit the role's permission.
These permissions control both button visibility and the corresponding action
endpoints, and the role must also have access to the associated menu. Super
Admin always retains all operational permissions. User/role administration and
system access configuration remain Super Admin-only.

Super Admin can add custom sidebar menus and submenus at `/menu-settings`,
including their Bootstrap icon, display order, internal URL, and parent menu.
Submenus can be placed under built-in sections such as Settings or under a
custom menu, then moved to a different section. Changing sidebar navigation
does not create a page or grant access to it; the target route must already
exist and continue to enforce its own role authorization.

### Academic data and classroom attendance

After signing in as Super Admin, add subjects, teachers, lesson periods,
extracurricular activities, kokurikuler activities, and an academic year from
their Master Data pages. Kokurikuler activities have their own CRUD page at
`/kokurikuler`; access to its create, edit, and delete actions can be managed
per role or per user from `/button-settings`. Student kokurikuler assessments
are entered at `/academic-assessments/kokurikuler`, using the same A-D scale as
extracurricular assessments, and are included in the student report. Create the
assessment storage table with `create_kokurikuler_assessments.sql` or run its
migration. Class attendance can then be
recorded at `/class-attendance/create`: select the class, subject, teacher,
period, academic year, and date, then set each active student's status. The
class attendance list and student-level details are available at
`/class-attendance`. Attendance status options are present, sick, excused, and
absent; a class/subject/period/year combination can only be recorded once per
date.

Before entering grades, assign subjects to teachers at `/teacher-subjects`.
The `Input Nilai` page lets an authorized role select a class, academic year,
subject, and a teacher assigned to that subject, then save UTS, UAS, and
Praktik scores (0–100) for each active student in the class. Scores are stored
separately per student, class, subject, teacher, and academic year/semester;
blank components are allowed until a score is available. From the grade list,
use `Lihat Detail` on a student to view the saved score components and their
class, subject, teacher, and semester context.

### Public SekolahKu website

Super Admin manages public articles through **Website Sekolah → Kelola News**.
Articles are stored in the `school_website_news` table in the EduSync database;
new installations start with no articles. **Lihat News** opens the public
SekolahKu homepage at `/news`, and the article listing is at `/news/news`.
The attendance kiosk remains available at `/`; public pages stay under the
`/news/` prefix. The main EduSync application does not connect to the separate
SekolahKu CMS database.

Super Admin can update the public contact page from **Website Sekolah →
Pengaturan Kontak** in the EduSync panel. Changes to the school address,
telephone, email, and working hours appear on `/news/contact`; this does not
open a separate CMS admin.

The same **Website Sekolah** group includes **Pengaturan Beranda**,
**Pengaturan Profil**, and **Pengaturan Program**. These pages update the
homepage hero, statistics, principal welcome, school profile, and visible
program cards using tables in the EduSync database. The website-settings,
program, and news tables are created empty; no settings, programs, or articles
are copied from the SekolahKu database or inserted as seed data.

Set the SPMB registration period from **Website Sekolah → Jadwal SPMB Online**.
The start and end dates are inclusive in the `Asia/Jakarta` timezone. Leaving
both dates empty keeps registration open without a schedule; entering a period
closes the public form outside that range, shows a notice when visitors open
SPMB Online, and rejects direct submissions after it closes.

The panel notification bell shows new account registrations and successful
SPMB applications for Super Admin. **Settings → User Sedang Login** lists
verified accounts active in the previous five minutes; stale session activity
is removed after two hours, and logging out removes that session immediately.
Run `php spark migrate` to create the notification, per-user read-status, and
online-session tables. The runtime database account needs `SELECT`, `INSERT`,
`UPDATE`, and `DELETE` access to `admin_notifications`,
`admin_notification_reads`, and `admin_online_sessions`.

The public **SPMB Online** button opens `/news/spmb`. The registration form
collects student, contact, address, previous-school, and parent/guardian data.
It does not request an admission path, report-card scores, parent income,
geotag selfie, SPTJM, or path-specific certificates.
Required documents are limited to the family card, birth certificate, guardian
identity, previous-school certificate, and recent photo; each upload is
limited to 2 MB. Submitted values and private document metadata are stored in
EduSync, while files are kept under `writable/uploads/spmb`. Applicants receive
a PDF registration receipt after submission. Super Admin can review
applications and download their documents from **Website Sekolah → Pendaftar
SPMB**. Applicant details are presented in a grouped popup with document links.
CSRF checks remain enabled; tokens stay stable across submissions so a long
registration form is not invalidated by another open form.

Run `php spark migrate` with a database account authorized to create and alter
the website, SPMB, and custom-sidebar tables. The runtime database account
needs `SELECT`, `INSERT`, `UPDATE`, and `DELETE` on the tables it uses,
including `custom_sidebar_items`, and the web server must be allowed to write
to the `writable/uploads/spmb` directory. The runtime account also needs
`SELECT`, `INSERT`, `UPDATE`, and `DELETE` access to `role_action_settings`
and `user_action_settings` for per-role and per-user button permissions.

## Berkas SQL Pendukung

Berkas berikut ada di akar proyek untuk instalasi atau perbaikan manual:

| Berkas                                  | Kegunaan                       |
| --------------------------------------- | ------------------------------ |
| `edusync.sql`                           | Dump skema/data keseluruhan.   |
| `create_master_kokurikuler.sql`         | Tabel master kokurikuler.      |
| `create_kokurikuler_assessments.sql`    | Tabel asesmen kokurikuler.     |
| `create_role_action_settings.sql`       | Tabel izin aksi per peran.     |
| `setup_sidebar_menu_icon_overrides.sql` | Timpaan ikon menu sidebar.     |
| `fix_master_siswa_status.sql`           | Perbaikan kolom status siswa.  |
| `grant_attendance_read_access.sql`      | Hak baca untuk akun pelaporan. |

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
