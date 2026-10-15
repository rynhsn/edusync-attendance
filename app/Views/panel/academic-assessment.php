<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= esc($title); ?></h1>
        <p class="text-muted mb-0">
            <?= $type === 'personality'
                ? 'Input penilaian perkembangan akhlak siswa untuk dicantumkan pada raport.'
                : 'Input penilaian kegiatan ' . ($type === 'kokurikuler' ? 'kokurikuler' : 'ekstrakurikuler') . ' siswa untuk dicantumkan pada raport.'; ?>
        </p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>
    <?php if ($selectionError !== null) : ?><div class="alert alert-danger" role="alert"><?= esc($selectionError); ?></div><?php endif; ?>

    <?php if ($type === 'personality') : ?>
    <div class="card mb-4">
            <div class="card-header"><h2 class="h5 mb-0">Kelola Aspek Kepribadian</h2></div>
            <div class="card-body">
                <p class="text-muted">Tambahkan atau ubah aspek yang akan dinilai. Menghapus aspek hanya menyembunyikannya dari input baru; nilai raport yang sudah tersimpan tetap ada.</p>
                <?php if (\App\Libraries\ActionAccess::can('academic-assessments.save')) : ?>
                <form method="post" action="<?= site_url('academic-assessments/personality'); ?>" class="row g-2 align-items-end mb-4">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="action" value="add_aspect">
                    <?php foreach ($selected as $name => $value) : ?>
                        <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                    <?php endforeach; ?>
                    <div class="col-md-8">
                        <label class="form-label" for="newPersonalityAspect">Aspek Kepribadian Baru</label>
                        <input class="form-control" id="newPersonalityAspect" name="nama_aspek" maxlength="100" required>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Tambah Aspek</button>
                    </div>
                </form>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light"><tr><th>No.</th><th>Nama Aspek</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php foreach ($personalityAspects as $index => $aspect) : ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td>
                                        <?php if (\App\Libraries\ActionAccess::can('academic-assessments.save')) : ?>
                                        <form method="post" action="<?= site_url('academic-assessments/personality'); ?>" class="d-flex gap-2">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="action" value="update_aspect">
                                            <input type="hidden" name="aspek_id" value="<?= esc($aspect['aspek_id']); ?>">
                                            <?php foreach ($selected as $name => $value) : ?>
                                                <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                                            <?php endforeach; ?>
                                            <label class="visually-hidden" for="personalityAspect<?= esc($aspect['aspek_id']); ?>">Nama aspek</label>
                                            <input class="form-control" id="personalityAspect<?= esc($aspect['aspek_id']); ?>" name="nama_aspek" value="<?= esc($aspect['nama_aspek']); ?>" maxlength="100" required>
                                            <button class="btn btn-outline-primary text-nowrap" type="submit">Simpan</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (\App\Libraries\ActionAccess::can('academic-assessments.save')) : ?>
                                        <form method="post" action="<?= site_url('academic-assessments/personality'); ?>" onsubmit="return confirm('Hapus aspek ini dari daftar penilaian baru? Nilai yang sudah tersimpan tetap ada.');">
                                            <?= csrf_field(); ?>
                                            <input type="hidden" name="action" value="delete_aspect">
                                            <input type="hidden" name="aspek_id" value="<?= esc($aspect['aspek_id']); ?>">
                                            <?php foreach ($selected as $name => $value) : ?>
                                                <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                                            <?php endforeach; ?>
                                            <button class="btn btn-outline-danger" type="submit">Hapus</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($personalityAspects === []) : ?>
                                <tr><td colspan="3" class="text-center text-muted">Belum ada aspek kepribadian.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Pilih Data Siswa</h2></div>
        <div class="card-body">
            <form method="get" action="<?= site_url('academic-assessments/' . $type); ?>" id="assessmentSelectionForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="assessmentClass">Kelas</label>
                        <select class="form-select" id="assessmentClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $selected['kelas_id'] === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                    <?= esc($class['kelas_tingkat'] . ' - ' . $class['kelas_nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="assessmentYear">Tahun Ajaran / Semester</label>
                        <select class="form-select" id="assessmentYear" name="tahun_ajaran_id" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= esc($year['tahun_ajaran_id']); ?>" <?= $selected['tahun_ajaran_id'] === (int) $year['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                    <?= esc($year['tahun_ajaran_nama']); ?> - Semester <?= (int) $year['semester'] === 1 ? 'Ganjil' : 'Genap'; ?><?= (bool) $year['is_active'] ? ' - Aktif' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="assessmentStudent">Nama Siswa</label>
                        <select class="form-select" id="assessmentStudent" name="siswa_id">
                            <option value="">Pilih siswa</option>
                            <?php foreach ($students as $row) : ?>
                                <option value="<?= esc($row['siswa_id']); ?>" <?= $selected['siswa_id'] === (int) $row['siswa_id'] ? 'selected' : ''; ?>>
                                    <?= esc($row['nama_siswa']); ?><?= ! empty($row['nis']) ? ' - ' . esc($row['nis']) : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search me-2" aria-hidden="true"></i>Tampilkan Penilaian
                        </button>
                        <?php if ($selected['kelas_id'] !== null && $students === []) : ?>
                            <span class="form-text ms-2">Tidak ada siswa aktif pada kelas ini.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($student !== null && ($type !== 'kokurikuler' || $kokurikulerTableAvailable)) : ?>
        <div class="card">
            <div class="card-header">
                <h2 class="h5 mb-0"><?= esc($student['nama_siswa']); ?><?= ! empty($student['nis']) ? ' - ' . esc($student['nis']) : ''; ?></h2>
            </div>
            <div class="card-body">
                <?php if ($type === 'personality') : ?>
                <?php if (\App\Libraries\ActionAccess::can('academic-assessments.save')) : ?>
                <form method="post" action="<?= site_url('academic-assessments/personality'); ?>" id="assessmentForm">
                    <?= csrf_field(); ?>
                    <?php foreach ($selected as $name => $value) : ?>
                        <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                    <?php endforeach; ?>

                    <section class="mb-4">
                        <h3 class="h6 fw-bold">B. Penilaian Perkembangan Akhlak</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light"><tr><th>No.</th><th>Kepribadian</th><th>Nilai</th></tr></thead>
                                <tbody>
                                    <?php foreach ($personalityAspects as $index => $aspect) : ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <th scope="row"><?= esc($aspect['nama_aspek']); ?></th>
                                            <td>
                                                <select class="form-select" name="nilai_aspek[<?= esc($aspect['aspek_id']); ?>]" aria-label="Nilai <?= esc($aspect['nama_aspek']); ?>">
                                                    <option value="">Pilih nilai</option>
                                                    <?php foreach ($values as $value) : ?>
                                                        <option value="<?= esc($value); ?>" <?= ($personalityGrades[$aspect['aspek_id']] ?? '') === $value ? 'selected' : ''; ?>><?= esc($value); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($personalityAspects === []) : ?>
                                        <tr><td colspan="3" class="text-center text-muted">Tambahkan aspek kepribadian terlebih dahulu.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Penilaian Kepribadian
                        </button>
                    </section>
                </form>
                <?php endif; ?>
                <?php endif; ?>

                <?php if (in_array($type, ['extracurricular', 'kokurikuler'], true)) : ?>
                <?php if (\App\Libraries\ActionAccess::can('academic-assessments.save')) : ?>
                <?php
                $activityLabel = $type === 'kokurikuler' ? 'Kokurikuler' : 'Ekstrakurikuler';
                $activityAssessments = $type === 'kokurikuler' ? $kokurikulerGrades : $extracurriculars;
                $activityOptions = $type === 'kokurikuler' ? $kokurikulerOptions : $extracurricularOptions;
                ?>
                <form method="post" action="<?= site_url('academic-assessments/' . $type); ?>" id="activityAssessmentForm">
                    <?= csrf_field(); ?>
                    <?php foreach ($selected as $name => $value) : ?>
                        <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                    <?php endforeach; ?>

                    <section>
                        <h3 class="h6 fw-bold">Penilaian Kegiatan <?= esc($activityLabel); ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light"><tr><th>No.</th><th><?= esc($activityLabel); ?></th><th>Nilai</th><th class="no-print">Aksi</th></tr></thead>
                                <tbody id="assessmentActivityRows">
                                    <?php foreach ($activityAssessments as $index => $activity) : ?>
                                        <tr>
                                            <td class="row-number"><?= $index + 1; ?></td>
                                            <td>
                                                <select class="form-select" name="kegiatan[]" aria-label="Nama <?= esc($activityLabel); ?> <?= $index + 1; ?>">
                                                    <option value="">Pilih <?= esc(strtolower($activityLabel)); ?></option>
                                                    <?php foreach ($activityOptions as $option) : ?>
                                                        <option value="<?= esc($option); ?>" <?= $activity['nama_kegiatan'] === $option ? 'selected' : ''; ?>><?= esc($option); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select" name="nilai[]" aria-label="Nilai <?= esc($activityLabel); ?> <?= $index + 1; ?>">
                                                    <option value="">Pilih nilai</option>
                                                    <?php foreach ($values as $value) : ?>
                                                        <option value="<?= esc($value); ?>" <?= $activity['nilai'] === $value ? 'selected' : ''; ?>><?= esc($value); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="no-print"><button class="btn btn-outline-danger btn-sm remove-activity" type="button">Hapus</button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($activityAssessments === []) : ?>
                                        <tr>
                                            <td class="row-number">1</td>
                                            <td>
                                                <select class="form-select" name="kegiatan[]" aria-label="Nama <?= esc($activityLabel); ?> 1">
                                                    <option value="">Pilih <?= esc(strtolower($activityLabel)); ?></option>
                                                    <?php foreach ($activityOptions as $option) : ?>
                                                        <option value="<?= esc($option); ?>"><?= esc($option); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select" name="nilai[]" aria-label="Nilai <?= esc($activityLabel); ?> 1">
                                                    <option value="">Pilih nilai</option>
                                                    <?php foreach ($values as $value) : ?><option value="<?= esc($value); ?>"><?= esc($value); ?></option><?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="no-print"><button class="btn btn-outline-danger btn-sm remove-activity" type="button">Hapus</button></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-outline-primary btn-sm mb-3 no-print" id="addAssessmentActivity" type="button">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah <?= esc($activityLabel); ?>
                        </button>
                        <div><button class="btn btn-primary" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Penilaian <?= esc($activityLabel); ?></button></div>
                    </section>
                </form>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
