<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Gambar Galeri' : 'Tambah Gambar Galeri' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui detail gambar' : 'Upload gambar baru ke galeri' ?></p>
    </div>
    <a href="/admin/gallery" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/gallery<?= $item ? '/update/' . $item['id'] : '' ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Gambar</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" <?= $item ? '' : 'required' ?> id="image-input">
                        <input type="hidden" name="old_image" value="<?= $item['image'] ?? '' ?>">
                    </div>
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3 border bg-body-tertiary" style="min-height:200px;overflow:hidden" id="preview-wrapper">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= $item['image'] ?>" alt="preview" style="max-width:100%;max-height:300px;object-fit:contain" id="image-preview">
                            <?php else: ?>
                                <div class="text-center p-4" id="preview-placeholder">
                                    <i class="fas fa-image fa-3x text-body-tertiary opacity-50 mb-2"></i>
                                    <p class="small text-body-tertiary mb-0">Preview will appear here</p>
                                </div>
                                <img src="" alt="preview" style="max-width:100%;max-height:300px;object-fit:contain" id="image-preview" class="d-none">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Keterangan</label>
                        <textarea name="caption" class="form-control" rows="3" placeholder="A brief description of the image..."><?= $item['caption'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-folder me-2 text-primary"></i>Kategori</h6>
                </div>
                <div class="card-body">
                        <label class="form-label small fw-medium">Kategori</label>
                    <input type="text" name="category" class="form-control" value="<?= $item['category'] ?? '' ?>" placeholder="e.g. School Activity" list="category-list">
                    <datalist id="category-list">
                        <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['category']) ?>">
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="School Activity">
                        <option value="Graduation">
                        <option value="Sports">
                        <option value="Arts">
                        <option value="Events">
                    </datalist>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-sliders-h me-2 text-primary"></i>Tampilan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0">
                        <div class="form-text small">Angka kecil tampil lebih dulu</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?= isset($item['is_featured']) && $item['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label small fw-medium" for="is_featured">Gambar unggulan</label>
                        </div>
                        <div class="form-text small">Gambar unggulan tampil di bagian hero/spotlight</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show" value="1" id="show" <?= (!isset($item['show']) || $item['show']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="show">Tampil di website</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Gambar' : 'Simpan Gambar' ?></button>
                <a href="/admin/gallery" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('image-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('preview-placeholder');
            if (preview) {
                preview.src = ev.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>