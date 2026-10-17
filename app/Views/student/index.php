<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-0">Data Siswa</h1>
            <p class="text-muted mb-0">Kelola data siswa dan informasi kelas.</p>
        </div>
        <?php if (\App\Libraries\ActionAccess::can('student.create')) : ?>
            <button type="button" class="btn btn-primary" id="addStudentButton">
                <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Tambah Siswa
            </button>
        <?php endif; ?>
    </div>

    <?php if (! empty($importMessage)) : ?>
        <div class="alert alert-success" role="status"><?= esc($importMessage); ?></div>
    <?php endif; ?>
    <?php if (! empty($importError)) : ?>
        <div class="alert alert-danger student-import-error" role="alert"><?= esc($importError); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Import dan Export Excel</h2>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a class="btn btn-outline-primary" href="<?= site_url('student/import-template'); ?>">
                    <i class="bi bi-download me-2" aria-hidden="true"></i>Unduh Template Import
                </a>
                <a class="btn btn-outline-success" href="<?= site_url('student/export'); ?>">
                    <i class="bi bi-file-earmark-spreadsheet me-2" aria-hidden="true"></i>Export Data Siswa
                </a>
            </div>
            <?php if (\App\Libraries\ActionAccess::can('student.import')) : ?>
                <form class="d-flex flex-column flex-md-row align-items-md-end gap-2 mt-3" method="post" action="<?= site_url('student/import'); ?>" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="flex-grow-1">
                        <label class="form-label" for="studentImportFile">File Excel (.xlsx)</label>
                        <input class="form-control" id="studentImportFile" name="student_file" type="file" accept=".xlsx,.csv,.tsv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                    <button class="btn btn-primary flex-shrink-0" type="submit">
                        <i class="bi bi-upload me-2" aria-hidden="true"></i>Import Siswa
                    </button>
                </form>
                <p class="form-text mt-2 mb-0">Gunakan template Excel yang tersedia. Contoh di dalam template otomatis dilewati saat import. Template CSV/TSV lama juga didukung. Maksimal file 5 MB dan 3.000 siswa per impor.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Filter Siswa</h2>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-8">
                    <label class="form-label" for="filterStudent">Cari siswa</label>
                    <input class="form-control" id="filterStudent" type="search" placeholder="Cari nama, NIS, NISN, atau RFID">
                </div>
                <div class="col-lg-4">
                    <span class="form-label d-block">Status</span>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input class="form-check-input student-status-filter" id="filterActive" type="checkbox" value="aktif" checked>
                            <label class="form-check-label" for="filterActive">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input student-status-filter" id="filterInactive" type="checkbox" value="tidak_aktif" checked>
                            <label class="form-check-label" for="filterInactive">Tidak Aktif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 card-title mb-0">Daftar Siswa</h2>
            <span class="badge bg-primary-subtle text-primary"><?= count($students); ?> siswa</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="studentsTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">NIS</th>
                        <th scope="col">NISN</th>
                        <th scope="col">Jenis Kelamin</th>
                        <th scope="col">Tempat, Tanggal Lahir</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">RFID Tag</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student) : ?>
                        <?php $studentStatus = $student['status'] ?? 'aktif'; ?>
                        <tr data-student-id="<?= esc($student['siswa_id']); ?>" data-class-id="<?= esc($student['kelas_id'] ?? ''); ?>" data-status="<?= esc($studentStatus); ?>">
                            <td><?= $index + 1; ?></td>
                            <td><?= esc($student['nama_siswa'] ?? '-'); ?></td>
                            <td><?= esc($student['nis'] ?? '-'); ?></td>
                            <td><?= esc($student['nisn'] ?? '-'); ?></td>
                            <td><?= esc(JK[$student['jk'] ?? ''] ?? '-'); ?></td>
                            <td><?= esc(trim(($student['tempat_lahir'] ?? '') . ', ' . ($student['tanggal_lahir'] ?? ''), ', ')); ?></td>
                            <td><?= esc($student['alamat'] ?? '-'); ?></td>
                            <td><?= esc($student['rfid_tag'] ?? '-'); ?></td>
                            <td><?= esc($student['kelas'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?= $studentStatus === 'aktif' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'; ?>">
                                    <?= $studentStatus === 'aktif' ? 'Aktif' : 'Tidak Aktif'; ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap align-middle">
                                <div class="d-inline-flex align-items-center justify-content-center gap-2">

                                    <!-- 1. Tombol Edit -->
                                    <?php if (\App\Libraries\ActionAccess::can('student.edit')) : ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary edit-student"
                                            data-id="<?= esc($student['siswa_id']); ?>"
                                            data-student="<?= esc(json_encode($student, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP), 'attr'); ?>"
                                            aria-label="Edit <?= esc($student['nama_siswa']); ?>"
                                            title="Edit">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- 2. Sakelar Status (Padding & Margin Bawaan Dikunci Total) -->
                                    <?php if (\App\Libraries\ActionAccess::can('student.status')) : ?>
                                        <span class="form-check form-switch d-inline-flex align-items-center" style="padding: 0 !important; margin: 0 !important; min-height: auto;">
                                            <input class="form-check-input toggle-student-status cursor-pointer"
                                                type="checkbox"
                                                role="switch"
                                                id="switch-<?= esc($student['siswa_id']); ?>"
                                                data-id="<?= esc($student['siswa_id']); ?>"
                                                data-next-status="<?= $studentStatus === 'aktif' ? 'tidak_aktif' : 'aktif'; ?>"
                                                aria-label="<?= $studentStatus === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?> <?= esc($student['nama_siswa']); ?>"
                                                title="<?= $studentStatus === 'aktif' ? 'Nonaktifkan' : 'Aktifkan'; ?>"
                                                <?= $studentStatus === 'aktif' ? 'checked' : ''; ?>
                                                style="width: 2.0em; height: 1.0em; margin: 0 !important; padding: 0 !important; float: none;">
                                        </span>
                                    <?php endif; ?>

                                    <!-- 3. Tombol Hapus -->
                                    <?php if (\App\Libraries\ActionAccess::can('student.delete')) : ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-student"
                                            data-id="<?= esc($student['siswa_id']); ?>"
                                            aria-label="Hapus <?= esc($student['nama_siswa']); ?>"
                                            title="Hapus siswa">
                                            <i class="bi bi-trash" aria-hidden="true"></i>
                                        </button>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($students === []) : ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Belum ada data siswa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="student-modal" id="studentModal" hidden>
    <div class="student-modal-backdrop" data-close-student-modal></div>
    <section class="student-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="studentModalTitle">
        <div class="card border-0 shadow-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h5 card-title mb-0" id="studentModalTitle">Tambah Siswa</h2>
                <button type="button" class="btn-close" aria-label="Tutup" data-close-student-modal></button>
            </div>
            <form id="studentForm" data-create-url="<?= site_url('student/create'); ?>" data-update-url="<?= site_url('student/update'); ?>" data-delete-url="<?= site_url('student/delete'); ?>" data-status-url="<?= site_url('student/status'); ?>" data-csrf-token-name="<?= esc(config('Security')->tokenName, 'attr'); ?>" data-csrf-header-name="<?= esc(config('Security')->headerName, 'attr'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="siswa_id">
                <div class="card-body">
                    <div class="alert alert-danger" id="studentFormError" role="alert" hidden></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="studentName">Nama Siswa <span class="text-danger">*</span></label>
                            <input class="form-control" id="studentName" name="nama_siswa" maxlength="100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentParent">Orang Tua / Wali</label>
                            <select class="form-select" id="studentParent" name="orang_tua_id">
                                <option value="">Belum dipilih</option>
                                <?php foreach ($parents as $parent) : ?>
                                    <option value="<?= esc($parent['orang_tua_id']); ?>"><?= esc($parent['nama_orang_tua'] . ' - ' . $parent['no_whatsapp']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Belum ada? Tambahkan di <a href="<?= site_url('parents'); ?>">Data Orang Tua</a>.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentClass">Kelas</label>
                            <select class="form-select" id="studentClass" name="kelas_id">
                                <option value="">Belum ditentukan</option>
                                <?php foreach ($classes as $class) : ?>
                                    <option value="<?= esc($class['kelas_id']); ?>"><?= esc($class['kelas_nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentStatus">Status</label>
                            <select class="form-select" id="studentStatus" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentNis">NIS</label>
                            <input class="form-control" id="studentNis" name="nis" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentNisn">NISN</label>
                            <input class="form-control" id="studentNisn" name="nisn" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentGender">Jenis Kelamin</label>
                            <select class="form-select" id="studentGender" name="jk">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="l">Laki-laki</option>
                                <option value="p">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentRfid">RFID</label>
                            <input class="form-control" id="studentRfid" name="rfid_tag" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentBirthPlace">Tempat Lahir</label>
                            <input class="form-control" id="studentBirthPlace" name="tempat_lahir" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="studentBirthDate">Tanggal Lahir</label>
                            <input class="form-control" id="studentBirthDate" name="tanggal_lahir" type="date">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="studentAddress">Alamat</label>
                            <textarea class="form-control" id="studentAddress" name="alamat" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-close-student-modal>Batal</button>
                    <?php if (\App\Libraries\ActionAccess::can('student.create') || \App\Libraries\ActionAccess::can('student.edit')) : ?>
                        <button type="submit" class="btn btn-primary" id="saveStudentButton">Simpan</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
    .student-import-error {
        white-space: pre-line;
    }

    .student-modal-open {
        overflow: hidden;
    }

    .student-modal[hidden] {
        display: none;
    }

    .student-modal {
        position: fixed;
        inset: 0;
        z-index: 1080;
        display: grid;
        place-items: center;
        padding: 1rem;
    }

    .student-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgb(0 0 0 / 50%);
    }

    .student-modal-dialog {
        position: relative;
        width: min(100%, 760px);
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/main/panel-students.js'); ?>?v=<?= filemtime(FCPATH . 'assets/main/panel-students.js'); ?>" defer></script>
<?= $this->endSection(); ?>