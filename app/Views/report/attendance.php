<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Laporan Absensi</h1>
            <p class="text-muted mb-0">Riwayat absensi diurutkan berdasarkan nama siswa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-success" href="<?= site_url('extracurricular-attendance'); ?>">
                <i class="bi bi-stars me-2" aria-hidden="true"></i>Absensi Ekstrakurikuler
            </a>
            <a class="btn btn-outline-primary" href="<?= site_url('report/attendance/pdf') . '?' . http_build_query([
                'nama' => $studentName,
                'from' => $dateFrom,
                'to' => $dateTo,
            ]); ?>">
                <i class="bi bi-download me-2" aria-hidden="true"></i>Unduh PDF
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Filter Laporan</h2>
        </div>
        <div class="card-body">
            <?php if ($filterError !== null) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($filterError); ?></div>
            <?php endif; ?>
            <form method="get" action="<?= site_url('report/attendance'); ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" for="reportStudent">Nama Siswa</label>
                        <input class="form-control" id="reportStudent" name="nama" value="<?= esc($studentName); ?>" placeholder="Cari nama siswa">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="reportFrom">Tanggal Mulai</label>
                        <input class="form-control" id="reportFrom" name="from" type="date" value="<?= esc($dateFrom); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="reportTo">Tanggal Akhir</label>
                        <input class="form-control" id="reportTo" name="to" type="date" value="<?= esc($dateTo); ?>">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1" aria-hidden="true"></i>Filter
                        </button>
                        <a class="btn btn-outline-secondary" href="<?= site_url('report/attendance'); ?>">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Data Absensi</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($attendance); ?> catatan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Jam Masuk</th>
                        <th scope="col">Jam Pulang</th>
                        <th scope="col">Kehadiran</th>
                        <th scope="col">Jadwal Masuk</th>
                        <th scope="col">Jadwal Pulang</th>
                        <th scope="col">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td class="fw-medium"><?= esc($row['nama_siswa'] ?? 'Siswa tidak ditemukan'); ?></td>
                            <td><?= esc($row['kelas_nama'] ?? '-'); ?></td>
                            <td><?= esc($row['tanggal'] ?? '-'); ?></td>
                            <td>
                                <?= esc($row['waktu_masuk_aktual'] ?: '-'); ?>
                                <?php if (! empty($row['status_masuk'])) : ?>
                                    <br><span class="badge bg-<?= esc($row['status_masuk_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_masuk_warna'] ?? 'secondary'); ?>"><?= esc($row['status_masuk']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($row['waktu_pulang_aktual'] ?: '-'); ?>
                                <?php if (! empty($row['status_pulang'])) : ?>
                                    <br><span class="badge bg-<?= esc($row['status_pulang_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_pulang_warna'] ?? 'secondary'); ?>"><?= esc($row['status_pulang']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (! empty($row['status_kehadiran'])) : ?>
                                    <span class="badge bg-<?= esc($row['status_kehadiran_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_kehadiran_warna'] ?? 'secondary'); ?>"><?= esc($row['status_kehadiran']); ?></span>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($row['waktu_masuk'] ?? '-'); ?></td>
                            <td><?= esc($row['waktu_pulang'] ?? '-'); ?></td>
                            <td><?= esc($row['keterangan'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($attendance === []) : ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Tidak ada data absensi untuk filter ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    @media print {
        .admin-header, .admin-sidebar, .admin-footer, .card form, .btn { display: none !important; }
        .admin-main, .container-fluid { padding: 0 !important; }
        .card { border: 0 !important; box-shadow: none !important; }
    }
</style>
<?= $this->endSection(); ?>
