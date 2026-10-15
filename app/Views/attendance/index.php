<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Absensi RFID</h1>
        <p class="text-muted mb-0">Riwayat absensi siswa yang tercatat melalui kartu RFID.</p>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Data Absensi Siswa</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($attendance); ?> catatan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col" class="text-center">Masuk</th>
                        <th scope="col" class="text-center">Pulang</th>
                        <th scope="col" class="text-center">Kehadiran</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Dibuat</th>
                        <th scope="col">Diubah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $row) : ?>
                        <tr>
                            <td class="fw-medium"><?= esc($row['nama_siswa'] ?? '-'); ?></td>
                            <td><?= esc($row['tanggal'] ?? '-'); ?></td>
                            <td class="text-center">
                                <span class="text-success"><?= esc($row['waktu_masuk_aktual'] ?? '-'); ?></span>
                                <?php if (! empty($row['status_masuk'])) : ?>
                                    <br><span class="badge bg-<?= esc($row['status_masuk_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_masuk_warna'] ?? 'secondary'); ?>"><?= esc($row['status_masuk']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="text-danger"><?= esc($row['waktu_pulang_aktual'] ?? '-'); ?></span>
                                <?php if (! empty($row['status_pulang'])) : ?>
                                    <br><span class="badge bg-<?= esc($row['status_pulang_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_pulang_warna'] ?? 'secondary'); ?>"><?= esc($row['status_pulang']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (! empty($row['status_kehadiran'])) : ?>
                                    <span class="badge bg-<?= esc($row['status_kehadiran_warna'] ?? 'secondary'); ?>-subtle text-<?= esc($row['status_kehadiran_warna'] ?? 'secondary'); ?>"><?= esc($row['status_kehadiran']); ?></span>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($row['keterangan'] ?? '-'); ?></td>
                            <td><?= esc($row['created_at'] ?? '-'); ?></td>
                            <td><?= esc($row['updated_at'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($attendance === []) : ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data absensi RFID.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