(() => {
    const selectionForm = document.getElementById('assessmentSelectionForm');
    const classSelect = document.getElementById('assessmentClass');
    const yearSelect = document.getElementById('assessmentYear');
    const studentSelect = document.getElementById('assessmentStudent');
    if (selectionForm && classSelect && yearSelect && studentSelect) {
        const loadStudents = () => {
            studentSelect.value = '';
            if (classSelect.value && yearSelect.value) selectionForm.requestSubmit();
        };

        classSelect.addEventListener('change', loadStudents);
        yearSelect.addEventListener('change', loadStudents);
        studentSelect.addEventListener('change', () => {
            if (classSelect.value && yearSelect.value && studentSelect.value) {
                selectionForm.requestSubmit();
            }
        });
    }

    const rows = document.getElementById('assessmentActivityRows');
    const addButton = document.getElementById('addAssessmentActivity');
    if (!rows || !addButton) return;

    const renumber = () => {
        Array.from(rows.querySelectorAll('tr')).forEach((row, index) => {
            row.querySelector('.row-number').textContent = String(index + 1);
        });
    };

    const addRow = () => {
        if (rows.children.length >= 50) return;
        const row = document.createElement('tr');
        const rowNumber = document.createElement('td');
        rowNumber.className = 'row-number';
        row.append(rowNumber);

        const activityCell = document.createElement('td');
        const activitySelect = rows.querySelector('select[name="kegiatan[]"]').cloneNode(true);
        activitySelect.value = '';
        activitySelect.setAttribute('aria-label', 'Nama kegiatan');
        activityCell.append(activitySelect);
        row.append(activityCell);

        const gradeCell = document.createElement('td');
        const gradeSelect = rows.querySelector('select[name="nilai[]"]').cloneNode(true);
        gradeSelect.value = '';
        gradeSelect.setAttribute('aria-label', 'Nilai kegiatan');
        gradeCell.append(gradeSelect);
        row.append(gradeCell);

        const actionCell = document.createElement('td');
        actionCell.className = 'no-print';
        actionCell.innerHTML = '<button class="btn btn-outline-danger btn-sm remove-activity" type="button">Hapus</button>';
        row.append(actionCell);
        rows.append(row);
        renumber();
    };

    addButton.addEventListener('click', addRow);
    rows.addEventListener('click', (event) => {
        if (!event.target.closest('.remove-activity')) return;
        if (rows.children.length === 1) {
            rows.querySelector('select[name="kegiatan[]"]').value = '';
            rows.querySelector('select[name="nilai[]"]').value = '';
            return;
        }
        event.target.closest('tr').remove();
        renumber();
    });
})();
</script>
<?= $this->endSection(); ?>
