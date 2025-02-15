<!-- tampilan untuk tabel siswa -->
<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Absensi /</span> Absensi Sekolah</h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-kelas table">
                <thead>
                    <tr>
                        <!-- <th>id</th> -->
                        <th>Nama Siswa</th>
                        <th>Tanggal</th>
                        <th class="text-center">Masuk</th>
                        <th class="text-center">Pulang</th>
                        <th class="text-center">Kehadiran</th>
                        <th>Keterangan</th>
                        <th>Dibuat</th>
                        <th>Diubah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $row) : ?>
                        <tr>
                            <td><?= $row['nama_siswa']; ?></td>
                            <td><?= $row['tanggal']; ?></td>
                            <td class="text-center"><span class="text-success"><?= $row['waktu_masuk_aktual']; ?></span><br><span class="badge text-bg-<?= $row['status_masuk_warna']; ?>"><?= $row['status_masuk']; ?></span></td>
                            <td class="text-center"><span class="text-danger"><?= $row['waktu_pulang_aktual']; ?></span><br><span class="badge text-bg-<?= $row['status_pulang_warna']; ?>"><?= $row['status_pulang']; ?></span></td>
                            <td class="text-center"><span class="badge text-bg-<?= $row['status_kehadiran_warna']; ?>"><?= $row['status_kehadiran']; ?></span></td>
                            <td><?= $row['keterangan']; ?></td>
                            <td><?= $row['created_at']; ?></td>
                            <td><?= $row['updated_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal to add new record -->
    <div class=" offcanvas offcanvas-end" id="maintain-kelas">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Absensi Manual</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="maintain-kelas pt-0 row g-2" id="form-maintain-kelas" onsubmit="return false">
                <div class="col-sm-12">
                    <label class="form-label" for="tingkat">Tingkat</label>
                    <div class="input-group input-group-merge">
                        <span id="tingkat2" class="input-group-text"><i class="ti ti-briefcase"></i></span>
                        <input
                            type="text"
                            id="tingkat"
                            name="tingkat"
                            class="form-control dt-tingkat"
                            placeholder="Tingkat"
                            aria-label="Tingkat"
                            aria-describedby="tingkat2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="namaKelas">Nama Kelas</label>
                    <div class="input-group input-group-merge">
                        <span id="namaKelas2" class="input-group-text"><i class="ti ti-door"></i></span>
                        <input
                            type="text"
                            id="namaKelas"
                            class="form-control dt-nama-kelas"
                            name="nama_kelas"
                            placeholder="Nama Kelas"
                            aria-label="Nama Kelas"
                            aria-describedby="namaKelas2"
                            required />
                    </div>
                </div>
                <!-- //keterangan, textarea-->
                <div class="col-sm-12">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <div class="input-group input-group-merge">
                        <span id="keterangan2" class="input-group-text"><i class="ti ti-info-square-rounded"></i></span>
                        <textarea
                            type="text"
                            id="keterangan"
                            name="keterangan"
                            class="form-control dt-keterangan"
                            placeholder="Keterangan"
                            aria-label="Keterangan"
                            aria-describedby="keterangan2"
                            required></textarea>
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