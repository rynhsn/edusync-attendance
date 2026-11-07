<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <i class="bi bi-card-heading text-primary me-2"></i>Cetak Kartu Siswa
            </h1>
            <p class="text-muted mb-0">Kelola nomor peserta, atur format kartu asesmen / ujian siswa, dan cetak kartu massal maupun satuan.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($canSettings) : ?>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generateNumberModal">
                    <i class="bi bi-magic me-1"></i> Generate No. Peserta
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cardSettingsModal">
                    <i class="bi bi-gear-fill me-1"></i> Pengaturan Kartu
                </button>
            <?php endif; ?>
            <?php if ($canPrint) : ?>
                <button type="button" class="btn btn-primary" id="btn-print-selected" disabled>
                    <i class="bi bi-printer-fill me-1"></i> Cetak Terpilih (<span id="selected-count">0</span>)
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Overview Stats & Live Preview Row -->
    <div class="row g-4 mb-4">
        <!-- Stats & Quick Actions -->
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Siswa</div>
                                <div class="fs-4 fw-bold text-dark"><?= esc((string) $totalStudents); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-check2-circle fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Ada No. Peserta</div>
                                <div class="fs-4 fw-bold text-dark"><?= esc((string) $withNumber); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold text-dark"><i class="bi bi-info-circle me-1"></i> Informasi Kartu Aktif</span>
                                <?php if ($canSettings) : ?>
                                    <button class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#cardSettingsModal">Ubah</button>
                                <?php endif; ?>
                            </div>
                            <ul class="list-unstyled mb-0 small text-muted">
                                <li class="mb-1"><strong class="text-dark">Judul:</strong> <?= esc($settings['student_exam_card_title']); ?></li>
                                <li class="mb-1"><strong class="text-dark">Subjudul:</strong> <?= esc($settings['student_exam_card_subtitle']); ?></li>
                                <li class="mb-1"><strong class="text-dark">Titimangsa:</strong> <?= esc($settings['student_exam_card_city_date']); ?></li>
                                <li class="mb-1"><strong class="text-dark">Penandatangan:</strong> <?= esc($settings['student_exam_card_signer_name']); ?> (<?= esc($settings['student_exam_card_signer_role']); ?>)</li>
                                <li>
                                    <strong class="text-dark">Barcode TTD:</strong>
                                    <?php
                                        $bType = $settings['student_exam_card_barcode'] ?? 'none';
                                        if ($bType === 'smp') echo '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><i class="bi bi-qr-code me-1"></i>Barcode SMP</span>';
                                        elseif ($bType === 'smk') echo '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><i class="bi bi-qr-code me-1"></i>Barcode SMK</span>';
                                        elseif ($bType === 'custom') echo '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25"><i class="bi bi-qr-code me-1"></i>Barcode Kustom</span>';
                                        else echo '<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Tanpa Barcode (TTD Basah)</span>';
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Preview Card (Matching Image 1) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <h2 class="h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-eye text-primary me-1"></i> Pratinjau Desain Kartu (Sesuai Format Cetak)
                    </h2>
                    <span class="badge bg-light text-secondary border">Ukuran Skala Pratinjau</span>
                </div>
                <div class="card-body p-3 d-flex justify-content-center align-items-center bg-light bg-opacity-50">
                    <div class="exam-card-preview-container">
                        <div class="exam-card-box">
                            <!-- Header: Logo + Title -->
                            <div class="exam-card-header">
                                <div class="exam-card-logo-wrap">
                                    <img src="<?= esc($logoUrl, 'attr'); ?>" alt="Logo Sekolah" class="exam-card-logo">
                                </div>
                                <div class="exam-card-titles">
                                    <div class="exam-title-main"><?= esc($settings['student_exam_card_title']); ?></div>
                                    <div class="exam-title-sub"><?= esc($settings['student_exam_card_subtitle']); ?></div>
                                </div>
                            </div>

                            <!-- Double Line Divider (Kop Separator) -->
                            <div class="exam-card-divider"></div>

                            <!-- Student Identity Table -->
                            <table class="exam-card-table">
                                <tr>
                                    <td class="exam-label">No. Peserta</td>
                                    <td class="exam-sep">:</td>
                                    <td class="exam-val" id="preview-no-peserta"><?= esc(! empty($previewStudent['no_peserta']) ? $previewStudent['no_peserta'] : '035'); ?></td>
                                </tr>
                                <tr>
                                    <td class="exam-label">Nama</td>
                                    <td class="exam-sep">:</td>
                                    <td class="exam-val" id="preview-nama"><?= esc($previewStudent['nama_siswa'] ?? 'Azzahra Putriyani Muhtian'); ?></td>
                                </tr>
                                <tr>
                                    <td class="exam-label">Tempat, Tgl Lahir</td>
                                    <td class="exam-sep">:</td>
                                    <td class="exam-val" id="preview-ttl">
                                        <?= esc(\App\Controllers\StudentExamCardController::formatTtl($previewStudent['tempat_lahir'] ?? 'Tangerang', $previewStudent['tanggal_lahir'] ?? '2009-11-26')); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="exam-label">NIS</td>
                                    <td class="exam-sep">:</td>
                                    <td class="exam-val" id="preview-nis"><?= esc($previewStudent['nis'] ?? '22.23.134'); ?></td>
                                </tr>
                            </table>

                            <!-- Signature Block (Bottom Right) -->
                            <div class="exam-card-footer">
                                <div class="exam-signature-box">
                                    <div class="exam-sig-city-date"><?= esc($settings['student_exam_card_city_date']); ?></div>
                                    <div class="exam-sig-role"><?= esc($settings['student_exam_card_signer_role']); ?></div>
                                    <?php if (! empty($barcodeUrl)) : ?>
                                        <div class="exam-sig-barcode-wrap">
                                            <img src="<?= esc($barcodeUrl, 'attr'); ?>" alt="Barcode TTD" class="exam-sig-barcode">
                                        </div>
                                    <?php else : ?>
                                        <div class="exam-sig-space"></div>
                                    <?php endif; ?>
                                    <div class="exam-sig-name"><?= esc($settings['student_exam_card_signer_name']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-3 bg-white">
        <div class="card-body p-3 p-md-4">
            <!-- Filter Bar -->
            <form action="<?= site_url('student-exam-cards'); ?>" method="get" class="row g-2 align-items-center mb-3">
                <div class="col-md-4 col-sm-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-filter"></i></span>
                        <select name="kelas_id" class="form-select bg-light border-start-0" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($classes as $c) : ?>
                                <option value="<?= esc((string) $c['kelas_id']); ?>" <?= (string) $selectedClass === (string) $c['kelas_id'] ? 'selected' : ''; ?>>
                                    Kelas <?= esc($c['kelas_nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-5 col-sm-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="<?= esc($keyword); ?>" class="form-control bg-light border-start-0" placeholder="Cari nama siswa, NIS, atau no. peserta...">
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <?php if (! empty($selectedClass) || $keyword !== '') : ?>
                        <a href="<?= site_url('student-exam-cards'); ?>" class="btn btn-outline-secondary" title="Reset filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table of Students -->
            <form id="bulk-print-form" action="<?= site_url('student-exam-cards/print'); ?>" method="post" target="_blank">
                <?= csrf_field(); ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="student-table">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="check-all" title="Pilih semua siswa di tabel">
                                </th>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 140px;">No. Peserta</th>
                                <th style="width: 120px;">NIS</th>
                                <th>Nama Siswa</th>
                                <th style="width: 100px;">Kelas</th>
                                <th>Tempat, Tanggal Lahir</th>
                                <th style="width: 140px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)) : ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                        Tidak ada data siswa yang sesuai filter.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($students as $index => $s) : ?>
                                    <?php
                                        $ttlStr = \App\Controllers\StudentExamCardController::formatTtl($s['tempat_lahir'] ?? '', $s['tanggal_lahir'] ?? '');
                                    ?>
                                    <tr data-student-id="<?= esc((string) $s['siswa_id']); ?>"
                                        data-nama="<?= esc($s['nama_siswa']); ?>"
                                        data-nis="<?= esc($s['nis'] ?? '-'); ?>"
                                        data-ttl="<?= esc($ttlStr); ?>"
                                        data-no-peserta="<?= esc($s['no_peserta'] ?? ''); ?>">
                                        <td class="text-center">
                                            <input type="checkbox" name="selected_students[]" value="<?= esc((string) $s['siswa_id']); ?>" class="form-check-input student-checkbox">
                                        </td>
                                        <td class="text-center text-muted"><?= $index + 1; ?></td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                       class="form-control form-control-sm input-no-peserta"
                                                       data-id="<?= esc((string) $s['siswa_id']); ?>"
                                                       value="<?= esc($s['no_peserta'] ?? ''); ?>"
                                                       placeholder="cth: 035"
                                                       <?= ! $canSettings ? 'readonly' : ''; ?>>
                                                <?php if ($canSettings) : ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-save-no" data-id="<?= esc((string) $s['siswa_id']); ?>" title="Simpan no. peserta">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= esc($s['nis'] ?? '-'); ?></span></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($s['nama_siswa']); ?></div>
                                            <?php if (! empty($s['nisn'])) : ?>
                                                <small class="text-muted">NISN: <?= esc($s['nisn']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= esc($s['kelas'] ?? '-'); ?></span></td>
                                        <td class="small text-muted"><?= esc($ttlStr); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button"
                                                        class="btn btn-outline-info btn-preview-single"
                                                        data-student='<?= esc(json_encode([
                                                            'siswa_id'   => (int) $s['siswa_id'],
                                                            'nama_siswa' => $s['nama_siswa'],
                                                            'nis'        => $s['nis'] ?? '-',
                                                            'no_peserta' => $s['no_peserta'] ?? '-',
                                                            'ttl'        => $ttlStr,
                                                            'kelas'      => $s['kelas'] ?? '-',
                                                        ]), 'attr'); ?>'
                                                        title="Lihat Pratinjau Kartu">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <?php if ($canPrint) : ?>
                                                    <a href="<?= site_url('student-exam-cards/print/' . (int) $s['siswa_id']); ?>"
                                                       target="_blank"
                                                       class="btn btn-outline-primary"
                                                       title="Cetak Kartu Siswa Ini">
                                                        <i class="bi bi-printer"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Exact styling matching Image 1 for card preview */
.exam-card-preview-container {
    width: 100%;
    max-width: 540px;
    padding: 10px;
}
.exam-card-box {
    background: #ffffff;
    border: 2px solid #000000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 16px 20px;
    width: 100%;
    min-height: 330px;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    color: #000000;
}
.exam-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
}
.exam-card-logo-wrap {
    flex-shrink: 0;
    width: 60px;
    text-align: center;
}
.exam-card-logo {
    max-width: 58px;
    max-height: 58px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}
