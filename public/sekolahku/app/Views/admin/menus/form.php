<?php
$sectionKeys = [
    ''                => '— Tidak ada —',
    'profile'         => 'Profil',
    'programs'        => 'Program',
    'extracurriculars' => 'Ekstrakurikuler',
    'teachers'        => 'Guru',
    'achievements'    => 'Prestasi',
    'testimonials'    => 'Testimoni',
    'news'            => 'Berita',
    'events'          => 'Agenda',
    'gallery'         => 'Galeri',
    'faq'             => 'FAQ',
    'downloads'       => 'Download',
    'contact'         => 'Kontak',
];
$locked = $locked ?? false;
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Menu' : 'Tambah Menu' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui item navigasi' : 'Tambah item navigasi baru' ?></p>
    </div>
    <a href="/admin/menus" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/menus<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-bars me-2 text-primary"></i>Detail Menu</h6>
                    <?php if ($locked): ?>
                    <span class="badge bg-secondary mt-1">Menu bawaan — hanya posisi yang dapat diubah</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-tag text-body-tertiary"></i></span>
                            <?php if ($locked): ?>
                            <div class="form-control border-start-0 ps-0 bg-body-tertiary text-body-secondary"><?= esc($item['title'] ?? '') ?></div>
                            <input type="hidden" name="title" value="<?= esc($item['title'] ?? '') ?>">
                            <?php else: ?>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= esc($item['title'] ?? '') ?>" placeholder="e.g. Profil Sekolah" required>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">URL <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><code style="font-size:inherit">/</code></span>
                            <?php if ($locked): ?>
                            <div class="form-control border-start-0 ps-0 bg-body-tertiary text-body-secondary"><?= esc($item['url'] ?? '') ?></div>
                            <input type="hidden" name="url" value="<?= esc($item['url'] ?? '') ?>">
                            <?php else: ?>
                            <input type="text" name="url" class="form-control border-start-0 ps-0" value="<?= esc($item['url'] ?? '') ?>" placeholder="contoh: profile, visi-misi, news, #programs" required>
                            <?php endif; ?>
                        </div>
                        <?php if (!$locked): ?>
                        <div class="form-text small">Jangan gunakan <code>base_url()</code>. Tulis path relatif saja (e.g. <code>visi-misi</code>, <code>news</code>, <code>#profile</code>).</div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Target</label>
                            <?php if ($locked): ?>
                            <div class="form-control bg-body-tertiary text-body-secondary"><?= $item['target'] === '_blank' ? 'New Tab' : 'Same Tab' ?></div>
                            <input type="hidden" name="target" value="<?= $item['target'] ?? '_self' ?>">
                            <?php else: ?>
                            <select name="target" class="form-select">
                                <option value="_self" <?= isset($item['target']) && $item['target'] === '_self' ? 'selected' : '' ?>>Same Tab</option>
                                <option value="_blank" <?= isset($item['target']) && $item['target'] === '_blank' ? 'selected' : '' ?>>New Tab</option>
                            </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Icon (Font Awesome)</label>
                            <?php if ($locked): ?>
                            <div class="form-control bg-body-tertiary text-body-secondary"><?= esc($item['icon'] ?? '—') ?></div>
                            <input type="hidden" name="icon" value="<?= esc($item['icon'] ?? '') ?>">
                            <?php else: ?>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-icons text-body-tertiary"></i></span>
                                <input type="text" name="icon" class="form-control border-start-0 ps-0" value="<?= esc($item['icon'] ?? '') ?>" placeholder="e.g. fas fa-school">
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>Pengaturan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Induk Menu</label>
                        <select name="parent_id" class="form-select" <?= $locked ? '' : '' ?>>
                            <option value="">— Menu Utama —</option>
                            <?php foreach ($parents as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= isset($item['parent_id']) && $item['parent_id'] == $p['id'] ? 'selected' : '' ?>><?= esc($p['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Pilih jika menu ini adalah anak dari menu dropdown.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Section Key <span class="badge bg-info ms-1">Hybrid</span></label>
                        <?php if ($locked): ?>
                        <div class="form-control bg-body-tertiary text-body-secondary"><?= $sectionKeys[$item['section_key'] ?? ''] ?? $item['section_key'] ?? '— Tidak ada —' ?></div>
                        <input type="hidden" name="section_key" value="<?= esc($item['section_key'] ?? '') ?>">
                        <?php else: ?>
                        <select name="section_key" class="form-select">
                            <?php foreach ($sectionKeys as $key => $label): ?>
                            <option value="<?= $key ?>" <?= isset($item['section_key']) && $item['section_key'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Menu akan otomatis disembunyikan jika section ini dinonaktifkan di pengaturan.</div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0">
                        <div class="form-text small">Angka kecil tampil lebih dulu.</div>
                    </div>
                    <div class="form-check form-switch">
                        <?php if ($locked): ?>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-<?= (!isset($item['status']) || $item['status']) ? 'success' : 'secondary' ?>"><?= (!isset($item['status']) || $item['status']) ? 'Aktif' : 'Nonaktif' ?></span>
                            <span class="small text-body-tertiary">Status diatur oleh sistem</span>
                        </div>
                        <input type="hidden" name="status" value="<?= $item['status'] ?? 1 ?>">
                        <?php else: ?>
                        <input class="form-check-input" type="checkbox" name="status" value="1" id="status" <?= (!isset($item['status']) || $item['status']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="status">Aktif</label>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($item): ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Waktu</h6>
                </div>
                <div class="card-body small text-body-tertiary">
                    <div class="d-flex justify-content-between mb-1 flex-wrap gap-1">
                        <span>Dibuat</span>
                        <span class="text-nowrap"><?= isset($item['created_at']) ? date('d M Y H:i', strtotime($item['created_at'])) : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap gap-1">
                        <span>Diubah</span>
                        <span class="text-nowrap"><?= isset($item['updated_at']) ? date('d M Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? ($locked ? 'Simpan Posisi' : 'Perbarui Menu') : 'Simpan Menu' ?></button>
                <a href="/admin/menus" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const sectionKey = document.querySelector('[name="section_key"]');
    const urlInput   = document.querySelector('[name="url"]');
    if (!sectionKey || !urlInput) return;

    function syncUrl() {
        if (sectionKey.value) {
            urlInput.value = '#' + sectionKey.value;
            urlInput.readOnly = true;
            urlInput.classList.add('bg-body-tertiary');
        } else {
            urlInput.readOnly = false;
            urlInput.classList.remove('bg-body-tertiary');
        }
    }

    sectionKey.addEventListener('change', syncUrl);
    syncUrl();
});
</script>
<?= $this->endSection() ?>
