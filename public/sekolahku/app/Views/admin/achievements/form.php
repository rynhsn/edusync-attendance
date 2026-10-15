<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Prestasi' : 'Tambah Prestasi' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui catatan prestasi siswa' : 'Catat prestasi siswa baru' ?></p>
    </div>
    <a href="/admin/achievements" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/achievements<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-trophy me-2 text-primary"></i>Detail Prestasi</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Nama Siswa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-user-graduate text-body-tertiary"></i></span>
                                <input type="text" name="student_name" class="form-control border-start-0 ps-0" value="<?= $item['student_name'] ?? '' ?>" placeholder="e.g. Ahmad Rizki" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Kelas</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-school text-body-tertiary"></i></span>
                                <input type="text" name="class_name" class="form-control border-start-0 ps-0" value="<?= $item['class_name'] ?? '' ?>" placeholder="e.g. IX-A">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Prestasi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-medal text-body-tertiary"></i></span>
                            <input type="text" name="achievement" class="form-control border-start-0 ps-0" value="<?= $item['achievement'] ?? '' ?>" placeholder="e.g. Juara 1 Olimpiade Matematika Tingkat Nasional" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Tingkat</label>
                            <select name="level" class="form-select">
                                <option value="">- Pilih -</option>
                                <option value="School" <?= isset($item['level']) && $item['level'] === 'School' ? 'selected' : '' ?>>Sekolah</option>
                                <option value="District" <?= isset($item['level']) && $item['level'] === 'District' ? 'selected' : '' ?>>Kecamatan</option>
                                <option value="City" <?= isset($item['level']) && $item['level'] === 'City' ? 'selected' : '' ?>>Kota</option>
                                <option value="Province" <?= isset($item['level']) && $item['level'] === 'Province' ? 'selected' : '' ?>>Provinsi</option>
                                <option value="National" <?= isset($item['level']) && $item['level'] === 'National' ? 'selected' : '' ?>>Nasional</option>
                                <option value="International" <?= isset($item['level']) && $item['level'] === 'International' ? 'selected' : '' ?>>Internasional</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Tahun</label>
                            <input type="number" name="year" class="form-control" value="<?= $item['year'] ?? date('Y') ?>" min="2000" max="<?= date('Y') + 5 ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Medali</label>
                            <select name="medal" class="form-select">
                                <option value="">- Pilih -</option>
                                <option value="gold" <?= isset($item['medal']) && $item['medal'] === 'gold' ? 'selected' : '' ?>>Emas</option>
                                <option value="silver" <?= isset($item['medal']) && $item['medal'] === 'silver' ? 'selected' : '' ?>>Perak</option>
                                <option value="bronze" <?= isset($item['medal']) && $item['medal'] === 'bronze' ? 'selected' : '' ?>>Perunggu</option>
                            </select>
                        </div>
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
                            <img src="<?= $item['photo'] ?>" alt="preview" class="rounded-3 shadow-sm" style="width:120px;height:120px;object-fit:cover">
                        <?php else: ?>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-body-tertiary" style="width:120px;height:120px">
                                <i class="fas fa-user-graduate fa-4x text-body-tertiary opacity-50"></i>
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
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Prestasi' : 'Simpan Prestasi' ?></button>
                <a href="/admin/achievements" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>