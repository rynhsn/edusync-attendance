<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Data User</h1>
            <p class="text-muted mb-0">Daftar akun dan pengaturan role. Registrasi user otomatis terhubung dengan data guru.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2">
                <i class="bi bi-arrow-repeat me-1"></i> Auto-Sync Guru Aktif
            </span>

            <!-- Tombol Export Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> Export Data User
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><h6 class="dropdown-header text-uppercase small text-primary fw-bold">Khusus Akun Siswa (CBT)</h6></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= site_url('users/export?format=xlsx&role=siswa'); ?>">
                            <i class="bi bi-file-earmark-excel text-success fs-6"></i>
                            <div>
                                <span class="d-block fw-semibold">Export Siswa (.xlsx)</span>
                                <small class="text-muted">Format spreadsheet Excel modern</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= site_url('users/export?format=xls&role=siswa'); ?>">
                            <i class="bi bi-file-earmark-spreadsheet text-info fs-6"></i>
                            <div>
                                <span class="d-block fw-semibold">Export Siswa (.xls)</span>
                                <small class="text-muted">Kompatibel dengan Excel lama</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= site_url('users/export?format=csv&role=siswa'); ?>">
                            <i class="bi bi-file-earmark-text text-primary fs-6"></i>
                            <div>
                                <span class="d-block fw-semibold">Export Siswa (.csv)</span>
                                <small class="text-muted">Format teks CSV terpisah titik-koma</small>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header text-uppercase small text-secondary fw-bold">Semua Akun Pengguna</h6></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= site_url('users/export?format=xlsx&role=all'); ?>">
                            <i class="bi bi-file-earmark-excel text-success fs-6"></i>
                            <div>
                                <span class="d-block fw-semibold">Export Semua User (.xlsx)</span>
                                <small class="text-muted">Seluruh guru, siswa, & staf</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= site_url('users/export?format=csv&role=all'); ?>">
                            <i class="bi bi-file-earmark-text text-primary fs-6"></i>
                            <div>
                                <span class="d-block fw-semibold">Export Semua User (.csv)</span>
                                <small class="text-muted">Seluruh data user format CSV</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tombol Import Siswa -->
            <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#importUserModal">
                <i class="bi bi-file-earmark-arrow-up-fill me-1"></i> Import User Siswa
            </button>

            <!-- Tombol Tambah User Baru -->
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
            </button>
        </div>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="status">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h5 card-title mb-0 fw-semibold">Daftar User</h2>
                    <span id="userCountBadge" class="badge bg-primary-subtle text-primary px-3 py-2 fs-7"><?= count($users); ?> user terdaftar</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                    <!-- Live Search Box -->
                    <div class="input-group input-group-sm" style="min-width: 260px; max-width: 340px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchUserInput" class="form-control border-start-0 ps-0" placeholder="Cari nama user, username, NIS, kelas..." value="<?= esc($search ?? ''); ?>" autocomplete="off">
                        <button class="btn btn-outline-secondary border-start-0 <?= empty($search) ? 'd-none' : ''; ?>" type="button" id="clearSearchBtn" title="Hapus Pencarian">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Role Filter -->
                    <select id="filterRoleSelect" class="form-select form-select-sm" style="min-width: 150px; width: auto;">
                        <option value="all" <?= ($roleFilter ?? '') === 'all' || empty($roleFilter) ? 'selected' : ''; ?>>Semua Peran</option>
                        <option value="siswa" <?= ($roleFilter ?? '') === 'siswa' ? 'selected' : ''; ?>>Siswa (CBT)</option>
                        <?php foreach ($roles as $rVal => $rLabel) : ?>
                            <?php if ($rVal !== 'siswa') : ?>
                                <option value="<?= esc($rVal); ?>" <?= ($roleFilter ?? '') === $rVal ? 'selected' : ''; ?>><?= esc($rLabel); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 50px;">NO.</th>
                        <th scope="col">NAMA / IDENTITAS</th>
                        <th scope="col">EMAIL</th>
                        <th scope="col">PERAN</th>
                        <th scope="col" style="min-width: 220px;">ATUR ROLE</th>
                        <th scope="col">STATUS EMAIL</th>
                        <th scope="col">TGL DAFTAR</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <?php 
                    $currentUserId = (int) (session()->get('authUser')['user_id'] ?? 0);
                    foreach ($users as $index => $user) : 
                        $isSelf = (int) $user['user_id'] === $currentUserId;
                        $isNonaktif = ($user['status'] ?? 'active') === 'nonaktif';
                        $isSiswa = ($user['role'] ?? '') === 'siswa';
                        $searchKeywords = strtolower(trim(($user['display_name'] ?? '') . ' ' . ($user['username'] ?? '') . ' ' . ($user['email'] ?? '') . ' ' . ($user['role'] ?? '') . ' ' . ($user['nama_siswa'] ?? '') . ' ' . ($user['nis'] ?? '') . ' ' . ($user['kelas_nama'] ?? '') . ' ' . ($user['guru_nip'] ?? '')));
                    ?>
                        <tr class="user-row" 
                            data-search="<?= esc($searchKeywords); ?>"
                            data-role="<?= esc($user['role'] ?? ''); ?>"
                            data-status="<?= esc($user['status'] ?? 'active'); ?>">
                            <td class="text-center text-muted row-number"><?= $index + 1; ?></td>
                            <td>
                                <a href="javascript:void(0)" 
                                    class="text-dark fw-semibold text-decoration-none btn-edit-user d-inline-flex align-items-center gap-1"
                                    data-user-id="<?= $user['user_id']; ?>"
                                    data-display-name="<?= esc($user['display_name'] ?? ''); ?>"
                                    data-username="<?= esc($user['username'] ?? ''); ?>"
                                    data-email="<?= esc($user['email'] ?? ''); ?>"
                                    data-role="<?= esc($user['role'] ?? ''); ?>"
                                    data-status="<?= esc($user['status'] ?? 'active'); ?>"
                                    data-nip="<?= esc($user['guru_nip'] ?? ''); ?>"
                                    data-telepon="<?= esc($user['guru_telepon'] ?? ''); ?>"
                                    data-rfid="<?= esc($user['rfid_tag'] ?? ''); ?>"
                                    data-is-self="<?= $isSelf ? '1' : '0'; ?>"
                                    data-is-siswa="<?= $isSiswa ? '1' : '0'; ?>"
                                    title="Klik untuk edit data user">
                                    <span><?= esc($user['display_name'] ?: '-'); ?></span>
                                    <i class="bi bi-pencil text-muted small opacity-50"></i>
                                </a>
                                <?php if ($isSiswa) : ?>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-mortarboard me-1 text-info"></i><?= esc($user['nama_siswa'] ?: 'Siswa Terhubung'); ?> (<?= esc($user['kelas_nama'] ?: '-'); ?> &bull; NIS: <?= esc($user['nis'] ?: '-'); ?>)
                                    </div>
                                    <div class="small text-primary">
                                        <i class="bi bi-person-badge me-1"></i>Username: <strong><?= esc($user['username'] ?? '-'); ?></strong>
                                    </div>
                                <?php else : ?>
                                    <div class="small text-muted mt-1">
                                        Username: <strong><?= esc($user['username'] ?: '-'); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($user['email']); ?></td>
                            <td>
                                <?php if ($isSiswa) : ?>
                                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25">
                                        <i class="bi bi-mortarboard me-1"></i>Siswa
                                    </span>
                                <?php else : ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25">
                                        <?= esc($roles[$user['role']] ?? 'Tidak dikenal'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($isSelf) : ?>
                                    <span class="text-muted small fst-italic">Akun aktif Anda</span>
                                <?php else : ?>
                                    <form class="d-flex gap-2 align-items-center" method="post" action="<?= site_url('users/role/' . $user['user_id']); ?>">
                                        <?= csrf_field(); ?>
                                        <select class="form-select form-select-sm" name="role" aria-label="Pilih role <?= esc($user['display_name'] ?: $user['email']); ?>">
                                            <?php foreach ($roles as $roleValue => $roleLabel) : ?>
                                                <option value="<?= esc($roleValue); ?>" <?= $user['role'] === $roleValue ? 'selected' : ''; ?>><?= esc($roleLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm <?= $isNonaktif ? 'btn-success' : 'btn-outline-primary'; ?> text-nowrap" type="submit">
                                            <?= $isNonaktif ? '<i class="bi bi-check2 me-1"></i>Aktifkan' : 'Simpan'; ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div>
                                    <span class="badge <?= (bool) $user['is_verified'] ? 'bg-success-subtle text-success border border-success border-opacity-25' : 'bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25'; ?>">
                                        <i class="bi <?= (bool) $user['is_verified'] ? 'bi-check-circle' : 'bi-clock'; ?> me-1"></i>
                                        <?= (bool) $user['is_verified'] ? 'Terverifikasi' : 'Belum diverifikasi'; ?>
                                    </span>
                                </div>
                                <?php if ($isNonaktif) : ?>
                                    <div class="mt-1">
                                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25" title="Akun masih nonaktif, perlu diaktifkan oleh admin">
                                            <i class="bi bi-person-x me-1"></i>Nonaktif
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small text-muted">
                                    <?= esc(! empty($user['created_at']) ? date('Y-m-d H:i', strtotime($user['created_at'])) : '-'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($users === []) : ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data user.</td></tr>
                    <?php endif; ?>
                    <tr id="noSearchResultsRow" class="d-none">
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-search fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            <span>Tidak ada user yang sesuai dengan kata kunci atau filter pencarian.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('modals'); ?>
<!-- Modal Tambah User Baru (Termasuk Role Siswa) -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="addUserForm" method="post" action="<?= site_url('users/create'); ?>">
                <?= csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="addUserModalLabel">
                        <i class="bi bi-person-plus-fill"></i> Tambah User Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            Akun yang dibuat di sini langsung <strong>aktif dan terverifikasi</strong> tanpa perlu melalui proses registrasi atau verifikasi email oleh pengguna.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="new_role" class="form-label fw-bold">Pilih Role / Peran Akun <span class="text-danger">*</span></label>
                            <select class="form-select border-primary" id="new_role" name="role" required>
                                <option value="siswa" selected>Siswa (Akses Portal CBT Online)</option>
                                <?php foreach ($roles as $roleValue => $roleLabel) : ?>
                                    <?php if ($roleValue !== 'siswa') : ?>
                                        <option value="<?= esc($roleValue); ?>"><?= esc($roleLabel); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Khusus Role Siswa: Pemilihan Data Siswa -->
                        <div class="col-md-6" id="container_student_select">
                            <label for="new_siswa_id" class="form-label fw-bold">Pilih Siswa Terdaftar <span class="text-danger">*</span></label>
                            <select class="form-select" id="new_siswa_id" name="siswa_id">
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($students as $st) : ?>
                                    <option value="<?= $st['siswa_id']; ?>" 
                                        data-nama="<?= esc($st['nama_siswa']); ?>" 
                                        data-nis="<?= esc($st['nis'] ?? ''); ?>" 
                                        data-kelas="<?= esc($st['kelas_nama'] ?? $st['kelas'] ?? '-'); ?>"
                                        <?= ! empty($st['existing_user_id']) ? 'disabled' : ''; ?>>
                                        <?= esc($st['nama_siswa']); ?> (<?= esc($st['kelas_nama'] ?? $st['kelas'] ?? '-'); ?> - NIS: <?= esc($st['nis'] ?? '-'); ?>)
                                        <?= ! empty($st['existing_user_id']) ? ' [Sudah Punya Akun]' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Pilih siswa yang akan dibuatkan akses login CBT.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="new_display_name" class="form-label fw-bold">Nama Lengkap / Tampilan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="new_display_name" name="display_name" required maxlength="100" placeholder="Contoh: Ahmad Fauzi">
                        </div>

                        <div class="col-md-6">
                            <label for="new_username" class="form-label fw-bold">Username untuk Login <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control" id="new_username" name="username" required maxlength="32" placeholder="bukan NIS, contoh: ahmad.fauzi">
                            </div>
                            <div class="form-text">Digunakan siswa/user saat login. 3–32 karakter tanpa spasi.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="new_password" class="form-label fw-bold">Password Login <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="new_password" name="password" required minlength="8" maxlength="72" value="siswa123" placeholder="Minimal 8 karakter">
                                <button class="btn btn-outline-secondary" type="button" id="btnGenPassword" title="Acak Password">
                                    <i class="bi bi-shuffle"></i>
                                </button>
                            </div>
                            <div class="form-text">Catat password ini untuk diberikan kepada siswa/user.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="new_email" class="form-label fw-bold">Alamat Email</label>
                            <input type="email" class="form-control" id="new_email" name="email" maxlength="254" placeholder="Otomatis diisi jika kosong">
                            <div class="form-text">Jika dikosongkan untuk siswa, akan dibuatkan email internal sistem.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Buat & Aktifkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import User Siswa -->
<div class="modal fade" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="importUserForm" method="post" action="<?= site_url('users/import'); ?>" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="importUserModalLabel">
                        <i class="bi bi-file-earmark-arrow-up-fill"></i> Import Akun User Siswa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-start mb-3">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0 mt-1"></i>
                        <div>
                            Fitur ini digunakan untuk membuat akun login siswa secara massal untuk akses <strong>Portal CBT Ujian Online</strong>. Sistem otomatis mencocokkan siswa berdasarkan <strong>NIS</strong> atau <strong>Nama Siswa</strong> yang terdaftar di Data Siswa.
                        </div>
                    </div>

                    <!-- Unduh Template Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body p-3">
                            <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-download text-success"></i> Unduh Template Format Import
                            </h6>
                            <p class="small text-muted mb-2">
                                Gunakan salah satu format template berikut untuk mengisi data akun siswa yang akan diimpor:
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= site_url('users/import-template?format=xlsx'); ?>" class="btn btn-sm btn-success shadow-sm">
                                    <i class="bi bi-file-earmark-excel me-1"></i> Template Excel (.xlsx)
                                </a>
                                <a href="<?= site_url('users/import-template?format=xls'); ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Template Excel (.xls)
                                </a>
                                <a href="<?= site_url('users/import-template?format=csv'); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text me-1"></i> Template CSV (.csv)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <div class="mb-3">
                        <label for="userImportFile" class="form-label fw-bold">Pilih File Spreadsheet Siswa <span class="text-danger">*</span></label>
                        <input class="form-control" id="userImportFile" name="user_file" type="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Mendukung format file <strong>.xlsx</strong>, <strong>.xls</strong>, dan <strong>.csv</strong> (Maksimal 10 MB). Baris berisi contoh <code>[CONTOH]</code> otomatis dilewati oleh sistem.</div>
                    </div>

                    <!-- Panduan Kolom -->
                    <div class="border rounded p-3 bg-white small">
                        <span class="fw-bold text-dark d-block mb-1"><i class="bi bi-card-checklist me-1 text-primary"></i>Panduan Kolom Template:</span>
                        <ul class="mb-0 text-muted ps-3">
                            <li><strong>nis</strong>: Nomor Induk Siswa (wajib/sangat dianjurkan agar otomatis tersambung ke Data Siswa).</li>
                            <li><strong>nama_siswa</strong>: Nama lengkap siswa (alternatif pencocokan jika NIS kosong).</li>
                            <li><strong>username</strong>: Username login siswa (opsional, jika dikosongkan otomatis di-generate dari nama/nis).</li>
                            <li><strong>password</strong>: Password login siswa (opsional, minimal 8 karakter. Default: <code>siswa123</code>).</li>
                            <li><strong>email</strong>: Email siswa (opsional, default: <code>username@siswa.edusync</code>).</li>
                            <li><strong>status</strong>: Status akun (<code>active</code> atau <code>nonaktif</code>, default: <code>active</code>).</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i> Mulai Import User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User & Guru -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editUserForm" method="post" action="">
                <?= csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="editUserModalLabel">
                        <i class="bi bi-person-gear text-primary"></i> Edit Data User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-4" id="editSyncTeacherAlert">
                        <i class="bi bi-info-circle-fill me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            Perubahan data pada form ini akan <strong>otomatis tersinkronisasi</strong> antara akun login (Data User) dan Data Guru di sistem.
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-4 d-none" id="editSiswaAlert">
                        <i class="bi bi-mortarboard-fill me-2 fs-5 flex-shrink-0"></i>
                        <div>
                            Akun ini merupakan <strong>Akun Siswa</strong> yang terhubung langsung dengan Data Siswa untuk akses Ujian Online (CBT).
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="fw-bold text-muted border-bottom pb-2 mb-0">Informasi Akun User</h6>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_display_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_display_name" name="display_name" required maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_username" name="username" required maxlength="32">
                            <div class="form-text">3–32 karakter: huruf, angka, titik, atau strip.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required maxlength="254">
                        </div>

                        <div class="col-md-6">
                            <label for="edit_role" class="form-label">Peran / Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <?php foreach ($roles as $roleValue => $roleLabel) : ?>
                                    <option value="<?= esc($roleValue); ?>"><?= esc($roleLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Status Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Aktif (Dapat Login)</option>
                                <option value="nonaktif">Nonaktif (Menunggu Aktivasi)</option>
                            </select>
                        </div>

                        <div id="sectionTeacherData" class="col-12 p-0">
                            <div class="col-12 mt-4 px-3">
                                <h6 class="fw-bold text-muted border-bottom pb-2 mb-0">Data Profil Guru (Tersinkronisasi)</h6>
                            </div>

                            <div class="row g-3 px-3 pt-2">
                                <div class="col-md-6">
                                    <label for="edit_guru_nip" class="form-label">NIP Guru</label>
                                    <input type="text" class="form-control" id="edit_guru_nip" name="guru_nip" maxlength="30" placeholder="Contoh: 198501012010011001">
                                    <div class="form-text">Kosongkan jika belum memiliki NIP.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_guru_telepon" class="form-label">Nomor Telepon / WhatsApp</label>
                                    <input type="text" class="form-control" id="edit_guru_telepon" name="guru_telepon" maxlength="30" placeholder="Contoh: 08123456789">
                                </div>

                                <div class="col-12">
                                    <label for="edit_rfid_tag" class="form-label">RFID Tag Guru</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-broadcast"></i></span>
                                        <input type="text" class="form-control" id="edit_rfid_tag" name="rfid_tag" maxlength="50" placeholder="Tap kartu RFID atau masukkan kode tag RFID">
                                    </div>
                                    <div class="form-text">Digunakan untuk absensi kehadiran guru menggunakan mesin/scanner RFID.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold text-muted border-bottom pb-2 mb-0">Ubah Password Akun (Opsional)</h6>
                        </div>

                        <div class="col-12">
                            <label for="edit_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="edit_password" name="password" minlength="8" maxlength="72" placeholder="Kosongkan jika tidak ingin mengubah password akun">
                            <div class="form-text">Hanya isi bidang ini jika ingin mereset password akun user yang bersangkutan (minimal 8 karakter).</div>
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
    // Handling Add User Modal
    const selectNewRole = document.getElementById('new_role');
    const containerStudentSelect = document.getElementById('container_student_select');
    const selectNewSiswa = document.getElementById('new_siswa_id');
    const inputNewDisplayName = document.getElementById('new_display_name');
    const inputNewUsername = document.getElementById('new_username');
    const inputNewEmail = document.getElementById('new_email');
    const btnGenPassword = document.getElementById('btnGenPassword');
    const inputNewPassword = document.getElementById('new_password');

    function toggleStudentContainer() {
        if (!selectNewRole || !containerStudentSelect) return;
        const isSiswa = selectNewRole.value === 'siswa';
        containerStudentSelect.style.display = isSiswa ? 'block' : 'none';
        if (selectNewSiswa) {
            selectNewSiswa.required = isSiswa;
        }
    }

    if (selectNewRole) {
        selectNewRole.addEventListener('change', toggleStudentContainer);
        toggleStudentContainer();
    }

    if (selectNewSiswa) {
        selectNewSiswa.addEventListener('change', function () {
            const selectedOpt = this.options[this.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) return;

            const nama = selectedOpt.getAttribute('data-nama') || '';
            const nis = selectedOpt.getAttribute('data-nis') || '';

            inputNewDisplayName.value = nama;

            // Generate clean username from name: e.g. "Ahmad Fauzi" -> "ahmad.fauzi"
            let cleanUsername = nama.toLowerCase().replace(/[^a-z0-9]/g, '.').replace(/\.+/g, '.').replace(/^\.|\.$/g, '');
            if (!cleanUsername) {
                cleanUsername = 'siswa.' + (nis || Math.floor(1000 + Math.random() * 9000));
            }
            inputNewUsername.value = cleanUsername;
            inputNewEmail.value = cleanUsername + '@siswa.edusync';
        });
    }

    if (btnGenPassword && inputNewPassword) {
        btnGenPassword.addEventListener('click', function () {
            const chars = 'abcdefghjkmnpqrstuvwxyz23456789';
            let pass = '';
            for (let i = 0; i < 8; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            inputNewPassword.value = pass;
        });
    }

    // Handling Edit User Modal
    const editModalEl = document.getElementById('editUserModal');
    if (!editModalEl) return;

    const editForm = document.getElementById('editUserForm');
    const inputDisplayName = document.getElementById('edit_display_name');
    const inputUsername = document.getElementById('edit_username');
    const inputEmail = document.getElementById('edit_email');
    const selectRole = document.getElementById('edit_role');
    const selectStatus = document.getElementById('edit_status');
    const inputNip = document.getElementById('edit_guru_nip');
    const inputTelepon = document.getElementById('edit_guru_telepon');
    const inputRfid = document.getElementById('edit_rfid_tag');
    const inputPassword = document.getElementById('edit_password');
    const sectionTeacherData = document.getElementById('sectionTeacherData');
    const editSyncTeacherAlert = document.getElementById('editSyncTeacherAlert');
    const editSiswaAlert = document.getElementById('editSiswaAlert');

    document.querySelectorAll('.btn-edit-user').forEach(function (button) {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-user-id');
            const displayName = this.getAttribute('data-display-name') || '';
            const username = this.getAttribute('data-username') || '';
            const email = this.getAttribute('data-email') || '';
            const role = this.getAttribute('data-role') || '';
            const status = this.getAttribute('data-status') || 'active';
            const nip = this.getAttribute('data-nip') || '';
            const telepon = this.getAttribute('data-telepon') || '';
            const rfid = this.getAttribute('data-rfid') || '';
            const isSiswa = this.getAttribute('data-is-siswa') === '1';

            editForm.action = '<?= site_url('users/update'); ?>/' + userId;
            inputDisplayName.value = displayName;
            inputUsername.value = username;
            inputEmail.value = email;
            if (selectRole) selectRole.value = role;
            if (selectStatus) selectStatus.value = status;
            inputNip.value = nip;
            inputTelepon.value = telepon;
            inputRfid.value = rfid;
            inputPassword.value = '';

            if (isSiswa) {
                if (sectionTeacherData) sectionTeacherData.style.display = 'none';
                if (editSyncTeacherAlert) editSyncTeacherAlert.classList.add('d-none');
                if (editSiswaAlert) editSiswaAlert.classList.remove('d-none');
            } else {
                if (sectionTeacherData) sectionTeacherData.style.display = 'block';
                if (editSyncTeacherAlert) editSyncTeacherAlert.classList.remove('d-none');
                if (editSiswaAlert) editSiswaAlert.classList.add('d-none');
            }

            const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
            modalInstance.show();
        });
    });

    // ==========================================
    // Live Search and Role Filtering Logic
    // ==========================================
    const searchInput = document.getElementById('searchUserInput');
    const roleSelect = document.getElementById('filterRoleSelect');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const userRows = document.querySelectorAll('.user-row');
    const noResultsRow = document.getElementById('noSearchResultsRow');
    const countBadge = document.getElementById('userCountBadge');
    const totalUsers = userRows.length;

    function filterUsers() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedRole = roleSelect ? roleSelect.value : 'all';

        if (clearSearchBtn) {
            if (query !== '') {
                clearSearchBtn.classList.remove('d-none');
            } else {
                clearSearchBtn.classList.add('d-none');
            }
        }

        const terms = query.split(/\s+/).filter(Boolean);
        let visibleCount = 0;

        userRows.forEach(function (row) {
            const searchData = (row.getAttribute('data-search') || '').toLowerCase();
            const rowRole = row.getAttribute('data-role') || '';

            // Cocok jika semua kata kunci ada di baris
            const matchesQuery = terms.length === 0 || terms.every(function (term) {
                return searchData.indexOf(term) !== -1;
            });
            const matchesRole = (selectedRole === 'all' || rowRole === selectedRole);

            if (matchesQuery && matchesRole) {
                row.style.display = '';
                visibleCount++;
                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultsRow) {
            if (visibleCount === 0 && totalUsers > 0) {
                noResultsRow.classList.remove('d-none');
            } else {
                noResultsRow.classList.add('d-none');
            }
        }

        if (countBadge) {
            if (query !== '' || selectedRole !== 'all') {
                countBadge.textContent = 'Menampilkan ' + visibleCount + ' dari ' + totalUsers + ' user';
            } else {
                countBadge.textContent = totalUsers + ' user terdaftar';
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', filterUsers);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            filterUsers();
        });
    }

    // Jalankan filter awal jika ada input awal
    if ((searchInput && searchInput.value) || (roleSelect && roleSelect.value !== 'all')) {
        filterUsers();
    }
});
</script>
<?= $this->endSection(); ?>
