<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><?= $item ? 'Edit Agenda' : 'Tambah Agenda' ?></h4>
        <p class="text-body-tertiary small mb-0"><?= $item ? 'Perbarui informasi agenda' : 'Jadwalkan agenda baru' ?></p>
    </div>
    <a href="/admin/events" class="btn btn-outline-secondary rounded-pill btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<form action="/admin/events<?= $item ? '/update/' . $item['id'] : '' ?>" method="post">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Detail Agenda</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-heading text-body-tertiary"></i></span>
                            <input type="text" name="title" class="form-control border-start-0 ps-0" value="<?= $item['title'] ?? '' ?>" placeholder="e.g. Hari Guru Nasional 2026" required>
                        </div>
                        <div class="form-text small">Slug will be auto-generated</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Describe the event, agenda, and participants..."><?= $item['description'] ?? '' ?></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" value="<?= $item['event_date'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Waktu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="far fa-clock text-body-tertiary"></i></span>
                                <input type="text" name="event_time" class="form-control border-start-0 ps-0" value="<?= $item['event_time'] ?? '' ?>" placeholder="e.g. 08:00 - 12:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-medium">Lokasi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-map-marker-alt text-body-tertiary"></i></span>
                                <input type="text" name="location" class="form-control border-start-0 ps-0" value="<?= $item['location'] ?? '' ?>" placeholder="e.g. Aula Sekolah">
                            </div>
                        </div>
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
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Status Agenda</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= isset($item['status']) && $item['status'] === 'draft' ? 'selected' : '' ?>>Draf</option>
                            <option value="upcoming" <?= isset($item['status']) && $item['status'] === 'upcoming' ? 'selected' : '' ?>>Akan Datang</option>
                            <option value="ongoing" <?= isset($item['status']) && $item['status'] === 'ongoing' ? 'selected' : '' ?>>Berlangsung</option>
                            <option value="completed" <?= isset($item['status']) && $item['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
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
                <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i><?= $item ? 'Perbarui Agenda' : 'Simpan Agenda' ?></button>
                <a href="/admin/events" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>