<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<?php
$editing = null;
foreach ($items as $item) {
    if ((int) $item['id'] === $editId) {
        $editing = $item;
        break;
    }
}
?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Menu Sidebar</h1>
        <p class="text-muted mb-0">Buat menu dan submenu, atur ikon serta urutan, dan pindahkan submenu ke menu utama lain.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>
    <div class="alert alert-info" role="note">
        URL harus berupa route internal yang sudah dibuat. Pengaturan ini hanya mengubah navigasi; izin akses tetap ditentukan oleh route dan role aplikasi.
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0"><?= $editing === null ? 'Tambah Menu atau Submenu' : 'Ubah Menu atau Submenu'; ?></h2>
        </div>
        <div class="card-body">
            <form method="post" action="<?= site_url('menu-settings/save'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc((string) ($editing['id'] ?? '')); ?>">
                <?php
                $selectedParent = $editing === null
                    ? ''
                    : ($editing['parent_id'] !== null
                        ? 'custom:' . $editing['parent_id']
                        : (($editing['parent_key'] ?? null) !== null ? 'section:' . $editing['parent_key'] : ''));
                ?>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label" for="menu-label">Nama menu</label>
                        <input class="form-control" id="menu-label" name="label" maxlength="80" required value="<?= esc($editing['label'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label" for="menu-parent">Menu utama</label>
                        <select class="form-select" id="menu-parent" name="parent">
                            <option value="">Menu utama baru</option>
                            <optgroup label="Menu utama tambahan">
                                <?php foreach ($menus as $menu) : ?>
                                    <?php if ((int) $menu['id'] === $editId) { continue; } ?>
                                    <option value="custom:<?= esc((string) $menu['id']); ?>" <?= $selectedParent === 'custom:' . $menu['id'] ? 'selected' : ''; ?>>
                                        <?= esc($menu['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Menu utama bawaan">
                                <?php foreach ($sections as $sectionKey => $sectionLabel) : ?>
                                    <option value="section:<?= esc($sectionKey); ?>" <?= $selectedParent === 'section:' . $sectionKey ? 'selected' : ''; ?>>
                                        <?= esc($sectionLabel); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="form-text">Pilih menu utama bawaan atau tambahan untuk membuat/memindahkan submenu, atau biarkan kosong untuk membuat menu utama baru.</div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label" for="menu-url">URL internal</label>
                        <input class="form-control" id="menu-url" name="url" maxlength="255" pattern="[A-Za-z0-9][A-Za-z0-9/_-]*" placeholder="Contoh: fitur-baru" value="<?= esc($editing['url'] ?? ''); ?>">
                        <div class="form-text">Kosongkan untuk menu utama yang hanya menampung submenu.</div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label" for="menu-icon">Ikon Bootstrap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="menu-icon-preview" class="bi <?= esc($editing['icon'] ?? 'bi-folder'); ?>" aria-hidden="true"></i></span>
                            <input class="form-control" id="menu-icon" name="icon" maxlength="64" pattern="bi-[a-z][a-z0-9-]*" required value="<?= esc($editing['icon'] ?? 'bi-folder'); ?>">
                        </div>
                        <div class="form-text">Isi nama kelas seperti <code>bi-calendar3</code>.</div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <label class="form-label" for="menu-order">Urutan</label>
                        <input class="form-control" id="menu-order" name="sort_order" type="number" min="0" max="9999" required value="<?= esc((string) ($editing['sort_order'] ?? '0')); ?>">
                    </div>
                </div>
                <div class="mt-4 d-flex flex-wrap gap-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Menu</button>
                    <?php if ($editing !== null) : ?>
                        <a class="btn btn-outline-secondary" href="<?= site_url('menu-settings'); ?>">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Menu dan submenu bawaan</h2>
            <p class="text-muted small mb-0 mt-1">Ubah ikon menu yang sudah tersedia di sidebar.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Nama menu</th>
                    <th>Bagian</th>
                    <th style="min-width: 270px;">Ikon</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($builtInItems as $builtInItem) : ?>
                    <tr>
                        <td><i class="bi <?= esc($builtInItem['icon']); ?> me-2" aria-hidden="true"></i><?= esc($builtInItem['label']); ?></td>
                        <td><?= esc($builtInItem['parent']); ?></td>
                        <td>
                            <form class="d-flex gap-2 align-items-center" method="post" action="<?= site_url('menu-settings/icon'); ?>">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="menu_key" value="<?= esc($builtInItem['key']); ?>">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi <?= esc($builtInItem['icon']); ?>" data-icon-preview aria-hidden="true"></i></span>
                                    <input class="form-control" name="icon" maxlength="64" pattern="bi-[a-z][a-z0-9-]*" required value="<?= esc($builtInItem['icon']); ?>" aria-label="Ikon <?= esc($builtInItem['label']); ?>">
                                </div>
                                <button class="btn btn-sm btn-outline-primary text-nowrap" type="submit">Simpan ikon</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="h5 mb-0">Menu tambahan yang dapat diatur</h2>
        </div>
        <?php if ($menus === [] && $sectionItems === []) : ?>
            <div class="card-body text-muted">Belum ada menu tambahan. Menu dan submenu bawaan dapat diubah ikonnya pada bagian di atas.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>URL</th>
                        <th>Ikon</th>
                        <th>Urutan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($menus as $menu) : ?>
                        <tr class="table-light">
                            <td><i class="bi <?= esc($menu['icon']); ?> me-2" aria-hidden="true"></i><strong><?= esc($menu['label']); ?></strong></td>
                            <td><?= $menu['url'] === '' ? '<span class="text-muted">Grup submenu</span>' : esc($menu['url']); ?></td>
                            <td><code><?= esc($menu['icon']); ?></code></td>
                            <td><?= esc((string) $menu['sort_order']); ?></td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('menu-settings?edit=' . $menu['id']); ?>">Ubah</a>
                                <?php if ($menu['children'] === []) : ?>
                                    <form class="d-inline" method="post" action="<?= site_url('menu-settings/delete/' . $menu['id']); ?>" onsubmit="return confirm('Hapus menu ini?');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php foreach ($menu['children'] as $child) : ?>
                            <tr>
                                <td class="ps-5"><i class="bi <?= esc($child['icon']); ?> me-2" aria-hidden="true"></i><?= esc($child['label']); ?></td>
                                <td><?= esc($child['url']); ?></td>
                                <td><code><?= esc($child['icon']); ?></code></td>
                                <td><?= esc((string) $child['sort_order']); ?></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('menu-settings?edit=' . $child['id']); ?>">Ubah / Pindah</a>
                                    <form class="d-inline" method="post" action="<?= site_url('menu-settings/delete/' . $child['id']); ?>" onsubmit="return confirm('Hapus submenu ini?');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php foreach ($sections as $sectionKey => $sectionLabel) : ?>
                        <?php foreach ($sectionItems[$sectionKey] ?? [] as $sectionItem) : ?>
                            <tr>
                                <td><?= esc($sectionItem['label']); ?><br><small class="text-muted">Submenu di <?= esc($sectionLabel); ?></small></td>
                                <td><?= esc($sectionItem['url']); ?></td>
                                <td><code><?= esc($sectionItem['icon']); ?></code></td>
                                <td><?= esc((string) $sectionItem['sort_order']); ?></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('menu-settings?edit=' . $sectionItem['id']); ?>">Ubah / Pindah</a>
                                    <form class="d-inline" method="post" action="<?= site_url('menu-settings/delete/' . $sectionItem['id']); ?>" onsubmit="return confirm('Hapus submenu ini?');">
                                        <?= csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
(() => {
  const input = document.getElementById('menu-icon');
  const preview = document.getElementById('menu-icon-preview');
  const updatePreview = (field, iconPreview) => {
    iconPreview.className = 'bi ' + (/^bi-[a-z][a-z0-9-]*$/.test(field.value) ? field.value : 'bi-question-circle');
  };
  if (input && preview) {
    input.addEventListener('input', () => updatePreview(input, preview));
  }
  document.querySelectorAll('form[action$="/menu-settings/icon"]').forEach((form) => {
    const iconInput = form.querySelector('input[name="icon"]');
    const iconPreview = form.querySelector('[data-icon-preview]');
    if (iconInput && iconPreview) {
      iconInput.addEventListener('input', () => updatePreview(iconInput, iconPreview));
    }
  });
})();
</script>
<?= $this->endSection(); ?>
