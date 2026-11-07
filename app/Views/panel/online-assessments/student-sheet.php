<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('online-assessments'); ?>">Ujian Online</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('online-assessments/' . $peserta['ujian_id'] . '/results'); ?>">Hasil Ujian</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lembar Jawaban Siswa</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Lembar Jawaban: <?= esc($peserta['nama_siswa']); ?></h1>
            <p class="text-muted mb-0">
                <?= esc($peserta['judul']); ?> &bull; 
                Kelas: <?= esc($peserta['kelas_nama'] ?: '-'); ?> &bull; 
                NIS: <?= esc($peserta['nis'] ?: '-'); ?>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= site_url('online-assessments/' . $peserta['ujian_id'] . '/results'); ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Rekap Nilai
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak Lembar Ini
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

    <!-- Score Summary Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 text-center border-end-md">
                    <div class="text-muted small">TOTAL NILAI AKHIR</div>
                    <div class="display-5 fw-bold text-primary my-1"><?= (float) $peserta['nilai_total']; ?></div>
                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-1">
                        Status: Selesai
                    </span>
                </div>
                <div class="col-md-9">
                    <div class="row g-3 text-center">
                        <div class="col-sm-3 col-6">
                            <div class="p-3 rounded-2 bg-success-subtle border border-success border-opacity-25">
                                <div class="text-success small fw-semibold">Jawaban Benar (PG)</div>
                                <div class="h3 mb-0 fw-bold text-success"><?= (int) $peserta['jumlah_benar']; ?></div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 rounded-2 bg-danger-subtle border border-danger border-opacity-25">
                                <div class="text-danger small fw-semibold">Jawaban Salah (PG)</div>
                                <div class="h3 mb-0 fw-bold text-danger"><?= (int) $peserta['jumlah_salah']; ?></div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 rounded-2 bg-secondary-subtle border border-secondary border-opacity-25">
                                <div class="text-secondary small fw-semibold">Tidak Dijawab</div>
                                <div class="h3 mb-0 fw-bold text-secondary"><?= (int) $peserta['jumlah_tidak_dijawab']; ?></div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 rounded-2 bg-primary-subtle border border-primary border-opacity-25">
                                <div class="text-primary small fw-semibold">Nilai Pilihan Ganda</div>
                                <div class="h3 mb-0 fw-bold text-primary"><?= (float) $peserta['nilai_pg']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Section Heading -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Rincian Evaluasi Jawaban Siswa</h2>
        <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success border border-success border-opacity-25">
                <i class="bi bi-check-circle-fill me-1"></i> Benar
            </span>
            <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25">
                <i class="bi bi-x-circle-fill me-1"></i> Salah
            </span>
            <span class="badge bg-info-subtle text-info border border-info border-opacity-25">
                <i class="bi bi-card-text me-1"></i> Essay
            </span>
        </div>
    </div>

    <!-- Question Evaluation List -->
    <div class="d-flex flex-column gap-3">
        <?php foreach ($answers as $idx => $ans) : 
            $isPg = $ans['tipe_soal'] === 'pilihan_ganda';
            $isCorrect = (int) $ans['is_correct'] === 1;
            $studentAnswer = strtoupper(trim((string) $ans['jawaban_pg']));
            $correctKey = strtoupper(trim((string) $ans['kunci_jawaban']));
            $isAnswered = $studentAnswer !== '';
        ?>
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden <?= $isPg ? ($isCorrect ? 'border-start border-success border-4' : 'border-start border-danger border-4') : 'border-start border-info border-4'; ?>">
                <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark px-3 py-2">Soal No. <?= $idx + 1; ?></span>
                        <?php if ($isPg) : ?>
                            <?php if ($isCorrect) : ?>
                                <span class="badge bg-success text-white px-3 py-2">
                                    <i class="bi bi-check-circle-fill me-1"></i> JAWABAN BENAR
                                </span>
                            <?php elseif ($isAnswered) : ?>
                                <span class="badge bg-danger text-white px-3 py-2">
                                    <i class="bi bi-x-circle-fill me-1"></i> JAWABAN SALAH
                                </span>
                            <?php else : ?>
                                <span class="badge bg-secondary text-white px-3 py-2">
                                    <i class="bi bi-dash-circle me-1"></i> TIDAK DIJAWAB
                                </span>
                            <?php endif; ?>
                        <?php else : ?>
                            <span class="badge bg-info text-white px-3 py-2">
                                <i class="bi bi-card-text me-1"></i> SOAL ESSAY / URAIAN
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <span class="badge bg-light text-muted border fs-7">
                            Poin Diperoleh: <strong><?= (float) $ans['nilai_didapat']; ?></strong> / <?= (float) $ans['bobot_nilai']; ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Pertanyaan -->
                    <div class="fs-6 fw-semibold text-dark mb-3" style="white-space: pre-line;">
                        <?= esc($ans['pertanyaan']); ?>
                    </div>

                    <?php if ($isPg) : ?>
                        <!-- Opsi Jawaban Pilihan Ganda -->
                        <div class="row g-2 mb-3">
                            <?php 
                            $options = [
                                'A' => $ans['opsi_a'],
                                'B' => $ans['opsi_b'],
                                'C' => $ans['opsi_c'],
                                'D' => $ans['opsi_d'],
                                'E' => $ans['opsi_e'],
                            ];
                            foreach ($options as $optKey => $optVal) :
                                if (empty($optVal) && $optKey === 'E') continue;
                                $isThisKey = $correctKey === $optKey;
                                $isStudentChoice = $studentAnswer === $optKey;

                                $boxClass = 'bg-light border text-dark';
                                if ($isThisKey) {
                                    $boxClass = 'bg-success-subtle border-success text-success fw-bold';
                                } elseif ($isStudentChoice && ! $isCorrect) {
                                    $boxClass = 'bg-danger-subtle border-danger text-danger text-decoration-line-through';
                                }
                            ?>
                                <div class="col-md-6">
                                    <div class="p-2 px-3 rounded border d-flex align-items-center gap-2 <?= $boxClass; ?>">
                                        <span class="badge <?= $isThisKey ? 'bg-success text-white' : ($isStudentChoice ? 'bg-danger text-white' : 'bg-secondary text-white'); ?>">
                                            <?= $optKey; ?>
                                        </span>
                                        <span class="flex-grow-1"><?= esc($optVal ?: '-'); ?></span>
                                        <?php if ($isThisKey) : ?>
                                            <span class="badge bg-success text-white small" title="Kunci Jawaban Benar">
                                                <i class="bi bi-check2"></i> Kunci Benar
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($isStudentChoice && ! $isThisKey) : ?>
                                            <span class="badge bg-danger text-white small" title="Pilihan Siswa (Salah)">
                                                <i class="bi bi-x"></i> Pilihan Siswa
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Perbandingan Jawaban Siswa vs Kunci -->
                        <div class="p-3 rounded-2 <?= $isCorrect ? 'bg-success-subtle border border-success border-opacity-25' : 'bg-danger-subtle border border-danger border-opacity-25'; ?>">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="small text-muted">Jawaban Siswa:</div>
                                    <div class="fw-bold <?= $isCorrect ? 'text-success' : 'text-danger'; ?>">
                                        <?php if ($isAnswered) : ?>
                                            Pilihan <?= $studentAnswer; ?> <?= $isCorrect ? ' (BENAR)' : ' (SALAH)'; ?>
                                        <?php else : ?>
                                            <span class="text-muted fst-italic">Tidak Menjawab</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted">Kunci Jawaban Yang Benar:</div>
                                    <div class="fw-bold text-success">
                                        Pilihan <?= $correctKey; ?> (Kunci dari Guru)
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else : ?>
                        <!-- Jawaban Essay Siswa & Form Koreksi Guru -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Jawaban Siswa:</label>
                            <div class="p-3 rounded-2 border bg-light text-dark font-monospace" style="white-space: pre-line;">
                                <?= esc($ans['jawaban_essay'] ?: '(Siswa tidak mengisi jawaban essay ini)'); ?>
                            </div>
                        </div>

                        <!-- Form Penilaian Essay Guru -->
                        <form method="post" action="<?= site_url('online-assessments/grade-essay'); ?>" class="p-3 rounded-2 border bg-white shadow-2xs">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="jawaban_id" value="<?= $ans['jawaban_id']; ?>">
                            <input type="hidden" name="peserta_ujian_id" value="<?= $peserta['peserta_ujian_id']; ?>">

                            <div class="fw-bold text-primary mb-2">
                                <i class="bi bi-pencil-square me-1"></i> Penilaian & Feedback Guru untuk Essay Ini:
                            </div>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small">Nilai Poin (Maks: <?= (float) $ans['bobot_nilai']; ?>)</label>
                                    <input type="number" step="0.5" class="form-control" name="nilai_didapat" 
                                        value="<?= (float) $ans['nilai_didapat']; ?>" min="0" max="<?= (float) $ans['bobot_nilai']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Catatan / Koreksi untuk Siswa (Opsional)</label>
                                    <input type="text" class="form-control" name="catatan_guru" 
                                        value="<?= esc($ans['catatan_guru'] ?? ''); ?>" placeholder="Tuliskan catatan atau komentar koreksi...">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-check2-circle me-1"></i> Simpan Nilai Essay
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection(); ?>
