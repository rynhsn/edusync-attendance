<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Beranda</h1>
        <p class="text-muted mb-0">Atur sambutan, tombol, statistik, dan sambutan kepala sekolah pada halaman utama website.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <form action="<?= site_url('website-settings/home'); ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 card-title mb-0">Sambutan Utama</h2></div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="hero-badge" class="form-label">Label kecil</label>
                    <input id="hero-badge" name="hero_badge" class="form-control" maxlength="150" value="<?= esc(old('hero_badge', $settings['hero_badge'])); ?>">
                </div>
                <div class="mb-3">
                    <label for="hero-title" class="form-label">Judul beranda</label>
                    <input id="hero-title" name="hero_title" class="form-control" maxlength="200" required value="<?= esc(old('hero_title', $settings['hero_title'])); ?>">
                </div>
                <div class="mb-3">
                    <label for="hero-subtitle" class="form-label">Deskripsi</label>
                    <textarea id="hero-subtitle" name="hero_subtitle" class="form-control" rows="3" maxlength="500" required><?= esc(old('hero_subtitle', $settings['hero_subtitle'])); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="primary-text" class="form-label">Tombol utama</label>
                        <input id="primary-text" name="hero_btn_primary_text" class="form-control" maxlength="80" value="<?= esc(old('hero_btn_primary_text', $settings['hero_btn_primary_text'])); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="primary-url" class="form-label">Tujuan tombol utama</label>
                        <input id="primary-url" name="hero_btn_primary_url" class="form-control" maxlength="255" value="<?= esc(old('hero_btn_primary_url', $settings['hero_btn_primary_url'])); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="secondary-text" class="form-label">Tombol kedua</label>
                        <input id="secondary-text" name="hero_btn_secondary_text" class="form-control" maxlength="80" value="<?= esc(old('hero_btn_secondary_text', $settings['hero_btn_secondary_text'])); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="secondary-url" class="form-label">Tujuan tombol kedua</label>
                        <input id="secondary-url" name="hero_btn_secondary_url" class="form-control" maxlength="255" value="<?= esc(old('hero_btn_secondary_url', $settings['hero_btn_secondary_url'])); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 card-title mb-0">Statistik pada Sambutan</h2></div>
            <div class="card-body">
                <label for="hero-stats" class="form-label">Satu statistik per baris: nilai|label</label>
                <textarea id="hero-stats" name="hero_stats" class="form-control font-monospace" rows="4" maxlength="2000"><?= esc(old('hero_stats', $heroStatsText)); ?></textarea>
                <div class="form-text">Contoh: 1200+|Siswa. Kosongkan semua baris bila tidak ingin menampilkan statistik.</div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 card-title mb-0">Statistik Sekolah</h2></div>
            <div class="card-body">
                <label for="counter-stats" class="form-label">Satu data per baris: angka|akhiran|label|ikon Font Awesome</label>
                <textarea id="counter-stats" name="counter_stats" class="form-control font-monospace" rows="5" maxlength="3000"><?= esc(old('counter_stats', $counterStatsText)); ?></textarea>
                <div class="form-text">Contoh: 40|+|Rombongan Belajar|layer-group. Gunakan nama ikon Font Awesome tanpa awalan <code>fa-</code>.</div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 card-title mb-0">Sambutan Kepala Sekolah SMP dan SMK</h2></div>
            <div class="card-body">
                <div class="row g-4">
                    <?php foreach (['smp' => 'SMP', 'smk' => 'SMK'] as $level => $levelLabel) : ?>
                        <?php
                        $principalItem = $principal[$level] ?? [];
                        $principalPhoto = (string) ($principalItem['photo'] ?? '');
                        $principalPhotoUrl = $principalPhoto === ''
                            ? base_url('sekolahku/public/images/default-principal.jpg')
                            : (preg_match('#^https?://#i', $principalPhoto) === 1
                                ? $principalPhoto
                                : base_url(ltrim($principalPhoto, '/')));
                        ?>
                        <div class="col-lg-6">
                            <section class="border rounded p-3 h-100" aria-labelledby="principal-<?= esc($level); ?>-heading">
                                <h3 class="h6 fw-bold mb-3" id="principal-<?= esc($level); ?>-heading">Kepala Sekolah <?= esc($levelLabel); ?></h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="principal-<?= esc($level); ?>-name" class="form-label">Nama</label>
                                        <input id="principal-<?= esc($level); ?>-name" name="principal_<?= esc($level); ?>_name" class="form-control" maxlength="200" value="<?= esc(old('principal_' . $level . '_name', $principalItem['name'] ?? '')); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="principal-<?= esc($level); ?>-role" class="form-label">Jabatan</label>
                                        <input id="principal-<?= esc($level); ?>-role" name="principal_<?= esc($level); ?>_role" class="form-control" maxlength="150" value="<?= esc(old('principal_' . $level . '_role', $principalItem['role'] ?? 'Kepala Sekolah ' . $levelLabel)); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="principal-<?= esc($level); ?>-quote" class="form-label">Kata sambutan</label>
                                    <textarea id="principal-<?= esc($level); ?>-quote" name="principal_<?= esc($level); ?>_quote" class="form-control" rows="3" maxlength="1000"><?= esc(old('principal_' . $level . '_quote', $principalItem['quote'] ?? '')); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <img src="<?= esc($principalPhotoUrl); ?>" alt="Foto saat ini Kepala Sekolah <?= esc($levelLabel); ?>" class="img-thumbnail mb-2" style="width: 120px; height: 120px; object-fit: cover;">
                                    <label for="principal-<?= esc($level); ?>-photo" class="form-label d-block">Upload foto kepala sekolah <?= esc($levelLabel); ?></label>
                                    <input id="principal-<?= esc($level); ?>-photo" name="principal_<?= esc($level); ?>_photo" class="form-control" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    <div class="form-text">JPG, PNG, atau WebP; maksimal 5 MB. Kosongkan jika tidak ingin mengganti foto.</div>
                                </div>
                            </section>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <?php if (\App\Libraries\ActionAccess::can('website-home-settings.save')) : ?>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan Pengaturan Beranda</button>
            <?php endif; ?>
        </div>
    </form>
</div>
<?= $this->endSection(); ?>
