<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Absensi Kelas</h1>
            <p class="text-muted mb-0">Rekap dan riwayat absensi siswa berdasarkan kelas dan bulan.</p>
        </div>
        <?php if ($selectedClass !== null && $month !== '' && $filterError === null) : ?>
            <a class="btn btn-outline-primary" href="<?= site_url('class-attendance-report/pdf') . '?' . http_build_query([
                'kelas_id' => $classId,
                'bulan' => $month,
            ]); ?>">
                <i class="bi bi-download me-2" aria-hidden="true"></i>Unduh PDF
            </a>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 card-title mb-0">Filter Laporan</h2></div>
        <div class="card-body">
            <?php if ($filterError !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($filterError); ?></div>
            <?php endif; ?>
            <form method="get" action="<?= site_url('class-attendance-report'); ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label" for="reportClass">Kelas</label>
                        <select class="form-select" id="reportClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $classId === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                    <?= esc(trim(($class['kelas_tingkat'] ?? '') . ' ' . $class['kelas_nama'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reportMonth">Bulan</label>
                        <input class="form-control" id="reportMonth" name="bulan" type="month" value="<?= esc($month); ?>" required>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1" aria-hidden="true"></i>Tampilkan
                        </button>
                        <a class="btn btn-outline-secondary" href="<?= site_url('class-attendance-report'); ?>">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectedClass !== null && $month !== '' && $filterError === null) : ?>
        <div class="card mb-4">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h2 class="h5 card-title mb-0">Rekap Absensi Siswa</h2>
                    <span class="text-muted"><?= esc($selectedClass['kelas_nama']); ?> · <?= esc($monthLabel); ?></span>
                </div>
                <span class="badge bg-primary-subtle text-primary"><?= count($students); ?> siswa · <?= count($records); ?> catatan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col">NIS</th>
                            <th scope="col">Hadir</th>
                            <th scope="col">Sakit</th>
                            <th scope="col">Izin</th>
                            <th scope="col">Alpa</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student) : ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td class="fw-medium"><?= esc($student['nama_siswa']); ?></td>
                                <td><?= esc($student['nis'] ?? '-'); ?></td>
                                <td><span class="badge bg-success-subtle text-success"><?= (int) $student['hadir']; ?></span></td>
                                <td><span class="badge bg-warning-subtle text-warning-emphasis"><?= (int) $student['sakit']; ?></span></td>
                                <td><span class="badge bg-info-subtle text-info-emphasis"><?= (int) $student['izin']; ?></span></td>
                                <td><span class="badge bg-danger-subtle text-danger"><?= (int) $student['alpa']; ?></span></td>
                                <td><?= (int) $student['total']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($students === []) : ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data absensi untuk kelas dan bulan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 card-title mb-0">Riwayat Absensi</h2>
                <span class="badge bg-primary-subtle text-primary"><?= count($records); ?> catatan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Nama Siswa</th>
                            <th scope="col">NIS</th>
                            <th scope="col">Mapel</th>
                            <th scope="col">Guru</th>
                            <th scope="col">Jam</th>
                            <th scope="col">Status</th>
                            <th scope="col">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $statusLabels = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa'];
                        $statusClasses = [
                            'hadir' => 'bg-success-subtle text-success',
                            'sakit' => 'bg-warning-subtle text-warning-emphasis',
                            'izin' => 'bg-info-subtle text-info-emphasis',
                            'alpa' => 'bg-danger-subtle text-danger',
                        ];
                        ?>
                        <?php foreach ($records as $record) : ?>
                            <tr>
                                <td><?= esc($record['tanggal']); ?></td>
                                <td class="fw-medium"><?= esc($record['nama_siswa']); ?></td>
                                <td><?= esc($record['nis'] ?? '-'); ?></td>
                                <td><?= esc($record['mapel_nama']); ?></td>
                                <td><?= esc($record['guru_nama']); ?></td>
                                <td><?= esc($record['jam_ke']); ?> (<?= esc(substr($record['jam_mulai'], 0, 5)); ?>–<?= esc(substr($record['jam_selesai'], 0, 5)); ?>)</td>
                                <td><span class="badge <?= esc($statusClasses[$record['status']] ?? 'bg-secondary-subtle text-secondary'); ?>"><?= esc($statusLabels[$record['status']] ?? $record['status']); ?></span></td>
                                <td><?= esc($record['keterangan'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($records === []) : ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data absensi untuk kelas dan bulan ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
