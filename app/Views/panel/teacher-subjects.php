<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Mata Pelajaran Guru</h1>
        <p class="text-muted mb-0">Pilih mata pelajaran yang diajarkan oleh masing-masing guru.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= site_url('teacher-subjects'); ?>" class="row g-3 align-items-end">
                <div class="col-md-8 col-lg-6">
                    <label class="form-label" for="teacherId">Pilih Guru</label>
                    <select class="form-select" id="teacherId" name="guru_id" required <?= $teachers === [] ? 'disabled' : ''; ?>>
                        <?php foreach ($teachers as $teacher) : ?>
                            <option value="<?= esc($teacher['guru_id']); ?>" <?= $selectedTeacherId === (int) $teacher['guru_id'] ? 'selected' : ''; ?>>
                                <?= esc($teacher['guru_nama']); ?><?= $teacher['guru_nip'] ? ' (' . esc($teacher['guru_nip']) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary" type="submit" <?= $teachers === [] ? 'disabled' : ''; ?>>
                        <i class="bi bi-arrow-repeat me-2" aria-hidden="true"></i>Tampilkan Mapel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($teachers === []) : ?>
        <div class="alert alert-info" role="status">Belum ada data guru. Tambahkan data guru terlebih dahulu.</div>
    <?php elseif ($subjects === []) : ?>
        <div class="alert alert-info" role="status">Belum ada data mata pelajaran. Tambahkan data mapel terlebih dahulu.</div>
    <?php else : ?>
        <div class="alert alert-info" role="note">
            Centang mapel yang diajarkan guru ini. Pilihan tersebut akan digunakan pada formulir absensi kelas.
        </div>

        <div class="card">
            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <h2 class="h5 card-title mb-0">Mata Pelajaran Guru</h2>
                <span class="badge bg-primary-subtle text-primary"><?= count($subjects); ?> mapel</span>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('teacher-subjects'); ?>">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="guru_id" value="<?= esc($selectedTeacherId); ?>">
                    <div class="row g-3">
                        <?php foreach ($subjects as $subject) : ?>
                            <?php
                            $subjectId = (int) $subject['mapel_id'];
                            $inputId = 'teacher_subject_' . $subjectId;
                            ?>
                            <div class="col-md-6 col-xl-4">
                                <label class="panel-checkbox-card h-100" for="<?= esc($inputId); ?>">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="<?= esc($inputId); ?>"
                                        name="subjects[]"
                                        value="<?= esc($subjectId); ?>"
                                        <?= in_array($subjectId, $assignedSubjectIds, true) ? 'checked' : ''; ?>>
                                    <span class="fw-semibold">
                                        <?= esc($subject['mapel_nama']); ?>
                                        <?php if (! empty($subject['mapel_kode'])) : ?>
                                            <span class="text-muted small">(<?= esc($subject['mapel_kode']); ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <?php if (\App\Libraries\ActionAccess::can('teacher-subjects.save')) : ?>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Mapel Guru
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
