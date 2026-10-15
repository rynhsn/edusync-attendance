<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <a class="text-decoration-none" href="<?= esc($backUrl); ?>">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke daftar nilai
        </a>
        <h1 class="h3 mt-3 mb-1">Detail Nilai Siswa</h1>
        <p class="text-muted mb-0">Rincian nilai untuk mata pelajaran dan semester terpilih.</p>
    </div>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Data Siswa</h2></div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nama Siswa</dt>
                <dd class="col-sm-9"><?= esc($student['nama_siswa']); ?></dd>
                <dt class="col-sm-3">NIS</dt>
                <dd class="col-sm-9"><?= esc($student['nis'] ?: '-'); ?></dd>
                <dt class="col-sm-3">Kelas</dt>
                <dd class="col-sm-9"><?= esc($student['kelas_tingkat'] . ' - ' . $student['kelas_nama']); ?></dd>
                <dt class="col-sm-3">Mata Pelajaran</dt>
                <dd class="col-sm-9"><?= esc($context['mapel_nama'] ?? '-'); ?></dd>
                <dt class="col-sm-3">Guru</dt>
                <dd class="col-sm-9"><?= esc($context['guru_nama'] ?? '-'); ?></dd>
                <dt class="col-sm-3">Tahun Ajaran</dt>
                <dd class="col-sm-9">
                    <?= esc($context['tahun_ajaran_nama'] ?? '-'); ?>
                    <?php if ($context !== null) : ?>- Semester <?= esc($context['semester']); ?><?php endif; ?>
                </dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2 class="h5 mb-0">Rincian Nilai</h2></div>
        <div class="card-body">
            <?php if ($grade === null) : ?>
                <div class="alert alert-info mb-0" role="status">Nilai untuk siswa ini belum diinput.</div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Komponen</th>
                                <th scope="col">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (['UTS' => 'nilai_uts', 'UAS' => 'nilai_uas', 'Pengetahuan' => 'nilai_pengetahuan', 'Praktik' => 'nilai_praktik'] as $label => $field) : ?>
                                <tr>
                                    <th scope="row"><?= esc($label); ?></th>
                                    <td>
                                        <?php if ($field === 'nilai_pengetahuan' && ! $hasKnowledgeGrade) : ?>
                                            Belum tersedia sampai migrasi database dijalankan
                                        <?php else : ?>
                                            <?= $grade[$field] !== null ? esc(number_format((float) $grade[$field], 2)) : 'Belum diinput'; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="form-text mt-3 mb-0">
                    Terakhir diperbarui: <?= esc($grade['updated_at'] ?? $grade['created_at'] ?? '-'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
