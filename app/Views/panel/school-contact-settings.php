<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Kontak Sekolah</h1>
        <p class="text-muted mb-0">Informasi ini ditampilkan pada halaman Kontak website publik SekolahKu.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Informasi yang Ditampilkan di Halaman Kontak</h2>
        </div>
        <form action="<?= site_url('school-contact-settings'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="card-body">
                <div class="mb-3">
                    <label for="contact-address" class="form-label">Alamat Sekolah</label>
                    <textarea id="contact-address" name="address" class="form-control" rows="3" maxlength="2000" required><?= esc(old('address', $settings['address'])); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="contact-phone" class="form-label">Telepon</label>
                    <input id="contact-phone" name="phone" type="text" class="form-control" maxlength="100" value="<?= esc(old('phone', $settings['phone'])); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="contact-email" class="form-label">Email</label>
                    <input id="contact-email" name="email" type="email" class="form-control" maxlength="254" value="<?= esc(old('email', $settings['email'])); ?>" required>
                </div>
                <div class="mb-0">
                    <label for="contact-hours" class="form-label">Jam Kerja</label>
                    <input id="contact-hours" name="hours" type="text" class="form-control" maxlength="255" value="<?= esc(old('hours', $settings['hours'])); ?>" required>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <?php if (\App\Libraries\ActionAccess::can('school-contact-settings.save')) : ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
