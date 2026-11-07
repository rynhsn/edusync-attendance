<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($config['title']); ?></h1>
            <p class="text-muted mb-0">
                <?= $type === 'subjects'
                    ? 'Atur kelompok setiap mata pelajaran untuk pengelompokan raport SMK.'
                    : ($type === 'teacher-duty-schedules'
                        ? 'Atur guru piket berdasarkan hari, jam, dan tugas untuk menangani keterlambatan siswa serta mengabsen kelas saat guru berhalangan hadir.'
                    : ($type === 'parents'
                        ? 'Kelola kontak orang tua/wali, hubungkan dengan siswa melalui pencarian, dan gunakan untuk notifikasi absensi.'
                        : 'Kelola data yang digunakan pada pencatatan absensi kelas.')); ?>
            </p>
        </div>
        <?php if (\App\Libraries\ActionAccess::can($type . '.create')) : ?>
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createAcademicData" aria-expanded="<?= ! empty($error) && empty($editRecord) ? 'true' : 'false'; ?>" aria-controls="createAcademicData">
                <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Tambah Data
            </button>
        <?php endif; ?>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>
    <?php if (! empty($warning)) : ?>
        <div class="alert alert-warning" role="alert"><?= esc($warning); ?></div>
    <?php endif; ?>
    <?php if (in_array($type, ['subjects', 'teachers', 'parents', 'lesson-periods', 'academic-years'], true)) : ?>
        <?= $this->include('panel/partials/master-data-import-export'); ?>
    <?php endif; ?>
    <?php if (! empty($editRecord) && \App\Libraries\ActionAccess::can($type . '.edit')) : ?>
        <div class="card mb-4">
            <div class="card-header"><h2 class="h5 mb-0">Edit <?= esc($config['title']); ?></h2></div>
            <div class="card-body">
                <form method="post" action="<?= site_url($type . '/update/' . $editRecord[$config['key']]); ?>" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="row g-3">
                        <?php foreach ($config['fields'] as $field) : ?>
                            <?php $fieldValue = $editRecord[$field['name']] ?? ''; ?>
                            <?php if (($field['type'] ?? '') === 'student-picker') : ?>
                                <?= $this->include('panel/partials/parent-student-picker', [
                                    'students' => $students,
                                    'selectedStudentIds' => $selectedStudentIds,
                                    'pickerId' => 'editParentStudents',
                                ]); ?>
                            <?php elseif (($field['type'] ?? '') === 'photo') : ?>
                                <div class="col-md-6">
                                    <label class="form-label" for="edit_<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <input class="form-control" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    <div class="form-text">JPG, PNG, atau WebP; maksimal 2 MB. Latar foto diproses otomatis dan menjadi merah pada ID card. Kosongkan saat edit untuk mempertahankan foto saat ini.</div>
                                    <div class="teacher-photo-preview mt-2" id="edit_<?= esc($field['name']); ?>_preview" <?= empty($fieldValue) ? 'hidden' : ''; ?>>
                                        <img id="edit_<?= esc($field['name']); ?>_preview_img" src="<?= ! empty($fieldValue) ? base_url('uploads/student-photos/' . rawurlencode(basename((string) $fieldValue))) : ''; ?>" alt="Pratinjau <?= esc($field['label']); ?>">
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'checkbox') : ?>
                                <div class="col-12">
                                    <input type="hidden" name="<?= esc($field['name']); ?>" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="checkbox" value="1" <?= (int) $fieldValue === 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="edit_<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'subject-checklist') : ?>
                                <div class="col-12" data-duty-subject-field data-form-mode="edit" <?= ($editRecord['tugas'] ?? '') === 'keterlambatan' ? 'hidden' : ''; ?>>
                                    <label class="form-label"><?= esc($field['label']); ?></label>
                                    <div class="row g-3">
                                        <?php $selectedSubjectIdsForField = $selectedSubjectIds; ?>
                                        <?php foreach ($subjects as $subject) : ?>
                                            <?php $subjectId = (int) $subject['mapel_id']; ?>
                                            <div class="col-md-6 col-xl-4">
                                                <div class="form-check d-flex align-items-center gap-2 border rounded p-3 h-100">
                                                    <input class="form-check-input position-static m-0 flex-shrink-0" id="edit_<?= esc($field['name']); ?>_<?= esc($subjectId); ?>" name="<?= esc($field['name']); ?>[]" type="checkbox" value="<?= esc((string) $subjectId); ?>" <?= in_array($subjectId, $selectedSubjectIdsForField, true) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label m-0" for="edit_<?= esc($field['name']); ?>_<?= esc($subjectId); ?>"><?= esc($subject['mapel_nama']); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="<?= ($field['type'] ?? '') === 'textarea' ? 'col-12' : 'col-md-6'; ?>">
                                    <label class="form-label" for="edit_<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <?php if (($field['type'] ?? '') === 'textarea') : ?>
                                        <textarea class="form-control" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" rows="2"><?= esc($fieldValue); ?></textarea>
                                    <?php elseif (($field['type'] ?? '') === 'select') : ?>
                                        <select class="form-select" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" required>
                                            <option value="" disabled <?= (string) $fieldValue === '' ? 'selected' : ''; ?>>Pilih <?= esc(strtolower($field['label'])); ?></option>
                                            <?php foreach ($field['options'] as $optionValue => $optionLabel) : ?>
                                                <option value="<?= esc((string) $optionValue); ?>" <?= (string) $fieldValue === (string) $optionValue ? 'selected' : ''; ?>><?= esc($optionLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else : ?>
                                        <input class="form-control" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="<?= esc($field['type'] ?? 'text'); ?>" value="<?= esc($fieldValue); ?>"
                                            <?= isset($field['maxlength']) ? 'maxlength="' . esc((string) $field['maxlength']) . '"' : ''; ?>
                                            <?= isset($field['min']) ? 'min="' . esc((string) $field['min']) . '"' : ''; ?>
                                            <?= isset($field['max']) ? 'max="' . esc((string) $field['max']) . '"' : ''; ?>
                                            <?= ! empty($field['required']) ? 'required' : ''; ?>>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                            <a class="btn btn-outline-secondary" href="<?= site_url($type); ?>">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if (\App\Libraries\ActionAccess::can($type . '.create')) : ?>
    <div class="collapse <?= ! empty($error) && empty($editRecord) ? 'show' : ''; ?> mb-4" id="createAcademicData">
        <div class="card">
            <div class="card-header"><h2 class="h5 mb-0">Tambah <?= esc($config['title']); ?></h2></div>
            <div class="card-body">
                <form method="post" action="<?= site_url($type); ?>" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="row g-3">
                        <?php foreach ($config['fields'] as $field) : ?>
                            <?php if (($field['type'] ?? '') === 'student-picker') : ?>
                                <?= $this->include('panel/partials/parent-student-picker', [
                                    'students' => $students,
                                    'selectedStudentIds' => [],
                                    'pickerId' => 'newParentStudents',
                                ]); ?>
                            <?php elseif (($field['type'] ?? '') === 'photo') : ?>
                                <div class="col-md-6">
                                    <label class="form-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <input class="form-control" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                                    <div class="form-text">JPG, PNG, atau WebP; maksimal 2 MB. Latar foto diproses otomatis dan menjadi merah pada ID card.</div>
                                    <div class="teacher-photo-preview mt-2" id="<?= esc($field['name']); ?>_preview" hidden>
                                        <img id="<?= esc($field['name']); ?>_preview_img" src="" alt="Pratinjau <?= esc($field['label']); ?>">
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'checkbox') : ?>
                                <div class="col-12">
                                    <input type="hidden" name="<?= esc($field['name']); ?>" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="checkbox" value="1" <?= ! empty($field['checked']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'subject-checklist') : ?>
                                <div class="col-12" data-duty-subject-field data-form-mode="create" hidden>
                                    <label class="form-label"><?= esc($field['label']); ?></label>
                                    <div class="row g-3">
                                        <?php foreach ($subjects as $subject) : ?>
                                            <?php $subjectId = (int) $subject['mapel_id']; ?>
                                            <div class="col-md-6 col-xl-4">
                                                <div class="form-check d-flex align-items-center gap-2 border rounded p-3 h-100">
                                                    <input class="form-check-input position-static m-0 flex-shrink-0" id="<?= esc($field['name']); ?>_<?= esc($subjectId); ?>" name="<?= esc($field['name']); ?>[]" type="checkbox" value="<?= esc((string) $subjectId); ?>">
                                                    <label class="form-check-label m-0" for="<?= esc($field['name']); ?>_<?= esc($subjectId); ?>"><?= esc($subject['mapel_nama']); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'select') : ?>
                                <div class="<?= ($field['type'] ?? '') === 'textarea' ? 'col-12' : 'col-md-6'; ?>">
                                    <label class="form-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <select class="form-select" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" required>
                                        <option value="" disabled selected>Pilih <?= esc(strtolower($field['label'])); ?></option>
                                        <?php foreach ($field['options'] as $optionValue => $optionLabel) : ?>
                                            <option value="<?= esc((string) $optionValue); ?>"><?= esc($optionLabel); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php else : ?>
                                <div class="col-md-6">
                                    <label class="form-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <?php if (($field['type'] ?? '') === 'textarea') : ?>
                                        <textarea class="form-control" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" rows="2"></textarea>
                                    <?php else : ?>
                                        <input
                                            class="form-control"
                                            id="<?= esc($field['name']); ?>"
                                            name="<?= esc($field['name']); ?>"
                                            type="<?= esc($field['type'] ?? 'text'); ?>"
                                            <?= isset($field['maxlength']) ? 'maxlength="' . esc((string) $field['maxlength']) . '"' : ''; ?>
                                            <?= isset($field['min']) ? 'min="' . esc((string) $field['min']) . '"' : ''; ?>
                                            <?= isset($field['max']) ? 'max="' . esc((string) $field['max']) . '"' : ''; ?>
                                            <?= isset($field['placeholder']) ? 'placeholder="' . esc($field['placeholder']) . '"' : ''; ?>
                                            <?= ! empty($field['required']) ? 'required' : ''; ?>>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div class="col-12"><button class="btn btn-primary" type="submit">Simpan</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar Data</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($rows); ?> data</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <?php foreach ($config['columns'] as $label) : ?>
                            <th scope="col"><?= esc($label); ?></th>
                        <?php endforeach; ?>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $index => $row) : ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <?php foreach ($config['columns'] as $column => $label) : ?>
                                <td>
                                    <?php if ($column === 'foto') : ?>
                                        <?php if (! empty($row[$column])) : ?>
                                            <img src="<?= base_url('uploads/student-photos/' . rawurlencode(basename((string) $row[$column]))); ?>" alt="Foto <?= esc($row['guru_nama'] ?? ''); ?>" class="rounded-circle object-fit-cover border" style="width: 38px; height: 38px;">
                                        <?php else : ?>
                                            <span class="badge bg-light text-muted border">Tanpa Foto</span>
                                        <?php endif; ?>
                                    <?php elseif ($column === 'is_active') : ?>
                                        <span class="badge <?= (bool) $row[$column] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                            <?= (bool) $row[$column] ? 'Aktif' : 'Tidak Aktif'; ?>
                                        </span>
                                    <?php elseif ($column === 'semester') : ?>
                                        Semester <?= esc($row[$column]); ?>
                                    <?php elseif ($column === 'kelompok_rapor') : ?>
                                        <?php
                                        foreach ($config['fields'] as $field) {
                                            if ($field['name'] === $column) {
                                                echo esc($field['options'][$row[$column]] ?? $row[$column]);
                                                break;
                                            }
                                        }
                                        ?>
                                    <?php elseif ($column === 'siswa_list') : ?>
                                        <?= esc(($row[$column] ?? '') !== '' ? $row[$column] : '-'); ?>
                                    <?php else : ?>
                                        <?= esc(($row[$column] ?? '') !== '' ? $row[$column] : '-'); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-nowrap">
                                <?php if (\App\Libraries\ActionAccess::can($type . '.edit')) : ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url($type . '?edit=' . $row[$config['key']]); ?>">Edit</a>
                                <?php endif; ?>
                                <?php if (\App\Libraries\ActionAccess::can($type . '.delete')) : ?>
                                <form class="d-inline" method="post" action="<?= site_url($type . '/delete/' . $row[$config['key']]); ?>" onsubmit="return confirm('Hapus data ini?');">
                                    <?= csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($rows === []) : ?>
                        <tr><td colspan="<?= count($config['columns']) + 2; ?>" class="text-center text-muted py-4">Belum ada data. Gunakan tombol Tambah Data untuk mengisi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if ($type === 'teacher-duty-schedules') : ?>
