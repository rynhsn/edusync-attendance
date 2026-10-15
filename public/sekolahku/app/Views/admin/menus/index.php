<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Menu Navigasi</h4>
        <p class="text-body-tertiary small mb-0">Atur menu navigasi website — seret baris untuk mengubah urutan dan kedudukan (induk/anak)</p>
    </div>
    <a href="/admin/menus/new" class="btn btn-primary rounded-pill px-3">
        <i class="fas fa-plus me-1"></i>Tambah Menu
    </a>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-crud table-hover align-middle mb-0" id="menus-table">
                <thead>
                    <tr>
                        <th style="width:40px"><i class="fas fa-grip-vertical text-body-tertiary"></i></th>
                        <th style="width:60px">#</th>
                        <th>Judul</th>
                        <th style="width:200px">URL</th>
                        <th style="width:80px">Target</th>
                        <th style="width:70px">Tipe</th>
                        <th style="width:90px">Status</th>
                        <th style="width:60px">Urutan</th>
                        <th style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $num = 1; ?>
                    <?php foreach ($allItems as $item): ?>
                    <?php
                    $isChild   = !empty($item['parent_id']);
                    $childClass = $isChild ? ' table-row-child' : '';
                    $isSystem  = $item['type'] !== 'custom';
                    $typeLabels = ['custom' => 'Custom', 'system' => 'System', 'page' => 'Halaman'];
                    $typeBadge  = $typeLabels[$item['type']] ?? $item['type'];
                    $typeColor  = $item['type'] === 'system' ? 'secondary' : ($item['type'] === 'page' ? 'info' : 'light text-body');
                    ?>
                    <tr data-id="<?= $item['id'] ?>" data-parent-id="<?= $item['parent_id'] ?? '' ?>" draggable="true" class="<?= $childClass ?>">
                        <td class="drag-handle" style="cursor:grab"><i class="fas fa-grip-vertical text-body-tertiary"></i></td>
                        <td><span class="badge bg-body-tertiary text-body-tertiary fw-normal"><?= $num++ ?></span></td>
                        <td class="fw-medium">
                            <?php if ($isChild): ?><span class="child-indent"><i class="fas fa-level-up-alt fa-rotate-90 me-2 text-body-tertiary small"></i></span><?php endif; ?>
                            <?= esc($item['title']) ?>
                            <?php if ($item['type'] === 'page' && !empty($item['page_id']) && isset($pageTitles[$item['page_id']])): ?>
                            <span class="badge bg-info-subtle text-info-emphasis ms-1 fw-normal" style="font-size:0.65rem"><?= esc($pageTitles[$item['page_id']]) ?></span>
                            <?php endif; ?>
                            <?php if ($item['icon']): ?><i class="<?= esc($item['icon']) ?> ms-1 text-body-tertiary"></i><?php endif; ?>
                        </td>
                        <td class="small"><code>/<?= esc($item['url']) ?></code></td>
                        <td class="small"><?= $item['target'] === '_blank' ? '<i class="fas fa-external-link-alt me-1"></i>Blank' : 'Same' ?></td>
                        <td><span class="badge bg-<?= $typeColor ?>"><?= $typeBadge ?></span></td>
                        <td>
                            <?php if ($item['status']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-light text-body-tertiary border"><?= $item['sort_order'] ?? 0 ?></span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if ($isSystem): ?>
                                <span class="btn btn-sm btn-outline-secondary border-0 disabled" title="Menu bawaan tidak dapat diedit">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <?php else: ?>
                                <a href="/admin/menus/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger border-0" title="Hapus" data-coreui-toggle="modal" data-coreui-target="#deleteModal<?= $item['id'] ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php if (!$isSystem): ?>
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
                                            <a href="/admin/menus/delete/<?= $item['id'] ?>" class="btn btn-sm btn-danger">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($allItems)): ?>
                    <tr><td colspan="9" class="text-center py-5 text-body-tertiary">Tidak ada menu ditemukan. <a href="/admin/menus/new">Tambah menu</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header bg-transparent py-3">
        <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Keterangan</h6>
    </div>
    <div class="card-body small">
        <p class="mb-1">
            <span class="badge bg-secondary">System</span> — menu bawaan yang tidak dapat diedit atau dihapus (hanya posisi yang bisa diubah).
        </p>
        <p class="mb-1">
            <span class="badge bg-info">Halaman</span> — menu otomatis dari halaman statis, judul & URL disinkronkan otomatis.
        </p>
        <p class="mb-1">
            <span class="badge bg-light text-body border">Custom</span> — menu buatan manual yang dapat diedit dan dihapus.
        </p>
        <p class="mb-1">Menu dengan <code>section_key</code> akan otomatis disembunyikan jika section terkait dinonaktifkan di Pengaturan &gt; Section Settings.</p>
        <p class="mb-1">Section Key tersedia: <code>profile</code>, <code>programs</code>, <code>extracurriculars</code>, <code>teachers</code>, <code>achievements</code>, <code>testimonials</code>, <code>news</code>, <code>events</code>, <code>gallery</code>, <code>faq</code>, <code>downloads</code>, <code>contact</code>.</p>
        <p class="mb-0 text-warning-emphasis bg-warning bg-opacity-10 rounded p-2 mt-2">
            <i class="fas fa-clock me-1"></i><strong>Cache menu: ~5 menit.</strong> Perubahan menu baru akan tercermin di halaman publik setelah 5 menit. Untuk melihat perubahan segera, hapus folder <code>writable/cache/</code> atau tambahkan <code>?nocache</code> di URL publik.
        </p>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
