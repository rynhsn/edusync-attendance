<?= $this->extend($pdfMode ? 'layouts/grade-report-pdf' : 'layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h1 class="h3 mb-1">Raport Siswa</h1>
            <p class="text-muted mb-0">Buat dan cetak raport SMP atau SMK dari data siswa dan nilai akademik.</p>
        </div>
        <?php if ($reportAvailable && ! $pdfMode) : ?>
            <form class="d-flex flex-wrap align-items-end gap-2 no-print" method="get" action="<?= site_url('report/grades/pdf'); ?>">
                <input type="hidden" name="kelas_id" value="<?= esc($selected['kelas_id']); ?>">
                <input type="hidden" name="tahun_ajaran_id" value="<?= esc($selected['tahun_ajaran_id']); ?>">
                <input type="hidden" name="siswa_id" value="<?= esc($selected['siswa_id']); ?>">
                <input type="hidden" name="kkm" value="<?= esc($kkm); ?>">
                <input type="hidden" name="catatan" value="<?= esc($comment); ?>">
                <input type="hidden" name="pat_component" value="<?= esc($patComponent); ?>">
                <input type="hidden" name="generate" value="1">
                <?php foreach ($selectedSubjectIds as $subjectId) : ?>
                    <input type="hidden" name="mapel_id[]" value="<?= esc($subjectId); ?>">
                <?php endforeach; ?>
                <div>
                    <label class="form-label mb-1" for="reportPaperSize">Ukuran kertas PDF</label>
                    <select class="form-select" id="reportPaperSize" name="paper_size">
                        <option value="A4" <?= $paperSize === 'A4' ? 'selected' : ''; ?>>A4 Portrait</option>
                        <option value="Legal" <?= $paperSize === 'Legal' ? 'selected' : ''; ?>>Legal Portrait</option>
                    </select>
                </div>
                <button class="btn btn-outline-primary" type="submit">
                    <i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Download PDF
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($selectionError !== null) : ?>
        <div class="alert alert-danger no-print" role="alert"><?= esc($selectionError); ?></div>
    <?php endif; ?>

    <div class="card mb-4 no-print">
        <div class="card-header"><h2 class="h5 mb-0">Data Raport</h2></div>
        <div class="card-body">
            <form method="get" action="<?= site_url('report/grades'); ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="reportClass">Kelas</label>
                        <select class="form-select" id="reportClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $selected['kelas_id'] === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                    <?= esc($class['kelas_tingkat'] . ' - ' . $class['kelas_nama']); ?>
                                    (<?= (int) $class['kelas_tingkat'] <= 9 ? 'SMP' : 'SMK'; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reportYear">Tahun Ajaran / Semester</label>
                        <select class="form-select" id="reportYear" name="tahun_ajaran_id" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= esc($year['tahun_ajaran_id']); ?>" <?= $selected['tahun_ajaran_id'] === (int) $year['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                    <?= esc($year['tahun_ajaran_nama']); ?> - Semester <?= (int) $year['semester'] === 1 ? 'Ganjil' : 'Genap'; ?><?= (bool) $year['is_active'] ? ' - Aktif' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reportStudent">Nama Siswa</label>
                        <select class="form-select" id="reportStudent" name="siswa_id" required>
                            <option value="">Pilih siswa</option>
                            <?php foreach ($students as $row) : ?>
                                <option value="<?= esc($row['siswa_id']); ?>" <?= $selected['siswa_id'] === (int) $row['siswa_id'] ? 'selected' : ''; ?>>
                                    <?= esc($row['nama_siswa']); ?><?= ! empty($row['nis']) ? ' - ' . esc($row['nis']) : ''; ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if ($selected['kelas_id'] !== null && $students === []) : ?>
                                <option value="" disabled>Tidak ada siswa aktif pada kelas ini</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <?php if ($student !== null) : ?>
                        <div class="col-md-3">
                            <label class="form-label" for="reportKkm">KKM</label>
                            <input class="form-control" id="reportKkm" name="kkm" type="number" min="1" max="100" value="<?= esc($kkm); ?>">
                        </div>
                        <?php if ($template === 'smk') : ?>
                            <div class="col-md-3">
                                <label class="form-label" for="reportPatComponent">Nilai untuk PAT</label>
                                <select class="form-select" id="reportPatComponent" name="pat_component">
                                    <option value="uas" <?= $patComponent === 'uas' ? 'selected' : ''; ?>>UAS</option>
                                    <option value="uts" <?= $patComponent === 'uts' ? 'selected' : ''; ?>>UTS</option>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="<?= $template === 'smk' ? 'col-md-6' : 'col-md-9'; ?>">
                            <label class="form-label" for="reportComment">Catatan Wali Kelas</label>
                            <input class="form-control" id="reportComment" name="catatan" maxlength="1000" value="<?= esc($comment); ?>" placeholder="Opsional">
                        </div>
                        <div class="col-12">
                            <fieldset>
                                <legend class="form-label fw-semibold mb-2">Mata Pelajaran pada Raport</legend>
                                <?php if ($subjects === []) : ?>
                                    <p class="text-muted mb-0">Belum ada data mata pelajaran. Tambahkan melalui menu Data Mapel.</p>
                                <?php else : ?>
                                    <div class="subject-options">
                                        <?php foreach ($subjects as $subject) : ?>
                                            <label class="panel-checkbox-card subject-option" for="subject<?= esc($subject['mapel_id']); ?>">
                                                <input class="form-check-input" type="checkbox" name="mapel_id[]" value="<?= esc($subject['mapel_id']); ?>" id="subject<?= esc($subject['mapel_id']); ?>" <?= in_array((int) $subject['mapel_id'], $selectedSubjectIds, true) ? 'checked' : ''; ?>>
                                                <span>
                                                    <?= esc($subject['mapel_nama']); ?>
                                                    <?php if (! $subject['has_scores']) : ?>
                                                        <small class="d-block text-muted">Nilai belum diinput</small>
                                                    <?php endif; ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </fieldset>
                            <p class="form-text mb-0 mt-2">
                                Nilai Pengetahuan memakai input Nilai Pengetahuan (nilai lama memakai rata-rata UTS/UAS); Keterampilan memakai nilai Praktik. Sumber nilai PAT dapat dipilih dari UTS atau UAS.
                                <a href="<?= site_url('grades?' . http_build_query(array_filter(['kelas_id' => $selected['kelas_id'], 'tahun_ajaran_id' => $selected['tahun_ajaran_id']], static fn ($value): bool => $value !== null))); ?>">Buka Input Nilai</a>
                            </p>
                            <?php if (! $hasKnowledgeGrade) : ?>
                                <div class="alert alert-warning mt-2 mb-0" role="status">
                                    Kolom Nilai Pengetahuan belum tersedia di database. Nilai lama UTS/UAS tetap digunakan sampai administrator menjalankan migrasi.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-12 d-flex flex-wrap gap-2">
                        <?php if ($student === null) : ?>
                            <button class="btn btn-primary" type="submit" name="prepare" value="1">
                                <i class="bi bi-search me-2" aria-hidden="true"></i>Tampilkan Mata Pelajaran
                            </button>
                        <?php else : ?>
                            <button class="btn btn-primary" type="submit" name="generate" value="1">
                                <i class="bi bi-file-earmark-medical me-2" aria-hidden="true"></i>Buat Raport
                            </button>
                            <a class="btn btn-outline-secondary" href="<?= site_url('report/grades'); ?>">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            <?php if ($classes === [] || $years === []) : ?>
                <div class="alert alert-warning mt-3 mb-0" role="alert">
                    Lengkapi data kelas dan tahun ajaran terlebih dahulu melalui menu Master Akademik.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($reportAvailable) : ?>
        <?php if ($pdfMode) : ?>
            <div class="pdf-report-frame <?= esc($template); ?>">
        <?php endif; ?>
        <article class="report-paper <?= esc($template); ?>">
            <header class="report-heading">
                <img
                    class="report-logo"
                    src="<?= $pdfMode ? esc($reportLogoData) : base_url('assets/img/logo/' . ($template === 'smp' ? 'smp.png' : 'smk.png')); ?>"
                    alt="Logo <?= $template === 'smp' ? 'SMP' : 'SMK'; ?> Miftahul Huda">
                <?php if ($template === 'smp') : ?>
                    <h1>LAPORAN HASIL BELAJAR PESERTA DIDIK</h1>
                    <h3>ASESMEN SUMATIF AKHIR SEMESTER <?= esc($student['tahun_ajaran_nama']); ?></h3>
                    <h3>SMP IT MIFTAHUL HUDA</h3>
                    <div class="report-separator" aria-hidden="true"><span></span><span></span></div>
                <?php else : ?>
                    <p class="report-foundation">YAYASAN PONDOK PESANTREN MIFTAHUL HUDA</p>
                    <h2>SMK MIFTAHUL HUDA</h2>
                    <p>Jalan Raya AMD Lintas Timur, Pandeglang - Banten</p>
                    <div class="report-separator" aria-hidden="true"><span></span><span></span></div>
                    <h1>LAPORAN HASIL BELAJAR</h1>
                    <h3>TAHUN PELAJARAN <?= esc($student['tahun_ajaran_nama']); ?></h3>
                <?php endif; ?>
            </header>

            <table class="report-identity <?= $template === 'smp' ? 'smp-report-identity' : ''; ?>">
                <tbody>
                    <tr><th>Nama Sekolah</th><td><?= $template === 'smp' ? 'SMP IT MIFTAHUL HUDA' : 'SMK MIFTAHUL HUDA'; ?></td><th>Kelas</th><td><?= esc($student['kelas_tingkat'] . ' - ' . $student['kelas_nama']); ?></td></tr>
                    <tr><th>Alamat</th><td>Pandeglang</td><th>Semester</th><td><?= (int) $student['semester'] === 1 ? 'Ganjil' : 'Genap'; ?></td></tr>
                    <tr><th>Nama</th><td><?= esc($student['nama_siswa']); ?></td><th>Tahun Pelajaran</th><td><?= esc($student['tahun_ajaran_nama']); ?></td></tr>
                    <tr><th>NIS</th><td><?= esc($student['nis'] ?: '-'); ?></td><th>NISN</th><td><?= esc($student['nisn'] ?: '-'); ?></td></tr>
                </tbody>
            </table>

            <?php if ($template === 'smp') : ?>
                <table class="report-grades">
                    <thead>
                        <tr><th>No.</th><th>Mata Pelajaran</th><th>KKM</th><th>Angka</th><th>Huruf</th><th>Deskripsi Kemajuan Belajar</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportSubjects as $index => $subject) : ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= esc($subject['mapel_nama']); ?></td>
                                <td><?= esc($kkm); ?></td>
                                <td><?= $subject['nilai_akhir'] !== null ? esc(number_format($subject['nilai_akhir'], 0)) : '-'; ?></td>
                                <td><i><?= esc($subject['terbilang']); ?></i></td>
                                <td><i><?= esc($subject['deskripsi']); ?></i></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($reportSubjects === []) : ?>
                            <tr><td colspan="6" class="text-center">Tidak ada mata pelajaran yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <table class="report-grades">
                    <colgroup>
                        <col style="width: 5%">
                        <col style="width: 27%">
                        <col style="width: 9%">
                        <col style="width: 11%">
                        <col style="width: 13%">
                        <col style="width: 11%">
                        <col style="width: 15%">
                        <col style="width: 9%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th rowspan="2">No.</th><th rowspan="2">Mata Pelajaran</th><th rowspan="2">KKM</th>
                            <th colspan="2">Pengetahuan</th><th colspan="2">Keterampilan / Praktik</th><th rowspan="2">PAT (<?= strtoupper(esc($patComponent)); ?>)</th>
                        </tr>
                        <tr><th>Nilai</th><th>Predikat</th><th>Nilai</th><th>Predikat</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $lastReportGroup = null;
                        $reportGroupLabels = [
                            'nasional' => 'A. Muatan Nasional : *',
                            'kewilayahan' => 'B. Muatan Kewilayahan : **',
                            'dasar_keahlian' => 'C1. Dasar Bidang Keahlian',
                            'program_keahlian' => 'C2. Dasar Program Keahlian',
                        ];
                        $kejuruanHeaderRendered = false;
                        ?>
                        <?php foreach ($reportSubjects as $index => $subject) : ?>
                            <?php if ($subject['kelompok_rapor'] !== $lastReportGroup) : ?>
                                <?php if (in_array($subject['kelompok_rapor'], ['dasar_keahlian', 'program_keahlian'], true) && ! $kejuruanHeaderRendered) : ?>
                                    <tr class="report-group-heading"><th colspan="8">C. Muatan Peminatan Kejuruan</th></tr>
                                    <?php $kejuruanHeaderRendered = true; ?>
                                <?php endif; ?>
                                <tr class="report-group-heading"><th colspan="8"><?= esc($reportGroupLabels[$subject['kelompok_rapor']] ?? 'A. Muatan Nasional : *'); ?></th></tr>
                                <?php $lastReportGroup = $subject['kelompok_rapor']; ?>
                            <?php endif; ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= esc($subject['mapel_nama']); ?></td>
                                <td><?= esc($kkm); ?></td>
                                <td><?= $subject['nilai_pengetahuan'] !== null ? esc(number_format($subject['nilai_pengetahuan'], 0)) : '-'; ?></td>
                                <td><?= esc($subject['predikat_pengetahuan']); ?></td>
                                <td><?= $subject['nilai_praktik'] !== null ? esc(number_format((float) $subject['nilai_praktik'], 0)) : '-'; ?></td>
                                <td><?= esc($subject['predikat_keterampilan']); ?></td>
                                <td><?= $subject['nilai_pat'] !== null ? esc(number_format((float) $subject['nilai_pat'], 0)) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($reportSubjects === []) : ?>
                            <tr><td colspan="8" class="text-center">Tidak ada mata pelajaran yang dipilih.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($template === 'smp') : ?>
                <?php
                $attendanceRows = [
                    ['label' => 'SAKIT', 'value' => $attendance['sakit']],
                    ['label' => 'IZIN', 'value' => $attendance['izin']],
                    ['label' => 'TANPA KETERANGAN', 'value' => $attendance['alpa']],
                ];
                $assessmentRowCount = max(count($personalityAssessments), count($attendanceRows));
                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ];
                $reportDate = date('d') . ' ' . $monthNames[(int) date('n')] . ' ' . date('Y');
                ?>
                <table class="smp-assessment-table">
                    <thead>
                        <tr>
                            <th colspan="2">Akhlak dan Kepribadian</th>
                            <th colspan="4">Ketidakhadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($index = 0; $index < $assessmentRowCount; $index++) : ?>
                            <?php $assessment = $personalityAssessments[$index] ?? null; ?>
                            <?php $absence = $attendanceRows[$index] ?? null; ?>
                            <tr>
                                <td><?= $assessment !== null ? esc(mb_strtoupper($assessment['nama_aspek'])) : ''; ?></td>
                                <td><?= $assessment !== null ? esc($assessment['nilai'] ?: '-') : ''; ?></td>
                                <td><?= $absence !== null ? $index + 1 . '.' : ''; ?></td>
                                <td><?= $absence !== null ? esc($absence['label']) : ''; ?></td>
                                <td><?= $absence !== null ? esc($absence['value']) : ''; ?></td>
                                <td><?= $absence !== null ? 'hari' : ''; ?></td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>

                <section class="smp-homeroom-note">
                    <h2>CATATAN WALI KELAS</h2>
                    <div><?= $comment !== '' ? nl2br(esc(mb_strtoupper($comment))) : '&nbsp;'; ?></div>
                </section>

                <footer class="smp-signatures">
                    <div>Orang Tua/Wali<div class="smp-signature-line"></div></div>
                    <div>Pandeglang, <?= esc($reportDate); ?><br>Wali Kelas<div class="smp-signature-line"></div></div>
                </footer>
            <?php else : ?>
                <div class="report-lower-grid">
                    <div class="report-assessment-column">
                        <section class="report-assessment-section">
                            <h2>B. Penilaian Perkembangan Akhlak</h2>
                            <table class="report-assessment-table">
                                <thead><tr><th>No.</th><th>Kepribadian</th><th>Nilai</th></tr></thead>
                                <tbody>
                                    <?php foreach ($personalityAssessments as $index => $assessment) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= esc($assessment['nama_aspek']); ?></td>
                                            <td><?= esc($assessment['nilai'] ?: '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($personalityAssessments === []) : ?>
                                        <tr><td colspan="3">Belum ada aspek kepribadian</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </section>

                        <section class="report-assessment-section">
                            <h2>C. Kegiatan Ekstrakurikuler</h2>
                            <table class="report-assessment-table">
                                <thead><tr><th>No.</th><th>Ekstrakurikuler</th><th>Nilai</th></tr></thead>
                                <tbody>
                                    <?php foreach ($extracurriculars as $index => $activity) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= esc($activity['nama_kegiatan']); ?></td>
                                            <td><?= esc($activity['nilai'] ?: '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($extracurriculars === []) : ?>
                                        <tr><td colspan="3">Belum ada kegiatan ekstrakurikuler</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </section>

                        <section class="report-assessment-section">
                            <h2>D. Kegiatan Kokurikuler</h2>
                            <table class="report-assessment-table">
                                <thead><tr><th>No.</th><th>Kokurikuler</th><th>Nilai</th></tr></thead>
                                <tbody>
                                    <?php foreach ($kokurikulerGrades as $index => $activity) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= esc($activity['nama_kegiatan']); ?></td>
                                            <td><?= esc($activity['nilai'] ?: '-'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($kokurikulerGrades === []) : ?>
                                        <tr><td colspan="3">Belum ada kegiatan kokurikuler</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </section>
                    </div>

                    <table class="report-assessment-table report-attendance-table">
                        <thead><tr><th>Ketidakhadiran</th><th>Hari</th></tr></thead>
                        <tbody>
                            <tr><td>Izin</td><td><?= esc($attendance['izin']); ?></td></tr>
                            <tr><td>Sakit</td><td><?= esc($attendance['sakit']); ?></td></tr>
                            <tr><td>Tanpa Keterangan</td><td><?= esc($attendance['alpa']); ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <?php if ($comment !== '') : ?>
                    <div class="report-comment"><strong>Catatan Wali Kelas</strong><br><?= nl2br(esc($comment)); ?></div>
                <?php endif; ?>

                <footer class="report-signatures">
                    <div>Mengetahui,<br>Orang Tua / Wali<div class="signature-line"></div></div>
                    <div>Pandeglang, <?= esc(date('d-m-Y')); ?><br>Wali Kelas<div class="signature-line"></div></div>
                </footer>
                <p class="report-generated">Format SMK - nilai raport dihitung dari nilai yang tersimpan.</p>
            <?php endif; ?>
        </article>
        <?php if ($pdfMode) : ?></div><?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<?php if (! $pdfMode) : ?>
<script>
(() => {
    const form = document.querySelector('form[action*="report/grades"]:not([action*="/pdf"])');
    const classSelect = document.getElementById('reportClass');
    const yearSelect = document.getElementById('reportYear');
    const studentSelect = document.getElementById('reportStudent');
    if (!form || !classSelect || !yearSelect || !studentSelect) return;

    const reloadStudents = () => {
        if (!classSelect.value || !yearSelect.value) return;

        const params = new URLSearchParams(new FormData(form));
        params.delete('siswa_id');
        params.delete('generate');
        params.delete('mapel_id[]');
        params.set('prepare', '1');
        window.location.assign(`${form.action}?${params.toString()}`);
    };

    classSelect.addEventListener('change', reloadStudents);
    yearSelect.addEventListener('change', reloadStudents);
})();
</script>
<?php endif; ?>
<style>
    .report-paper { background: #fff; border: 1px solid #adb5bd; color: #111; margin: 0 auto; max-width: 1100px; padding: 22px 28px 28px; }
    .report-heading { min-height: 120px; padding: 18px 90px 0; position: relative; text-align: center; }
    .report-logo { height: 82px; left: 0; object-fit: contain; position: absolute; top: 14px; width: 82px; }
    .report-heading h1 { font-size: 1.1rem; font-weight: 700; margin: 20px 0 8px; }
    .report-heading h2 { font-size: 1.4rem; font-weight: 700; margin: 10px 0 8px; }
    .report-heading h3 { font-size: 1rem; font-weight: 700; margin: 8px 0; }
    .report-heading p { margin: 6px 0; }
    .report-heading .report-foundation { font-weight: 700; }
    .report-separator { display: grid; gap: 3px; margin: 16px 0 18px; margin-left: -90px; width: calc(100% + 180px); }
    .report-separator span { border-top: 2px solid #222; display: block; }
    .report-separator span + span { border-top-width: 1px; }
    .report-identity, .report-grades { border-collapse: collapse; margin-top: 18px; width: 100%; }
    .report-identity th, .report-identity td, .report-grades th, .report-grades td { border: 1px solid #222; padding: 5px 7px; }
    .report-identity th { text-align: left; white-space: nowrap; width: 13%; }
    .report-grades { font-size: .9rem; }
    .report-grades th { background: #d9ead3; text-align: center; }
    .smp .report-grades th { background: #0bb5e8; color: #111; }
    .report-grades .report-group-heading th { background: #d9ead3; text-align: left; }
    .report-grades td:first-child, .report-grades td:nth-child(n+3) { text-align: center; }
    .report-lower-grid { align-items: start; display: grid; gap: 4%; grid-template-columns: 42% 54%; margin-top: 14px; }
    .report-assessment-column { min-width: 0; }
    .report-assessment-section + .report-assessment-section { margin-top: 14px; }
    .report-assessment-section h2 { background: #d9ead3; border: 1px solid #222; font-size: .9rem; font-weight: 700; margin: 0; padding: 5px 7px; }
    .report-assessment-table { border-collapse: collapse; font-size: .9rem; width: 100%; }
    .report-assessment-table th, .report-assessment-table td { border: 1px solid #222; padding: 4px 7px; }
    .report-assessment-table th:first-child, .report-assessment-table td:first-child, .report-assessment-table th:last-child, .report-assessment-table td:last-child { text-align: center; }
    .report-attendance-table { margin-top: 14px; }
    .report-attendance-table th { background: #d9ead3; }
    .report-comment { border: 1px solid #222; border-radius: 8px; margin-top: 18px; min-height: 58px; padding: 10px; }
    .subject-options { display: grid; gap: .5rem; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); }
    .subject-option { min-width: 0; }
    .report-signatures { display: flex; justify-content: space-between; margin-top: 34px; min-height: 135px; }
    .report-signatures > div { min-width: 200px; }
    .signature-line { border-bottom: 1px solid #222; height: 75px; margin-top: 8px; width: 85%; }
    .report-generated { color: #666; font-size: .75rem; margin: 8px 0 0; text-align: right; }
    .smp.report-paper { font-family: Arial, sans-serif; }
    .smp .report-heading { min-height: 112px; padding: 0 82px; }
    .smp .report-heading h1 { font-size: 1.2rem; margin: 0 0 4px; }
    .smp .report-heading h3 { font-size: 1.05rem; margin: 3px 0; }
    .smp .report-separator { margin: 24px 0 0; margin-left: -82px; width: calc(100% + 164px); }
    .smp .smp-report-identity { border: 0; margin-top: 14px; table-layout: fixed; }
    .smp .smp-report-identity th, .smp .smp-report-identity td { border: 0; padding: 2px 4px 2px 0; }
    .smp .smp-report-identity th { width: auto; }
    .smp .smp-report-identity th:nth-child(1) { width: 17%; }
    .smp .smp-report-identity td:nth-child(2) { width: 50%; }
    .smp .smp-report-identity th:nth-child(3) { width: 18%; }
    .smp .smp-report-identity td:nth-child(4) { width: 15%; }
    .smp .smp-report-identity th::after { content: ':'; float: right; margin-right: 5px; }
    .smp .smp-report-identity tr:nth-child(3) td:nth-child(2) { font-weight: 700; }
    .smp .report-grades { font-size: .82rem; margin-top: 18px; table-layout: fixed; }
    .smp .report-grades th, .smp .report-grades td { padding: 4px 5px; }
    .smp .report-grades th:nth-child(1), .smp .report-grades td:nth-child(1) { width: 4%; }
    .smp .report-grades th:nth-child(2), .smp .report-grades td:nth-child(2) { width: 28%; }
    .smp .report-grades th:nth-child(3), .smp .report-grades td:nth-child(3) { width: 7%; }
    .smp .report-grades th:nth-child(4), .smp .report-grades td:nth-child(4) { width: 7%; }
    .smp .report-grades th:nth-child(5), .smp .report-grades td:nth-child(5) { width: 22%; }
    .smp .report-grades th:nth-child(6), .smp .report-grades td:nth-child(6) { width: 32%; }
    .smp-assessment-table { border-collapse: collapse; font-size: .82rem; margin-top: 0; table-layout: fixed; width: 100%; }
    .smp-assessment-table th, .smp-assessment-table td { border: 1px solid #222; padding: 4px 5px; }
    .smp-assessment-table th { background: #0bb5e8; font-weight: 400; text-align: center; }
    .smp-assessment-table td:nth-child(1), .smp-assessment-table td:nth-child(4) { text-align: left; }
    .smp-assessment-table td:nth-child(2), .smp-assessment-table td:nth-child(3), .smp-assessment-table td:nth-child(5), .smp-assessment-table td:nth-child(6) { text-align: center; }
    .smp-assessment-table tbody td:nth-child(1) { width: 23%; }
    .smp-assessment-table tbody td:nth-child(2) { width: 11%; }
    .smp-assessment-table tbody td:nth-child(3) { width: 5%; }
    .smp-assessment-table tbody td:nth-child(4) { width: 38%; }
    .smp-assessment-table tbody td:nth-child(5) { width: 13%; }
    .smp-assessment-table tbody td:nth-child(6) { width: 10%; }
    .smp-homeroom-note { margin-top: 0; text-align: center; }
    .smp-homeroom-note h2 { border: 1px solid #222; font-size: .85rem; margin: 0; padding: 4px; }
    .smp-homeroom-note > div { align-items: center; background: #0bb5e8; border: 1px solid #222; border-top: 0; display: flex; font-size: .9rem; font-style: italic; font-weight: 700; justify-content: center; min-height: 46px; padding: 5px; }
    .smp-signatures { display: flex; justify-content: space-between; margin-top: 20px; min-height: 105px; }
    .smp-signatures > div { min-width: 32%; }
    .smp-signatures > div:last-child { text-align: left; }
    .smp-signature-line { border-bottom: 1px solid #222; height: 72px; margin-top: 4px; width: 75%; }
    @page {
        size: A4 portrait;
        margin: 12mm 10mm 12mm;
    }
    @media print {
        html, body { margin: 0 !important; padding: 0 !important; }
        body { background: #fff !important; }
        .admin-header, .admin-sidebar, .admin-footer, .sidebar-backdrop, .no-print, #loading-screen { display: none !important; }
        .admin-main, .admin-wrapper, .container-fluid { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .report-paper { border: 0; max-width: none; margin: 0; padding: 14mm 10mm 10mm; }
        .report-logo { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        .report-grades { font-size: 9pt; }
        .report-lower-grid { break-inside: avoid; }
        .smp.report-paper, .smp .report-grades, .smp-assessment-table, .smp-homeroom-note { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        .smp .report-grades, .smp-assessment-table { font-size: 8pt; }
        .smp-assessment-table, .smp-homeroom-note, .smp-signatures { break-inside: avoid; }
    }
    @media screen and (max-width: 700px) {
        .report-lower-grid { gap: 14px; grid-template-columns: 1fr; }
    }
</style>
<?= $this->endSection(); ?>
