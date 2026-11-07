<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Ujian Online (CBT)</h1>
            <p class="text-muted mb-0">Kelola asesmen online pilihan ganda dan essay dengan sistem penilaian otomatis.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= site_url('cbt'); ?>" target="_blank" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-up-right"></i> Buka Portal CBT Siswa
            </a>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createExamModal">
                <i class="bi bi-plus-circle"></i> Buat Ujian Baru
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (! empty($message)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                        <i class="bi bi-journal-bookmark"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Ujian</div>
                        <div class="h4 mb-0 fw-bold"><?= count($ujians); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Ujian Aktif</div>
                        <div class="h4 mb-0 fw-bold">
                            <?= count(array_filter($ujians, fn($u) => $u['status'] === 'aktif')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Partisipasi</div>
                        <div class="h4 mb-0 fw-bold">
                            <?= array_sum(array_column($ujians, 'total_peserta')); ?> siswa
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-circle fs-4">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sudah Selesai</div>
                        <div class="h4 mb-0 fw-bold">
                            <?= array_sum(array_column($ujians, 'peserta_selesai')); ?> sesi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="h5 card-title mb-0 fw-semibold">Daftar Ujian Online</h2>
                <?php if ($isAdmin) : ?>
                    <small class="text-muted"><i class="bi bi-shield-lock-fill text-primary me-1"></i>Akses Administrator: Menampilkan seluruh ujian dari semua guru</small>
                <?php else : ?>
                    <small class="text-muted"><i class="bi bi-person-check-fill text-success me-1"></i>Menampilkan ujian yang Anda buat (<?= esc($currentTeacher['guru_nama'] ?? 'Guru'); ?>)</small>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <form method="get" action="<?= site_url('online-assessments'); ?>" class="d-flex align-items-center gap-2 m-0">
                    <label for="filter_tahun_ajaran" class="small text-muted text-nowrap mb-0 d-none d-md-inline">
                        <i class="bi bi-calendar3 me-1"></i>Tahun Ajaran:
                    </label>
                    <select name="tahun_ajaran_id" id="filter_tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="active" <?= ($selectedYearFilter === 'active' || $selectedYearFilter === '') ? 'selected' : ''; ?>>
                            Tahun Ajaran Aktif <?= $activeYear ? '(' . esc($activeYear['tahun_ajaran_nama']) . ' - Sem ' . esc($activeYear['semester']) . ')' : ''; ?>
                        </option>
                        <option value="all" <?= ($selectedYearFilter === 'all') ? 'selected' : ''; ?>>-- Semua Tahun Ajaran (Termasuk Nonaktif) --</option>
                        <?php foreach ($academicYears as $ay) : ?>
                            <option value="<?= $ay['tahun_ajaran_id']; ?>" <?= ($selectedYearFilter == $ay['tahun_ajaran_id']) ? 'selected' : ''; ?>>
                                <?= esc($ay['tahun_ajaran_nama']); ?> - Semester <?= esc($ay['semester']); ?><?= (bool) $ay['is_active'] ? ' (Aktif)' : ' (Nonaktif)'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-7"><?= count($ujians); ?> data</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">NO.</th>
                        <th scope="col">JUDUL UJIAN & MAPEL</th>
                        <th scope="col">KELAS</th>
                        <th scope="col">DURASI & TOKEN</th>
                        <th scope="col" class="text-center">BUTIR SOAL</th>
                        <th scope="col" class="text-center">PESERTA</th>
                        <th scope="col" class="text-center">STATUS</th>
                        <th scope="col" class="text-center" style="width: 220px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ujians as $idx => $u) : ?>
                        <tr>
                            <td class="text-center text-muted"><?= $idx + 1; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($u['judul']); ?></div>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-book me-1"></i><?= esc($u['mapel_nama'] ?: 'Semua Mapel'); ?>
                                    <?php if (! empty($u['guru_nama'])) : ?>
                                        &bull; Guru: <?= esc($u['guru_nama']); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                        <i class="bi bi-calendar3 me-1 text-primary"></i><?= esc($u['tahun_ajaran_nama'] ?? '-'); ?> Sem <?= esc($u['tahun_ajaran_semester'] ?? '-'); ?>
                                    </span>
                                    <?php if ((int)($u['tahun_ajaran_is_active'] ?? 0) === 1): ?>
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-muted border" style="font-size: 0.72rem;">
                                            <i class="bi bi-dash-circle me-1"></i>Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= esc($u['kelas_nama'] ?: 'Semua Kelas'); ?>
                                </span>
                            </td>
                            <td>
                                <div><i class="bi bi-clock me-1 text-muted"></i><?= (int) $u['durasi_menit']; ?> Menit</div>
                                <?php if (! empty($u['token'])) : ?>
                                    <div class="mt-1 d-flex align-items-center gap-1">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 font-monospace fs-7">
                                            <i class="bi bi-key-fill me-1"></i><?= esc($u['token']); ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-light border p-0 px-2 btn-copy-token" data-token="<?= esc($u['token']); ?>" title="Salin Token">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                <?php else : ?>
                                    <span class="small text-muted fst-italic">Tanpa Token</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('online-assessments/' . $u['ujian_id'] . '/questions'); ?>" class="btn btn-sm btn-light border text-nowrap" title="Kelola Butir Soal">
                                    <i class="bi bi-list-check me-1 text-primary"></i>
                                    <strong><?= (int) ($u['total_soal'] ?? 0); ?></strong> soal
                                    <span class="small text-muted d-block">
                                        (<?= (int) ($u['soal_pg'] ?? 0); ?> PG, <?= (int) ($u['soal_essay'] ?? 0); ?> Essay)
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('online-assessments/' . $u['ujian_id'] . '/results'); ?>" class="btn btn-sm btn-light border text-nowrap" title="Lihat Nilai & Hasil">
                                    <i class="bi bi-people me-1 text-success"></i>
                                    <strong><?= (int) ($u['total_peserta'] ?? 0); ?></strong> siswa
                                    <span class="small text-muted d-block">
                                        (<?= (int) ($u['peserta_selesai'] ?? 0); ?> selesai)
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                <form method="post" action="<?= site_url('online-assessments/status/' . $u['ujian_id']); ?>" class="d-inline">
                                    <?= csrf_field(); ?>
                                    <?php if ($u['status'] === 'aktif') : ?>
                                        <button type="submit" class="badge bg-success-subtle text-success border border-success border-opacity-25 p-2 btn" title="Klik untuk mengubah status">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </button>
                                    <?php elseif ($u['status'] === 'selesai') : ?>
                                        <button type="submit" class="badge bg-secondary-subtle text-secondary border p-2 btn" title="Klik untuk mengubah status">
                                            <i class="bi bi-archive me-1"></i>Selesai
                                        </button>
                                    <?php else : ?>
                                        <button type="submit" class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 p-2 btn" title="Klik untuk mengubah status">
                                            <i class="bi bi-pencil-square me-1"></i>Draft
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('online-assessments/' . $u['ujian_id'] . '/questions'); ?>" class="btn btn-outline-primary" title="Kelola Butir Soal">
                                        <i class="bi bi-ui-checks"></i> Soal
                                    </a>
                                    <a href="<?= site_url('online-assessments/' . $u['ujian_id'] . '/results'); ?>" class="btn btn-outline-success" title="Lihat Nilai & Lembar Jawaban">
                                        <i class="bi bi-trophy"></i> Nilai
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-edit-exam" 
                                        data-bs-toggle="modal" data-bs-target="#editExamModal"
                                        data-exam="<?= esc(json_encode($u), 'attr'); ?>" title="Edit Pengaturan Ujian">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <form method="post" action="<?= site_url('online-assessments/delete/' . $u['ujian_id']); ?>" class="d-inline" onsubmit="return confirm('Hapus ujian ini beserta seluruh butir soal dan data nilai terkait?');">
                                        <?= csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Ujian">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($ujians === []) : ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                <?php if (! $isAdmin) : ?>
                                    Anda belum membuat ujian online. Silakan klik tombol <strong>Buat Ujian Baru</strong> di atas untuk membuat ujian Anda.
                                <?php else : ?>
                                    Belum ada ujian online yang tersedia. Silakan klik tombol <strong>Buat Ujian Baru</strong> di atas untuk memulai.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('modals'); ?>
<!-- Modal Create Exam -->
<div class="modal fade" id="createExamModal" tabindex="-1" aria-labelledby="createExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="post" action="<?= site_url('online-assessments'); ?>">
                <?= csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="createExamModalLabel">
                        <i class="bi bi-plus-circle text-primary"></i> Buat Ujian Online Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="create_tahun_ajaran_id" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check text-primary me-1"></i>Tahun Ajaran & Semester <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary-subtle" id="create_tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <?php foreach ($academicYears as $ay) : ?>
                                    <option value="<?= $ay['tahun_ajaran_id']; ?>" <?= ((bool) $ay['is_active']) ? 'selected' : ''; ?>>
                                        <?= esc($ay['tahun_ajaran_nama']); ?> - Semester <?= esc($ay['semester']); ?><?= (bool) $ay['is_active'] ? ' (Aktif Saat Ini)' : ' (Tidak Aktif)'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Pilih tahun ajaran untuk ujian ini. Ujian hanya akan muncul di portal CBT siswa jika tahun ajaran berstatus <strong>Aktif</strong>.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="create_judul" class="form-label">Judul Ujian / Asesmen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_judul" name="judul" required placeholder="Contoh: Penilaian Harian 1 - Dasar Pemrograman Web">
                        </div>

                        <div class="col-md-6">
                            <?php if ($isAdmin) : ?>
                                <label for="create_guru_id" class="form-label">Guru Pengampu</label>
                                <select class="form-select" id="create_guru_id" name="guru_id">
                                    <option value="">-- Pilih Guru Pengampu --</option>
                                    <?php foreach ($teachers as $t) : ?>
                                        <option value="<?= $t['guru_id']; ?>"><?= esc($t['guru_nama']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Pilih guru untuk memfilter mata pelajaran yang sesuai penugasan mapel.</div>
                            <?php else : ?>
                                <label class="form-label">Guru Pengampu</label>
                                <input type="hidden" id="create_guru_id" name="guru_id" value="<?= esc($currentGuruId ?? ''); ?>">
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" class="form-control bg-light" value="<?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?>" readonly>
                                </div>
                                <div class="form-text text-muted">Akun Anda otomatis ditetapkan sebagai guru pengampu.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="create_mapel_id" class="form-label">Mata Pelajaran</label>
                            <select class="form-select" id="create_mapel_id" name="mapel_id">
                                <?php if ($isAdmin) : ?>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <?php foreach ($allMapels as $m) : ?>
                                        <option value="<?= $m['mapel_id']; ?>"><?= esc($m['mapel_nama']); ?><?= ! empty($m['mapel_kode']) ? ' (' . esc($m['mapel_kode']) . ')' : ''; ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?php if (! empty($mapels)) : ?>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        <?php foreach ($mapels as $m) : ?>
                                            <option value="<?= $m['mapel_id']; ?>"><?= esc($m['mapel_nama']); ?><?= ! empty($m['mapel_kode']) ? ' (' . esc($m['mapel_kode']) . ')' : ''; ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="" disabled selected>-- Belum ada mapel ditugaskan --</option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($isAdmin) : ?>
                                <div id="create_mapel_notice" class="form-text text-muted">Disesuaikan dengan mapel guru pengampu yang dipilih.</div>
                            <?php else : ?>
                                <?php if (empty($mapels)) : ?>
                                    <div class="form-text text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Anda belum memiliki mata pelajaran yang dicentang di menu <strong>Pengaturan &rarr; Mapel Guru</strong>. Hubungi admin.
                                    </div>
                                <?php else : ?>
                                    <div class="form-text text-muted">Hanya menampilkan mata pelajaran yang ditugaskan kepada Anda.</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="create_kelas_id" class="form-label">Target Kelas</label>
                            <select class="form-select" id="create_kelas_id" name="kelas_id">
                                <option value="">-- Semua Kelas --</option>
                                <?php foreach ($classes as $c) : ?>
                                    <option value="<?= $c['kelas_id']; ?>"><?= esc($c['kelas_nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="create_durasi_menit" class="form-label">Durasi (Menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="create_durasi_menit" name="durasi_menit" value="60" min="1" max="360" required>
                        </div>

                        <div class="col-md-5">
                            <label for="create_token" class="form-label d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span>Token Ujian</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 small">
                                    <i class="bi bi-magic me-1"></i>Generate Otomatis
                                </span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace text-uppercase fw-bold text-primary" id="create_token" name="token" maxlength="15" placeholder="Otomatis saat simpan">
                                <button class="btn btn-outline-primary d-inline-flex align-items-center gap-1 text-nowrap px-3" type="button" id="btnGenTokenCreate" title="Klik untuk membuat token baru sekarang">
                                    <i class="bi bi-shuffle"></i>
                                    <span>Acak Token</span>
                                </button>
                            </div>
                            <div class="form-text">Biarkan kosong agar otomatis dibuat saat simpan, atau klik <strong>Acak Token</strong>.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="create_waktu_mulai" class="form-label">Waktu Mulai Akses (Opsional)</label>
                            <input type="datetime-local" class="form-control" id="create_waktu_mulai" name="waktu_mulai">
                        </div>

                        <div class="col-md-6">
                            <label for="create_waktu_selesai" class="form-label">Waktu Selesai Akses (Opsional)</label>
                            <input type="datetime-local" class="form-control" id="create_waktu_selesai" name="waktu_selesai">
                        </div>

                        <div class="col-12">
                            <label for="create_petunjuk" class="form-label">Petunjuk Ujian</label>
                            <textarea class="form-control" id="create_petunjuk" name="petunjuk" rows="2" placeholder="Petunjuk pengerjaan ujian bagi siswa..."></textarea>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="fw-bold mb-2">Opsi Pengaturan Soal & Nilai:</div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="create_acak_soal" name="acak_soal" value="1">
                                            <label class="form-check-label" for="create_acak_soal">Acak urutan soal untuk tiap siswa</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="create_acak_opsi" name="acak_opsi" value="1">
                                            <label class="form-check-label" for="create_acak_opsi">Acak pilihan opsi (A-E)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="create_tampil_nilai" name="tampil_nilai" value="1" checked>
                                            <label class="form-check-label" for="create_tampil_nilai">Tampilkan nilai langsung ke siswa setelah submit</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="create_tampil_pembahasan" name="tampil_pembahasan" value="1" checked>
                                            <label class="form-check-label" for="create_tampil_pembahasan">Tampilkan lembar mana yang benar & salah setelah submit</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="create_status" class="form-label">Status Ujian</label>
                            <select class="form-select" id="create_status" name="status">
                                <option value="aktif">Aktif (Siswa dapat mengerjakan)</option>
                                <option value="draft">Draft (Belum dibuka untuk siswa)</option>
                                <option value="selesai">Selesai (Ditutup)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan & Lanjut Tambah Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Exam -->
<div class="modal fade" id="editExamModal" tabindex="-1" aria-labelledby="editExamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editExamForm" method="post" action="">
                <?= csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="editExamModalLabel">
                        <i class="bi bi-gear text-primary"></i> Edit Pengaturan Ujian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_tahun_ajaran_id" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check text-primary me-1"></i>Tahun Ajaran & Semester <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary-subtle" id="edit_tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <?php foreach ($academicYears as $ay) : ?>
                                    <option value="<?= $ay['tahun_ajaran_id']; ?>">
                                        <?= esc($ay['tahun_ajaran_nama']); ?> - Semester <?= esc($ay['semester']); ?><?= (bool) $ay['is_active'] ? ' (Aktif Saat Ini)' : ' (Tidak Aktif)'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">
                                <i class="bi bi-info-circle me-1"></i>Jika tahun ajaran tidak aktif, ujian ini beserta soalnya tidak akan tampil pada portal CBT siswa.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="edit_judul" class="form-label">Judul Ujian / Asesmen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_judul" name="judul" required>
                        </div>

                        <div class="col-md-6">
                            <?php if ($isAdmin) : ?>
                                <label for="edit_guru_id" class="form-label">Guru Pengampu</label>
                                <select class="form-select" id="edit_guru_id" name="guru_id">
                                    <option value="">-- Pilih Guru Pengampu --</option>
                                    <?php foreach ($teachers as $t) : ?>
                                        <option value="<?= $t['guru_id']; ?>"><?= esc($t['guru_nama']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Pilih guru untuk menyesuaikan daftar mata pelajaran yang diajarkan.</div>
                            <?php else : ?>
                                <label class="form-label">Guru Pengampu</label>
                                <input type="hidden" id="edit_guru_id" name="guru_id" value="<?= esc($currentGuruId ?? ''); ?>">
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                                    <input type="text" class="form-control bg-light" value="<?= esc($currentTeacher['guru_nama'] ?? (session('authUser')['display_name'] ?? 'Guru')); ?>" readonly>
                                </div>
                                <div class="form-text text-muted">Guru pengampu terkunci pada akun guru Anda.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_mapel_id" class="form-label">Mata Pelajaran</label>
                            <select class="form-select" id="edit_mapel_id" name="mapel_id">
                                <?php if ($isAdmin) : ?>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <?php foreach ($allMapels as $m) : ?>
                                        <option value="<?= $m['mapel_id']; ?>"><?= esc($m['mapel_nama']); ?><?= ! empty($m['mapel_kode']) ? ' (' . esc($m['mapel_kode']) . ')' : ''; ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <?php if (! empty($mapels)) : ?>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        <?php foreach ($mapels as $m) : ?>
                                            <option value="<?= $m['mapel_id']; ?>"><?= esc($m['mapel_nama']); ?><?= ! empty($m['mapel_kode']) ? ' (' . esc($m['mapel_kode']) . ')' : ''; ?></option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="" disabled selected>-- Belum ada mapel ditugaskan --</option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($isAdmin) : ?>
                                <div id="edit_mapel_notice" class="form-text text-muted">Disesuaikan dengan mapel guru pengampu.</div>
                            <?php else : ?>
                                <div class="form-text text-muted">Hanya menampilkan mata pelajaran yang ditugaskan kepada Anda.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="edit_kelas_id" class="form-label">Target Kelas</label>
                            <select class="form-select" id="edit_kelas_id" name="kelas_id">
                                <option value="">-- Semua Kelas --</option>
                                <?php foreach ($classes as $c) : ?>
                                    <option value="<?= $c['kelas_id']; ?>"><?= esc($c['kelas_nama']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_durasi_menit" class="form-label">Durasi (Menit) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_durasi_menit" name="durasi_menit" min="1" max="360" required>
                        </div>

                        <div class="col-md-5">
                            <label for="edit_token" class="form-label d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span>Token Ujian</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 small">
                                    <i class="bi bi-magic me-1"></i>Generate Otomatis
                                </span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace text-uppercase fw-bold text-primary" id="edit_token" name="token" maxlength="15" placeholder="Otomatis jika kosong">
                                <button class="btn btn-outline-primary d-inline-flex align-items-center gap-1 text-nowrap px-3" type="button" id="btnGenTokenEdit" title="Klik untuk membuat token baru sekarang">
                                    <i class="bi bi-shuffle"></i>
                                    <span>Acak Token</span>
                                </button>
                            </div>
                            <div class="form-text">Kosongkan untuk generate otomatis, atau klik <strong>Acak Token</strong>.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_waktu_mulai" class="form-label">Waktu Mulai Akses</label>
                            <input type="datetime-local" class="form-control" id="edit_waktu_mulai" name="waktu_mulai">
                        </div>

                        <div class="col-md-6">
                            <label for="edit_waktu_selesai" class="form-label">Waktu Selesai Akses</label>
                            <input type="datetime-local" class="form-control" id="edit_waktu_selesai" name="waktu_selesai">
                        </div>

                        <div class="col-12">
                            <label for="edit_petunjuk" class="form-label">Petunjuk Ujian</label>
                            <textarea class="form-control" id="edit_petunjuk" name="petunjuk" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="fw-bold mb-2">Opsi Pengaturan Soal & Nilai:</div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_acak_soal" name="acak_soal" value="1">
                                            <label class="form-check-label" for="edit_acak_soal">Acak urutan soal untuk tiap siswa</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_acak_opsi" name="acak_opsi" value="1">
                                            <label class="form-check-label" for="edit_acak_opsi">Acak pilihan opsi (A-E)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_tampil_nilai" name="tampil_nilai" value="1">
                                            <label class="form-check-label" for="edit_tampil_nilai">Tampilkan nilai langsung ke siswa setelah submit</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_tampil_pembahasan" name="tampil_pembahasan" value="1">
                                            <label class="form-check-label" for="edit_tampil_pembahasan">Tampilkan lembar mana yang benar & salah setelah submit</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="edit_status" class="form-label">Status Ujian</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="aktif">Aktif (Siswa dapat mengerjakan)</option>
                                <option value="draft">Draft (Belum dibuka untuk siswa)</option>
                                <option value="selesai">Selesai (Ditutup)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isAdmin = <?= json_encode((bool) $isAdmin); ?>;
    const allMapels = <?= json_encode($allMapels ?? []); ?>;
    const teacherMapelMap = <?= json_encode($teacherMapelMap ?? []); ?>;

    const editModalEl = document.getElementById('editExamModal');
    const editForm = document.getElementById('editExamForm');
    const selectEditTahunAjaran = document.getElementById('edit_tahun_ajaran_id');
    const inputJudul = document.getElementById('edit_judul');
    const selectMapel = document.getElementById('edit_mapel_id');
    const selectKelas = document.getElementById('edit_kelas_id');
    const selectGuru = document.getElementById('edit_guru_id');
    const inputDurasi = document.getElementById('edit_durasi_menit');
    const inputToken = document.getElementById('edit_token');
    const inputWaktuMulai = document.getElementById('edit_waktu_mulai');
    const inputWaktuSelesai = document.getElementById('edit_waktu_selesai');
    const inputPetunjuk = document.getElementById('edit_petunjuk');
    const checkAcakSoal = document.getElementById('edit_acak_soal');
    const checkAcakOpsi = document.getElementById('edit_acak_opsi');
    const checkTampilNilai = document.getElementById('edit_tampil_nilai');
    const checkTampilPembahasan = document.getElementById('edit_tampil_pembahasan');
    const selectStatus = document.getElementById('edit_status');

    const createGuruSelect = document.getElementById('create_guru_id');
    const createMapelSelect = document.getElementById('create_mapel_id');
    const createMapelNotice = document.getElementById('create_mapel_notice');
    const editMapelNotice = document.getElementById('edit_mapel_notice');

    function populateMapelSelect(selectEl, selectedGuruId, selectedMapelId = null, noticeEl = null) {
        if (! selectEl || ! isAdmin) return;

        selectEl.innerHTML = '';
        const guruIdInt = parseInt(selectedGuruId);

        if (! guruIdInt) {
            const defOpt = document.createElement('option');
            defOpt.value = '';
            defOpt.textContent = '-- Pilih Guru Pengampu Terlebih Dahulu --';
            selectEl.appendChild(defOpt);

            allMapels.forEach(function (m) {
                const opt = document.createElement('option');
                opt.value = m.mapel_id;
                opt.textContent = m.mapel_nama + (m.mapel_kode ? ' (' + m.mapel_kode + ')' : '');
                if (selectedMapelId && parseInt(selectedMapelId) === parseInt(m.mapel_id)) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });

            if (noticeEl) {
                noticeEl.textContent = 'Pilih guru pengampu terlebih dahulu untuk memfilter mapel.';
                noticeEl.className = 'form-text text-muted';
            }
            return;
        }

        const assignedMapelIds = teacherMapelMap[guruIdInt] || [];
        const filteredMapels = allMapels.filter(function (m) {
            return assignedMapelIds.includes(parseInt(m.mapel_id));
        });

        if (filteredMapels.length === 0) {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '-- Guru ini belum memiliki mapel di Pengaturan Mapel Guru --';
            selectEl.appendChild(emptyOpt);

            if (noticeEl) {
                noticeEl.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Guru ini belum memiliki mapel yang ditugaskan pada <strong>Pengaturan &rarr; Mapel Guru</strong>.</span>';
                noticeEl.className = 'form-text text-danger';
            }
        } else {
            const defOpt = document.createElement('option');
            defOpt.value = '';
            defOpt.textContent = '-- Pilih Mata Pelajaran --';
            selectEl.appendChild(defOpt);

            let hasSelected = false;
            filteredMapels.forEach(function (m) {
                const opt = document.createElement('option');
                opt.value = m.mapel_id;
                opt.textContent = m.mapel_nama + (m.mapel_kode ? ' (' + m.mapel_kode + ')' : '');
                if (selectedMapelId && parseInt(selectedMapelId) === parseInt(m.mapel_id)) {
                    opt.selected = true;
                    hasSelected = true;
                }
                selectEl.appendChild(opt);
            });

            if (selectedMapelId && ! hasSelected) {
                const legacy = allMapels.find(m => parseInt(m.mapel_id) === parseInt(selectedMapelId));
                if (legacy) {
                    const legOpt = document.createElement('option');
                    legOpt.value = legacy.mapel_id;
                    legOpt.textContent = legacy.mapel_nama + ' (Mapel Sebelumnya)';
                    legOpt.selected = true;
                    selectEl.appendChild(legOpt);
                }
            }

            if (noticeEl) {
                noticeEl.textContent = 'Daftar mapel disaring sesuai penugasan di Pengaturan Mapel Guru (' + filteredMapels.length + ' mapel).';
                noticeEl.className = 'form-text text-success';
            }
        }
    }

    if (isAdmin && createGuruSelect && createMapelSelect) {
        createGuruSelect.addEventListener('change', function () {
            populateMapelSelect(createMapelSelect, this.value, null, createMapelNotice);
        });
        if (createGuruSelect.value) {
            populateMapelSelect(createMapelSelect, createGuruSelect.value, null, createMapelNotice);
        }
    }

    if (isAdmin && selectGuru && selectMapel) {
        selectGuru.addEventListener('change', function () {
            populateMapelSelect(selectMapel, this.value, null, editMapelNotice);
        });
    }

    // Event listener untuk tombol setting/edit ujian (delegated listener agar selalu bekerja)
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.btn-edit-exam');
        if (! button) return;

        const dataStr = button.getAttribute('data-exam');
        if (! dataStr) return;

        let data;
        try {
            data = JSON.parse(dataStr);
        } catch (err) {
            console.error('Gagal membaca data ujian:', err);
            return;
        }

        if (editForm) {
            editForm.action = '<?= site_url('online-assessments/update'); ?>/' + data.ujian_id;
        }
        if (selectEditTahunAjaran) {
            selectEditTahunAjaran.value = data.tahun_ajaran_id || '';
        }
        if (inputJudul) {
            inputJudul.value = data.judul || '';
        }

        if (isAdmin && selectGuru) {
            selectGuru.value = data.guru_id || '';
            populateMapelSelect(selectMapel, data.guru_id, data.mapel_id, editMapelNotice);
        }

        if (selectMapel) selectMapel.value = data.mapel_id || '';
        if (selectKelas) selectKelas.value = data.kelas_id || '';
        if (inputDurasi) inputDurasi.value = data.durasi_menit || 60;
        if (inputToken) inputToken.value = data.token || '';
        if (inputWaktuMulai) inputWaktuMulai.value = data.waktu_mulai ? data.waktu_mulai.replace(' ', 'T').substring(0, 16) : '';
        if (inputWaktuSelesai) inputWaktuSelesai.value = data.waktu_selesai ? data.waktu_selesai.replace(' ', 'T').substring(0, 16) : '';
        if (inputPetunjuk) inputPetunjuk.value = data.petunjuk || '';
        if (checkAcakSoal) checkAcakSoal.checked = parseInt(data.acak_soal) === 1;
        if (checkAcakOpsi) checkAcakOpsi.checked = parseInt(data.acak_opsi) === 1;
        if (checkTampilNilai) checkTampilNilai.checked = parseInt(data.tampil_nilai) === 1;
        if (checkTampilPembahasan) checkTampilPembahasan.checked = parseInt(data.tampil_pembahasan) === 1;
        if (selectStatus) selectStatus.value = data.status || 'aktif';

        if (editModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
            modalInstance.show();
        }
    });

    function generateRandomTokenStr(len = 6) {
        const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        let res = '';
        for (let i = 0; i < len; i++) {
            res += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return res;
    }

    const btnGenCreate = document.getElementById('btnGenTokenCreate');
    if (btnGenCreate) {
        btnGenCreate.addEventListener('click', function () {
            document.getElementById('create_token').value = generateRandomTokenStr();
        });
    }

    const btnGenEdit = document.getElementById('btnGenTokenEdit');
    if (btnGenEdit) {
        btnGenEdit.addEventListener('click', function () {
            document.getElementById('edit_token').value = generateRandomTokenStr();
        });
    }

    document.querySelectorAll('.btn-copy-token').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const token = this.getAttribute('data-token');
            if (! token) return;

            navigator.clipboard.writeText(token).then(() => {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = 'bi bi-check2 text-success';
                    setTimeout(() => {
                        icon.className = 'bi bi-clipboard';
                    }, 2000);
                }
            }).catch(() => {
                prompt('Salin Token Ujian:', token);
            });
        });
    });
});
</script>
<?= $this->endSection(); ?>
