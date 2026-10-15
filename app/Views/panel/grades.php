<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Input Nilai Siswa</h1>
        <p class="text-muted mb-0">Masukkan nilai UTS, UAS, dan Praktik berdasarkan kelas, mata pelajaran, guru, dan tahun ajaran.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>
    <?php if (! $hasKnowledgeGrade) : ?>
        <div class="alert alert-warning" role="status">
            Kolom penyimpanan Nilai Pengetahuan belum tersedia di database. Input tetap ditampilkan, tetapi nilainya belum dapat disimpan sebelum migrasi database dijalankan oleh administrator.
        </div>
    <?php endif; ?>
    <?php if (! empty($selectionError)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($selectionError); ?></div>
    <?php endif; ?>

    <?php if ($classes === [] || $years === [] || $subjects === [] || $teachers === []) : ?>
        <div class="alert alert-warning" role="alert">
            Lengkapi data kelas, tahun ajaran, mata pelajaran, guru, dan penetapan mapel guru sebelum menginput nilai.
            <?php if ($subjects === []) : ?><a href="<?= site_url('subjects'); ?>">Tambah mapel</a>.<?php endif; ?>
            <?php if ($teachers === []) : ?><a href="<?= site_url('teachers'); ?>">Tambah guru</a>.<?php endif; ?>
            <?php if ($years === []) : ?><a href="<?= site_url('academic-years'); ?>">Tambah tahun ajaran</a>.<?php endif; ?>
            <a href="<?= site_url('teacher-subjects'); ?>">Atur mapel guru</a>.
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Pilih Data Akademik</h2></div>
        <div class="card-body">
            <form method="get" action="<?= site_url('grades'); ?>" id="gradeFilters">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="gradeClass">Kelas</label>
                        <select class="form-select" id="gradeClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $selected['kelas_id'] === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                    <?= esc($class['kelas_tingkat'] . ' - ' . $class['kelas_nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="gradeYear">Tahun Ajaran</label>
                        <select class="form-select" id="gradeYear" name="tahun_ajaran_id" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= esc($year['tahun_ajaran_id']); ?>" <?= $selected['tahun_ajaran_id'] === (int) $year['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                    <?= esc($year['tahun_ajaran_nama']); ?> - Semester <?= esc($year['semester']); ?><?= (bool) $year['is_active'] ? ' - Aktif' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="gradeSubject">Mata Pelajaran</label>
                        <select class="form-select" id="gradeSubject" name="mapel_id" required>
                            <option value="">Pilih mapel</option>
                            <?php foreach ($subjects as $subject) : ?>
                                <?php $subjectTeacherIds = $teachersBySubject[(int) $subject['mapel_id']] ?? []; ?>
                                <option
                                    value="<?= esc($subject['mapel_id']); ?>"
                                    data-teachers="<?= esc(implode(',', $subjectTeacherIds)); ?>"
                                    <?= $selected['mapel_id'] === (int) $subject['mapel_id'] ? 'selected' : ''; ?>>
                                    <?= esc($subject['mapel_nama']); ?><?= ! empty($subject['mapel_kode']) ? ' (' . esc($subject['mapel_kode']) . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label" for="gradeTeacher">Guru</label>
                        <select class="form-select" id="gradeTeacher" name="guru_id" required>
                            <option value="">Pilih guru</option>
                            <?php foreach ($teachers as $teacher) : ?>
                                <option
                                    value="<?= esc($teacher['guru_id']); ?>"
                                    <?= $selected['guru_id'] === (int) $teacher['guru_id'] ? 'selected' : ''; ?>>
                                    <?= esc($teacher['guru_nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-funnel me-2" aria-hidden="true"></i>Tampilkan Siswa
                        </button>
                        <span class="form-text ms-2">Mapel dan guru harus sesuai dengan penetapan pada menu Mapel Guru.</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectionError === null && ! in_array(null, $selected, true)) : ?>
        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <h2 class="h5 mb-0">Nilai Siswa</h2>
                <span class="badge bg-primary-subtle text-primary"><?= count($studentRows); ?> siswa</span>
            </div>
            <div class="card-body">
                <?php if ($studentRows === []) : ?>
                    <div class="alert alert-info mb-0" role="status">Tidak ada siswa aktif di kelas yang dipilih.</div>
                <?php else : ?>
                    <form method="post" action="<?= site_url('grades'); ?>">
                        <?= csrf_field(); ?>
                        <?php foreach ($selected as $name => $value) : ?>
                            <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                        <?php endforeach; ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No.</th>
                                        <th scope="col">Nama Siswa</th>
                                        <th scope="col">NIS</th>
                                        <th scope="col">Nilai UTS</th>
                                        <th scope="col">Nilai UAS</th>
                                        <th scope="col">Nilai Pengetahuan</th>
                                        <th scope="col">Nilai Praktik</th>
                                        <th scope="col">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentRows as $index => $student) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= esc($student['nama_siswa']); ?></td>
                                            <td><?= esc($student['nis'] ?? '-'); ?></td>
                                            <?php foreach (['uts' => 'nilai_uts', 'uas' => 'nilai_uas', 'pengetahuan' => 'nilai_pengetahuan', 'praktik' => 'nilai_praktik'] as $component => $column) : ?>
                                                <td>
                                                    <input
                                                        class="form-control"
                                                        type="number"
                                                        name="nilai[<?= esc($student['siswa_id']); ?>][<?= esc($component); ?>]"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        value="<?= esc($student[$column] ?? ''); ?>"
                                                        aria-label="Nilai <?= esc(ucfirst($component)); ?> <?= esc($student['nama_siswa']); ?>"
                                                        placeholder="0–100">
                                                </td>
                                            <?php endforeach; ?>
                                            <td>
                                                <a
                                                    class="btn btn-sm btn-outline-primary"
                                                    href="<?= site_url('grades/student/' . $student['siswa_id'] . '?' . http_build_query($selected)); ?>">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="form-text">Nilai boleh dikosongkan jika belum tersedia. Rentang nilai 0 sampai 100.</p>
                        <?php if (\App\Libraries\ActionAccess::can('grades.save')) : ?>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Nilai
                            </button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
(() => {
    const subject = document.getElementById('gradeSubject');
    const teacher = document.getElementById('gradeTeacher');
    const filterForm = document.getElementById('gradeFilters');
    if (!subject || !teacher || !filterForm) return;

    const updateTeachers = () => {
        const allowedTeachers = (subject.selectedOptions[0]?.dataset.teachers || '').split(',').filter(Boolean);
        let selectedTeacherAvailable = false;
        Array.from(teacher.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }
            const allowed = allowedTeachers.includes(option.value);
            option.hidden = !allowed;
            option.disabled = !allowed;
            if (allowed && option.selected) selectedTeacherAvailable = true;
        });
        if (!selectedTeacherAvailable) teacher.value = '';
    };

    subject.addEventListener('change', updateTeachers);
    updateTeachers();
})();
</script>
<?= $this->endSection(); ?>