#menus-table tbody tr.dragging { opacity:.35; }
#menus-table tbody tr.insert-indicator { border-top:2px solid var(--bs-primary); }
#menus-table tbody tr.insert-indicator td { padding:0!important; height:0!important; }
</style>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tbody = document.querySelector('#menus-table tbody');
  if (!tbody) return;

  let dragSrc = null;
  let dragChildren = [];
  let insertAfterRow = null; // the row after which the indicator is shown

  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
  }

  function findChildren(row) {
    const id = row.dataset.id;
    return Array.from(tbody.querySelectorAll('tr')).filter(function(r) {
      return r.dataset.parentId === id;
    });
  }

  function removeInsertIndicator() {
    tbody.querySelectorAll('.insert-indicator').forEach(function(el) { el.remove(); });
    insertAfterRow = null;
  }

  function showInsertIndicator(targetRow, position) {
    removeInsertIndicator();
    var indicator = document.createElement('tr');
    indicator.className = 'insert-indicator';
    indicator.innerHTML = '<td colspan="9"></td>';
    if (position === 'before') {
      targetRow.before(indicator);
    } else {
      targetRow.after(indicator);
    }
    insertAfterRow = position === 'after' ? targetRow : targetRow.previousElementSibling;
  }

  // --- Drag events ---

  tbody.addEventListener('dragstart', function(e) {
    const tr = e.target.closest('tr');
    if (!tr) return;
    if (tr.classList.contains('insert-indicator')) { e.preventDefault(); return; }

    dragSrc = tr;
    dragSrc.classList.add('dragging');
    dragChildren = findChildren(tr);
    dragChildren.forEach(function(child) { child.classList.add('dragging'); });

    e.dataTransfer.effectAllowed = 'move';
    // Make Firefox happy
    e.dataTransfer.setData('text/plain', tr.dataset.id);
  });

  tbody.addEventListener('dragend', function(e) {
    removeInsertIndicator();
    if (dragSrc) {
      dragSrc.classList.remove('dragging', 'table-active');
      dragChildren.forEach(function(child) { child.classList.remove('dragging', 'table-active'); });
    }
    dragSrc = null;
    dragChildren = [];
  });

  tbody.addEventListener('dragover', function(e) {
    e.preventDefault();
    const tr = e.target.closest('tr');
    if (!tr || !dragSrc || tr === dragSrc || tr.classList.contains('insert-indicator')) return;
    if (dragChildren.includes(tr)) return; // can't drop on own child

    // Determine position based on mouse Y relative to the row
    var rect = tr.getBoundingClientRect();
    var midY = rect.top + rect.height / 2;
    var position = e.clientY < midY ? 'before' : 'after';

    showInsertIndicator(tr, position);
  });

  tbody.addEventListener('dragleave', function(e) {
  });

  // --- Drop ---

  tbody.addEventListener('drop', function(e) {
    e.preventDefault();
    if (!dragSrc) return;

    // Which row was the indicator after?
    var afterRow = insertAfterRow;
    var indicator = tbody.querySelector('.insert-indicator');
    removeInsertIndicator();

    if (!afterRow && indicator) {
      // Dropped before the first row
      afterRow = null;
    } else if (!afterRow) {
      // Fallback: use the target row under cursor
      var tr = e.target.closest('tr');
      if (!tr || tr === dragSrc || dragChildren.includes(tr)) {
        dragSrc.classList.remove('dragging');
        return;
      }
      var rect = tr.getBoundingClientRect();
      var midY = rect.top + rect.height / 2;
      afterRow = e.clientY < midY ? tr.previousElementSibling : tr;
    }

    // Check if afterRow is null (insert at beginning) or a valid row
    // Move the dragged group
    var group = [dragSrc, ...dragChildren];

    // Clean up drag state
    dragSrc.classList.remove('dragging');
    dragChildren.forEach(function(child) { child.classList.remove('dragging'); });

    // If drop position is after dragSrc itself (or within dragged group), no-op
    if (afterRow && group.includes(afterRow)) {
      dragSrc = null;
      dragChildren = [];
      return;
    }

    // Move DOM nodes
    group.forEach(function(row) { row.remove(); });

    // Re-query afterRow since DOM changed
    // If afterRow was removed (it was after dragSrc which was before the drop zone), find its position
    if (afterRow === null) {
      // Insert at the beginning
      tbody.prepend(...group);
    } else {
      // Find afterRow in the current DOM
      var currentAfter = tbody.querySelector('tr[data-id="' + afterRow.dataset.id + '"]');
      if (currentAfter) {
        currentAfter.after(...group);
      } else {
        // Fallback: append to end
        tbody.append(...group);
      }
    }

    // Update parent_id of dragSrc based on position context
    var newParentId = determineNewParentId(dragSrc, tbody);
    dragSrc.dataset.parentId = newParentId;

    // Save
    saveOrder(tbody);

    dragSrc = null;
    dragChildren = [];
  });

  function determineNewParentId(row, tbody) {
    var prev = row.previousElementSibling;
    var next = row.nextElementSibling;

    // Dropped between a parent and its first child → become child of that parent
    if (prev && next && next.dataset.parentId === prev.dataset.id) {
      return prev.dataset.id;
    }

    // Otherwise: become sibling of the row before
    if (prev && !prev.classList.contains('insert-indicator')) {
      return prev.dataset.parentId;
    }
    // Or sibling of the row after
    if (next && !next.classList.contains('insert-indicator')) {
      return next.dataset.parentId;
    }
    // First and only row → top-level
    return '';
  }

  function saveOrder(tbody) {
    var rows = Array.from(tbody.querySelectorAll('tr'));
    var items = rows.map(function(row, idx) {
      return {
        id: row.dataset.id,
        parent_id: row.dataset.parentId || null,
        sort_order: idx
      };
    });

    fetch('/admin/menus/reorder', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({items: items})
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        if (data.csrfHash) {
          document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrfHash);
        }
        rows.forEach(function(row, idx) {
          var numBadge = row.querySelector('td:nth-child(2) .badge');
          if (numBadge) numBadge.textContent = idx + 1;
          var sortBadge = row.querySelector('td:nth-child(7) .badge');
          if (sortBadge) sortBadge.textContent = idx;

          // Update visual child indent
          var isChild = row.dataset.parentId && row.dataset.parentId !== '';
          var indentSpan = row.querySelector('td:nth-child(3) .child-indent');
          if (isChild) {
            row.classList.add('table-row-child');
            if (!indentSpan) {
              var titleTd = row.querySelector('td:nth-child(3)');
              var icon = document.createElement('span');
              icon.className = 'child-indent';
              icon.innerHTML = '<i class="fas fa-level-up-alt fa-rotate-90 me-2 text-body-tertiary small"></i>';
              titleTd.prepend(icon);
            }
          } else {
            row.classList.remove('table-row-child');
            if (indentSpan) indentSpan.remove();
          }
        });
      }
    });
  }
});
</script>
<?= $this->endSection() ?>
