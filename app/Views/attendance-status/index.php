<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Status Absensi</h1>
        <p class="text-muted mb-0">Referensi status absensi RFID dan status kehadiran siswa.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <?php if (\App\Libraries\ActionAccess::can('attendance-status.manage')) : ?>
            <div class="card mb-4">
                <div class="card-header"><h2 class="h5 mb-0"><?= $editType === 'rfid' && $editStatus !== null ? 'Edit Status Absensi RFID' : 'Tambah Status Absensi RFID'; ?></h2></div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('attendance-status/save'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="type" value="rfid">
                        <input type="hidden" name="id" value="<?= $editType === 'rfid' ? esc((string) ($editStatus['status_absensi_id'] ?? '')) : ''; ?>">
                        <div class="mb-3">
                            <label class="form-label" for="rfidStatusName">Nama Status</label>
                            <input class="form-control" id="rfidStatusName" name="status_absensi" maxlength="20" required value="<?= $editType === 'rfid' ? esc($editStatus['status_absensi'] ?? '') : ''; ?>">
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="rfidStatusColor">Warna</label>
                            <select class="form-select" id="rfidStatusColor" name="status_absensi_warna" required>
                                <?php foreach (['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'] as $color) : ?>
                                    <option value="<?= esc($color); ?>" <?= ($editType === 'rfid' ? ($editStatus['status_absensi_warna'] ?? 'secondary') : 'secondary') === $color ? 'selected' : ''; ?>><?= esc(ucfirst($color)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="rfidStatusDescription">Keterangan</label>
                            <textarea class="form-control" id="rfidStatusDescription" name="status_absensi_keterangan" rows="2"><?= $editType === 'rfid' ? esc($editStatus['status_absensi_keterangan'] ?? '') : ''; ?></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit"><?= $editType === 'rfid' && $editStatus !== null ? 'Simpan Perubahan' : 'Tambah Status'; ?></button>
                        <?php if ($editType === 'rfid' && $editStatus !== null) : ?><a class="btn btn-outline-secondary" href="<?= site_url('attendance-status'); ?>">Batal</a><?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 card-title mb-0">Status Absensi RFID</h2>
                    <span class="badge bg-primary-subtle text-primary"><?= count($absenceStatus); ?> status</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Status</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absenceStatus as $status) : ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= esc($status['status_absensi_warna']); ?>">
                                            <?= esc($status['status_absensi']); ?>
                                        </span>
                                    </td>
                                    <td><?= esc($status['status_absensi_keterangan'] ?: '-'); ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php if (\App\Libraries\ActionAccess::can('attendance-status.manage')) : ?>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('attendance-status?edit_type=rfid&edit_id=' . (int) $status['status_absensi_id']); ?>">Ubah</a>
                                        <form class="d-inline" method="post" action="<?= site_url('attendance-status/delete/rfid/' . (int) $status['status_absensi_id']); ?>" onsubmit="return confirm('Hapus status ini?');">
                                            <?= csrf_field(); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($absenceStatus === []) : ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada status absensi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <?php if (\App\Libraries\ActionAccess::can('attendance-status.manage')) : ?>
            <div class="card mb-4">
                <div class="card-header"><h2 class="h5 mb-0"><?= $editType === 'attendance' && $editStatus !== null ? 'Edit Status Kehadiran' : 'Tambah Status Kehadiran'; ?></h2></div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('attendance-status/save'); ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="type" value="attendance">
                        <input type="hidden" name="id" value="<?= $editType === 'attendance' ? esc((string) ($editStatus['status_kehadiran_id'] ?? '')) : ''; ?>">
                        <div class="mb-3">
                            <label class="form-label" for="attendanceStatusName">Nama Status</label>
                            <input class="form-control" id="attendanceStatusName" name="status_kehadiran" maxlength="20" required value="<?= $editType === 'attendance' ? esc($editStatus['status_kehadiran'] ?? '') : ''; ?>">
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label" for="attendanceStatusColor">Warna</label>
                            <select class="form-select" id="attendanceStatusColor" name="status_kehadiran_warna" required>
                                <?php foreach (['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'] as $color) : ?>
                                    <option value="<?= esc($color); ?>" <?= ($editType === 'attendance' ? ($editStatus['status_kehadiran_warna'] ?? 'secondary') : 'secondary') === $color ? 'selected' : ''; ?>><?= esc(ucfirst($color)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="attendanceStatusDescription">Keterangan</label>
                            <textarea class="form-control" id="attendanceStatusDescription" name="status_kehadiran_keterangan" rows="2"><?= $editType === 'attendance' ? esc($editStatus['status_kehadiran_keterangan'] ?? '') : ''; ?></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit"><?= $editType === 'attendance' && $editStatus !== null ? 'Simpan Perubahan' : 'Tambah Status'; ?></button>
                        <?php if ($editType === 'attendance' && $editStatus !== null) : ?><a class="btn btn-outline-secondary" href="<?= site_url('attendance-status'); ?>">Batal</a><?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 card-title mb-0">Status Kehadiran Siswa</h2>
                    <span class="badge bg-primary-subtle text-primary"><?= count($attendanceStatus); ?> status</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Status</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceStatus as $status) : ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= esc($status['status_kehadiran_warna']); ?>">
                                            <?= esc($status['status_kehadiran']); ?>
                                        </span>
                                    </td>
                                    <td><?= esc($status['status_kehadiran_keterangan'] ?: '-'); ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php if (\App\Libraries\ActionAccess::can('attendance-status.manage')) : ?>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('attendance-status?edit_type=attendance&edit_id=' . (int) $status['status_kehadiran_id']); ?>">Ubah</a>
                                        <form class="d-inline" method="post" action="<?= site_url('attendance-status/delete/attendance/' . (int) $status['status_kehadiran_id']); ?>" onsubmit="return confirm('Hapus status ini?');">
                                            <?= csrf_field(); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($attendanceStatus === []) : ?>
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada status kehadiran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
