<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Data Kelas</h1>
        <p class="text-muted mb-0">Daftar kelas yang tersimpan di sistem.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <?php if (\App\Libraries\ActionAccess::can('class.manage')) : ?>
    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0"><?= $editClass === null ? 'Tambah Kelas' : 'Edit Kelas'; ?></h2></div>
        <div class="card-body">
            <form method="post" action="<?= site_url('class/save'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="kelas_id" value="<?= esc((string) ($editClass['kelas_id'] ?? '')); ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="classGrade">Tingkat</label>
                        <input class="form-control" id="classGrade" name="kelas_tingkat" type="number" min="1" max="12" required value="<?= esc((string) ($editClass['kelas_tingkat'] ?? '')); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="col-md-4">
                        <label class="form-label" for="className">Nama Kelas</label>
                        <input class="form-control" id="className" name="kelas_nama" maxlength="50" required value="<?= esc($editClass['kelas_nama'] ?? ''); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="classDescription">Keterangan</label>
                        <textarea class="form-control" id="classDescription" name="kelas_keterangan" rows="1"><?= esc($editClass['kelas_keterangan'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button class="btn btn-primary" type="submit"><?= $editClass === null ? 'Tambah Kelas' : 'Simpan Perubahan'; ?></button>
                        <?php if ($editClass !== null) : ?><a class="btn btn-outline-secondary" href="<?= site_url('class'); ?>">Batal</a><?php endif; ?>
                    </div>
                </div>
                <?php if ($editClass === null) : ?><p class="form-text mb-0 mt-3">Jadwal waktu default akan dibuat otomatis untuk kelas baru.</p><?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar Kelas</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($classes); ?> kelas</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Tingkat</th>
                        <th scope="col">Nama Kelas</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $index => $class) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($class['kelas_tingkat'] ?? '-'); ?></td>
                            <td><?= esc($class['kelas_nama'] ?? '-'); ?></td>
                            <td><?= esc($class['kelas_keterangan'] ?: '-'); ?></td>
                            <td class="text-end text-nowrap">
                                <?php if (\App\Libraries\ActionAccess::can('class.manage')) : ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('class?edit=' . (int) $class['kelas_id']); ?>">Ubah</a>
                                <?php endif; ?>
                                <?php if (\App\Libraries\ActionAccess::can('class.delete')) : ?>
                                <form class="d-inline" method="post" action="<?= site_url('class/delete/' . (int) $class['kelas_id']); ?>" onsubmit="return confirm('Hapus kelas ini?');">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($classes === []) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data kelas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
