<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Absensi Ekstrakurikuler</h1>
            <p class="text-muted mb-0">Catat kehadiran siswa untuk setiap kegiatan ekstrakurikuler.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="<?= site_url('report/extracurricular-attendance'); ?>">
                <i class="bi bi-file-earmark-text me-2" aria-hidden="true"></i>Laporan Absensi Ekstrakurikuler
            </a>
            <a class="btn btn-outline-secondary" href="<?= site_url('report/attendance'); ?>">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Laporan Absensi RFID
            </a>
        </div>
    </div>

    <?php if ($message !== null) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if ($error !== null) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Catat Absensi</h2></div>
        <div class="card-body">
            <form method="get" action="<?= site_url('extracurricular-attendance'); ?>" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label" for="attendanceClass">Kelas</label>
                    <select class="form-select" id="attendanceClass" name="kelas_id" required>
                        <option value="">Pilih kelas</option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= esc($class['kelas_id']); ?>" <?= $formClassId === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                <?= esc(($class['kelas_tingkat'] ?? '') . ' - ' . $class['kelas_nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="attendanceDate">Tanggal Kegiatan</label>
                    <input class="form-control" id="attendanceDate" name="tanggal" type="date" value="<?= esc($formDate); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="attendanceActivity">Nama Ekstrakurikuler</label>
                    <select class="form-select" id="attendanceActivity" name="nama_kegiatan" required>
                        <option value="">Pilih ekstrakurikuler</option>
                        <?php foreach ($activities as $activity) : ?>
                            <option value="<?= esc($activity); ?>" <?= $formActivity === $activity ? 'selected' : ''; ?>><?= esc($activity); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-people me-1" aria-hidden="true"></i>Tampilkan Siswa
                    </button>
                </div>
            </form>

            <?php if ($formClassId > 0 && $students !== []) : ?>
                <form method="post" action="<?= site_url('extracurricular-attendance/save'); ?>">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="kelas_id" value="<?= esc($formClassId); ?>">
                    <input type="hidden" name="tanggal" value="<?= esc($formDate); ?>">
                    <input type="hidden" name="nama_kegiatan" value="<?= esc($formActivity); ?>">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                        <p class="text-muted mb-0"><?= count($students); ?> siswa aktif. Isi nama kegiatan di atas sebelum menyimpan.</p>
                        <?php if (\App\Libraries\ActionAccess::can('extracurricular-attendance.save')) : ?>
                            <button class="btn btn-primary" type="submit" <?= $formActivity === '' ? 'disabled' : ''; ?>>
                                <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Absensi
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>No.</th><th>NIS</th><th>Nama Siswa</th><th>Status</th><th>Keterangan</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student) : ?>
                                    <tr>
                                        <td><?= $index + 1; ?></td>
                                        <td><?= esc($student['nis'] ?? '-'); ?></td>
                                        <td class="fw-medium"><?= esc($student['nama_siswa']); ?></td>
                                        <td>
                                            <label class="visually-hidden" for="studentStatus<?= esc($student['siswa_id']); ?>">Status <?= esc($student['nama_siswa']); ?></label>
                                            <select class="form-select" id="studentStatus<?= esc($student['siswa_id']); ?>" name="status[<?= esc($student['siswa_id']); ?>]">
                                                <?php foreach ($statuses as $value => $label) : ?>
                                                    <option value="<?= esc($value); ?>" <?= ($student['status'] ?? 'hadir') === $value ? 'selected' : ''; ?>><?= esc($label); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label class="visually-hidden" for="studentNote<?= esc($student['siswa_id']); ?>">Keterangan <?= esc($student['nama_siswa']); ?></label>
                                            <input class="form-control" id="studentNote<?= esc($student['siswa_id']); ?>" name="keterangan[<?= esc($student['siswa_id']); ?>]" maxlength="500" value="<?= esc($student['keterangan'] ?? ''); ?>">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <?php if (\App\Libraries\ActionAccess::can('extracurricular-attendance.save')) : ?>
                            <button class="btn btn-primary" type="submit" <?= $formActivity === '' ? 'disabled' : ''; ?>>
                                <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan Absensi
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            <?php elseif ($formClassId > 0) : ?>
                <?php if ($activities === []) : ?>
                    <p class="text-muted mb-0">Belum ada kegiatan ekstrakurikuler. Tambahkan melalui Master Akademik terlebih dahulu.</p>
                <?php elseif ($formActivity === '') : ?>
                    <p class="text-muted mb-0">Pilih ekstrakurikuler dari daftar, lalu klik Tampilkan Siswa.</p>
                <?php else : ?>
                    <p class="text-muted mb-0">Tidak ada siswa aktif di kelas ini.</p>
                <?php endif; ?>
            <?php else : ?>
                <p class="text-muted mb-0">Pilih kelas, tanggal, dan nama kegiatan untuk menampilkan daftar siswa.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>
