<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<?php
$value = static fn (string $key, string $default = ''): string => (string) old(
    $key,
    $article[$key] ?? $default
);
?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <a href="<?= site_url('website-news'); ?>" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Kembali ke daftar berita</a>
        <h1 class="h3 mt-3 mb-1"><?= $article['id'] ? 'Edit Berita' : 'Tambah Berita'; ?></h1>
        <p class="text-muted mb-0">Berita yang berstatus Terbit akan muncul di website publik.</p>
    </div>

    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <form action="<?= site_url('website-news/save'); ?>" method="post">
        <?= csrf_field(); ?>
        <input type="hidden" name="id" value="<?= esc($value('id')); ?>">
        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 mb-0">Konten Berita</h2></div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="news-title" class="form-label">Judul</label>
                    <input id="news-title" name="title" class="form-control" maxlength="200" required value="<?= esc($value('title')); ?>">
                </div>
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <label for="news-slug" class="form-label">Slug URL (opsional)</label>
                        <input id="news-slug" name="slug" class="form-control" maxlength="220" pattern="[a-z0-9]+(-[a-z0-9]+)*" value="<?= esc($value('slug')); ?>" placeholder="dibuat-otomatis-dari-judul">
                        <div class="form-text">Gunakan huruf kecil, angka, dan tanda hubung. Dikosongkan untuk dibuat dari judul.</div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label for="news-category" class="form-label">Kategori</label>
                        <input id="news-category" name="category" class="form-control" maxlength="150" value="<?= esc($value('category')); ?>" placeholder="Kegiatan Sekolah">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="news-excerpt" class="form-label">Ringkasan</label>
                        <textarea id="news-excerpt" name="excerpt" class="form-control" rows="2" maxlength="1000"><?= esc($value('excerpt')); ?></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="news-content" class="form-label">Isi Berita</label>
                        <textarea id="news-content" name="content" class="form-control" rows="12" maxlength="50000" required><?= esc($value('content')); ?></textarea>
                        <div class="form-text">Teks biasa; baris baru akan dipertahankan pada halaman publik.</div>
                    </div>
                    <div class="col-lg-8 mb-3">
                        <label for="news-image" class="form-label">URL gambar (opsional)</label>
                        <input id="news-image" name="image" type="text" class="form-control" maxlength="500" value="<?= esc($value('image')); ?>" placeholder="https://... atau /images/berita.jpg">
                    </div>
                    <div class="col-lg-4 mb-3">
                        <label for="news-author" class="form-label">Penulis</label>
                        <input id="news-author" name="author" class="form-control" maxlength="100" value="<?= esc($value('author')); ?>">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label for="news-tags" class="form-label">Tag</label>
                        <input id="news-tags" name="tags" class="form-control" maxlength="2000" value="<?= esc(old('tags', $article['tags_input'] ?? '')); ?>" placeholder="kegiatan, prestasi">
                        <div class="form-text">Pisahkan setiap tag dengan koma.</div>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="news-status" class="form-label">Status</label>
                        <select id="news-status" name="status" class="form-select" required>
                            <option value="draft" <?= $value('status', 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?= $value('status', 'draft') === 'published' ? 'selected' : ''; ?>>Terbit</option>
                        </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="news-published-at" class="form-label">Tanggal publikasi</label>
                        <input id="news-published-at" name="published_at" type="datetime-local" class="form-control" value="<?= esc(old('published_at', $article['published_at_input'] ?? '')); ?>">
                    </div>
                </div>
                <div class="form-check">
                    <input type="hidden" name="featured" value="0">
                    <input id="news-featured" name="featured" type="checkbox" value="1" class="form-check-input" <?= (int) old('featured', $article['featured'] ?? 0) === 1 ? 'checked' : ''; ?>>
                    <label for="news-featured" class="form-check-label">Tandai sebagai berita unggulan</label>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <a href="<?= site_url('website-news'); ?>" class="btn btn-outline-secondary">Batal</a>
                <?php if (\App\Libraries\ActionAccess::can('website-news.manage')) : ?>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan Berita</button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection(); ?>
