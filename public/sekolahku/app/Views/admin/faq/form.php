<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit FAQ' : 'Tambah FAQ' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui pertanyaan yang sering diajukan' : 'Tambah pertanyaan baru' ?></p>
    </div>
    <a href="/admin/faq" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/faq<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-question-circle me-2 text-primary"></i>Tanya Jawab</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Pertanyaan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-question text-body-tertiary"></i></span>
                            <input type="text" name="question" class="form-control border-start-0 ps-0" value="<?= $item['question'] ?? '' ?>" placeholder="e.g. Bagaimana cara mendaftar SPMB?" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Jawaban <span class="text-danger">*</span></label>
                        <textarea name="answer" class="form-control" rows="6" placeholder="Provide a clear and helpful answer..." required><?= $item['answer'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-folder me-2 text-primary"></i>Organisasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Kategori</label>
                        <input type="text" name="category" class="form-control" value="<?= $item['category'] ?? '' ?>" placeholder="e.g. Pendaftaran, Akademik" list="category-list">
                        <datalist id="category-list">
                            <option value="Pendaftaran">
                            <option value="Akademik">
                            <option value="Kurikulum">
                            <option value="Biaya">
                            <option value="Umum">
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= $item['sort_order'] ?? 0 ?>" min="0">
                        <div class="form-text small">Angka kecil tampil lebih dulu</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show" value="1" id="show" <?= (!isset($item['show']) || $item['show']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="show">Tampil di website</label>
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
                        <span class="text-nowrap"><?= isset($item['updated_at']) ? date('M d, Y H:i', strtotime($item['updated_at'])) : '-' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui FAQ' : 'Simpan FAQ' ?></button>
                <a href="/admin/faq" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>