.exam-card-titles {
    flex-grow: 1;
    text-align: center;
}
.exam-title-main {
    font-size: 15px;
    font-weight: 900;
    letter-spacing: 0.3px;
    line-height: 1.25;
    text-transform: uppercase;
    color: #000000;
    margin-bottom: 3px;
}
.exam-title-sub {
    font-size: 14px;
    font-weight: 900;
    letter-spacing: 0.5px;
    line-height: 1.25;
    text-transform: uppercase;
    color: #000000;
}
/* Double horizontal line: thick top, thin bottom */
.exam-card-divider {
    border-top: 2.5px solid #000000;
    border-bottom: 1px solid #000000;
    height: 3px;
    margin-top: 10px;
    margin-bottom: 14px;
    box-sizing: border-box;
}
.exam-card-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    line-height: 1.4;
    color: #000000;
    margin-bottom: 8px;
}
.exam-card-table td {
    padding: 3px 0;
    vertical-align: top;
}
.exam-label {
    width: 125px;
    font-weight: 700;
    white-space: nowrap;
}
.exam-sep {
    width: 18px;
    font-weight: 700;
    text-align: center;
}
.exam-val {
    font-weight: 700;
    word-break: break-word;
}
.exam-card-footer {
    margin-top: auto;
    display: flex;
    justify-content: flex-end;
    padding-top: 8px;
}
.exam-signature-box {
    min-width: 210px;
    text-align: left;
    font-size: 12.5px;
    font-weight: 700;
    color: #000000;
    line-height: 1.35;
}
.exam-sig-city-date {
    margin-bottom: 2px;
}
.exam-sig-space {
    height: 44px;
}
.exam-sig-barcode-wrap {
    display: flex;
    align-items: center;
    min-height: 48px;
    padding: 2px 0;
}
.exam-sig-barcode {
    max-height: 46px;
    max-width: 46px;
    object-fit: contain;
    display: block;
}
.exam-sig-name {
    font-weight: 700;
}
</style>
<?= $this->endSection(); ?>

