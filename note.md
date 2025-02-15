<!-- hal yang belum dilakukan -->

## Menu Kelas:

- Fitur Edit dan Delete belum ada


## alur logic absensi:
1. Cek apakah ada rfid_tag tidak kosong? 
    - Jika ya, maka lanjut ke tahap 2
    - Jika tidak, maka
        > return "rfid_tag kosong"
2. Cek apakah ada data siswa dengan rfid_tag?
    - Jika ya, maka lanjut ke tahap 3
    - Jika tidak, maka
        > return "data siswa tidak ditemukan"
3. Cek apakah siswa sudah absen hari ini?