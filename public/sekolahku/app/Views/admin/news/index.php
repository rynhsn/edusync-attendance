<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Berita</h4>
        <p class="text-body-tertiary small mb-0">Kelola artikel berita sekolah</p>
    </div>
    <a href="/admin/news/new" class="btn btn-primary rounded-pill px-3">
        <i class="fas fa-plus me-1"></i>Tambah Berita
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table table-crud table-hover align-middle mb-0" id="news-table">
            <thead>
                <tr>
                    <th style="width:60px">#</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th style="width:100px">Status</th>
                    <th>Penulis</th>
                    <th style="width:110px">Terbit</th>
                    <th style="width:180px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr class="news-data-row" data-id="<?= $item['id'] ?>">
                    <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $i + 1 + (($pager->getCurrentPage() - 1) * 10) ?></span></td>
                    <td class="fw-medium news-title"><?= esc($item['title']) ?></td>
                    <td class="text-body-secondary news-category"><?= esc($item['category'] ?? '-') ?></td>
                    <td class="news-status-cell">
                        <?php if ($item['status'] === 'published'): ?>
                            <span class="badge bg-success news-badge">Terbit</span>
                        <?php elseif ($item['status'] === 'draft'): ?>
                            <span class="badge bg-secondary news-badge">Draf</span>
                        <?php elseif ($item['status'] === 'archived'): ?>
                            <span class="badge bg-warning news-badge">Arsip</span>
                        <?php else: ?>
                            <span class="badge bg-secondary news-badge"><?= esc($item['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-body-secondary small news-author"><?= esc($item['author'] ?? '-') ?></td>
                    <td class="text-body-secondary small news-date"><?= $item['published_at'] ? date('d M Y', strtotime($item['published_at'])) : '-' ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/admin/news/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-info border-0 quick-edit-btn" title="Quick Edit" data-id="<?= $item['id'] ?>">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0" title="Delete" data-coreui-toggle="modal" data-coreui-target="#deleteModal<?= $item['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="modal fade" id="deleteModal<?= $item['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="modal-title">Konfirmasi Hapus</h6>
                                        <button type="button" class="btn-close" data-coreui-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus "<strong><?= esc($item['title']) ?></strong>"?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <a href="/admin/news/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="quick-edit-row d-none" id="qe-<?= $item['id'] ?>">
                    <td colspan="7" class="p-0">
                        <div class="p-3 bg-light border-bottom">
                            <form class="quick-edit-form">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Judul</label>
                                        <input type="text" class="form-control form-control-sm" name="title" value="<?= esc($item['title']) ?>" placeholder="Judul" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Status</label>
                                        <select class="form-select form-select-sm" name="status">
                                            <option value="draft" <?= $item['status'] === 'draft' ? 'selected' : '' ?>>Draf</option>
                                            <option value="published" <?= $item['status'] === 'published' ? 'selected' : '' ?>>Terbit</option>
                                            <option value="archived" <?= $item['status'] === 'archived' ? 'selected' : '' ?>>Arsip</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Kategori</label>
                                        <input type="text" class="form-control form-control-sm" name="category" value="<?= esc($item['category'] ?? '') ?>" placeholder="Kategori">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small mb-1">Penulis</label>
                                        <input type="text" class="form-control form-control-sm" name="author" value="<?= esc($item['author'] ?? '') ?>" placeholder="Penulis">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center gap-2 pt-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="is_featured" value="1" id="qe-featured-<?= $item['id'] ?>" <?= $item['is_featured'] ? 'checked' : '' ?>>
                                                <label class="form-check-label small" for="qe-featured-<?= $item['id'] ?>">Featured</label>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary ms-auto">
                                                <i class="fas fa-check me-1"></i>Simpan
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary cancel-qe">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="7" class="text-center py-5 text-body-tertiary">Tidak ada berita ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center py-3 border-top">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<style>
.quick-edit-row td {
    border-bottom: 2px solid var(--cui-primary, #0d6efd) !important;
}
.quick-edit-form .form-label {
    color: var(--cui-secondary-color);
}
</style>
<script>
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.quick-edit-btn');
    if (btn) {
        e.preventDefault();
        var id = btn.getAttribute('data-id');
        var dataRow = document.querySelector('.news-data-row[data-id="' + id + '"]');
        var qeRow = document.getElementById('qe-' + id);
        if (dataRow) dataRow.classList.add('d-none');
        if (qeRow) qeRow.classList.remove('d-none');
        return;
    }

    var cancelBtn = e.target.closest('.cancel-qe');
    if (cancelBtn) {
        var qeRow = cancelBtn.closest('.quick-edit-row');
        if (qeRow) {
            var id = qeRow.id.replace('qe-', '');
            var dataRow = document.querySelector('.news-data-row[data-id="' + id + '"]');
            qeRow.classList.add('d-none');
            if (dataRow) dataRow.classList.remove('d-none');
        }
        return;
    }
});

document.addEventListener('submit', function(e) {
    var form = e.target.closest('.quick-edit-form');
    if (!form) return;
    e.preventDefault();

    var qeRow = form.closest('.quick-edit-row');
    var id = qeRow.id.replace('qe-', '');
    var submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

    var formData = new FormData(form);

    fetch('/admin/news/quick-update/' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrfHash);
            var item = data.item;
            var dataRow = document.querySelector('.news-data-row[data-id="' + id + '"]');

            if (dataRow) {
                dataRow.querySelector('.news-title').textContent = item.title;
                dataRow.querySelector('.news-category').textContent = item.category || '-';
                dataRow.querySelector('.news-author').textContent = item.author || '-';

                var statusMap = { published: 'Terbit', draft: 'Draf', archived: 'Arsip' };
                var statusClassMap = { published: 'bg-success', draft: 'bg-secondary', archived: 'bg-warning' };
                var badge = dataRow.querySelector('.news-badge');
                if (badge) {
                    badge.textContent = statusMap[item.status] || item.status;
                    badge.className = 'badge ' + (statusClassMap[item.status] || 'bg-secondary') + ' news-badge';
                }

                var dateCell = dataRow.querySelector('.news-date');
                if (dateCell) {
                    dateCell.textContent = item.published_at ? new Date(item.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
                    if (dateCell.textContent === 'Invalid Date' || dateCell.textContent === 'Invalid Date') {
                        dateCell.textContent = item.published_at ? item.published_at.substring(0, 10) : '-';
                    }
                }
            }

            form.querySelector('[name="title"]').value = item.title;
            form.querySelector('[name="category"]').value = item.category || '';
            form.querySelector('[name="author"]').value = item.author || '';
            form.querySelector('[name="status"]').value = item.status;
            form.querySelector('[name="is_featured"]').checked = item.is_featured == 1;

            qeRow.classList.add('d-none');
            if (dataRow) dataRow.classList.remove('d-none');
        } else {
            alert(data.error || 'Gagal menyimpan');
        }
    })
    .catch(function() {
        alert('Terjadi kesalahan. Silakan coba lagi.');
    })
    .finally(function() {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Simpan';
    });
});
</script>
<?= $this->endSection() ?>
