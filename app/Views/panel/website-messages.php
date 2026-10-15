<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pesan Website</h1>
        <p class="text-muted mb-0">
            Pesan masuk: <?= count($messages); ?> ditampilkan,
            <?= number_format($unreadCount); ?> belum dibaca.
        </p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Pengirim</th>
                        <th>Subjek dan pesan</th>
                        <th>Diterima</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($messages === []) : ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">Belum ada pesan yang masuk dari website.</td></tr>
                <?php else : ?>
                    <?php foreach ($messages as $websiteMessage) : ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($websiteMessage['name']); ?></div>
                            <a href="mailto:<?= esc($websiteMessage['email']); ?>"><?= esc($websiteMessage['email']); ?></a>
                        </td>
                        <td>
                            <div class="fw-semibold"><?= esc($websiteMessage['subject'] ?: '(Tanpa subjek)'); ?></div>
                            <details class="mt-1">
                                <summary class="small text-primary">Baca pesan</summary>
                                <div class="mt-2 text-break" style="white-space: pre-wrap;"><?= esc($websiteMessage['message']); ?></div>
                            </details>
                        </td>
                        <td class="text-nowrap"><?= esc($websiteMessage['created_at'] ?? '-'); ?></td>
                        <td>
                            <span class="badge <?= (int) $websiteMessage['is_read'] === 1 ? 'text-bg-secondary' : 'text-bg-primary'; ?>">
                                <?= (int) $websiteMessage['is_read'] === 1 ? 'Dibaca' : 'Baru'; ?>
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <?php if ((int) $websiteMessage['is_read'] !== 1) : ?>
                            <form action="<?= site_url('website-messages/read/' . $websiteMessage['id']); ?>" method="post">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Tandai dibaca</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager->getPageCount() > 1) : ?>
        <div class="card-body"><?= $pager->links(); ?></div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection(); ?>
