<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Input Nilai Siswa</h1>
            <p class="text-muted mb-0">Masukkan nilai UTS, UAS, Pengetahuan, dan Praktik berdasarkan kelas, mata pelajaran, guru, dan tahun ajaran.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if ($isAdmin) : ?>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-shield-check me-1"></i>Mode Manajemen (<?= esc(\App\Libraries\RoleAccess::label($authUserRole ?? '')); ?>)
                </span>
            <?php elseif (($authUserRole ?? '') === 'wali_kelas') : ?>
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-person-workspace me-1"></i>Wali Kelas: <?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?>
                </span>
            <?php else : ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-person-badge me-1"></i>Guru: <?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="status">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div><?= esc($message); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div><?= esc($error); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (! $hasKnowledgeGrade) : ?>
        <div class="alert alert-warning d-flex align-items-center gap-2" role="status">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <div>Kolom penyimpanan <strong>Nilai Pengetahuan</strong> belum tersedia di database. Input tetap ditampilkan, tetapi nilainya belum dapat disimpan permanen sebelum migrasi database dijalankan oleh administrator.</div>
        </div>
    <?php endif; ?>
    <?php if (! empty($selectionError)) : ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
            <div><?= esc($selectionError); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($isAdmin && ($classes === [] || $years === [] || $subjects === [] || $teachers === [])) : ?>
        <div class="alert alert-warning border-start border-4 border-warning shadow-sm mb-4" role="alert">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Data Master Belum Lengkap</div>
            <div class="small">Lengkapi data kelas, tahun ajaran, mata pelajaran, guru, dan penetapan mapel guru sebelum menginput nilai:</div>
            <ul class="mb-0 mt-1 small ps-3">
                <?php if ($subjects === []) : ?><li><a href="<?= site_url('subjects'); ?>">Tambah mapel</a></li><?php endif; ?>
                <?php if ($teachers === []) : ?><li><a href="<?= site_url('teachers'); ?>">Tambah guru</a></li><?php endif; ?>
                <?php if ($years === []) : ?><li><a href="<?= site_url('academic-years'); ?>">Tambah tahun ajaran</a></li><?php endif; ?>
                <li><a href="<?= site_url('teacher-subjects'); ?>">Atur penetapan Mapel Guru</a></li>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (! $isAdmin && empty($subjects)) : ?>
        <div class="alert alert-warning border-start border-4 border-warning shadow-sm rounded-3 d-flex align-items-start gap-3 p-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0 mt-1"></i>
            <div>
                <h2 class="h6 fw-bold mb-1">Mata Pelajaran Belum Ditetapkan</h2>
                <p class="mb-0 text-secondary small">
                    Akun guru Anda (<strong><?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?></strong>) belum memiliki mata pelajaran yang ditetapkan pada sistem.
                    Silakan hubungi <strong>Kurikulum</strong> atau <strong>Administrator</strong> untuk mengatur mata pelajaran Anda pada menu <strong>Mapel Guru</strong> agar dapat menginput nilai.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <h2 class="h6 mb-0 fw-bold text-dark">
                    <i class="bi bi-sliders text-primary me-2"></i>Pilih Data Akademik
                </h2>
                <?php if (! $isAdmin) : ?>
                    <span class="badge bg-light text-secondary border">
                        <i class="bi bi-lock me-1"></i>Mapel disesuaikan dengan penetapan akun Anda
                    </span>
                <?php else : ?>
                    <span class="badge bg-light text-primary border">
                        <i class="bi bi-shuffle me-1"></i>Filter interaktif Guru &amp; Mapel aktif
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="get" action="<?= site_url('grades'); ?>" id="gradeFilters">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2 d-block" for="gradeClass">
                            Kelas <span class="text-danger">*</span>
                        </label>
                        <select class="form-select shadow-none" id="gradeClass" name="kelas_id" required>
                            <option value="">Pilih kelas</option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= esc($class['kelas_id']); ?>" <?= $selected['kelas_id'] === (int) $class['kelas_id'] ? 'selected' : ''; ?>>
                                    <?= esc($class['kelas_tingkat'] . ' - ' . $class['kelas_nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2 d-block" for="gradeYear">
                            Tahun Ajaran <span class="text-danger">*</span>
                        </label>
                        <select class="form-select shadow-none" id="gradeYear" name="tahun_ajaran_id" required>
                            <option value="">Pilih tahun ajaran</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= esc($year['tahun_ajaran_id']); ?>" <?= $selected['tahun_ajaran_id'] === (int) $year['tahun_ajaran_id'] ? 'selected' : ''; ?>>
                                    <?= esc($year['tahun_ajaran_nama']); ?> - Semester <?= esc($year['semester']); ?><?= (bool) $year['is_active'] ? ' (Aktif)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2 d-flex justify-content-between align-items-center" for="gradeSubject">
                            <span>Mata Pelajaran <span class="text-danger">*</span></span>
                            <?php if (! $isAdmin) : ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-normal" style="font-size: 0.68rem;">Mapel Anda</span>
                            <?php endif; ?>
                        </label>
                        <select class="form-select shadow-none" id="gradeSubject" name="mapel_id" required <?= (! $isAdmin && empty($subjects)) ? 'disabled' : ''; ?>>
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
                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2 d-flex justify-content-between align-items-center" for="gradeTeacher">
                            <span>Guru Pengampu <span class="text-danger">*</span></span>
                            <?php if (! $isAdmin) : ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-normal" style="font-size: 0.68rem;">Terkunci</span>
                            <?php endif; ?>
                        </label>
                        <?php if ($isAdmin) : ?>
                            <select class="form-select shadow-none" id="gradeTeacher" name="guru_id" required>
                                <option value="">Pilih guru</option>
                                <?php foreach ($teachers as $teacher) : ?>
                                    <?php $teacherSubjectIds = $subjectsByTeacher[(int) $teacher['guru_id']] ?? []; ?>
                                    <option
                                        value="<?= esc($teacher['guru_id']); ?>"
                                        data-subjects="<?= esc(implode(',', $teacherSubjectIds)); ?>"
                                        <?= $selected['guru_id'] === (int) $teacher['guru_id'] ? 'selected' : ''; ?>>
                                        <?= esc($teacher['guru_nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <input type="hidden" id="gradeTeacher" name="guru_id" value="<?= esc($currentGuruId ?? ''); ?>">
                            <div class="position-relative">
                                <input type="text" class="form-control bg-light text-dark fw-medium pe-5 shadow-none" value="<?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?>" readonly style="cursor: not-allowed;" title="Akun guru Anda otomatis digunakan">
                                <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" title="Akun guru Anda otomatis digunakan">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-primary px-4 fw-medium shadow-sm" type="submit" <?= (! $isAdmin && empty($subjects)) ? 'disabled' : ''; ?>>
                                <i class="bi bi-search me-1.5" aria-hidden="true"></i>Tampilkan Siswa
                            </button>
                            <?php if (! empty($selected['kelas_id']) || ! empty($selected['mapel_id']) || ! empty($selected['tahun_ajaran_id'])) : ?>
                                <a href="<?= site_url('grades'); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted d-flex align-items-center">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <span>Pilihan mapel dan guru disesuaikan dengan penetapan pada menu <strong>Mapel Guru</strong>.</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectionError === null && ! in_array(null, $selected, true)) : ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h2 class="h6 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Daftar Nilai Siswa</h2>
                    <small class="text-muted">Masukkan nilai UTS, UAS, Pengetahuan, dan Praktik untuk siswa berikut</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-medium">
                        <i class="bi bi-people-fill me-1"></i><?= count($studentRows); ?> Siswa
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if ($studentRows === []) : ?>
                    <div class="p-4">
                        <div class="alert alert-info mb-0 d-flex align-items-center gap-2" role="status">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div>Tidak ada siswa aktif di kelas yang dipilih.</div>
                        </div>
                    </div>
                <?php else : ?>
                    <form method="post" action="<?= site_url('grades'); ?>" id="gradeForm">
                        <?= csrf_field(); ?>
                        <?php foreach ($selected as $name => $value) : ?>
                            <input type="hidden" name="<?= esc($name); ?>" value="<?= esc($value); ?>">
                        <?php endforeach; ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-nowrap">
                                    <tr>
                                        <th scope="col" class="text-center" style="width: 50px;">No.</th>
                                        <th scope="col" style="min-width: 180px;">Nama Siswa</th>
                                        <th scope="col" class="text-center" style="min-width: 100px;">NIS</th>
                                        <th scope="col" class="text-center" style="min-width: 120px;">Nilai UTS</th>
                                        <th scope="col" class="text-center" style="min-width: 120px;">Nilai UAS</th>
                                        <th scope="col" class="text-center" style="min-width: 120px;">Nilai Pengetahuan</th>
                                        <th scope="col" class="text-center" style="min-width: 120px;">Nilai Praktik</th>
                                        <th scope="col" class="text-center" style="width: 110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentRows as $index => $student) : ?>
                                        <tr>
                                            <td class="text-center fw-medium text-muted"><?= $index + 1; ?></td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= esc($student['nama_siswa']); ?></div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border font-monospace"><?= esc($student['nis'] ?: '-'); ?></span>
                                            </td>
                                            <?php foreach (['uts' => 'nilai_uts', 'uas' => 'nilai_uas', 'pengetahuan' => 'nilai_pengetahuan', 'praktik' => 'nilai_praktik'] as $component => $column) : ?>
                                                <td>
                                                    <input
                                                        class="form-control text-center mx-auto shadow-none"
                                                        style="max-width: 105px;"
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
                                            <td class="text-center">
                                                <a
                                                    class="btn btn-sm btn-outline-primary"
                                                    href="<?= site_url('grades/student/' . $student['siswa_id'] . '?' . http_build_query($selected)); ?>"
                                                    title="Lihat detail nilai siswa">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 bg-light border-top d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Rentang nilai <strong>0 sampai 100</strong>. Desimal menggunakan tanda titik (contoh: 85.50). Kolom nilai dapat dikosongkan jika belum tersedia.
                            </div>
                            <?php if (\App\Libraries\ActionAccess::can('grades.save')) : ?>
                                <button class="btn btn-success px-4 fw-semibold shadow-sm text-nowrap" type="submit">
                                    <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>Simpan Perubahan Nilai
                                </button>
                            <?php else : ?>
                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">
                                    <i class="bi bi-eye me-1"></i>Hanya Lihat (Tidak memiliki hak akses simpan nilai)
                                </span>
                            <?php endif; ?>
                        </div>
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

    const isTeacherSelect = teacher.tagName.toLowerCase() === 'select';
    if (!isTeacherSelect) {
        // Profil guru terkunci untuk user non-admin. Pilihan mapel sudah disaring dari server.
        return;
    }

    let isUpdating = false;

    const updateTeachers = () => {
        if (isUpdating) return;
        isUpdating = true;
        const selectedSubjectOption = subject.options[subject.selectedIndex];
        const allowedTeachers = (selectedSubjectOption?.dataset.teachers || '').split(',').filter(Boolean);

        let selectedTeacherValid = false;
        Array.from(teacher.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }
            const isAllowed = subject.value === '' || allowedTeachers.includes(option.value);
            option.hidden = !isAllowed;
            option.disabled = !isAllowed;
            if (isAllowed && option.value === teacher.value) {
                selectedTeacherValid = true;
            }
        });

        if (!selectedTeacherValid && subject.value !== '') {
            teacher.value = '';
        }
        isUpdating = false;
    };

    const updateSubjects = () => {
        if (isUpdating) return;
        isUpdating = true;
        const selectedTeacherOption = teacher.options[teacher.selectedIndex];
        const allowedSubjects = (selectedTeacherOption?.dataset.subjects || '').split(',').filter(Boolean);

        let selectedSubjectValid = false;
        Array.from(subject.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                option.disabled = false;
                return;
            }
            const isAllowed = teacher.value === '' || allowedSubjects.includes(option.value);
            option.hidden = !isAllowed;
            option.disabled = !isAllowed;
            if (isAllowed && option.value === subject.value) {
                selectedSubjectValid = true;
            }
        });

        if (!selectedSubjectValid && teacher.value !== '') {
            subject.value = '';
        }
        isUpdating = false;
    };

    teacher.addEventListener('change', updateSubjects);
    subject.addEventListener('change', updateTeachers);

    // Inisialisasi awal saat halaman dimuat
    if (teacher.value !== '') {
        updateSubjects();
    } else if (subject.value !== '') {
        updateTeachers();
    }
})();
</script>
<?= $this->endSection(); ?>
