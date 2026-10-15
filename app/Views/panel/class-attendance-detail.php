<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <a class="text-decoration-none" href="<?= site_url('class-attendance'); ?>"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke data absensi</a>
            <h1 class="h3 mt-3 mb-1">Detail Absensi Kelas</h1>
            <p class="text-muted mb-0"><?= esc($session['tanggal']); ?> · Kelas <?= esc($session['kelas_nama']); ?> · <?= esc($session['mapel_nama']); ?> · <?= esc($session['guru_nama']); ?></p>
            <p class="text-muted mb-0">Jam <?= esc($session['jam_ke']); ?> (<?= esc(substr($session['jam_mulai'], 0, 5)); ?>–<?= esc(substr($session['jam_selesai'], 0, 5)); ?>) · Tahun ajaran <?= esc($session['tahun_ajaran_nama']); ?> - Semester <?= esc($session['semester']); ?></p>
        </div>
        <a class="btn btn-outline-success" href="<?= site_url('class-attendance/' . $session['absensi_kelas_id'] . '/pdf'); ?>">
            <i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Unduh PDF
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Kehadiran Siswa</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($students); ?> siswa</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th scope="col">No.</th><th scope="col">Nama Siswa</th><th scope="col">NIS</th><th scope="col">Status</th><th scope="col">Keterangan</th></tr>
                </thead>
                <tbody>
                    <?php
                    $statusLabels = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa'];
                    $statusClasses = ['hadir' => 'bg-success-subtle text-success', 'sakit' => 'bg-warning-subtle text-warning-emphasis', 'izin' => 'bg-info-subtle text-info-emphasis', 'alpa' => 'bg-danger-subtle text-danger'];
                    ?>
                    <?php foreach ($students as $index => $student) : ?>
                        <?php $status = $student['status']; ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($student['nama_siswa']); ?></td>
                            <td><?= esc($student['nis'] ?? '-'); ?></td>
                            <td><span class="badge <?= esc($statusClasses[$status] ?? 'bg-secondary-subtle text-secondary'); ?>"><?= esc($statusLabels[$status] ?? $status); ?></span></td>
                            <td><?= esc($student['keterangan'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($students === []) : ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data siswa pada absensi ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
