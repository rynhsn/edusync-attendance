<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">User Sedang Login</h1>
        <p class="text-muted mb-0">Daftar akun yang beraktivitas dalam 5 menit terakhir. Sesi yang tidak aktif selama 2 jam tidak ditampilkan.</p>
    </div>

    <?php if (! $storageAvailable) : ?>
        <div class="alert alert-warning" role="alert">Penyimpanan sesi online belum tersedia. Jalankan migrasi database aplikasi terlebih dahulu.</div>
    <?php elseif ($users === []) : ?>
        <div class="alert alert-info" role="status">Saat ini tidak ada user yang terdeteksi aktif.</div>
    <?php else : ?>
        <div class="row mb-3">
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="online-user-search">Cari user</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input class="form-control" id="online-user-search" type="search" placeholder="Nama, username, email, atau role">
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th>Nama user</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Login sejak</th>
                            <th>Aktivitas terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <?php $searchText = implode(' ', [
                                $user['display_name'],
                                $user['username'],
                                $user['email'],
                                $roleLabels[$user['role']] ?? $user['role'],
                            ]); ?>
                            <tr data-online-user-row data-search="<?= esc(mb_strtolower($searchText)); ?>">
                                <td class="text-center">
                                    <span class="d-inline-flex align-items-center justify-content-center gap-2 px-2 py-1 rounded-pill bg-success-subtle text-success-emphasis small fw-semibold">
                                        <span class="rounded-circle bg-success border border-2 border-white shadow-sm" style="width: 10px; height: 10px;" aria-hidden="true"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="fw-medium"><?= esc($user['display_name']); ?></td>
                                <td><?= esc($user['username']); ?></td>
                                <td><?= esc($user['email']); ?></td>
                                <td><?= esc($roleLabels[$user['role']] ?? $user['role']); ?></td>
                                <td class="text-nowrap"><?= esc($user['logged_in_at']); ?></td>
                                <td class="text-nowrap"><?= esc($user['last_seen_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="online-user-no-results" hidden>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada user yang cocok dengan pencarian.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            (() => {
                const searchInput = document.getElementById('online-user-search');
                const userRows = Array.from(document.querySelectorAll('[data-online-user-row]'));
                const noResultsRow = document.getElementById('online-user-no-results');
                if (!searchInput || !noResultsRow) return;

                searchInput.addEventListener('input', () => {
                    const query = searchInput.value.trim().toLocaleLowerCase();
                    let visibleCount = 0;
                    userRows.forEach(row => {
                        const matches = row.dataset.search.includes(query);
                        row.hidden = !matches;
                        if (matches) visibleCount++;
                    });
                    noResultsRow.hidden = visibleCount > 0;
                });
            })();
        </script>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>