<?= $this->section('modals'); ?>
<!-- Modal Pengaturan Kartu -->
<div class="modal fade" id="cardSettingsModal" tabindex="-1" aria-labelledby="cardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('student-exam-cards/settings'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="cardSettingsModalLabel">
                        <i class="bi bi-gear-fill me-2"></i>Pengaturan Format Kartu Ujian Siswa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Judul Kartu (Header Baris 1)</label>
                            <input type="text" name="student_exam_card_title" class="form-control" value="<?= esc($settings['student_exam_card_title']); ?>" required>
                            <div class="form-text">Contoh: <code>KARTU ASESMENT SUMATIF AKHIR TAHUN</code></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Subjudul / Tahun Pelajaran (Header Baris 2)</label>
                            <input type="text" name="student_exam_card_subtitle" class="form-control" value="<?= esc($settings['student_exam_card_subtitle']); ?>" required>
                            <div class="form-text">Contoh: <code>TAHUN PELAJARAN 2022/2023</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tempat & Tanggal Titimangsa</label>
                            <input type="text" name="student_exam_card_city_date" class="form-control" value="<?= esc($settings['student_exam_card_city_date']); ?>" required>
                            <div class="form-text">Contoh: <code>Pandeglang, 03 Juni 2023</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jabatan Penandatangan</label>
                            <input type="text" name="student_exam_card_signer_role" class="form-control" value="<?= esc($settings['student_exam_card_signer_role']); ?>" required>
                            <div class="form-text">Contoh: <code>Kepala Sekolah</code></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Nama Penandatangan</label>
                            <input type="text" name="student_exam_card_signer_name" class="form-control" value="<?= esc($settings['student_exam_card_signer_name']); ?>" required>
                            <div class="form-text">Contoh: <code>Uswatun Hasanah, S.Pd</code></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan Logo Sekolah</label>
                            <select name="student_exam_card_logo" id="select-logo-type" class="form-select">
                                <option value="smp" <?= ($settings['student_exam_card_logo'] ?? 'smp') === 'smp' ? 'selected' : ''; ?>>Logo SMP (Default Hijau Miftahul Huda)</option>
                                <option value="smk" <?= ($settings['student_exam_card_logo'] ?? '') === 'smk' ? 'selected' : ''; ?>>Logo SMK</option>
                                <option value="custom" <?= ($settings['student_exam_card_logo'] ?? '') === 'custom' ? 'selected' : ''; ?>>Upload Logo Kustom Sendiri</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilihan TTD Barcode (QR Code)</label>
                            <select name="student_exam_card_barcode" id="select-barcode-type" class="form-select">
                                <option value="none" <?= ($settings['student_exam_card_barcode'] ?? 'none') === 'none' ? 'selected' : ''; ?>>Tanpa Barcode (Ruang Kosong untuk TTD Basah)</option>
                                <option value="smp" <?= ($settings['student_exam_card_barcode'] ?? '') === 'smp' ? 'selected' : ''; ?>>Barcode TTD SMP (Logo SMP Miftahul Huda)</option>
                                <option value="smk" <?= ($settings['student_exam_card_barcode'] ?? '') === 'smk' ? 'selected' : ''; ?>>Barcode TTD SMK (Logo SMK Miftahul Huda)</option>
                                <option value="custom" <?= ($settings['student_exam_card_barcode'] ?? '') === 'custom' ? 'selected' : ''; ?>>Upload Barcode Kustom Sendiri</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="custom-barcode-upload-group" style="<?= ($settings['student_exam_card_barcode'] ?? '') === 'custom' ? '' : 'display: none;'; ?>">
                            <label class="form-label fw-bold">Upload File Barcode Baru (Opsional)</label>
                            <input type="file" name="custom_barcode_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text">Format PNG transparan atau JPG, maksimal 2MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Format Layout Cetak</label>
                            <select name="student_exam_card_layout" class="form-select">
                                <option value="landscape_4" <?= ($settings['student_exam_card_layout'] ?? 'landscape_4') === 'landscape_4' ? 'selected' : ''; ?>>4 Kartu per Lembar A4 (Landscape 2x2 - Hemat & Standar)</option>
                                <option value="landscape_2" <?= ($settings['student_exam_card_layout'] ?? '') === 'landscape_2' ? 'selected' : ''; ?>>2 Kartu per Lembar A4 (Landscape Side-by-side)</option>
                                <option value="portrait_2" <?= ($settings['student_exam_card_layout'] ?? '') === 'portrait_2' ? 'selected' : ''; ?>>2 Kartu per Lembar A4 (Portrait Atas-Bawah)</option>
                                <option value="single" <?= ($settings['student_exam_card_layout'] ?? '') === 'single' ? 'selected' : ''; ?>>1 Kartu per Halaman (Ukuran Penuh)</option>
                            </select>
                        </div>
                        <div class="col-12" id="custom-logo-upload-group" style="<?= ($settings['student_exam_card_logo'] ?? '') === 'custom' ? '' : 'display: none;'; ?>">
                            <label class="form-label fw-bold">Upload File Logo Baru (Opsional)</label>
                            <input type="file" name="custom_logo_file" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text">Format PNG transparan atau JPG, ukuran maksimal 2MB.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generate Nomor Peserta -->
