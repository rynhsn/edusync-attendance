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
        <?php
        $fieldLabels = [
            'contact_email' => 'Email aktif',
            'contact_phone' => 'Nomor HP / WhatsApp calon siswa',
            'parent_phone' => 'Nomor HP orang tua / wali',
            'student_name' => 'Nama lengkap',
            'student_nik' => 'NIK',
            'student_nisn' => 'NISN',
            'gender' => 'Jenis kelamin',
            'birth_place' => 'Tempat lahir',
            'birth_date' => 'Tanggal lahir',
            'address' => 'Alamat lengkap',
            'rt' => 'RT',
            'rw' => 'RW',
            'postal_code' => 'Kode pos',
            'province' => 'Provinsi',
            'regency' => 'Kabupaten / kota',
            'district' => 'Kecamatan',
            'village' => 'Kelurahan / desa',
            'previous_school' => 'Sekolah asal',
            'graduation_year' => 'Tahun lulus',
            'education_level' => 'Jenjang sebelumnya',
            'parent_name' => 'Nama orang tua / wali',
            'parent_relationship' => 'Hubungan dengan calon siswa',
            'parent_job' => 'Pekerjaan orang tua / wali',
        ];
        $fieldGroups = [
            'Kontak' => ['contact_email', 'contact_phone', 'parent_phone'],
            'Data calon siswa' => ['student_name', 'student_nik', 'student_nisn', 'gender', 'birth_place', 'birth_date'],
            'Alamat domisili' => ['address', 'rt', 'rw', 'postal_code', 'province', 'regency', 'district', 'village'],
            'Data sekolah asal' => ['previous_school', 'graduation_year', 'education_level'],
            'Orang tua / wali' => ['parent_name', 'parent_relationship', 'parent_job'],
        ];
        ?>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Nomor pendaftaran</th>
                        <th>Calon siswa</th>
                        <th>NIK / NISN</th>
                        <th>Kontak</th>
                        <th>Detail</th>
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
                                <span class="text-muted">
                                    <?= esc($application['application_data']['birth_place'] ?? ''); ?>,
                                    <?= esc($application['application_data']['birth_date'] ?? ''); ?>
                                </span>
                            </td>
                            <td><?= esc($application['student_nik']); ?><br><?= esc($application['student_nisn']); ?></td>
                            <td>
                                <?= esc($application['contact_email']); ?><br>
                                <span class="text-muted"><?= esc($application['application_data']['contact_phone'] ?? ''); ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#spmb-details-<?= (int) $application['id']; ?>">
                                    <i class="bi bi-eye me-1"></i>Lihat detail
                                </button>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex flex-column gap-2">
                                    <span><?= esc($application['created_at']); ?></span>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('spmb/applications/' . $application['id'] . '/proof'); ?>">
                                        Download bukti
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php foreach ($applications as $application) : ?>
            <?php
            $applicationData = $application['application_data'];
            $renderedFields = [];
            ?>
            <div class="modal fade" id="spmb-details-<?= (int) $application['id']; ?>" tabindex="-1" aria-labelledby="spmb-details-title-<?= (int) $application['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h2 class="modal-title fs-5" id="spmb-details-title-<?= (int) $application['id']; ?>">Detail Pendaftar</h2>
                                <div class="small text-muted"><?= esc($application['registration_code']); ?> · <?= esc($application['student_name']); ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <?php foreach ($fieldGroups as $groupTitle => $fields) : ?>
                                <section class="mb-4">
                                    <h3 class="h6 border-bottom pb-2 mb-3"><?= esc($groupTitle); ?></h3>
                                    <dl class="row mb-0">
                                        <?php foreach ($fields as $field) : ?>
                                            <?php
                                            $renderedFields[] = $field;
                                            $value = $applicationData[$field] ?? '';
                                            $displayValue = is_scalar($value) ? trim((string) $value) : json_encode($value, JSON_UNESCAPED_UNICODE);
                                            if ($field === 'gender') {
                                                $displayValue = ['L' => 'Laki-laki', 'P' => 'Perempuan'][$displayValue] ?? $displayValue;
                                            } elseif ($field === 'parent_relationship') {
                                                $displayValue = ['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'][$displayValue] ?? $displayValue;
                                            } elseif ($field === 'education_level') {
                                                $displayValue = ['SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA', 'SMK' => 'SMK'][$displayValue] ?? $displayValue;
                                            } elseif ($field === 'birth_date' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $displayValue) === 1) {
                                                $displayValue = date('d/m/Y', strtotime($displayValue));
                                            }
                                            ?>
                                            <dt class="col-md-4 text-muted fw-normal"><?= esc($fieldLabels[$field]); ?></dt>
                                            <dd class="col-md-8 mb-2 text-break" data-no-translate><?= $displayValue !== '' ? nl2br(esc($displayValue)) : '<span class="text-muted">—</span>'; ?></dd>
                                        <?php endforeach; ?>
                                    </dl>
                                </section>
                            <?php endforeach; ?>

                            <?php
                            $otherFields = array_diff(array_keys($applicationData), array_merge($renderedFields, ['declaration']));
                            if ($otherFields !== []) :
                            ?>
                                <section class="mb-4">
                                    <h3 class="h6 border-bottom pb-2 mb-3">Data tambahan</h3>
                                    <dl class="row mb-0">
                                        <?php foreach ($otherFields as $field) : ?>
                                            <?php
                                            $value = $applicationData[$field];
                                            $displayValue = is_scalar($value) ? trim((string) $value) : json_encode($value, JSON_UNESCAPED_UNICODE);
                                            ?>
                                            <dt class="col-md-4 text-muted fw-normal"><?= esc($fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field))); ?></dt>
                                            <dd class="col-md-8 mb-2 text-break" data-no-translate><?= $displayValue !== '' ? nl2br(esc($displayValue)) : '<span class="text-muted">—</span>'; ?></dd>
                                        <?php endforeach; ?>
                                    </dl>
                                </section>
                            <?php endif; ?>

                            <section>
                                <h3 class="h6 border-bottom pb-2 mb-3">Dokumen persyaratan</h3>
                                <?php if ($application['documents'] === []) : ?>
                                    <p class="text-muted mb-0">Tidak ada dokumen yang tercatat.</p>
                                <?php else : ?>
                                    <div class="list-group">
                                        <?php foreach ($application['documents'] as $key => $document) : ?>
                                            <?php $documentUrl = site_url('spmb/applications/' . $application['id'] . '/documents/' . rawurlencode((string) $key)); ?>
                                            <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-3">
                                                <div class="me-auto">
                                                    <div class="fw-medium" data-no-translate><?= esc($document['label'] ?? $key); ?></div>
                                                    <small class="text-muted"><?= esc(number_format(((int) ($document['size'] ?? 0)) / 1024, 0)); ?> KB</small>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a class="btn btn-sm btn-outline-primary" href="<?= esc($documentUrl . '?view=1'); ?>" target="_blank" rel="noopener noreferrer">
                                                        <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                                                    </a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="<?= esc($documentUrl); ?>">
                                                        <i class="bi bi-download me-1" aria-hidden="true"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </section>
                        </div>
                        <div class="modal-footer">
                            <a class="btn btn-outline-primary" href="<?= site_url('spmb/applications/' . $application['id'] . '/proof'); ?>">Download bukti pendaftaran</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
