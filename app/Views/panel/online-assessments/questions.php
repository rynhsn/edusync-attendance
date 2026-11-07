<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <!-- Breadcrumb & Back -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('online-assessments'); ?>">Ujian Online</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Soal</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1"><?= esc($ujian['judul']); ?></h1>
            <p class="text-muted mb-0">
                <i class="bi bi-book me-1"></i><?= esc($ujian['mapel_nama'] ?: 'Semua Mapel'); ?> &bull; 
                <i class="bi bi-door-open me-1"></i><?= esc($ujian['kelas_nama'] ?: 'Semua Kelas'); ?> &bull;
                <i class="bi bi-clock me-1"></i><?= (int) $ujian['durasi_menit']; ?> Menit
            </p>
            <div class="d-flex align-items-center gap-2 mt-2">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning fs-6 px-3 py-1">
                    <i class="bi bi-key-fill me-1"></i> Token Ujian: <strong class="font-monospace fs-6 ms-1"><?= esc($ujian['token'] ?: '-'); ?></strong>
                </span>
                <?php if (! empty($ujian['token'])) : ?>
                    <button type="button" class="btn btn-sm btn-outline-warning border text-dark btn-copy-token" data-token="<?= esc($ujian['token']); ?>" title="Salin Token Ujian">
                        <i class="bi bi-clipboard me-1"></i> Salin Token
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= site_url('online-assessments'); ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/results'); ?>" class="btn btn-outline-success d-inline-flex align-items-center gap-1">
                <i class="bi bi-trophy"></i> Hasil & Nilai
            </a>
            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#importQuestionsModal">
                <i class="bi bi-file-earmark-arrow-up"></i> Import Soal
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-dark dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel / CSV
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li class="dropdown-header small text-uppercase fw-bold text-muted">Unduh Template</li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/template?format=xlsx'); ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Template Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/template?format=csv'); ?>">
                            <i class="bi bi-file-earmark-text text-primary"></i> Template CSV (.csv)
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header small text-uppercase fw-bold text-muted">Export Soal Ujian</li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/export?format=xlsx'); ?>">
                            <i class="bi bi-download text-success"></i> Export ke Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/export?format=csv'); ?>">
                            <i class="bi bi-download text-primary"></i> Export ke CSV (.csv)
                        </a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addPgModal">
                <i class="bi bi-plus-circle"></i> + Soal Pilihan Ganda
            </button>
            <button type="button" class="btn btn-info text-white d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addEssayModal">
                <i class="bi bi-card-text"></i> + Soal Essay
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (! empty($message)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Box -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Total Butir Soal</div>
                        <div class="h4 mb-0 fw-bold text-dark"><?= count($questions); ?> Soal</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-5">
                        <i class="bi bi-journal-check"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Soal Pilihan Ganda</div>
                        <div class="h4 mb-0 fw-bold text-success">
                            <?= count(array_filter($questions, fn($q) => $q['tipe_soal'] === 'pilihan_ganda')); ?> Soal
                        </div>
                    </div>
                    <span class="badge bg-success-subtle text-success p-3 rounded-circle fs-5">
                        <i class="bi bi-ui-checks"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Soal Essay / Uraian</div>
                        <div class="h4 mb-0 fw-bold text-info">
                            <?= count(array_filter($questions, fn($q) => $q['tipe_soal'] === 'essay')); ?> Soal
                        </div>
                    </div>
                    <span class="badge bg-info-subtle text-info p-3 rounded-circle fs-5">
                        <i class="bi bi-textarea-t"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0 fw-semibold">Daftar Butir Soal Ujian</h2>
            <span class="badge bg-light text-secondary border"><?= count($questions); ?> butir soal</span>
        </div>
        <div class="card-body p-4">
            <?php if ($questions === []) : ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-plus fs-1 text-muted opacity-50 d-block mb-3"></i>
                    <h5 class="fw-semibold">Belum Ada Soal Ditambahkan</h5>
                    <p class="text-muted mb-4">Tambahkan soal pilihan ganda atau essay untuk memulai ujian ini.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPgModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Pilihan Ganda
                        </button>
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#addEssayModal">
                            <i class="bi bi-card-text me-1"></i> Tambah Essay
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importQuestionsModal">
                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Soal (Excel/CSV)
                        </button>
                        <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/template?format=xlsx'); ?>" class="btn btn-light border">
                            <i class="bi bi-download me-1 text-success"></i> Unduh Template
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($questions as $idx => $q) : ?>
                        <div class="border rounded-3 p-3 p-md-4 bg-white position-relative shadow-2xs hover-shadow transition">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary px-3 py-2 fs-6">No. <?= $idx + 1; ?></span>
                                    <?php if ($q['tipe_soal'] === 'pilihan_ganda') : ?>
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25">
                                            <i class="bi bi-ui-checks me-1"></i>Pilihan Ganda
                                        </span>
                                        <span class="badge bg-success text-white px-2 py-1">
                                            <i class="bi bi-key-fill me-1"></i>Kunci: <?= esc($q['kunci_jawaban']); ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-info-subtle text-info border border-info border-opacity-25">
                                            <i class="bi bi-card-text me-1"></i>Essay / Uraian
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge bg-light text-muted border">
                                        Bobot: <?= (float) $q['bobot_nilai']; ?> poin
                                    </span>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary btn-edit-question"
                                        data-bs-toggle="modal" data-bs-target="#editQuestionModal"
                                        data-question="<?= esc(json_encode($q), 'attr'); ?>" title="Edit Soal">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                    <form method="post" action="<?= site_url('online-assessments/questions/delete/' . $q['soal_id']); ?>" class="d-inline" onsubmit="return confirm('Hapus soal nomor <?= $idx + 1; ?> ini?');">
                                        <?= csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Soal">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-3 fs-6 text-dark" style="white-space: pre-line;"><?= esc($q['pertanyaan']); ?></div>

                            <!-- Options if Multiple Choice -->
                            <?php if ($q['tipe_soal'] === 'pilihan_ganda') : ?>
                                <div class="row g-2">
                                    <?php 
                                    $options = [
                                        'A' => $q['opsi_a'],
                                        'B' => $q['opsi_b'],
                                        'C' => $q['opsi_c'],
                                        'D' => $q['opsi_d'],
                                        'E' => $q['opsi_e'],
                                    ];
                                    foreach ($options as $optKey => $optVal) :
                                        if (empty($optVal) && $optKey === 'E') continue;
                                        $isKey = strtoupper(trim((string) $q['kunci_jawaban'])) === $optKey;
                                    ?>
                                        <div class="col-md-6">
                                            <div class="p-2 px-3 rounded-2 border d-flex align-items-center gap-2 <?= $isKey ? 'bg-success-subtle border-success text-success fw-bold' : 'bg-light text-dark'; ?>">
                                                <span class="badge <?= $isKey ? 'bg-success text-white' : 'bg-secondary text-white'; ?>"><?= $optKey; ?></span>
                                                <span class="flex-grow-1"><?= esc($optVal ?: '-'); ?></span>
                                                <?php if ($isKey) : ?>
                                                    <i class="bi bi-check-circle-fill text-success" title="Kunci Jawaban Benar"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('modals'); ?>
<!-- Modal Import Soal -->
<div class="modal fade" id="importQuestionsModal" tabindex="-1" aria-labelledby="importQuestionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/import'); ?>" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="importQuestionsModalLabel">
                        <i class="bi bi-file-earmark-excel text-success"></i> Import Soal Pilihan Ganda & Essay
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Template Download Box -->
                    <div class="p-3 bg-light border rounded-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="bi bi-cloud-arrow-down text-primary me-1"></i>Belum punya format template?</h6>
                                <p class="small text-muted mb-0">Unduh template contoh soal (pilihan ganda dan essay sudah termasuk dalam 1 file):</p>
                            </div>
                            <div class="btn-group">
                                <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/template?format=xlsx'); ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Template Excel (.xlsx)
                                </a>
                                <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions/template?format=csv'); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-text me-1"></i> Template CSV (.csv)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk Pengisian -->
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i>Petunjuk Format Kolom:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>Kolom tipe_soal</strong>: Isi <code>PG</code> untuk Pilihan Ganda atau <code>ESSAY</code> untuk Essay/Uraian.</li>
                            <li><strong>Kolom pertanyaan</strong>: Teks butir soal yang diujikan.</li>
                            <li><strong>Kolom opsi_a s/d opsi_e</strong>: Diisi untuk soal PG. Kosongkan untuk soal Essay.</li>
                            <li><strong>Kolom kunci_jawaban</strong>: Huruf kunci (<code>A</code>, <code>B</code>, <code>C</code>, <code>D</code>, atau <code>E</code>) untuk PG. Untuk Essay, dapat diisi panduan jawaban/kata kunci.</li>
                            <li><strong>Kolom bobot_nilai</strong>: Angka bobot per butir soal (contoh: <code>1</code> atau <code>5</code>).</li>
                        </ul>
                    </div>

                    <!-- File Input -->
                    <div class="mb-3">
                        <label for="questions_file" class="form-label fw-bold">Pilih File Spreadsheet <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="questions_file" name="questions_file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Mendukung format file <strong>.xlsx</strong>, <strong>.xls</strong>, atau <strong>.csv</strong> (Maksimal 10 MB).</div>
                    </div>

                    <!-- Checkbox Truncate Existing -->
                    <div class="form-check p-3 bg-warning-subtle border border-warning border-opacity-25 rounded-2">
                        <input class="form-check-input" type="checkbox" id="truncate_existing" name="truncate_existing" value="1">
                        <label class="form-check-label fw-semibold text-warning-emphasis" for="truncate_existing">
                            Hapus semua soal yang sudah ada di ujian ini sebelum mengimpor (Ganti Semua Soal)
                        </label>
                        <div class="small text-muted ms-1">Jika tidak dicentang, butir soal baru dari file akan ditambahkan di urutan terakhir.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Unggah & Impor Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Multiple Choice -->
<div class="modal fade" id="addPgModal" tabindex="-1" aria-labelledby="addPgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="tipe_soal" value="pilihan_ganda">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addPgModalLabel">
                        <i class="bi bi-ui-checks text-primary"></i> Tambah Soal Pilihan Ganda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="pg_pertanyaan" class="form-label">Teks Pertanyaan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pg_pertanyaan" name="pertanyaan" rows="3" required placeholder="Tuliskan teks pertanyaan di sini..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="fw-bold mb-2">Pilihan Jawaban (A - E):</div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text fw-bold">A</span>
                                <input type="text" class="form-control" name="opsi_a" required placeholder="Teks pilihan A...">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text fw-bold">B</span>
                                <input type="text" class="form-control" name="opsi_b" required placeholder="Teks pilihan B...">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text fw-bold">C</span>
                                <input type="text" class="form-control" name="opsi_c" required placeholder="Teks pilihan C...">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text fw-bold">D</span>
                                <input type="text" class="form-control" name="opsi_d" required placeholder="Teks pilihan D...">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="input-group">
                                <span class="input-group-text fw-bold">E</span>
                                <input type="text" class="form-control" name="opsi_e" placeholder="Teks pilihan E (Opsional)...">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="pg_kunci_jawaban" class="form-label">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                            <select class="form-select border-success" id="pg_kunci_jawaban" name="kunci_jawaban" required>
                                <option value="" disabled selected>-- Pilih Kunci Jawaban Benar --</option>
                                <option value="A">Pilihan A</option>
                                <option value="B">Pilihan B</option>
                                <option value="C">Pilihan C</option>
                                <option value="D">Pilihan D</option>
                                <option value="E">Pilihan E</option>
                            </select>
                            <div class="form-text text-success">Jawaban ini yang akan dicocokkan secara otomatis oleh sistem.</div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label for="pg_bobot_nilai" class="form-label">Bobot Poin Soal <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" class="form-control" id="pg_bobot_nilai" name="bobot_nilai" value="1.0" min="0.1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Soal Pilihan Ganda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Essay -->
<div class="modal fade" id="addEssayModal" tabindex="-1" aria-labelledby="addEssayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="tipe_soal" value="essay">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addEssayModalLabel">
                        <i class="bi bi-card-text text-info"></i> Tambah Soal Essay / Uraian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="essay_pertanyaan" class="form-label">Teks Pertanyaan Essay <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="essay_pertanyaan" name="pertanyaan" rows="4" required placeholder="Tuliskan pertanyaan essay yang membutuhkan jawaban uraian dari siswa..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="essay_bobot_nilai" class="form-label">Bobot Nilai Maksimal <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" class="form-control" id="essay_bobot_nilai" name="bobot_nilai" value="10.0" min="0.5" required>
                            <div class="form-text">Nilai maksimal yang dapat diberikan guru saat mengoreksi jawaban essay ini.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save me-1"></i> Simpan Soal Essay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Question -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editQuestionForm" method="post" action="">
                <?= csrf_field(); ?>
                <input type="hidden" id="edit_tipe_soal" name="tipe_soal" value="pilihan_ganda">
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="editQuestionModalLabel">
                        <i class="bi bi-pencil-square text-primary"></i> Edit Butir Soal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_pertanyaan" class="form-label">Teks Pertanyaan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_pertanyaan" name="pertanyaan" rows="3" required></textarea>
                        </div>

                        <div id="edit_pg_options_container" class="col-12">
                            <div class="fw-bold mb-2">Pilihan Jawaban (A - E):</div>
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">A</span>
                                        <input type="text" class="form-control" id="edit_opsi_a" name="opsi_a">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">B</span>
                                        <input type="text" class="form-control" id="edit_opsi_b" name="opsi_b">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">C</span>
                                        <input type="text" class="form-control" id="edit_opsi_c" name="opsi_c">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">D</span>
                                        <input type="text" class="form-control" id="edit_opsi_d" name="opsi_d">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">E</span>
                                        <input type="text" class="form-control" id="edit_opsi_e" name="opsi_e">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="edit_kunci_container">
                            <label for="edit_kunci_jawaban" class="form-label">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                            <select class="form-select border-success" id="edit_kunci_jawaban" name="kunci_jawaban">
                                <option value="A">Pilihan A</option>
                                <option value="B">Pilihan B</option>
                                <option value="C">Pilihan C</option>
                                <option value="D">Pilihan D</option>
                                <option value="E">Pilihan E</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_bobot_nilai" class="form-label">Bobot Poin Soal <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" class="form-control" id="edit_bobot_nilai" name="bobot_nilai" min="0.1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModalEl = document.getElementById('editQuestionModal');
    if (!editModalEl) return;

    const editForm = document.getElementById('editQuestionForm');
    const inputPertanyaan = document.getElementById('edit_pertanyaan');
    const inputTipe = document.getElementById('edit_tipe_soal');
    const inputOpsiA = document.getElementById('edit_opsi_a');
    const inputOpsiB = document.getElementById('edit_opsi_b');
    const inputOpsiC = document.getElementById('edit_opsi_c');
    const inputOpsiD = document.getElementById('edit_opsi_d');
    const inputOpsiE = document.getElementById('edit_opsi_e');
    const selectKunci = document.getElementById('edit_kunci_jawaban');
    const inputBobot = document.getElementById('edit_bobot_nilai');
    const pgOptionsContainer = document.getElementById('edit_pg_options_container');
    const kunciContainer = document.getElementById('edit_kunci_container');

    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-edit-question');
        if (! button) return;

        const dataStr = button.getAttribute('data-question');
        if (! dataStr) return;

        let data;
        try {
            data = JSON.parse(dataStr);
        } catch (err) {
            console.error('Gagal membaca data soal:', err);
            return;
        }

        if (editForm) {
            editForm.action = '<?= site_url('online-assessments/questions/update'); ?>/' + data.soal_id;
        }
        if (inputPertanyaan) inputPertanyaan.value = data.pertanyaan || '';
        if (inputTipe) inputTipe.value = data.tipe_soal || 'pilihan_ganda';
        if (inputBobot) inputBobot.value = data.bobot_nilai || 1.0;

        if (data.tipe_soal === 'pilihan_ganda') {
            if (pgOptionsContainer) pgOptionsContainer.style.display = 'block';
            if (kunciContainer) kunciContainer.style.display = 'block';
            if (inputOpsiA) inputOpsiA.value = data.opsi_a || '';
            if (inputOpsiB) inputOpsiB.value = data.opsi_b || '';
            if (inputOpsiC) inputOpsiC.value = data.opsi_c || '';
            if (inputOpsiD) inputOpsiD.value = data.opsi_d || '';
            if (inputOpsiE) inputOpsiE.value = data.opsi_e || '';
            if (selectKunci) selectKunci.value = data.kunci_jawaban || 'A';
        } else {
            if (pgOptionsContainer) pgOptionsContainer.style.display = 'none';
            if (kunciContainer) kunciContainer.style.display = 'none';
        }

        if (editModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
            modalInstance.show();
        }
    });

    document.querySelectorAll('.btn-copy-token').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const token = this.getAttribute('data-token');
            if (! token) return;

            navigator.clipboard.writeText(token).then(() => {
                const origHtml = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2 text-success me-1"></i> Tersalin!';
                setTimeout(() => {
                    this.innerHTML = origHtml;
                }, 2000);
            }).catch(() => {
                prompt('Salin Token Ujian:', token);
            });
        });
    });
});
</script>
<?= $this->endSection(); ?>
