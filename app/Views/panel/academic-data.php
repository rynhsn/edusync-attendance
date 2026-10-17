<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1"><?= esc($config['title']); ?></h1>
            <p class="text-muted mb-0">
                <?= $type === 'subjects'
                    ? 'Atur kelompok setiap mata pelajaran untuk pengelompokan raport SMK.'
                    : ($type === 'parents'
                        ? 'Kelola kontak orang tua/wali, hubungkan dengan siswa melalui pencarian, dan gunakan untuk notifikasi absensi.'
                        : 'Kelola data yang digunakan pada pencatatan absensi kelas.'); ?>
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
                <form method="post" action="<?= site_url($type . '/update/' . $editRecord[$config['key']]); ?>">
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
                            <?php elseif (($field['type'] ?? '') === 'checkbox') : ?>
                                <div class="col-12">
                                    <input type="hidden" name="<?= esc($field['name']); ?>" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="checkbox" value="1" <?= (int) $fieldValue === 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="edit_<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="<?= ($field['type'] ?? '') === 'textarea' ? 'col-12' : 'col-md-6'; ?>">
                                    <label class="form-label" for="edit_<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <?php if (($field['type'] ?? '') === 'textarea') : ?>
                                        <textarea class="form-control" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" rows="2"><?= esc($fieldValue); ?></textarea>
                                    <?php elseif (($field['type'] ?? '') === 'select') : ?>
                                        <select class="form-select" id="edit_<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" required>
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
                <form method="post" action="<?= site_url($type); ?>">
                    <?= csrf_field(); ?>
                    <div class="row g-3">
                        <?php foreach ($config['fields'] as $field) : ?>
                            <?php if (($field['type'] ?? '') === 'student-picker') : ?>
                                <?= $this->include('panel/partials/parent-student-picker', [
                                    'students' => $students,
                                    'selectedStudentIds' => [],
                                    'pickerId' => 'newParentStudents',
                                ]); ?>
                            <?php elseif (($field['type'] ?? '') === 'checkbox') : ?>
                                <div class="col-12">
                                    <input type="hidden" name="<?= esc($field['name']); ?>" value="0">
                                    <div class="form-check">
                                        <input class="form-check-input" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" type="checkbox" value="1">
                                        <label class="form-check-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    </div>
                                </div>
                            <?php elseif (($field['type'] ?? '') === 'select') : ?>
                                <div class="<?= ($field['type'] ?? '') === 'textarea' ? 'col-12' : 'col-md-6'; ?>">
                                    <label class="form-label" for="<?= esc($field['name']); ?>"><?= esc($field['label']); ?></label>
                                    <select class="form-select" id="<?= esc($field['name']); ?>" name="<?= esc($field['name']); ?>" required>
                                        <option value="">Pilih <?= esc(strtolower($field['label'])); ?></option>
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
                                    <?php if ($column === 'is_active') : ?>
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
<?= $this->endSection(); ?>
