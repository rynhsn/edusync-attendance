<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - EduSync CBT</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('metis-1.0.0/dist-modern/assets/main-Kuf3cpUe.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .review-header {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px 0;
            margin-bottom: 24px;
        }
        .score-hero {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 32px 24px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="review-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary fs-5 p-2 rounded-3"><i class="bi bi-laptop"></i></span>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Hasil Ujian CBT</h5>
                    <small class="text-muted"><?= esc($peserta['judul']); ?></small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= site_url('cbt'); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-house me-1"></i> Portal CBT
                </a>
                <a href="<?= site_url('logout'); ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        <!-- Alert Notification -->
        <?php if (! empty($message)) : ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="status">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i><?= esc($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Hero Score Banner -->
        <div class="score-hero mb-4 text-center">
            <div class="badge bg-white text-primary px-3 py-1 fs-7 fw-bold rounded-pill mb-2">
                UJIAN SELESAI
            </div>
            <h2 class="fw-bold mb-1"><?= esc($peserta['nama_siswa']); ?></h2>
            <p class="text-white text-opacity-75 mb-3">
                Kelas: <?= esc($peserta['kelas_nama'] ?: '-'); ?> &bull; 
                NIS: <?= esc($peserta['nis'] ?: '-'); ?> &bull;
                Waktu Selesai: <?= ! empty($peserta['waktu_selesai']) ? date('d/m/Y H:i', strtotime($peserta['waktu_selesai'])) : '-'; ?>
            </p>

            <div class="d-inline-block bg-white bg-opacity-20 backdrop-blur rounded-4 px-5 py-3 my-2 border border-white border-opacity-25">
                <div class="text-white text-opacity-75 small fw-semibold">NILAI PILIHAN GANDA (OTOMATIS)</div>
                <div class="display-3 fw-bold text-white"><?= (float) $peserta['nilai_pg']; ?></div>
                <div class="text-white text-opacity-90 small">Skala 0 - 100</div>
            </div>

            <!-- Stats Bar -->
            <div class="row g-2 justify-content-center mt-3 max-w-md mx-auto">
                <div class="col-sm-4 col-4">
                    <div class="bg-white bg-opacity-10 rounded-3 p-2 text-center">
                        <div class="fs-4 fw-bold text-success"><?= (int) $peserta['jumlah_benar']; ?></div>
                        <div class="small text-white text-opacity-75">Jawaban Benar</div>
                    </div>
                </div>
                <div class="col-sm-4 col-4">
                    <div class="bg-white bg-opacity-10 rounded-3 p-2 text-center">
                        <div class="fs-4 fw-bold text-danger"><?= (int) $peserta['jumlah_salah']; ?></div>
                        <div class="small text-white text-opacity-75">Jawaban Salah</div>
                    </div>
                </div>
                <div class="col-sm-4 col-4">
                    <div class="bg-white bg-opacity-10 rounded-3 p-2 text-center">
                        <div class="fs-4 fw-bold text-white"><?= (int) $peserta['jumlah_tidak_dijawab']; ?></div>
                        <div class="small text-white text-opacity-75">Tidak Dijawab</div>
                    </div>
                </div>
            </div>

            <?php if ($totalEssay > 0) : ?>
                <div class="mt-3 text-white text-opacity-90 small">
                    <i class="bi bi-info-circle me-1"></i> Ujian ini memiliki <?= $totalEssay; ?> soal essay yang sedang diperiksa oleh guru pengampu.
                </div>
            <?php endif; ?>
        </div>

        <!-- Section: Pembahasan Jawaban (Mana yang Salah dan Mana yang Benar) -->
        <?php if ((int) $peserta['tampil_pembahasan'] === 1) : ?>
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="h5 fw-bold mb-0 text-dark">
                            <i class="bi bi-ui-checks-grid me-2 text-primary"></i> Pembahasan Soal & Kunci Jawaban
                        </h4>
                        <small class="text-muted">Periksa kembali jawaban Anda, bandingkan dengan kunci jawaban yang benar.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-check-circle me-1"></i>Benar</span>
                        <span class="badge bg-danger text-white px-2 py-1"><i class="bi bi-x-circle me-1"></i>Salah</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-4">
                        <?php foreach ($answers as $idx => $ans) : 
                            $isPg = $ans['tipe_soal'] === 'pilihan_ganda';
                            $isCorrect = (int) $ans['is_correct'] === 1;
                            $studentAnswer = strtoupper(trim((string) $ans['jawaban_pg']));
                            $correctKey = strtoupper(trim((string) $ans['kunci_jawaban']));
                            $isAnswered = $studentAnswer !== '';
                        ?>
                            <div class="border rounded-3 p-3 p-md-4 bg-white <?= $isPg ? ($isCorrect ? 'border-start border-success border-4' : 'border-start border-danger border-4') : 'border-start border-info border-4'; ?>">
                                <!-- Question Header -->
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-dark px-3 py-2">Soal No. <?= $idx + 1; ?></span>
                                        <?php if ($isPg) : ?>
                                            <?php if ($isCorrect) : ?>
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-1">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Jawaban Anda Benar
                                                </span>
                                            <?php elseif ($isAnswered) : ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-3 py-1">
                                                    <i class="bi bi-x-circle-fill me-1"></i> Jawaban Anda Salah
                                                </span>
                                            <?php else : ?>
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 px-3 py-1">
                                                    <i class="bi bi-dash-circle me-1"></i> Anda Tidak Menjawab
                                                </span>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span class="badge bg-info-subtle text-info border border-info border-opacity-25 px-3 py-1">
                                                <i class="bi bi-card-text me-1"></i> Soal Essay
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-light text-muted border">
                                        Poin: <?= (float) $ans['nilai_didapat']; ?> / <?= (float) $ans['bobot_nilai']; ?>
                                    </span>
                                </div>

                                <!-- Pertanyaan -->
                                <div class="fs-6 text-dark mb-3 lh-base" style="white-space: pre-line;">
                                    <?= esc($ans['pertanyaan']); ?>
                                </div>

                                <?php if ($isPg) : ?>
                                    <!-- Pilihan Jawaban -->
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

                                            $itemClass = 'bg-light border text-dark';
                                            if ($isThisKey) {
                                                $itemClass = 'bg-success-subtle border-success text-success fw-bold';
                                            } elseif ($isStudentChoice && ! $isCorrect) {
                                                $itemClass = 'bg-danger-subtle border-danger text-danger text-decoration-line-through';
                                            }
                                        ?>
                                            <div class="col-md-6">
                                                <div class="p-2 px-3 rounded border d-flex align-items-center gap-2 <?= $itemClass; ?>">
                                                    <span class="badge <?= $isThisKey ? 'bg-success text-white' : ($isStudentChoice ? 'bg-danger text-white' : 'bg-secondary text-white'); ?>">
                                                        <?= $optKey; ?>
                                                    </span>
                                                    <span class="flex-grow-1"><?= esc($optVal ?: '-'); ?></span>
                                                    <?php if ($isThisKey) : ?>
                                                        <span class="badge bg-success text-white small">
                                                            <i class="bi bi-check2"></i> Kunci Benar
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($isStudentChoice && ! $isThisKey) : ?>
                                                        <span class="badge bg-danger text-white small">
                                                            <i class="bi bi-x"></i> Pilihan Anda
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Alert Keterangan Evaluasi -->
                                    <div class="p-3 rounded-2 <?= $isCorrect ? 'bg-success-subtle border border-success border-opacity-25' : 'bg-danger-subtle border border-danger border-opacity-25'; ?>">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi <?= $isCorrect ? 'bi-check-circle-fill text-success fs-5' : 'bi-x-circle-fill text-danger fs-5'; ?>"></i>
                                            <div>
                                                <?php if ($isCorrect) : ?>
                                                    <span class="fw-bold text-success">Luar biasa!</span> Jawaban Anda <strong>(Pilihan <?= $studentAnswer; ?>)</strong> sesuai dengan kunci jawaban yang benar.
                                                <?php elseif ($isAnswered) : ?>
                                                    <span class="fw-bold text-danger">Kurang tepat.</span> Jawaban Anda adalah <strong>Pilihan <?= $studentAnswer; ?></strong>, sedangkan kunci jawaban yang benar adalah <strong>Pilihan <?= $correctKey; ?></strong>.
                                                <?php else : ?>
                                                    <span class="fw-bold text-danger">Soal ini tidak Anda jawab.</span> Kunci jawaban yang benar adalah <strong>Pilihan <?= $correctKey; ?></strong>.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                <?php else : ?>
                                    <!-- Essay Review -->
                                    <div class="mb-3">
                                        <div class="small fw-bold text-muted mb-1">Jawaban Anda:</div>
                                        <div class="p-3 rounded-2 bg-light border font-monospace" style="white-space: pre-line;">
                                            <?= esc($ans['jawaban_essay'] ?: '(Anda tidak mengisi jawaban essay ini)'); ?>
                                        </div>
                                    </div>
                                    <?php if (! empty($ans['catatan_guru'])) : ?>
                                        <div class="alert alert-info py-2 px-3 small mb-0">
                                            <i class="bi bi-chat-left-text me-1"></i> <strong>Catatan Guru:</strong> <?= esc($ans['catatan_guru']); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="card border-0 shadow-sm rounded-3 p-4 text-center text-muted">
                <i class="bi bi-eye-slash fs-1 d-block mb-2 opacity-50"></i>
                Pengaturan ujian ini tidak menampilkan lembar pembahasan detail kepada peserta. Silakan hubungi guru Anda untuk informasi lebih lanjut.
            </div>
        <?php endif; ?>

        <!-- Action Footer -->
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 mb-2">
            <a href="<?= site_url('cbt'); ?>" class="btn btn-primary px-4 py-2">
                <i class="bi bi-house me-1"></i> Kembali ke Portal CBT
            </a>
            <a href="<?= site_url('logout'); ?>" class="btn btn-outline-danger px-4 py-2">
                <i class="bi bi-box-arrow-right me-1"></i> Selesai & Logout
            </a>
        </div>
    </main>

    <footer class="text-center py-4 text-muted small border-top bg-white">
        &copy; <?= date('Y'); ?> EduSync Attendance & Assessment System
    </footer>
</body>
</html>
