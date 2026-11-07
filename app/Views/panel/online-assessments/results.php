<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <!-- Breadcrumb & Back -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= site_url('online-assessments'); ?>">Ujian Online</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hasil & Nilai</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">Hasil & Nilai: <?= esc($ujian['judul']); ?></h1>
            <p class="text-muted mb-0">
                <i class="bi bi-book me-1"></i><?= esc($ujian['mapel_nama'] ?: 'Semua Mapel'); ?> &bull; 
                <i class="bi bi-door-open me-1"></i><?= esc($ujian['kelas_nama'] ?: 'Semua Kelas'); ?> &bull;
                <i class="bi bi-clock me-1"></i><?= (int) $ujian['durasi_menit']; ?> Menit
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= site_url('online-assessments'); ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Ujian
            </a>
            <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/questions'); ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-ui-checks"></i> Kelola Soal (<?= $totalPg + $totalEssay; ?>)
            </a>
            <a href="<?= site_url('online-assessments/' . $ujian['ujian_id'] . '/export'); ?>" target="_blank" class="btn btn-outline-success d-inline-flex align-items-center gap-1">
                <i class="bi bi-printer"></i> Cetak / Rekap Nilai
            </a>
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

    <!-- Summary Statistics -->
    <?php
    $selesaiCount = count(array_filter($participants, fn($p) => $p['status_pengerjaan'] === 'selesai'));
    $scores = array_map(fn($p) => (float) $p['nilai_total'], array_filter($participants, fn($p) => $p['status_pengerjaan'] === 'selesai'));
    $avgScore = $scores !== [] ? round(array_sum($scores) / count($scores), 1) : 0;
    $maxScore = $scores !== [] ? max($scores) : 0;
    $minScore = $scores !== [] ? min($scores) : 0;
    ?>
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Total Peserta</div>
                        <div class="h4 mb-0 fw-bold"><?= count($participants); ?> Siswa</div>
                        <div class="small text-muted mt-1"><?= $selesaiCount; ?> sudah selesai</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary p-3 rounded-circle fs-5">
                        <i class="bi bi-people"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Rata-Rata Nilai</div>
                        <div class="h4 mb-0 fw-bold text-info"><?= $avgScore; ?></div>
                        <div class="small text-muted mt-1">Skala 0 - 100</div>
                    </div>
                    <span class="badge bg-info-subtle text-info p-3 rounded-circle fs-5">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Nilai Tertinggi</div>
                        <div class="h4 mb-0 fw-bold text-success"><?= $maxScore; ?></div>
                        <div class="small text-muted mt-1">Skor maksimal</div>
                    </div>
                    <span class="badge bg-success-subtle text-success p-3 rounded-circle fs-5">
                        <i class="bi bi-arrow-up-circle"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small">Nilai Terendah</div>
                        <div class="h4 mb-0 fw-bold text-danger"><?= $minScore; ?></div>
                        <div class="small text-muted mt-1">Skor minimal</div>
                    </div>
                    <span class="badge bg-danger-subtle text-danger p-3 rounded-circle fs-5">
                        <i class="bi bi-arrow-down-circle"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table of Results -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0 fw-semibold">Rekap Hasil Pengerjaan Siswa</h2>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-7"><?= count($participants); ?> siswa</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">NO.</th>
                        <th scope="col">NAMA SISWA</th>
                        <th scope="col">KELAS / NIS</th>
                        <th scope="col" class="text-center">BENAR (PG)</th>
                        <th scope="col" class="text-center">SALAH (PG)</th>
                        <th scope="col" class="text-center">KOSONG</th>
                        <th scope="col" class="text-center">NILAI PG (AUTO)</th>
                        <?php if ($totalEssay > 0) : ?>
                            <th scope="col" class="text-center">NILAI ESSAY</th>
                        <?php endif; ?>
                        <th scope="col" class="text-center">TOTAL NILAI</th>
                        <th scope="col" class="text-center">STATUS</th>
                        <th scope="col" class="text-center" style="width: 150px;">LEMBAR JAWABAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $idx => $p) : ?>
                        <tr>
                            <td class="text-center text-muted"><?= $idx + 1; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($p['nama_siswa'] ?: '-'); ?></div>
                                <div class="small text-muted">
                                    Mulai: <?= ! empty($p['waktu_mulai']) ? date('H:i', strtotime($p['waktu_mulai'])) : '-'; ?>
                                    <?php if (! empty($p['waktu_selesai'])) : ?>
                                        &bull; Selesai: <?= date('H:i', strtotime($p['waktu_selesai'])); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div><span class="badge bg-light text-dark border"><?= esc($p['kelas_nama'] ?: '-'); ?></span></div>
                                <div class="small text-muted font-monospace mt-1">NIS: <?= esc($p['nis'] ?: '-'); ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i><?= (int) $p['jumlah_benar']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 px-2 py-1">
                                    <i class="bi bi-x me-1"></i><?= (int) $p['jumlah_salah']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted"><?= (int) $p['jumlah_tidak_dijawab']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-primary fs-6"><?= (float) $p['nilai_pg']; ?></span>
                            </td>
                            <?php if ($totalEssay > 0) : ?>
                                <td class="text-center">
                                    <span class="fw-bold text-info fs-6"><?= (float) $p['nilai_essay']; ?></span>
                                </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <span class="badge bg-primary text-white fs-6 px-3 py-2">
                                    <?= (float) $p['nilai_total']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($p['status_pengerjaan'] === 'selesai') : ?>
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-25">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                <?php else : ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25">
                                        <i class="bi bi-hourglass-split me-1"></i>Mengerjakan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('online-assessments/student-sheet/' . $p['peserta_ujian_id']); ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" title="Lihat Lembar Jawaban Siswa">
                                    <i class="bi bi-eye"></i> Detail Lembar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($participants === []) : ?>
                        <tr>
                            <td colspan="<?= $totalEssay > 0 ? '11' : '10'; ?>" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Belum ada siswa yang mengerjakan ujian ini. Siswa dapat mengakses ujian melalui <a href="<?= site_url('cbt'); ?>" target="_blank">Portal CBT</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
