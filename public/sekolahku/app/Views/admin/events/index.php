<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Agenda</h4>
        <p class="text-body-tertiary small mb-0">Kelola agenda dan jadwal sekolah</p>
    </div>
    <a href="/admin/events/new" class="btn btn-primary rounded-pill px-3">
        <i class="fas fa-plus me-1"></i>Tambah Agenda
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table table-crud table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Judul</th>
                    <th style="width:110px">Tanggal</th>
                    <th style="width:90px">Waktu</th>
                    <th>Lokasi</th>
                    <th style="width:100px">Status</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $i + 1 + (($pager->getCurrentPage() - 1) * 10) ?></span></td>
                    <td class="fw-medium"><?= esc($item['title']) ?></td>
                    <td class="text-body-secondary small"><?= $item['event_date'] ? date('d M Y', strtotime($item['event_date'])) : '-' ?></td>
                    <td class="text-body-secondary small"><?= esc($item['event_time'] ?? '-') ?></td>
                    <td class="text-body-secondary small"><?= esc($item['location'] ?? '-') ?></td>
                    <td>
                        <?php if (($item['status'] ?? '') === 'upcoming'): ?>
                            <span class="badge bg-primary">Akan Datang</span>
                        <?php elseif (($item['status'] ?? '') === 'ongoing'): ?>
                            <span class="badge bg-success">Berlangsung</span>
                        <?php elseif (($item['status'] ?? '') === 'completed'): ?>
                            <span class="badge bg-secondary">Selesai</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= esc($item['status'] ?? 'draft') ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/admin/events/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger border-0" title="Delete" data-coreui-toggle="modal" data-coreui-target="#deleteModal<?= $item['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="modal fade" id="deleteModal<?= $item['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="modal-title">Konfirmasi Hapus</h6>
                                        <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus "<strong><?= esc($item['title']) ?></strong>"?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <a href="/admin/events/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center py-5 text-body-tertiary">Tidak ada agenda ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager->links()): ?>
    <div class="d-flex justify-content-center py-3 border-top">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
