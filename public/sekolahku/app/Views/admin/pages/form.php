<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Halaman' : 'Tambah Halaman' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui konten halaman statis' : 'Buat halaman statis baru' ?></p>
    </div>
    <a href="/admin/pages" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/pages<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Konten Halaman</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-heading text-body-tertiary"></i></span>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= $item['title'] ?? '' ?>" placeholder="e.g. Visi dan Misi" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Konten</label>
                        <div class="quill-editor" style="min-height:300px"><?= $item['content'] ?? '' ?></div>
                        <textarea name="content" class="d-none"><?= $item['content'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Ringkasan</label>
                        <textarea name="excerpt" class="form-control" rows="3" placeholder="Ringkasan singkat halaman..."><?= $item['excerpt'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>SEO</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= $item['meta_title'] ?? '' ?>" placeholder="Biarkan kosong untuk menggunakan judul halaman">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-medium">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" placeholder="Deskripsi untuk SEO..."><?= $item['meta_description'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>Status</h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" value="published" id="status" <?= (isset($item['status']) && $item['status'] === 'published') ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="status">Published</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="add_to_menu" value="1" id="add_to_menu" <?= (!isset($item) || empty($item)) ? 'checked' : (!empty($menuItem) ? 'checked' : '') ?>>
                        <label class="form-check-label small fw-medium" for="add_to_menu">Tambah ke menu navigasi</label>
                        <?php if (!empty($menuItem)): ?>
                        <div class="form-text small text-body-tertiary">Judul & URL menu otomatis tersinkronisasi dengan halaman ini.</div>
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
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Halaman' : 'Simpan Halaman' ?></button>
                <a href="/admin/pages" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editorEl = document.querySelector('.quill-editor');
    const textarea = document.querySelector('textarea[name="content"]');
    if (!editorEl || !textarea) return;

    const quill = new Quill(editorEl, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1,2,3,false] }],
                ['bold','italic','underline','strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'code-block'],
                [{ align: [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    quill.root.innerHTML = textarea.value;

    document.querySelector('form').addEventListener('submit', function() {
        textarea.value = quill.root.innerHTML;
    });
});
</script>
<?= $this->endSection() ?>
