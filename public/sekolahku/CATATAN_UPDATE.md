# CATATAN_UPDATE — Rilis Berikutnya

> Catatan perubahan yang **belum di-commit** (working tree). Ditujukan sebagai bahan rilis berikutnya.
> Baseline versi aktif saat ini: `v3.1.2` (`app/Config/Constants.php`).
> Status: **draf** — perubahan keamanan sudah dilintasi `php -l`, namun belum melalui UAT penuh.

---

## 1. Perbaikan Keamanan (Bug Fix)

### 1.1 Path Traversal — `app/Controllers/Admin/Uploads.php` (`delete()`)
- **Masalah:** Pengecekan lama hanya `str_starts_with($filePath, FCPATH . 'uploads/')` yang dapat dilewati menggunakan `..` (mis. `uploads/../config/...`), sehingga memungkinkan penghapusan file di luar direktori uploads.
- **Perbaikan:** Path di-resolve dengan `realpath()` dan diverifikasi benar-benar berada di dalam `FCPATH . 'uploads'` (prefix `$uploadsRoot . DIRECTORY_SEPARATOR`). Request dengan path tidak valid ditolak dengan pesan error.
- **File:** `app/Controllers/Admin/Uploads.php`

### 1.2 Arbitrary File Upload / RCE — Upload Gambar
- **Masalah:** Di 4 controller, `getimagesize()` yang gagal (hasil `false`) di-destructure menjadi null, lalu file tetap dipindahkan dengan ekstensi asli dari klien (`getRandomName()` mempertahankan ekstensi klien). File `.php` — atau gambar valid yang di-rename `.php` — dapat diunggah ke `public/uploads/` dan dieksekusi (tidak ada `.htaccess` pemblokir di direktori uploads) => potensi Remote Code Execution.
- **Perbaikan:**
  - Validasi tipe file dengan `getimagesize()` sebelum diproses; hanya **JPG/PNG/WEBP** yang diterima.
  - Nama file di-generate ulang dengan ekstensi aman berdasar tipe hasil deteksi (bukan ekstensi klien) melalui `buildSafeName()`.
  - `create()`/`update()` kini mengembalikan error bila upload gagal (alih-alih menyimpan tanpa gambar).
  - `Settings::save()` mempertahankan gambar lama bila upload gagal.
- **File:**
  - `app/Controllers/Admin/Teachers.php`
  - `app/Controllers/Admin/Gallery.php`
  - `app/Controllers/Admin/News.php`
  - `app/Controllers/Admin/Settings.php`

### 1.3 Refactor — Deduplikasi `buildSafeName()`
- **Perubahan:** `buildSafeName()` (sebelumnya duplikat `private` di 4 controller) dipindah menjadi `protected` di `Admin/BaseController`. Tidak ada perubahan perilaku.
- **File:** `app/Controllers/Admin/BaseController.php`

---

## 2. Versi Aplikasi
- `app/Config/Constants.php`: `APP_VERSION` dinaikkan **v3.0.2 → v3.1.2**.

---

## 3. Dokumentasi
- `PRD.md`: diperbarui menjadi spesifikasi **as-built v3.1.2** —
  - Rebranding istilah **PPDB → SPMB** (`spmb_url`).
  - Struktur database diperbarui ke **16 tabel** (menambah `pages`, `menu_items`).
  - Riwayat rilis (§9) ditambahkan; tabel proyek/halaman publik, token desain, dan daftar istilah disinkronkan dengan implementasi aktual.
- `TODO.md`: dikosongkan (item fitur lama dihapus).

---

## 4. Skrip Deployment — `deploy-production.sh`
- **Step [1/8]:** `minify.php` dijalankan pada source **sebelum** rsync, sehingga file `style.min.css` / `script.min.js` ikut terdistribusi (sebelumnya minify dijalankan di target).
- **Step [5/8]:** Pembuatan database memakai `COLLATE utf8mb4_general_ci` untuk kompatibilitas MySQL legacy.
- **Step [6/8]:** Pada dump `sekolahku_db.sql`, kolasi `utf8mb4_0900_ai_ci` diganti menjadi `utf8mb4_general_ci` via `sed`.

---

## 5. Langkah Lanjutan (dikerjakan nanti)
- [ ] UAT end-to-end: upload foto/gambar di **Teachers, Gallery, News, Settings (about & principal)** — termasuk file non-gambar dan gambar `.php` yang harus ditolak.
- [ ] UAT **Upload Manager**: coba `rel_path` traversal (`../../etc/passwd`, `../config/...`) — harus ditolak.
- [ ] Verifikasi nama file hasil upload memakai ekstensi sesuai tipe (`.jpg/.png/.webp`).
- [ ] Tinjau dump DB & kolasi saat deploy produksi.
- [ ] Commit perubahan ini ke git (belum di-commit).
