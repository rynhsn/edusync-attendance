<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pendaftar SPMB</h1>
        <p class="text-muted mb-0">Data dan dokumen pendaftar tersimpan di database EduSync. Dokumen hanya dapat diakses oleh admin.</p>
    </div>

    <?php if ($applications === []) : ?>
        <div class="alert alert-info" role="status">Belum ada pendaftaran SPMB.</div>
    <?php else : ?>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Nomor</th>
                        <th>Calon Siswa</th>
                        <th>NIK / NISN</th>
                        <th>Kontak</th>
                        <th>Data dan Dokumen</th>
                        <th>Diterima</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($applications as $application) : ?>
                        <tr>
                            <td class="text-nowrap">
                                <strong><?= esc($application['registration_code']); ?></strong><br>
                                <span class="badge text-bg-secondary"><?= esc($application['status']); ?></span>
                            </td>
                            <td>
                                <strong><?= esc($application['student_name']); ?></strong><br>
                                <?= esc($application['application_data']['birth_place'] ?? ''); ?>,
                                <?= esc($application['application_data']['birth_date'] ?? ''); ?>
                            </td>
                            <td><?= esc($application['student_nik']); ?><br><?= esc($application['student_nisn']); ?></td>
                            <td><?= esc($application['contact_email']); ?><br><?= esc($application['application_data']['contact_phone'] ?? ''); ?></td>
                            <td>
                                <details>
                                    <summary class="text-primary">Lihat detail</summary>
                                    <dl class="row small mt-3 mb-0">
                                        <?php foreach ($application['application_data'] as $field => $value) : ?>
                                            <?php if (in_array($field, [
                                                'admission_path', 'admission_path_label', 'latitude', 'longitude',
                                                'grade_semester_1', 'grade_semester_2', 'grade_semester_3',
                                                'grade_semester_4', 'grade_semester_5', 'parent_income',
                                            ], true)) { continue; } ?>
                                            <dt class="col-sm-5"><?= esc(ucwords(str_replace('_', ' ', $field))); ?></dt>
                                            <dd class="col-sm-7"><?= esc((string) $value); ?></dd>
                                        <?php endforeach; ?>
                                    </dl>
                                    <h3 class="h6 mt-3">Dokumen</h3>
                                    <ul class="mb-0">
                                        <?php foreach (json_decode($application['documents'], true) ?: [] as $key => $document) : ?>
                                            <?php if (in_array($key, [
                                                'report_card_1', 'report_card_2', 'report_card_3', 'report_card_4', 'report_card_5',
                                                'geotag_selfie', 'parent_statement', 'achievement_certificate',
                                                'affirmation_certificate', 'transfer_certificate',
                                            ], true)) { continue; } ?>
                                            <li>
                                                <a href="<?= site_url('spmb/applications/' . $application['id'] . '/documents/' . rawurlencode($key)); ?>">
                                                    <?= esc($document['label'] ?? $key); ?>
                                                </a>
                                                <small class="text-muted">(<?= esc(number_format(($document['size'] ?? 0) / 1024, 0)); ?> KB)</small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column gap-2">
                                    <span><?= esc($application['created_at']); ?></span>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('spmb/applications/' . $application['id'] . '/proof'); ?>">
                                        Download Bukti
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
