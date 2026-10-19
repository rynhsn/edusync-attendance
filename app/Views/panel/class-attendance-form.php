<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <a class="text-decoration-none" href="<?= site_url('class-attendance'); ?>"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke data absensi</a>
        <h1 class="h3 mt-3 mb-1"><?= $record === null ? 'Catat Absensi Kelas' : 'Edit Absensi Kelas'; ?></h1>
        <p class="text-muted mb-0">Pilih jadwal pelajaran, lalu tandai kehadiran setiap siswa di kelas tersebut.</p>
    </div>

    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <?php if ($classes === [] || $subjects === [] || $teachers === [] || $periods === [] || $academicYears === []) : ?>
        <div class="alert alert-warning" role="alert">
            Lengkapi data kelas, mapel, guru, jam pelajaran, dan tahun ajaran terlebih dahulu sebelum mencatat absensi.
            <?php if ($subjects === []) : ?><a href="<?= site_url('subjects'); ?>">Tambah mapel</a>.<?php endif; ?>
            <?php if ($teachers === []) : ?><a href="<?= site_url('teachers'); ?>">Tambah guru</a>.<?php endif; ?>
            <?php if ($periods === []) : ?><a href="<?= site_url('lesson-periods'); ?>">Tambah jam pelajaran</a>.<?php endif; ?>
            <?php if ($academicYears === []) : ?><a href="<?= site_url('academic-years'); ?>">Tambah tahun ajaran</a>.<?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $record === null ? site_url('class-attendance') : site_url('class-attendance/update/' . $record['absensi_kelas_id']); ?>" id="classAttendanceForm">
        <?= csrf_field(); ?>
        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 mb-0">Jadwal</h2></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="attendanceClass">Kelas</label>
                        <select class="form-select" id="attendanceClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $record !== null && (int) $record['kelas_id'] === (int) $class['kelas_id'] ? 'selected' : ''; ?>><?= esc($class['kelas_tingkat'] . ' - ' . $class['kelas_nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attendanceTeacher">Guru</label>
                        <select class="form-select" id="attendanceTeacher" name="guru_id" required>
                            <option value="">Pilih guru</option>
                            <?php foreach ($teachers as $teacher) : ?>
                                <option value="<?= esc($teacher['guru_id']); ?>" <?= $record !== null && (int) $record['guru_id'] === (int) $teacher['guru_id'] ? 'selected' : ''; ?>><?= esc($teacher['guru_nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attendanceSubject">Mata Pelajaran</label>
                        <select class="form-select" id="attendanceSubject" name="mapel_id" required>
                            <option value="">Pilih mapel</option>
                            <?php foreach ($subjects as $subject) : ?>
                                <option
                                    value="<?= esc($subject['mapel_id']); ?>"
                                    data-teachers="<?= esc(implode(',', $subjectTeacherIds[(int) $subject['mapel_id']] ?? [])); ?>"
                                    <?= $record !== null && (int) $record['mapel_id'] === (int) $subject['mapel_id'] ? 'selected' : ''; ?>><?= esc($subject['mapel_nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text" id="subjectAssignmentHint">Daftar mapel mengikuti pengaturan mapel untuk guru yang dipilih.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attendancePeriod">Jam Pelajaran</label>
                        <select class="form-select" id="attendancePeriod" name="jam_pelajaran_id" required>
                            <option value="">Pilih jam pelajaran</option>
                            <?php foreach ($periods as $period) : ?>
                                <option value="<?= esc($period['jam_pelajaran_id']); ?>" <?= $record !== null && (int) $record['jam_pelajaran_id'] === (int) $period['jam_pelajaran_id'] ? 'selected' : ''; ?>>Jam <?= esc($period['jam_ke']); ?><?= ! empty($period['jam_nama']) ? ' - ' . esc($period['jam_nama']) : ''; ?> (<?= esc(substr($period['jam_mulai'], 0, 5)); ?>–<?= esc(substr($period['jam_selesai'], 0, 5)); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attendanceYear">Tahun Ajaran</label>
                        <select class="form-select" id="attendanceYear" name="tahun_ajaran_id" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php foreach ($academicYears as $year) : ?>
                                <option value="<?= esc($year['tahun_ajaran_id']); ?>" <?= $record !== null && (int) $record['tahun_ajaran_id'] === (int) $year['tahun_ajaran_id'] ? 'selected' : ''; ?>><?= esc($year['tahun_ajaran_nama']); ?> - Semester <?= esc($year['semester']); ?><?= (bool) $year['is_active'] ? ' - Aktif' : ''; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attendanceDate">Tanggal</label>
                        <input class="form-control" id="attendanceDate" name="tanggal" type="date" value="<?= esc($record['tanggal'] ?? date('Y-m-d')); ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">Daftar Siswa</h2>
                <span id="visibleStudentCount" class="badge bg-primary-subtle text-primary">Pilih kelas</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th scope="col">No.</th><th scope="col">Nama Siswa</th><th scope="col">NIS</th><th scope="col">Status Kehadiran</th><th scope="col">Keterangan</th></tr>
                    </thead>
                    <tbody id="attendanceStudents">
                        <?php foreach ($students as $index => $student) : ?>
                            <tr data-class-id="<?= esc($student['kelas_id']); ?>" hidden>
                                <td><?= $index + 1; ?></td>
                                <td><?= esc($student['nama_siswa']); ?></td>
                                <td><?= esc($student['nis'] ?? '-'); ?></td>
                                <td>
                                    <select class="form-select" name="attendance[<?= esc($student['siswa_id']); ?>]" disabled required>
                                        <?php foreach ($statuses as $status => $label) : ?>
                                            <option value="<?= esc($status); ?>" <?= ($student['status'] ?? 'hadir') === $status ? 'selected' : ''; ?>><?= esc($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input class="form-control" name="keterangan[<?= esc($student['siswa_id']); ?>]" maxlength="500" placeholder="Opsional" value="<?= esc($student['keterangan'] ?? ''); ?>" disabled></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($students === []) : ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada siswa aktif.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (\App\Libraries\ActionAccess::can($record === null ? 'class-attendance.create' : 'class-attendance.edit')) : ?>
            <button class="btn btn-primary" type="submit" id="saveAttendance" <?= $classes === [] || $subjects === [] || $teachers === [] || $periods === [] || $academicYears === [] ? 'disabled' : ''; ?>>
                <i class="bi bi-check2-circle me-2" aria-hidden="true"></i><?= $record === null ? 'Simpan Absensi' : 'Simpan Perubahan'; ?>
            </button>
        <?php endif; ?>
    </form>
</div>

<script>
(() => {
    const classSelect = document.getElementById('attendanceClass');
    const teacherSelect = document.getElementById('attendanceTeacher');
    const subjectSelect = document.getElementById('attendanceSubject');
    const attendanceDate = document.getElementById('attendanceDate');
    const studentRows = Array.from(document.querySelectorAll('#attendanceStudents tr[data-class-id]'));
    const counter = document.getElementById('visibleStudentCount');
    const submitButton = document.getElementById('saveAttendance');
    const dutyTeacherSubjectIds = <?= json_encode($dutyTeacherSubjectIds, JSON_THROW_ON_ERROR); ?>;

    if (!classSelect || !teacherSelect || !subjectSelect || !attendanceDate || !counter || !submitButton) return;

    const getDutyDayName = () => {
        const value = attendanceDate.value;
        if (!value) return '';
        const parsed = new Date(`${value}T00:00:00`);
        if (Number.isNaN(parsed.getTime())) return '';
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return days[parsed.getDay()];
    };

    const updateAssignedSubjects = () => {
        const teacherId = teacherSelect.value;
        const dutySubjectsForTeacher = teacherId !== '' && dutyTeacherSubjectIds[teacherId]
            ? dutyTeacherSubjectIds[teacherId][getDutyDayName()] ?? []
            : [];
        const dutySubjectSet = new Set(dutySubjectsForTeacher.map(Number));
        let selectedStillAvailable = false;
        Array.from(subjectSelect.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }
            const teacherIds = (option.dataset.teachers || '').split(',').filter(Boolean).map(Number);
            const allowed = teacherId !== '' && (teacherIds.includes(Number(teacherId)) || dutySubjectSet.has(Number(option.value)));
            option.hidden = !allowed;
            option.disabled = !allowed;
            if (allowed && option.selected) selectedStillAvailable = true;
        });
        if (!selectedStillAvailable) subjectSelect.value = '';
        document.getElementById('subjectAssignmentHint').textContent = teacherId === ''
            ? 'Pilih guru untuk menampilkan mapel yang ditetapkan.'
            : selectedStillAvailable || Array.from(subjectSelect.options).some((option) => option.value !== '' && !option.disabled)
                ? 'Daftar mapel mengikuti pengaturan mapel untuk guru yang dipilih atau jadwal piket aktif.'
                : 'Belum ada mapel yang ditetapkan untuk guru ini. Atur melalui menu Settings > Mapel Guru atau Jadwal Piket Guru.';
    };

    const updateStudentRows = () => {
        let visibleCount = 0;
        studentRows.forEach((row) => {
            const visible = classSelect.value !== '' && row.dataset.classId === classSelect.value;
            row.hidden = !visible;
            row.querySelectorAll('select, input').forEach((field) => {
                field.disabled = !visible;
            });
            if (visible) visibleCount += 1;
        });

        counter.textContent = classSelect.value === '' ? 'Pilih kelas' : `${visibleCount} siswa`;
        submitButton.disabled = classSelect.value === '' || visibleCount === 0;
    };

    classSelect.addEventListener('change', updateStudentRows);
    teacherSelect.addEventListener('change', updateAssignedSubjects);
    attendanceDate.addEventListener('change', updateAssignedSubjects);
    updateAssignedSubjects();
    updateStudentRows();
})();
</script>
<?= $this->endSection(); ?>
