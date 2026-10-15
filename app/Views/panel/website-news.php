<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola News</h1>
            <p class="text-muted mb-0">Berita website publik disimpan di database EduSync.</p>
        </div>
        <?php if (\App\Libraries\ActionAccess::can('website-news.manage')) : ?>
            <a href="<?= site_url('website-news/new'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Berita
            </a>
        <?php endif; ?>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Publikasi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($news === []) : ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">Belum ada berita. Tambahkan berita pertama untuk ditampilkan di website.</td></tr>
                <?php else : ?>
                    <?php foreach ($news as $article) : ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= esc($article['title']); ?></div>
                            <div class="small text-muted">/news/news/<?= esc($article['slug']); ?></div>
                        </td>
                        <td><?= esc($article['category'] ?: '-'); ?></td>
                        <td>
                            <span class="badge <?= $article['status'] === 'published' ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                                <?= $article['status'] === 'published' ? 'Terbit' : 'Draft'; ?>
                            </span>
                        </td>
                        <td><?= esc($article['published_at'] ?: '-'); ?></td>
                        <td class="text-end text-nowrap">
                            <?php if ($article['status'] === 'published') : ?>
                            <a href="<?= site_url('news/news/' . rawurlencode($article['slug'])); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" aria-label="Lihat berita">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (\App\Libraries\ActionAccess::can('website-news.manage')) : ?>
                                <a href="<?= site_url('website-news/edit/' . $article['id']); ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <?php endif; ?>
                            <?php if (\App\Libraries\ActionAccess::can('website-news.delete')) : ?>
                            <form action="<?= site_url('website-news/delete/' . $article['id']); ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus berita ini?')">
                                <?= csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
