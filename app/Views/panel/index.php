<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<?php
$maxWeeklyTotal = max(1, ...array_column($weeklyAttendance, 'total'));
$attendanceTotal = max(1, (int) $stats['total']);
$canManage = $role === 'super_admin';
?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted mb-0"><?= esc($roleLabel); ?> &middot; Ringkasan absensi &middot; diperbarui <?= esc(date('H:i')); ?></p>
        </div>
        <div class="d-flex gap-2 flex-shrink-0">
            <?php if ($canManage) : ?>
                <a class="btn btn-primary" href="<?= site_url('class-attendance/create'); ?>">
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline">Catat Absensi</span>
                </a>
            <?php endif; ?>
            <a class="btn btn-outline-secondary" href="<?= site_url('panel'); ?>" title="Muat ulang dashboard" aria-label="Muat ulang dashboard">
                <i class="bi bi-arrow-clockwise icon-hover" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <div class="row g-3 g-lg-4 mb-4">
        <?php foreach ($cards as $card) : ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card stats-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stats-icon bg-<?= esc($card['tone']); ?> bg-opacity-10 text-<?= esc($card['tone']); ?>">
                                    <i class="bi <?= esc($card['icon']); ?>" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="h6 mb-0 text-muted"><?= esc($card['label']); ?></p>
                                <div class="h3 mb-0"><?= number_format((int) $card['value']); ?></div>
                                <small class="text-muted"><?= esc(date('d-m-Y', strtotime($today))); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 card-title mb-0">Ringkasan Kehadiran (7 Hari Terakhir)</h2>
                    <span class="badge bg-primary-subtle text-primary">Hadir / Total</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-end justify-content-around gap-2" style="height: 280px;" role="img" aria-label="Grafik jumlah siswa hadir dan total absensi dalam tujuh hari terakhir">
                        <?php foreach ($weeklyAttendance as $day) : ?>
                            <?php
                            $totalHeight = max(2, (int) round(($day['total'] / $maxWeeklyTotal) * 205));
                            $presentHeight = $day['total'] > 0
                                ? max(2, (int) round(($day['hadir'] / $maxWeeklyTotal) * 205))
                                : 0;
                            ?>
                            <div class="d-flex flex-column align-items-center justify-content-end h-100 flex-fill">
                                <small class="text-muted mb-2"><?= number_format($day['total']); ?></small>
                                <div class="d-flex align-items-end justify-content-center gap-1 w-100" style="height: 220px;">
                                    <div class="bg-primary rounded-top" style="width: 28%; height: <?= $presentHeight; ?>px;" title="Hadir: <?= $day['hadir']; ?>"></div>
                                    <div class="bg-info bg-opacity-50 rounded-top" style="width: 28%; height: <?= $totalHeight; ?>px;" title="Total: <?= $day['total']; ?>"></div>
                                </div>
                                <small class="text-muted mt-2"><?= esc($day['label']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-center gap-4 mt-3 small">
                        <span><i class="bi bi-square-fill text-primary me-1" aria-hidden="true"></i>Hadir</span>
                        <span><i class="bi bi-square-fill text-info opacity-50 me-1" aria-hidden="true"></i>Total siswa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h5 card-title mb-0">Absensi Terbaru</h2>
                </div>
                <div class="card-body">
                    <?php if ($recentSessions === []) : ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-2 d-block mb-2" aria-hidden="true"></i>
                            Belum ada pencatatan absensi kelas.
                        </div>
                    <?php else : ?>
                        <div class="activity-feed">
                            <?php foreach (array_slice($recentSessions, 0, 6) as $session) : ?>
                                <div class="activity-item">
                                    <div class="activity-icon"><i class="bi bi-journal-check" aria-hidden="true"></i></div>
                                    <div class="activity-content">
                                        <p class="mb-1">
                                            <a class="text-decoration-none" href="<?= site_url('class-attendance/' . $session['absensi_kelas_id']); ?>">
                                                <?= esc($session['kelas_nama']); ?> &middot; <?= esc($session['mapel_nama']); ?>
                                            </a>
                                        </p>
                                        <small class="text-muted"><?= esc($session['guru_nama']); ?> &middot; <?= esc(date('d-m-Y', strtotime($session['tanggal']))); ?> &middot; <?= (int) $session['jumlah_hadir']; ?>/<?= (int) $session['jumlah_siswa']; ?> hadir</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h5 card-title mb-0">Status Kehadiran Hari Ini</h2>
                </div>
                <div class="card-body">
                    <?php foreach ($statusBreakdown as $status) : ?>
                        <?php $percentage = (int) round(($status['value'] / $attendanceTotal) * 100); ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= esc($status['label']); ?></span>
                                <span class="text-muted"><?= number_format($status['value']); ?> (<?= $percentage; ?>%)</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="<?= esc($status['label']); ?>" aria-valuenow="<?= $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="height: 10px;">
                                <div class="progress-bar bg-<?= esc($status['tone']); ?>" style="width: <?= $percentage; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($stats['total'] === 0) : ?>
                        <p class="text-muted small mb-0">Belum ada status kehadiran kelas tercatat hari ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="h5 card-title mb-0">Data Sekolah</h2>
                    <?php if ($role === 'super_admin') : ?>
                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('users'); ?>">Kelola User</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h3 mb-1"><?= number_format($stats['students']); ?></div>
                                <small class="text-muted">Siswa Aktif</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded">
                                <div class="h3 mb-1"><?= number_format($stats['classes']); ?></div>
                                <small class="text-muted">Kelas</small>
                            </div>
                        </div>
                        <?php if ($role === 'super_admin') : ?>
                            <div class="col-6">
                                <div class="p-3 border rounded">
                                    <div class="h3 mb-1"><?= number_format($stats['teachers']); ?></div>
                                    <small class="text-muted">Guru</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded">
                                    <div class="h3 mb-1"><?= number_format($stats['users']); ?></div>
                                    <small class="text-muted">Akun User</small>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-12">
                                <a class="btn btn-outline-primary w-100" href="<?= site_url('class-attendance'); ?>">
                                    <i class="bi bi-journal-check me-2" aria-hidden="true"></i>Lihat Data Absensi
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
