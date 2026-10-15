<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Prestasi</h4>
        <p class="text-body-tertiary small mb-0">Kelola prestasi dan penghargaan siswa</p>
    </div>
    <a href="/admin/achievements/new" class="btn btn-primary rounded-pill px-3">
        <i class="fas fa-plus me-1"></i>Tambah Prestasi
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="table-responsive">
        <table class="table table-crud table-hover align-middle mb-0" id="achievements-table">
            <thead>
                <tr>
                    <th style="width:40px"><i class="fas fa-grip-vertical text-body-tertiary"></i></th>
                    <th style="width:60px">#</th>
                    <th>Nama Siswa</th>
                    <th>Prestasi</th>
                    <th>Tingkat</th>
                    <th>Tahun</th>
                    <th>Medali</th>
                    <th style="width:60px">Urutan</th>
                    <th style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr data-id="<?= $item['id'] ?>" draggable="true">
                    <td class="drag-handle" style="cursor:grab"><i class="fas fa-grip-vertical text-body-tertiary"></i></td>
                    <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $i + 1 + (($pager->getCurrentPage() - 1) * 10) ?></span></td>
                    <td class="fw-medium"><?= esc($item['student_name']) ?></td>
                    <td class="text-body-secondary"><?= esc($item['achievement']) ?></td>
                    <td class="text-body-secondary small"><?= esc($item['level'] ?? '-') ?></td>
                    <td class="text-body-secondary small"><?= $item['year'] ?? '-' ?></td>
                    <td>
                        <?php if (($item['medal'] ?? '') === 'gold'): ?>
                            <span class="badge" style="background:#ffc107;color:#000">Emas</span>
                        <?php elseif (($item['medal'] ?? '') === 'silver'): ?>
                            <span class="badge" style="background:#adb5bd;color:#000">Perak</span>
                        <?php elseif (($item['medal'] ?? '') === 'bronze'): ?>
                            <span class="badge" style="background:#cd7f32;color:#fff">Perunggu</span>
                        <?php else: ?>
                            <?= esc($item['medal'] ?? '-') ?>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-light text-body-tertiary border"><?= $item['sort_order'] ?? 0 ?></span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="/admin/achievements/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
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
                                        Yakin ingin menghapus "<strong><?= esc($item['student_name']) ?></strong>"?
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Batal</button>
                                        <a href="/admin/achievements/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                <tr><td colspan="9" class="text-center py-5 text-body-tertiary">Tidak ada prestasi ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pager->links()): ?>
    <div class="d-flex justify-content-center py-3 border-top">
        <?= $pager->links() ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.querySelector('#achievements-table tbody');
  if (!tbody) return;

  let dragSrc = null;

  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  tbody.addEventListener('dragstart', function(e) {
    const tr = e.target.closest('tr');
    if (!tr) return;
    dragSrc = tr;
    tr.classList.add('table-active');
    e.dataTransfer.effectAllowed = 'move';
  });

  tbody.addEventListener('dragend', function(e) {
    const tr = e.target.closest('tr');
    if (tr) tr.classList.remove('table-active');
  });

  tbody.addEventListener('dragover', function(e) {
    e.preventDefault();
    const tr = e.target.closest('tr');
    if (!tr || tr === dragSrc) return;
    tr.classList.add('table-info');
  });

  tbody.addEventListener('dragleave', function(e) {
    const tr = e.target.closest('tr');
    if (tr) tr.classList.remove('table-info');
  });

  tbody.addEventListener('drop', function(e) {
    e.preventDefault();
    const tr = e.target.closest('tr');
    if (!tr || !dragSrc || tr === dragSrc) return;
    tr.classList.remove('table-info');

    const parent = tbody;
    const rows = Array.from(parent.children);
    const fromIdx = rows.indexOf(dragSrc);
    const toIdx = rows.indexOf(tr);
    if (fromIdx < toIdx) {
      tr.after(dragSrc);
    } else {
      tr.before(dragSrc);
    }

    const ids = Array.from(parent.children).map(function(row) {
      return row.dataset.id;
    });

    fetch('/admin/achievements/reorder', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ids: ids})
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        if (data.csrfHash) {
          document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrfHash);
        }
        parent.querySelectorAll('tr').forEach(function(row, idx) {
          var numBadge = row.querySelector('td:nth-child(2) .badge');
          if (numBadge) numBadge.textContent = idx + 1 + (<?= ($pager->getCurrentPage() - 1) * 10 ?>);
          var sortBadge = row.querySelector('td:nth-child(8) .badge');
          if (sortBadge) sortBadge.textContent = idx;
        });
      }
    });
  });
});
</script>
<?= $this->endSection() ?>