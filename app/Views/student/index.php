<!-- tampilan untuk tabel siswa -->
<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master Data /</span> Siswa</h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-siswa table text-nowrap">
                <thead>
                    <tr>
                        <th></th>
                        <th>Aksi</th>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Jenis Kelamin</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Alamat</th>
                        <th>RFID Tag</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="maintain-siswa">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Tambah Data Siswa</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="maintain-siswa pt-0 row g-2" id="form-maintain-siswa" onsubmit="return false">
                <div class="col-sm-12">
                    <label class="form-label" for="namaSiswa">Nama Siswa</label>
                    <div class="input-group input-group-merge">
                        <span id="namaSiswa2" class="input-group-text"><i class="ti ti-user"></i></span>
                        <input
                            type="text"
                            id="namaSiswa"
                            name="nama_siswa"
                            class="form-control dt-nama-siswa"
                            placeholder="Nama Siswa"
                            aria-label="Nama Siswa"
                            aria-describedby="namaSiswa2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="nis">NIS</label>
                    <div class="input-group input-group-merge">
                        <span id="nis2" class="input-group-text"><i class="ti ti-id"></i></span>
                        <input
                            type="text"
                            id="nis"
                            class="form-control dt-nis"
                            name="nis"
                            placeholder="NIS"
                            aria-label="NIS"
                            aria-describedby="nis2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="nisn">NISN</label>
                    <div class="input-group input-group-merge">
                        <span id="nisn2" class="input-group-text"><i class="ti ti-id"></i></span>
                        <input
                            type="text"
                            id="nisn"
                            class="form-control dt-nisn"
                            name="nisn"
                            placeholder="NISN"
                            aria-label="NISN"
                            aria-describedby="nisn2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="jenisKelamin">Jenis Kelamin</label>
                    <div class="input-group input-group-merge">
                        <span id="jenisKelamin2" class="input-group-text"><i class="ti ti-gender-bigender"></i></span>
                        <select id="jenisKelamin" name="jenis_kelamin" class="form-control form-select dt-jenis-kelamin" required>
                            <option value="" disabled>Pilih Jenis Kelamin</option>
                            <option value="l">Laki-laki</option>
                            <option value="p">Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="tempatLahir">Tempat Lahir</label>
                    <div class="input-group input-group-merge">
                        <span id="tempatLahir2" class="input-group-text"><i class="ti ti-map-pin"></i></span>
                        <input
                            type="text"
                            id="tempatLahir"
                            class="form-control dt-tempat-lahir"
                            name="tempat_lahir"
                            placeholder="Tempat Lahir"
                            aria-label="Tempat Lahir"
                            aria-describedby="tempatLahir2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="tanggalLahir">Tanggal Lahir</label>
                    <div class="input-group input-group-merge">
                        <span id="tanggalLahir2" class="input-group-text"><i class="ti ti-calendar"></i></span>
                        <input
                            type="date"
                            id="tanggalLahir"
                            class="form-control dt-tanggal-lahir"
                            name="tanggal_lahir"
                            placeholder="Tanggal Lahir"
                            aria-label="Tanggal Lahir"
                            aria-describedby="tanggalLahir2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="alamat">Alamat</label>
                    <div class="input-group input-group-merge">
                        <span id="alamat2" class="input-group-text"><i class="ti ti-home"></i></span>
                        <textarea
                            id="alamat"
                            name="alamat"
                            class="form-control dt-alamat"
                            placeholder="Alamat"
                            aria-label="Alamat"
                            aria-describedby="alamat2"
                            required></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="kelas">Kelas</label>
                    <div class="input-group input-group-merge">
                        <span id="kelas2" class="input-group-text"><i class="ti ti-door"></i></span>
                        <select id="kelas" name="kelas_id" class="form-control form-select dt-kelas" required>
                            <option value="" disabled>Pilih Kelas</option>
                            <?php foreach ($classes as $c) : ?>
                                <option value="<?= $c['kelas_id']; ?>"><?= $c['kelas_tingkat']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="rfidTag">RFID Tag</label>
                    <div class="input-group input-group-merge">
                        <span id="rfidTag2" class="input-group-text"><i class="ti ti-tag"></i></span>
                        <input
                            type="text"
                            id="rfidTag"
                            class="form-control dt-rfid-tag"
                            name="rfid_tag"
                            placeholder="RFID Tag"
                            aria-label="RFID Tag"
                            aria-describedby="rfidTag2"
                            required />
                    </div>
                </div>
                <!-- Tambahkan field lain sesuai kebutuhan -->
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Save</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal to add new record -->

    <!--/ DataTable with Buttons -->

</div>
<!-- / Content -->

<?= $this->endSection(); ?>