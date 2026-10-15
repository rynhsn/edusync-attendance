<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Berita' : 'Tambah Berita' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui konten dan metadata artikel' : 'Tulis artikel baru untuk website' ?></p>
    </div>
    <a href="/admin/news" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/news<?= $item ? '/update/' . $item['id'] : '' ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-newspaper me-2 text-primary"></i>Konten</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-heading text-body-tertiary"></i></span>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= $item['title'] ?? '' ?>" placeholder="e.g. Kegiatan MPLS Tahun Ajaran 2026/2027" required>
                        </div>
                        <div class="form-text small">Slug will be auto-generated: <span class="text-secondary fw-medium"><?= isset($item['slug']) ? $item['slug'] : 'your-article-title' ?></span></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Kategori <span class="text-body-tertiary fw-normal">(pisahkan dengan koma untuk multiple)</span></label>
                            <input type="text" name="category" class="form-control" value="<?= $item['category'] ?? '' ?>" placeholder="e.g. Akademik,Prestasi,Kegiatan" list="category-list">
                            <datalist id="category-list">
                                <option value="Akademik">
                                <option value="Prestasi">
                                <option value="Kegiatan">
                                <option value="Pengumuman">
                                <option value="Olahraga">
                                <option value="Seni">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Tag <span class="text-body-tertiary fw-normal">(pisahkan dengan koma)</span></label>
                            <input type="text" name="tags" class="form-control" value="<?= esc(implode(', ', array_filter(json_decode($item['tags'] ?? '[]', true) ?? []))) ?>" placeholder="e.g. mpls, siswa, 2026">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Ringkasan</label>
                        <textarea name="excerpt" class="form-control" rows="2" placeholder="Short summary for preview cards and meta description..."><?= $item['excerpt'] ?? '' ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Konten <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="16" placeholder="Write your article content here..." style="display:none"><?= $item['content'] ?? '' ?></textarea>
                        <div id="editor-content"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Gambar Unggulan</label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="image-input">
                        <input type="hidden" name="old_image" value="<?= $item['image'] ?? '' ?>">
                        <?php if (!empty($item['image'])): ?>
                            <div class="mt-2" id="preview-wrapper">
                                <img src="<?= $item['image'] ?>" alt="preview" class="rounded-3 border" style="max-height:120px;width:auto" id="image-preview">
                            </div>
                        <?php else: ?>
                            <div class="mt-2 d-none" id="preview-wrapper">
                                <img src="" alt="preview" class="rounded-3 border" style="max-height:120px;width:auto" id="image-preview">
                            </div>
                        <?php endif; ?>
                        <div class="form-text small">Upload image (will be resized to 1200px width). Max 5MB, JPG/PNG/WebP</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>Pengaturan Publikasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= isset($item['status']) && $item['status'] === 'draft' ? 'selected' : '' ?>>Draf</option>
                            <option value="published" <?= isset($item['status']) && $item['status'] === 'published' ? 'selected' : '' ?>>Terbit</option>
                            <option value="archived" <?= isset($item['status']) && $item['status'] === 'archived' ? 'selected' : '' ?>>Arsip</option>
                        </select>
                        <div class="form-text small">Atur ke Terbit agar terlihat di website</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Penulis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-user-pen text-body-tertiary"></i></span>
                            <input type="text" name="author" class="form-control border-start-0 ps-0" value="<?= $item['author'] ?? session()->get('username') ?? '' ?>" placeholder="Author name">
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?= isset($item['is_featured']) && $item['is_featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="is_featured">Artikel unggulan</label>
                    </div>
                    <div class="form-text small mt-0">Artikel unggulan tampil di bagian hero/spotlight</div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>SEO</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul Meta</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= $item['meta_title'] ?? '' ?>" placeholder="SEO-optimized title">
                        <div class="form-text small">Leave empty to use article title</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Deskripsi Meta</label>
                        <textarea name="meta_description" class="form-control" rows="3" placeholder="Brief description for search results..."><?= $item['meta_description'] ?? '' ?></textarea>
                        <div class="form-text small">Recommended: 150–160 characters</div>
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
                    <div class="d-flex justify-content-between mb-1 flex-wrap gap-1">
                        <span>Diubah</span>
                        <span class="text-nowrap"><?= isset($item['updated_at']) ? date('d M Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap gap-1">
                        <span>Diterbitkan</span>
                        <span class="text-nowrap"><?= isset($item['published_at']) ? date('M d, Y H:i', strtotime($item['published_at'])) : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap gap-1 mt-2 pt-2 border-top">
                        <span>Dilihat</span>
                        <span class="fw-medium"><?= isset($item['view_count']) ? number_format($item['view_count']) : '0' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Artikel' : 'Terbitkan Artikel' ?></button>
                <a href="/admin/news" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
const quill = new Quill('#editor-content', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ header: [false, 1, 2, 3] }],
            ['bold', 'italic', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['link', 'image'],
            ['clean']
        ]
    },
    placeholder: 'Write your article content here...'
});

const ta = document.querySelector('textarea[name="content"]');
quill.root.innerHTML = ta.value;

const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const content = quill.root.innerHTML;
    ta.value = content;
    if (content === '<p><br></p>' || content === '' || content === '<p></p>') {
        e.preventDefault();
        alert('Content cannot be empty.');
    }
});

document.getElementById('image-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('image-preview');
            const wrapper = document.getElementById('preview-wrapper');
            if (preview) {
                preview.src = ev.target.result;
                wrapper?.classList.remove('d-none');
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>