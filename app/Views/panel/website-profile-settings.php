<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Profil Sekolah</h1>
        <p class="text-muted mb-0">Konten ini tampil pada bagian Profil di beranda website publik.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header"><h2 class="h5 card-title mb-0">Profil dan Visi Misi</h2></div>
        <form action="<?= site_url('website-settings/profile'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="profile-section-title" class="form-label">Label bagian profil</label>
                        <input id="profile-section-title" name="section_title" class="form-control" maxlength="150" required value="<?= esc(old('section_title', $section['title'] ?? 'Tentang Kami')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="profile-section-subtitle" class="form-label">Deskripsi singkat bagian</label>
                        <input id="profile-section-subtitle" name="section_subtitle" class="form-control" maxlength="500" value="<?= esc(old('section_subtitle', $section['subtitle'] ?? '')); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="profile-content-title" class="form-label">Judul profil</label>
                    <input id="profile-content-title" name="content_title" class="form-control" maxlength="200" required value="<?= esc(old('content_title', $about['content_title'] ?? '')); ?>">
                </div>
                <div class="mb-3">
                    <label for="profile-image" class="form-label">URL foto/gambar profil</label>
                    <input id="profile-image" name="image" class="form-control" maxlength="500" value="<?= esc(old('image', $about['image'] ?? '')); ?>">
                </div>
                <div class="mb-3">
                    <label for="profile-visi" class="form-label">Visi</label>
                    <textarea id="profile-visi" name="visi" class="form-control" rows="3" maxlength="2000"><?= esc(old('visi', $about['visi'] ?? '')); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="profile-misi" class="form-label">Misi</label>
                    <textarea id="profile-misi" name="misi" class="form-control" rows="4" maxlength="3000"><?= esc(old('misi', $about['misi'] ?? '')); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="profile-content-1" class="form-label">Paragraf profil 1</label>
                    <textarea id="profile-content-1" name="content_1" class="form-control" rows="3" maxlength="3000"><?= esc(old('content_1', $about['content_1'] ?? '')); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="profile-content-2" class="form-label">Paragraf profil 2</label>
                    <textarea id="profile-content-2" name="content_2" class="form-control" rows="3" maxlength="3000"><?= esc(old('content_2', $about['content_2'] ?? '')); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="profile-accreditation" class="form-label">Akreditasi</label>
                        <input id="profile-accreditation" name="accreditation" class="form-control" maxlength="20" value="<?= esc(old('accreditation', $about['accreditation'] ?? '')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="profile-accreditation-label" class="form-label">Keterangan akreditasi</label>
                        <input id="profile-accreditation-label" name="accreditation_label" class="form-control" maxlength="150" value="<?= esc(old('accreditation_label', $about['accreditation_label'] ?? 'Standar Nasional')); ?>">
                    </div>
                </div>
                <div class="mb-0">
                    <label for="profile-highlights" class="form-label">Keunggulan sekolah</label>
                    <textarea id="profile-highlights" name="highlights" class="form-control" rows="4" maxlength="2000"><?= esc(old('highlights', implode("\n", array_map(static fn ($item): string => is_array($item) ? (string) ($item['text'] ?? '') : (string) $item, $about['highlights'] ?? [])))); ?></textarea>
                    <div class="form-text">Satu keunggulan per baris.</div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <?php if (\App\Libraries\ActionAccess::can('website-profile-settings.save')) : ?>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan Profil</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
