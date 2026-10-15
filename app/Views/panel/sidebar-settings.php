<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Akses Menu Role</h1>
        <p class="text-muted mb-0">Super Admin dapat memilih menu yang boleh diakses oleh setiap role.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="alert alert-info" role="note">
        Menu yang dicentang akan tampil di sidebar dan dapat dibuka role tersebut. Operasi perubahan data tetap dibatasi untuk Super Admin.
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= site_url('sidebar-settings'); ?>" class="row g-3 align-items-end">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="sidebarRole">Pilih Role</label>
                    <select class="form-select" id="sidebarRole" name="role" required>
                        <?php foreach ($roles as $role => $roleLabel) : ?>
                            <option value="<?= esc($role); ?>" <?= $selectedRole === $role ? 'selected' : ''; ?>><?= esc($roleLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>Tampilkan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <h2 class="h5 card-title mb-0">Menu untuk <?= esc($roles[$selectedRole]); ?></h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($menuItems); ?> menu</span>
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('sidebar-settings'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="role" value="<?= esc($selectedRole); ?>">
                <?php foreach ($menuSections as $sectionLabel => $sectionItems) : ?>
                    <section class="mb-4" aria-labelledby="menu-section-<?= esc(url_title($sectionLabel, '-', true)); ?>">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h3 class="h6 fw-bold mb-0" id="menu-section-<?= esc(url_title($sectionLabel, '-', true)); ?>"><?= esc($sectionLabel); ?></h3>
                            <span class="text-muted small"><?= count($sectionItems); ?> menu</span>
                        </div>
                        <div class="row g-3">
                            <?php foreach ($sectionItems as $key => $label) : ?>
                                <?php
                                $checked = (bool) ($settings[$key] ?? false);
                                $inputId = 'sidebar_' . $key;
                                ?>
                                <div class="col-md-6 col-xl-4">
                                    <label class="panel-checkbox-card h-100" for="<?= esc($inputId); ?>">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="<?= esc($inputId); ?>"
                                            name="menus[<?= esc($key); ?>]"
                                            value="1"
                                            <?= $checked ? 'checked' : ''; ?>>
                                        <span class="fw-semibold"><?= esc($label); ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
                <div class="mt-4 d-flex flex-wrap align-items-center gap-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Pengaturan
                    </button>
                    <span class="text-muted small">Dashboard selalu bisa dibuka meskipun disembunyikan dari sidebar.</span>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
