<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Notifikasi</h1>
        <p class="text-muted mb-0">Pendaftaran akun baru dan pendaftaran SPMB Online.</p>
    </div>

    <?php if (! $storageAvailable) : ?>
        <div class="alert alert-warning" role="alert">Penyimpanan notifikasi belum tersedia. Jalankan migrasi database aplikasi terlebih dahulu.</div>
    <?php endif; ?>
    <?php if (! empty($error = session()->getFlashdata('notificationError'))) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <?php if ($notifications === []) : ?>
        <div class="alert alert-info" role="status">Belum ada notifikasi.</div>
    <?php else : ?>
        <div class="list-group">
            <?php foreach ($notifications as $notification) : ?>
                <a class="list-group-item list-group-item-action d-flex align-items-start gap-3 <?= $notification['read_at'] === null ? 'border-start border-4 border-primary' : ''; ?>" href="<?= site_url('notifications/open/' . (int) $notification['id']); ?>">
                    <span class="fs-5 text-primary" aria-hidden="true">
                        <i class="bi <?= $notification['type'] === 'spmb_application' ? 'bi-mortarboard' : 'bi-person-plus'; ?>"></i>
                    </span>
                    <span class="flex-grow-1">
                        <span class="d-flex flex-wrap justify-content-between gap-2">
                            <strong data-no-translate><?= esc($notification['title']); ?></strong>
                            <small class="text-muted"><?= esc($notification['created_at']); ?></small>
                        </span>
                        <span class="d-block text-muted" data-no-translate><?= esc($notification['message']); ?></span>
                    </span>
                    <?php if ($notification['read_at'] === null) : ?><span class="badge text-bg-primary">Baru</span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
