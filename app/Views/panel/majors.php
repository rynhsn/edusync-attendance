<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Data Jurusan</h1>
        <p class="text-muted mb-0">Daftar program keahlian yang tersimpan di sistem.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>
    <?= $this->include('panel/partials/master-data-import-export'); ?>

    <?php if (\App\Libraries\ActionAccess::can('major.manage')) : ?>
    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0"><?= $editMajor === null ? 'Tambah Jurusan' : 'Edit Jurusan'; ?></h2></div>
        <div class="card-body">
            <form method="post" action="<?= site_url('major/save'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="jurusan_id" value="<?= esc((string) ($editMajor['jurusan_id'] ?? '')); ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="majorName">Nama Jurusan</label>
                        <input class="form-control" id="majorName" name="jurusan_nama" maxlength="50" required value="<?= esc($editMajor['jurusan_nama'] ?? ''); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <label class="form-label" for="majorCode">Kode</label>
                        <input class="form-control" id="majorCode" name="jurusan_kode" maxlength="10" required value="<?= esc($editMajor['jurusan_kode'] ?? ''); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="majorDescription">Keterangan</label>
                        <textarea class="form-control" id="majorDescription" name="jurusan_keterangan" rows="1"><?= esc($editMajor['jurusan_keterangan'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit"><?= $editMajor === null ? 'Tambah Jurusan' : 'Simpan Perubahan'; ?></button>
                        <?php if ($editMajor !== null) : ?><a class="btn btn-outline-secondary" href="<?= site_url('major'); ?>">Batal</a><?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar Jurusan</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($majors); ?> jurusan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Nama Jurusan</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($majors as $index => $major) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($major['jurusan_nama'] ?? '-'); ?></td>
                            <td><?= esc($major['jurusan_kode'] ?? '-'); ?></td>
                            <td><?= esc($major['jurusan_keterangan'] ?: '-'); ?></td>
                            <td class="text-end text-nowrap">
                                <?php if (\App\Libraries\ActionAccess::can('major.manage')) : ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('major?edit=' . (int) $major['jurusan_id']); ?>">Ubah</a>
                                <?php endif; ?>
                                <?php if (\App\Libraries\ActionAccess::can('major.delete')) : ?>
                                <form class="d-inline" method="post" action="<?= site_url('major/delete/' . (int) $major['jurusan_id']); ?>" onsubmit="return confirm('Hapus jurusan ini?');">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($majors === []) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data jurusan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
