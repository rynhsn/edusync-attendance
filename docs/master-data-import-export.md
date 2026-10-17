# Import dan Export Data Master

Menu **Data Jurusan**, **Data Kelas**, **Data Mapel**, **Data Guru**,
**Data Orang Tua**, **Data Jam Pelajaran**, dan **Data Tahun Ajaran** menyediakan
unduh template, import, serta export Excel.
Template dan export menggunakan `.xlsx`; import juga menerima CSV dan TSV.
Ukuran file maksimal 5 MB dengan batas 3.000 baris per impor.

Unduh template dari halaman data yang akan diimpor. Header wajib dipertahankan
dengan urutan berikut:

| Data | Header |
| --- | --- |
| Jurusan | `jurusan_nama`, `jurusan_kode`, `jurusan_keterangan` |
| Kelas | `kelas_tingkat`, `kelas_nama`, `kelas_keterangan` |
| Mapel | `mapel_kode`, `mapel_nama`, `kelompok_rapor`, `mapel_keterangan` |
| Guru | `guru_nip`, `guru_nama`, `guru_email`, `guru_telepon` |
| Orang tua | `nama_orang_tua`, `no_whatsapp`, `nama_siswa` |
| Jam pelajaran | `jam_ke`, `jam_nama`, `jam_mulai`, `jam_selesai` |
| Tahun ajaran | `tahun_ajaran_nama`, `semester`, `tanggal_mulai`, `tanggal_selesai`, `is_active` |

Baris bertanda `[CONTOH]` otomatis dilewati. Baris data lain diperiksa sebelum
disimpan; jika ada kesalahan, tidak ada baris dari file tersebut yang
disimpan. Nilai unik yang sudah ada—seperti kode jurusan/mapel, NIP, urutan
jam, dan kombinasi tingkat + nama kelas—tidak dapat diimpor kembali. Impor
kelas juga membuat pengaturan waktu default Senin–Sabtu seperti penambahan
kelas melalui formulir. Impor guru menyinkronkan akun Data User seperti
penambahan guru melalui formulir. Untuk data orang tua, isi `nama_siswa`
dengan nama siswa yang tepat; jika lebih dari satu, pisahkan dengan tanda
`|` (contoh: `Ayu Lestari | Bima Lestari`). Nama yang tidak ada atau tidak
unik akan ditolak agar siswa tidak ditautkan ke data yang salah. Siswa yang
diimpor akan ditautkan ke kontak orang tua tersebut. Untuk tahun ajaran,
semester harus `1` atau `2`, tanggal memakai `YYYY-MM-DD` atau dikosongkan,
dan `is_active` adalah `0` atau `1`. Aktifkan paling banyak satu tahun ajaran
dalam setiap file.