<div class="modal fade" id="generateNumberModal" tabindex="-1" aria-labelledby="generateNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= site_url('student-exam-cards/generate-numbers'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="generateNumberModalLabel">
                        <i class="bi bi-magic me-2"></i>Generate Nomor Peserta Otomatis
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Target Kelas</label>
                            <select name="kelas_id" class="form-select">
                                <option value="all">-- Semua Kelas --</option>
                                <?php foreach ($classes as $c) : ?>
                                    <option value="<?= esc((string) $c['kelas_id']); ?>" <?= (string) $selectedClass === (string) $c['kelas_id'] ? 'selected' : ''; ?>>
                                        Kelas <?= esc($c['kelas_nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Awalan / Prefix (Opsional)</label>
                            <input type="text" name="prefix" class="form-control" placeholder="cth: kosongkan atau 0">
                            <div class="form-text">Biarkan kosong jika tidak perlu.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Digit Angka</label>
                            <select name="digits" class="form-select">
                                <option value="3" selected>3 Digit (cth: 001, 002, 035)</option>
                                <option value="2">2 Digit (cth: 01, 02, 35)</option>
                                <option value="4">4 Digit (cth: 0001, 0035)</option>
                                <option value="1">Tanpa digit nol (cth: 1, 2, 35)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor Mulai</label>
                            <input type="number" name="start_number" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Urutkan Siswa</label>
                            <select name="order_by" class="form-select">
                                <option value="nama_siswa" selected>Nama Siswa (A - Z)</option>
                                <option value="nis">NIS Siswa</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="overwrite" value="1" id="check-overwrite">
                                <label class="form-check-label fw-bold text-danger" for="check-overwrite">
                                    Timpa nomor peserta yang sudah ada
                                </label>
                            </div>
                            <div class="form-text">Jika tidak dicentang, hanya siswa yang belum memiliki nomor peserta yang akan diisi.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play-fill me-1"></i> Mulai Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Pratinjau Kartu Satuan -->
<div class="modal fade" id="singlePreviewModal" tabindex="-1" aria-labelledby="singlePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="singlePreviewModalLabel">
                    <i class="bi bi-card-heading me-2"></i>Pratinjau Kartu Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light d-flex justify-content-center">
                <div class="exam-card-preview-container" style="max-width: 520px;">
                    <div class="exam-card-box">
                        <div class="exam-card-header">
                            <div class="exam-card-logo-wrap">
                                <img src="<?= esc($logoUrl, 'attr'); ?>" alt="Logo Sekolah" class="exam-card-logo">
                            </div>
                            <div class="exam-card-titles">
                                <div class="exam-title-main"><?= esc($settings['student_exam_card_title']); ?></div>
                                <div class="exam-title-sub"><?= esc($settings['student_exam_card_subtitle']); ?></div>
                            </div>
                        </div>

                        <div class="exam-card-divider"></div>

                        <table class="exam-card-table">
                            <tr>
                                <td class="exam-label">No. Peserta</td>
                                <td class="exam-sep">:</td>
                                <td class="exam-val" id="modal-val-no-peserta">-</td>
                            </tr>
                            <tr>
                                <td class="exam-label">Nama</td>
                                <td class="exam-sep">:</td>
                                <td class="exam-val" id="modal-val-nama">-</td>
                            </tr>
                            <tr>
                                <td class="exam-label">Tempat, Tgl Lahir</td>
                                <td class="exam-sep">:</td>
                                <td class="exam-val" id="modal-val-ttl">-</td>
                            </tr>
                            <tr>
                                <td class="exam-label">NIS</td>
                                <td class="exam-sep">:</td>
                                <td class="exam-val" id="modal-val-nis">-</td>
                            </tr>
                        </table>

                        <div class="exam-card-footer">
                            <div class="exam-signature-box">
                                <div class="exam-sig-city-date"><?= esc($settings['student_exam_card_city_date']); ?></div>
                                <div class="exam-sig-role"><?= esc($settings['student_exam_card_signer_role']); ?></div>
                                <?php if (! empty($barcodeUrl)) : ?>
                                    <div class="exam-sig-barcode-wrap">
                                        <img src="<?= esc($barcodeUrl, 'attr'); ?>" alt="Barcode TTD" class="exam-sig-barcode">
                                    </div>
                                <?php else : ?>
                                    <div class="exam-sig-space"></div>
                                <?php endif; ?>
                                <div class="exam-sig-name"><?= esc($settings['student_exam_card_signer_name']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="modal-btn-print" target="_blank" class="btn btn-primary">
                    <i class="bi bi-printer-fill me-1"></i> Cetak Kartu Ini
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle custom logo upload field
    const selectLogo = document.getElementById('select-logo-type');
    const customGroup = document.getElementById('custom-logo-upload-group');
    if (selectLogo && customGroup) {
        selectLogo.addEventListener('change', function () {
            customGroup.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    }

    // Toggle custom barcode upload field
    const selectBarcode = document.getElementById('select-barcode-type');
    const customBarcodeGroup = document.getElementById('custom-barcode-upload-group');
    if (selectBarcode && customBarcodeGroup) {
        selectBarcode.addEventListener('change', function () {
            customBarcodeGroup.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    }

    // Checkbox select all & count
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const btnPrintSelected = document.getElementById('btn-print-selected');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-print-form');

    function updateSelectionState() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        const count = checked.length;
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        if (btnPrintSelected) btnPrintSelected.disabled = count === 0;

        if (checkAll) {
            checkAll.checked = count > 0 && count === checkboxes.length;
            checkAll.indeterminate = count > 0 && count < checkboxes.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => { cb.checked = checkAll.checked; });
            updateSelectionState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionState);
    });

    if (btnPrintSelected && bulkForm) {
        btnPrintSelected.addEventListener('click', function () {
            bulkForm.submit();
        });
    }

    // Inline save participant number
    const saveNumberUrl = '<?= site_url('student-exam-cards/save-number'); ?>';
    const csrfToken = '<?= csrf_hash(); ?>';
    const csrfHeader = '<?= csrf_token(); ?>';

    function saveStudentNumber(studentId, inputElem) {
        const noVal = inputElem.value.trim();
        const originalBg = inputElem.style.backgroundColor;
        inputElem.style.backgroundColor = '#fff3cd';

        const formData = new FormData();
        formData.append('siswa_id', studentId);
        formData.append('no_peserta', noVal);
        formData.append('<?= csrf_token(); ?>', '<?= csrf_hash(); ?>');

        fetch(saveNumberUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                inputElem.style.backgroundColor = '#d1e7dd';
                setTimeout(() => { inputElem.style.backgroundColor = ''; }, 1200);

                // Update row dataset
                const row = inputElem.closest('tr');
                if (row) {
                    row.setAttribute('data-no-peserta', noVal);
                    // Also update preview if it's the previewed student
                    const previewNo = document.getElementById('preview-no-peserta');
                    if (previewNo && row.getAttribute('data-student-id') === '<?= esc((string) ($previewStudent['siswa_id'] ?? 0)); ?>') {
                        previewNo.textContent = noVal || '-';
                    }
                }
            } else {
                inputElem.style.backgroundColor = '#f8d7da';
                alert(data.message || 'Gagal menyimpan nomor peserta.');
            }
        })
        .catch(err => {
            inputElem.style.backgroundColor = '#f8d7da';
            console.error(err);
        });
    }

    document.querySelectorAll('.btn-save-no').forEach(btn => {
        btn.addEventListener('click', function () {
            const studentId = this.getAttribute('data-id');
            const input = document.querySelector(`.input-no-peserta[data-id="${studentId}"]`);
            if (input) saveStudentNumber(studentId, input);
        });
    });

    document.querySelectorAll('.input-no-peserta').forEach(input => {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const studentId = this.getAttribute('data-id');
                saveStudentNumber(studentId, this);
            }
        });
    });

    // Preview Single Student Modal
    const singleModal = new bootstrap.Modal(document.getElementById('singlePreviewModal'));
    const modalValNo = document.getElementById('modal-val-no-peserta');
    const modalValNama = document.getElementById('modal-val-nama');
    const modalValTtl = document.getElementById('modal-val-ttl');
    const modalValNis = document.getElementById('modal-val-nis');
    const modalBtnPrint = document.getElementById('modal-btn-print');

    document.querySelectorAll('.btn-preview-single').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.getAttribute('data-student'));
            // Fetch live value of no_peserta from input if edited
            const input = document.querySelector(`.input-no-peserta[data-id="${data.siswa_id}"]`);
            const liveNo = input ? input.value.trim() : data.no_peserta;

            modalValNo.textContent = liveNo || '-';
            modalValNama.textContent = data.nama_siswa || '-';
            modalValTtl.textContent = data.ttl || '-';
            modalValNis.textContent = data.nis || '-';
            modalBtnPrint.href = '<?= site_url('student-exam-cards/print/'); ?>' + data.siswa_id;

            singleModal.show();
        });
    });
});
</script>
<?= $this->endSection(); ?>
