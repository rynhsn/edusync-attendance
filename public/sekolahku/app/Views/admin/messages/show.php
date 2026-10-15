<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= esc($item['subject'] ?? '(Tanpa Subjek)') ?></h4>
        <p class="text-body-tertiary small mb-0">Detail pesan dari <?= esc($item['name']) ?></p>
    </div>
    <a href="/admin/messages" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <h6 class="small fw-semibold text-body-tertiary text-uppercase mb-3">
                    <i class="fas fa-comment-dots me-1"></i>Pesan
                </h6>
                <div class="p-3 bg-body-tertiary rounded-3" style="white-space:pre-wrap;line-height:1.7"><?= esc($item['message'] ?? '-') ?></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Pengirim</h6>
            </div>
            <div class="card-body pt-0">
                <dl class="mb-0">
                    <dt class="small fw-semibold text-body-tertiary mb-1">Nama</dt>
                    <dd class="mb-3"><?= esc($item['name']) ?></dd>
                    <dt class="small fw-semibold text-body-tertiary mb-1">Email</dt>
                    <dd class="mb-3">
                        <a href="mailto:<?= esc($item['email']) ?>"><?= esc($item['email']) ?></a>
                    </dd>
                    <dt class="small fw-semibold text-body-tertiary mb-1">Subjek</dt>
                    <dd class="mb-3"><?= esc($item['subject'] ?? '-') ?></dd>
                    <dt class="small fw-semibold text-body-tertiary mb-1">Diterima</dt>
                    <dd class="mb-3"><?= isset($item['created_at']) ? date('d M Y H:i', strtotime($item['created_at'])) : '-' ?></dd>
                    <dt class="small fw-semibold text-body-tertiary mb-1">Status</dt>
                    <dd>
                        <?php if (!empty($item['is_read'])): ?>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i>Sudah dibaca</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2"><i class="fas fa-envelope me-1"></i>Baru</span>
                        <?php endif; ?>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="/admin/messages" class="btn btn-outline-secondary rounded-pill btn-sm flex-grow-1">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <form action="/admin/messages/delete/<?= $item['id'] ?>" method="post" class="flex-grow-1">
                        <?= csrf_field() ?>
                        <button class="btn btn-outline-danger rounded-pill btn-sm w-100" onclick="return confirm('Yakin ingin menghapus pesan dari &quot;<?= esc($item['name']) ?>&quot;?')">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
