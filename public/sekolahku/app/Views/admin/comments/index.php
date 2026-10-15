<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Komentar</h4>
        <p class="text-body-tertiary small mb-0">Kelola komentar pengunjung pada artikel berita</p>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table table-crud table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Berita</th>
                    <th>Komentator</th>
                    <th>Komentar</th>
                    <th style="width:110px">Tanggal</th>
                    <th style="width:100px">Status</th>
                    <th style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $i + 1 + (($pager->getCurrentPage() - 1) * 10) ?></span></td>
                    <td class="text-body-secondary"><?= esc(substr($item['news_title'] ?? $item['news_id'] ?? '', 0, 40)) . (strlen($item['news_title'] ?? '') > 40 ? '...' : '') ?></td>
                    <td class="fw-medium"><?= esc($item['name'] ?? $item['commenter_name'] ?? '-') ?></td>
                    <td class="text-body-secondary small"><?= esc(substr($item['comment'] ?? '', 0, 60)) . (strlen($item['comment'] ?? '') > 60 ? '...' : '') ?></td>
                    <td class="text-body-secondary small"><?= isset($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : '-' ?></td>
                    <td>
                        <?php if (!empty($item['is_approved'])): ?>
                            <span class="badge bg-success">Disetujui</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Menunggu</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <?php if (empty($item['is_approved'])): ?>
                                <form action="/admin/comments/approve/<?= $item['id'] ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-success border-0" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form action="/admin/comments/delete/<?= $item['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus komentar ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger border-0" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center py-5 text-body-tertiary">Tidak ada komentar ditemukan.</td></tr>
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
