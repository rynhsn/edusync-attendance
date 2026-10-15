<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Laporan Absensi Ekstrakurikuler</h1>
            <p class="text-muted mb-0">Lihat dan saring riwayat kehadiran siswa pada kegiatan ekstrakurikuler.</p>
        </div>
        <a class="btn btn-outline-primary" href="<?= site_url('extracurricular-attendance'); ?>">
            <i class="bi bi-clipboard-check me-2" aria-hidden="true"></i>Catat Absensi
        </a>
    </div>

    <?php if ($filterError !== null) : ?><div class="alert alert-danger" role="alert"><?= esc($filterError); ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Unduh Laporan Bulanan per Siswa</h2></div>
        <div class="card-body">
            <form method="get" action="<?= site_url('report/extracurricular-attendance/pdf'); ?>" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label" for="pdfStudent">Pilih Siswa (Nama / NIS)</label>
                    <input class="form-control" id="pdfStudent" type="search" list="pdfStudentOptions" placeholder="Ketik nama atau NIS siswa" autocomplete="off" required>
                    <input id="pdfStudentId" name="siswa_id" type="hidden">
                    <datalist id="pdfStudentOptions">
                        <?php foreach ($students as $student) : ?>
                            <option value="<?= esc($student['nama_siswa'] . (! empty($student['nis']) ? ' - ' . $student['nis'] : '')); ?>" data-student-id="<?= esc($student['siswa_id']); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="pdfMonth">Bulan</label>
                    <input class="form-control" id="pdfMonth" name="bulan" type="month" value="<?= esc(date('Y-m')); ?>" required>
                </div>
                <div class="col-md-3 d-grid">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Data Kehadiran</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($attendance); ?> catatan</span>
        </div>
        <div class="card-body border-bottom">
            <form method="get" action="<?= site_url('report/extracurricular-attendance'); ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label" for="filterFrom">Tanggal Mulai</label>
                    <input class="form-control" id="filterFrom" name="mulai" type="date" value="<?= esc($filterFrom); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filterTo">Tanggal Akhir</label>
                    <input class="form-control" id="filterTo" name="akhir" type="date" value="<?= esc($filterTo); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filterActivity">Ekstrakurikuler</label>
                    <input class="form-control" id="filterActivity" name="kegiatan" value="<?= esc($filterActivity); ?>" placeholder="Nama kegiatan">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filterStudent">Nama Siswa</label>
                    <input class="form-control" id="filterStudent" name="siswa" value="<?= esc($filterStudent); ?>" placeholder="Nama siswa">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filterClass">Kelas</label>
                    <select class="form-select" id="filterClass" name="kelas_filter">
                        <option value="">Semua kelas</option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= esc($class['kelas_id']); ?>" <?= $filterClassId === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                <?= esc(($class['kelas_tingkat'] ?? '') . ' - ' . $class['kelas_nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>Filter</button>
                    <a class="btn btn-outline-secondary" href="<?= site_url('report/extracurricular-attendance'); ?>">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>No.</th><th>Tanggal</th><th>Ekstrakurikuler</th><th>Nama Siswa</th><th>Kelas</th><th>Status</th><th>Keterangan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($row['tanggal']); ?></td>
                            <td class="fw-medium"><?= esc($row['nama_kegiatan']); ?></td>
                            <td><?= esc($row['nama_siswa'] ?? 'Siswa tidak ditemukan'); ?></td>
                            <td><?= esc($row['kelas_nama'] ?? '-'); ?></td>
                            <td>
                                <?php $statusColor = ['hadir' => 'success', 'sakit' => 'warning', 'izin' => 'info', 'alpa' => 'danger'][$row['status']] ?? 'secondary'; ?>
                                <span class="badge bg-<?= esc($statusColor); ?>-subtle text-<?= esc($statusColor); ?>"><?= esc($statuses[$row['status']] ?? $row['status']); ?></span>
                            </td>
                            <td><?= esc($row['keterangan'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($attendance === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data absensi ekstrakurikuler untuk filter ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
(() => {
    const studentInput = document.getElementById('pdfStudent');
    const studentIdInput = document.getElementById('pdfStudentId');
    const studentOptions = document.getElementById('pdfStudentOptions');
    if (!studentInput || !studentIdInput || !studentOptions) return;

    const syncStudentId = () => {
        const selectedOption = Array.from(studentOptions.options).find((option) => option.value === studentInput.value);
        studentIdInput.value = selectedOption?.dataset.studentId ?? '';
        studentInput.setCustomValidity(studentIdInput.value ? '' : 'Pilih siswa dari daftar nama atau NIS.');
    };

    studentInput.addEventListener('input', syncStudentId);
    studentInput.form?.addEventListener('submit', (event) => {
        syncStudentId();
        if (!studentIdInput.value) {
            event.preventDefault();
            studentInput.reportValidity();
        }
    });
})();
</script>
<?= $this->endSection(); ?>
