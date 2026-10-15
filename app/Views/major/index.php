<!-- Tampilan untuk jurusan -->
<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">


    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Master Data /</span> Jurusan</h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-jurusan table">
                <thead>
                    <tr>
                        <!-- <th>id</th> -->
                        <th>Jurusan</th>
                        <th>Kode</th>
                        <th>Keterangan</th>
                        <th>Dibuat</th>
                        <th>Diubah</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="maintain-jurusan">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Tambah Data Jurusan</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="maintain-jurusan pt-0 row g-2" id="form-maintain-jurusan" onsubmit="return false">
                <div class="col-sm-12">
                    <label class="form-label" for="jurusan">Jurusan</label>
                    <div class="input-group input-group-merge">
                        <span id="jurusan2" class="input-group-text"><i class="ti ti-bookmarks"></i></span>
                        <input
                            type="text"
                            id="jurusan"
                            class="form-control dt-jurusan"
                            name="nama_jurusan"
                            placeholder="Jurusan"
                            aria-label="Jurusan"
                            aria-describedby="jurusan2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label mt-2" for="kode">Kode</label>
                    <div class="input-group input-group-merge">
                        <span id="kode2" class="input-group-text"><i class="ti ti-qrcode"></i></span>
                        <input
                            type="text"
                            id="kode"
                            class="form-control dt-kode"
                            name="kode"
                            placeholder="Kode"
                            aria-label="Kode"
                            aria-describedby="kode2"
                            required />
                    </div>
                </div>
                <div class="col-sm-12">
                    <label class="form-label mt-2" for="keterangan">Keterangan</label>
                    <div class="input-group input-group-merge">
                        <span id="keterangan2" class="input-group-text"><i class="ti ti-info-square-rounded"></i></span>
                        <textarea
                            id="keterangan"
                            class="form-control dt-keterangan"
                            name="keterangan"
                            placeholder="Keterangan"
                            aria-label="Keterangan"
                            aria-describedby="keterangan2"
                            required></textarea>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                </div>
            </form>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>