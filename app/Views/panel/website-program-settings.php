<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Program Sekolah</h1>
        <p class="text-muted mb-0">Atur bagian Program dan daftar kartu program yang tampil pada beranda publik.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 card-title mb-0">Judul Bagian Program</h2></div>
        <form action="<?= site_url('website-settings/programs/section'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="program-section-title" class="form-label">Judul</label>
                        <input id="program-section-title" name="section_title" class="form-control" maxlength="150" required value="<?= esc(old('section_title', $section['title'] ?? 'Program Unggulan')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="program-section-subtitle" class="form-label">Deskripsi</label>
                        <input id="program-section-subtitle" name="section_subtitle" class="form-control" maxlength="500" value="<?= esc(old('section_subtitle', $section['subtitle'] ?? '')); ?>">
                    </div>
                </div>
                <div class="form-check">
                    <input type="hidden" name="show_programs" value="0">
                    <input id="show-programs" type="checkbox" name="show_programs" value="1" class="form-check-input" <?= (int) ($section['show'] ?? 1) === 1 ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="show-programs">Tampilkan bagian program di website</label>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <?php if (\App\Libraries\ActionAccess::can('website-programs.section')) : ?>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan Bagian</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h4 mb-0">Daftar Program</h2>
        <span class="text-muted small">Program yang dinonaktifkan tidak tampil di website publik.</span>
    </div>

    <?php foreach ($programs as $program) : ?>
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="h6 mb-0"><?= esc($program['title']); ?></h3>
                <span class="badge <?= (int) $program['show'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>">
                    <?= (int) $program['show'] === 1 ? 'Tampil' : 'Disembunyikan'; ?>
                </span>
            </div>
            <form action="<?= site_url('website-settings/programs/save'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($program['id']); ?>">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="program-title-<?= esc($program['id']); ?>" class="form-label">Nama program</label>
                            <input id="program-title-<?= esc($program['id']); ?>" name="title" class="form-control" maxlength="200" required value="<?= esc($program['title']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="program-icon-<?= esc($program['id']); ?>" class="form-label">Kelas ikon</label>
                            <input id="program-icon-<?= esc($program['id']); ?>" name="icon" class="form-control" maxlength="100" value="<?= esc($program['icon'] ?? 'fas fa-book'); ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="program-description-<?= esc($program['id']); ?>" class="form-label">Deskripsi</label>
                            <textarea id="program-description-<?= esc($program['id']); ?>" name="description" class="form-control" rows="3" maxlength="3000"><?= esc($program['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="program-url-<?= esc($program['id']); ?>" class="form-label">URL tombol (opsional)</label>
                            <input id="program-url-<?= esc($program['id']); ?>" name="link_url" class="form-control" maxlength="255" value="<?= esc($program['link_url'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="program-link-text-<?= esc($program['id']); ?>" class="form-label">Teks tombol</label>
                            <input id="program-link-text-<?= esc($program['id']); ?>" name="link_text" class="form-control" maxlength="100" value="<?= esc($program['link_text'] ?? 'Selengkapnya'); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="program-order-<?= esc($program['id']); ?>" class="form-label">Urutan</label>
                            <input id="program-order-<?= esc($program['id']); ?>" name="sort_order" type="number" min="0" max="255" class="form-control" required value="<?= esc($program['sort_order']); ?>">
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="show" value="1" class="form-check-input" id="program-show-<?= esc($program['id']); ?>" <?= (int) $program['show'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="program-show-<?= esc($program['id']); ?>">Tampilkan program di website publik</label>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <?php if (\App\Libraries\ActionAccess::can('website-programs.delete')) : ?>
                        <button type="submit" formaction="<?= site_url('website-settings/programs/delete/' . $program['id']); ?>" formmethod="post" formnovalidate class="btn btn-outline-danger" onclick="return confirm('Hapus program ini?')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (\App\Libraries\ActionAccess::can('website-programs.manage')) : ?>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Simpan Program</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php endforeach; ?>

    <?php if (\App\Libraries\ActionAccess::can('website-programs.manage')) : ?>
    <div class="card mt-4">
        <div class="card-header"><h3 class="h5 card-title mb-0">Tambah Program</h3></div>
        <form action="<?= site_url('website-settings/programs/save'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new-program-title" class="form-label">Nama program</label>
                        <input id="new-program-title" name="title" class="form-control" maxlength="200" required>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 mb-3">
                        <label for="new-program-icon" class="form-label">Kelas ikon</label>
                        <input id="new-program-icon" name="icon" class="form-control" maxlength="100" placeholder="fas fa-book">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="new-program-description" class="form-label">Deskripsi</label>
                        <textarea id="new-program-description" name="description" class="form-control" rows="3" maxlength="3000"></textarea>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="new-program-url" class="form-label">URL tombol (opsional)</label>
                        <input id="new-program-url" name="link_url" class="form-control" maxlength="255">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="new-program-link-text" class="form-label">Teks tombol</label>
                        <input id="new-program-link-text" name="link_text" class="form-control" maxlength="100" value="Selengkapnya">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="new-program-order" class="form-label">Urutan</label>
                        <input id="new-program-order" name="sort_order" type="number" min="0" max="255" class="form-control" required value="<?= count($programs); ?>">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="show" value="1" class="form-check-input" id="new-program-show" checked>
                    <label class="form-check-label" for="new-program-show">Tampilkan program di website publik</label>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Tambah Program</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