<script>
(() => {
    document.querySelectorAll('[data-duty-subject-field]').forEach((field) => {
        const form = field.closest('form');
        const taskSelect = form?.querySelector('select[name="tugas"]');
        if (!taskSelect) return;

        const updateSubjectVisibility = () => {
            const shouldShow = ['absen_kelas', 'keduanya'].includes(taskSelect.value);
            field.hidden = !shouldShow;
            field.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.disabled = !shouldShow;
            });
        };

        taskSelect.addEventListener('change', updateSubjectVisibility);
        updateSubjectVisibility();
    });
})();
</script>
<?php endif; ?>

<style>
    .teacher-photo-preview {
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: .5rem;
        display: flex;
        height: 120px;
        justify-content: center;
        overflow: hidden;
        padding: .25rem;
        width: 100px;
    }
    .teacher-photo-preview[hidden] {
        display: none;
    }
    .teacher-photo-preview img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
</style>

<script>
(() => {
    const setupPreview = (inputId, previewId, imgId) => {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const img = document.getElementById(imgId);
        if (!input || !preview || !img) return;

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    img.src = e.target.result;
                    preview.hidden = false;
                };
                reader.readAsDataURL(file);
            } else if (!img.getAttribute('src')) {
                preview.hidden = true;
            }
        });
    };

    setupPreview('foto', 'foto_preview', 'foto_preview_img');
    setupPreview('edit_foto', 'edit_foto_preview', 'edit_foto_preview_img');
})();
</script>
<?= $this->endSection(); ?>
