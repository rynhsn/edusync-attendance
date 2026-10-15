<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Testimoni' : 'Tambah Testimoni' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui testimoni ini' : 'Tambah testimoni baru dari siswa atau orang tua' ?></p>
    </div>
    <a href="/admin/testimonials" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/testimonials<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-quote-left me-2 text-primary"></i>Konten Testimoni</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-user text-body-tertiary"></i></span>
                            <input type="text" name="name" class="form-control border-start-0 ps-0" value="<?= $item['name'] ?? '' ?>" placeholder="e.g. Siti Nurhaliza" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Peran / Jabatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-tag text-body-tertiary"></i></span>
                            <input type="text" name="role" class="form-control border-start-0 ps-0" value="<?= $item['role'] ?? '' ?>" placeholder="e.g. Orang Tua Siswa / Siswi Kelas 9">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Testimoni <span class="text-danger">*</span></label>
                        <textarea name="quote" class="form-control" rows="5" placeholder="What did they say about the school? Write their testimonial here..." required><?= $item['quote'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Foto</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($item['photo'])): ?>
                            <img src="<?= $item['photo'] ?>" alt="preview" class="rounded-circle shadow-sm" style="width:100px;height:100px;object-fit:cover">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-tertiary" style="width:100px;height:100px">
                                <i class="fas fa-user fa-3x text-body-tertiary opacity-50"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <label class="form-label small fw-medium">URL Foto</label>
                    <input type="text" name="photo" class="form-control form-control-sm" value="<?= $item['photo'] ?? '' ?>" placeholder="https://...">
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
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="show" value="1" id="show" <?= (!isset($item['show']) || $item['show']) ? 'checked' : '' ?>>
                        <label class="form-check-label small fw-medium" for="show">Tampil di website</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Testimoni' : 'Simpan Testimoni' ?></button>
                <a href="/admin/testimonials" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>