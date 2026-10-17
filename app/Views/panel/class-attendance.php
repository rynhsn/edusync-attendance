<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Data Absensi Kelas</h1>
            <p class="text-muted mb-0">Riwayat kehadiran berdasarkan kelas, mapel, guru, dan jam pelajaran.</p>
        </div>
        <?php if ($canCreate) : ?>
            <a class="btn btn-primary" href="<?= site_url('class-attendance/create'); ?>">
                <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Catat Absensi
            </a>
        <?php endif; ?>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= site_url('class-attendance'); ?>" class="row g-3 align-items-end">
                <div class="col-sm-5 col-md-3">
                    <label class="form-label" for="filterDate">Tanggal</label>
                    <input class="form-control" id="filterDate" name="tanggal" type="date" value="<?= esc($date); ?>">
                </div>
                <div class="col-sm-7 col-md-4">
                    <label class="form-label" for="filterClass">Kelas</label>
                    <select class="form-select" id="filterClass" name="kelas_id">
                        <option value="">Semua kelas</option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= (int) $class['kelas_id']; ?>" <?= (string) $classId === (string) $class['kelas_id'] ? 'selected' : ''; ?>>
                                <?= esc($class['kelas_nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="<?= site_url('class-attendance'); ?>">Reset</a>
                </div>
            </form>
            <p class="small text-muted mt-3 mb-0">Notifikasi dikirim per mata pelajaran hanya untuk siswa sakit, izin, atau alpa. Tap RFID mengirim notifikasi datang saja.</p>
            <?php if ($canSendWhatsApp) : ?>
                <hr>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <p class="small text-muted mb-0">Kirim satu rekap untuk kelas terpilih, meliputi tap datang, status tiap jam pelajaran, dan tap pulang pada tanggal tersebut.</p>
                    <form method="post" action="<?= site_url('class-attendance/send-daily-whatsapp'); ?>" onsubmit="return confirm('Kirim rekap WhatsApp lengkap untuk kelas dan tanggal yang dipilih? Pesan akan dikirim kepada wali murid yang memiliki nomor WhatsApp.');">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="tanggal" value="<?= esc($date); ?>">
                        <input type="hidden" name="kelas_id" value="<?= esc($classId); ?>">
                        <button class="btn btn-success" type="submit" <?= $date === '' || $classId === '' ? 'disabled' : ''; ?>>
                            <i class="bi bi-whatsapp me-1" aria-hidden="true"></i>Kirim Rekap WhatsApp Kelas
                        </button>
                    </form>
                </div>
                <?php if ($date === '' || $classId === '') : ?>
                    <small class="text-muted d-block mt-2">Pilih tanggal dan satu kelas, lalu klik Filter untuk mengaktifkan pengiriman rekap.</small>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Riwayat Absensi</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($records); ?> sesi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Mapel</th>
                        <th scope="col">Guru</th>
                        <th scope="col">Jam</th>
                        <th scope="col">Tahun Ajaran</th>
                        <th scope="col">Hadir</th>
                        <th scope="col">Sakit</th>
                        <th scope="col">Izin</th>
                        <th scope="col">Alpa</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record) : ?>
                        <tr>
                            <td><?= esc($record['tanggal']); ?></td>
                            <td><?= esc($record['kelas_nama']); ?></td>
                            <td><?= esc($record['mapel_nama']); ?></td>
                            <td><?= esc($record['guru_nama']); ?></td>
                            <td><?= esc($record['jam_ke']); ?> (<?= esc(substr($record['jam_mulai'], 0, 5)); ?>–<?= esc(substr($record['jam_selesai'], 0, 5)); ?>)</td>
                            <td><?= esc($record['tahun_ajaran_nama']); ?> - Semester <?= esc($record['semester'] ?? '-'); ?></td>
                            <td><?= (int) $record['jumlah_hadir']; ?></td>
                            <td><?= (int) $record['jumlah_sakit']; ?></td>
                            <td><?= (int) $record['jumlah_izin']; ?></td>
                            <td><?= (int) $record['jumlah_alpa']; ?></td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-success" href="<?= site_url('class-attendance/' . $record['absensi_kelas_id'] . '/pdf'); ?>" aria-label="Unduh PDF absensi siswa">
                                    <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>PDF
                                </a>
                                <a class="btn btn-sm btn-outline-info" href="<?= site_url('class-attendance/' . $record['absensi_kelas_id']); ?>">Detail</a>
                                <?php if ($canSendWhatsApp) : ?>
                                    <form class="d-inline" method="post" action="<?= site_url('class-attendance/' . $record['absensi_kelas_id'] . '/send-whatsapp'); ?>" onsubmit="return confirm('Kirim ulang notifikasi WhatsApp untuk siswa yang tidak hadir pada sesi mapel ini? Notifikasi yang sudah diterima Fonnte akan dilewati.');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-success" type="submit">
                                            <i class="bi bi-whatsapp me-1" aria-hidden="true"></i>Kirim Ulang WA
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('class-attendance/' . $record['absensi_kelas_id'] . '/edit'); ?>">Edit</a>
                                <?php endif; ?>
                                <?php if ($canDelete) : ?>
                                    <form class="d-inline" method="post" action="<?= site_url('class-attendance/delete/' . $record['absensi_kelas_id']); ?>" onsubmit="return confirm('Hapus sesi absensi beserta seluruh detail siswanya?');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($records === []) : ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">Belum ada absensi kelas untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
