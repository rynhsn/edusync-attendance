<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Upload Manager</h4>
        <p class="text-body-tertiary small mb-0">Kelola semua file upload</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body border-bottom bg-light py-3">
        <form method="get" action="/admin/uploads" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small mb-1">Filter Direktori</label>
                <select name="directory" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Direktori</option>
                    <?php foreach ($directories as $dir): ?>
                        <option value="<?= $dir ?>" <?= $filterDir === $dir ? 'selected' : '' ?>><?= ucfirst($dir) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($filterDir): ?>
            <div class="col-auto">
                <a href="/admin/uploads" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-crud table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th style="width:80px">Pratinjau</th>
                    <th>Nama File</th>
                    <th>Direktori</th>
                    <th style="width:100px">Ukuran</th>
                    <th style="width:160px">Diubah</th>
                    <th style="width:80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $file): ?>
                <tr>
                    <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $i + 1 + (($currentPage - 1) * $perPage) ?></span></td>
                    <td>
                        <?php if ($file['type'] === 'image'): ?>
                            <img src="/uploads/<?= $file['dir'] . '/' . $file['name'] ?>" alt="<?= esc($file['name']) ?>" class="rounded" style="width:60px;height:40px;object-fit:cover">
                        <?php else: ?>
                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-body-tertiary" style="width:60px;height:40px">
                                <i class="fas fa-file text-body-tertiary"></i>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="fw-medium small"><?= esc($file['name']) ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><?= ucfirst($file['dir']) ?></span></td>
                    <td class="small"><?= number_format($file['size'] / 1024, 1) ?> KB</td>
                    <td class="small"><?= date('d M Y H:i', $file['modified']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger border-0" title="Hapus" data-coreui-toggle="modal" data-coreui-target="#deleteModal<?= $i ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                        <div class="modal fade" id="deleteModal<?= $i ?>" tabindex="-1">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="modal-title">Konfirmasi Hapus</h6>
                                        <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus file <strong><?= esc($file['name']) ?></strong>?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <form action="/admin/uploads/delete" method="post" class="d-inline">
                                            <input type="hidden" name="rel_path" value="<?= $file['rel_path'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center py-5 text-body-tertiary">Tidak ada file ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager): ?>
    <div class="d-flex justify-content-center py-3 border-top">
        <?= $pager ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
