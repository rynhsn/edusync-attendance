<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Konten Website: <?= esc($config['label']); ?></h1>
            <p class="text-muted mb-0">Kelola konten yang ditampilkan pada Website Sekolah.</p>
        </div>
        <a href="<?= site_url('news'); ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Website
        </a>
    </div>

    <nav class="nav nav-pills flex-wrap gap-2 mb-4" aria-label="Bagian konten website">
        <?php foreach ($sections as $key => $section) : ?>
            <a class="nav-link <?= $sectionKey === $key ? 'active' : ''; ?>" href="<?= site_url('website-content/' . $key); ?>">
                <?= esc($section['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header fw-semibold"><?= empty($item) ? 'Tambah ' : 'Edit '; ?><?= esc($config['label']); ?></div>
        <div class="card-body">
            <form action="<?= site_url('website-content/' . $sectionKey . '/save'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= esc($item['id'] ?? ''); ?>">
                <div class="row g-3">
                    <?php foreach ($config['fields'] as $key => $field) : ?>
                        <?php
                        $value = old($key, $item[$key] ?? ($field['type'] === 'color' ? '#14b8a6' : ''));
                        $width = in_array($field['type'], ['textarea'], true) ? 'col-12' : 'col-md-6';
                        ?>
                        <div class="<?= $width; ?>">
                            <?php if ($field['type'] === 'checkbox') : ?>
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="<?= esc($key); ?>" name="<?= esc($key); ?>" value="1" <?= (string) old($key, $item[$key] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?= esc($key); ?>"><?= esc($field['label']); ?></label>
                                </div>
                            <?php else : ?>
                                <label class="form-label" for="<?= esc($key); ?>"><?= esc($field['label']); ?></label>
                                <?php if ($field['type'] === 'textarea') : ?>
                                    <textarea class="form-control" id="<?= esc($key); ?>" name="<?= esc($key); ?>" rows="3" <?= ($field['required'] ?? false) ? 'required' : ''; ?>><?= esc($value); ?></textarea>
                                <?php elseif ($field['type'] === 'select') : ?>
                                    <select class="form-select" id="<?= esc($key); ?>" name="<?= esc($key); ?>">
                                        <?php foreach ($field['options'] as $option => $label) : ?>
                                            <option value="<?= esc($option); ?>" <?= (string) $value === (string) $option ? 'selected' : ''; ?>><?= esc($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input class="form-control" type="<?= esc($field['type']); ?>" id="<?= esc($key); ?>" name="<?= esc($key); ?>" value="<?= esc($value); ?>" <?= ($field['required'] ?? false) ? 'required' : ''; ?> <?= isset($field['max_length']) ? 'maxlength="' . (int) $field['max_length'] . '"' : ''; ?> <?= $field['type'] === 'number' ? 'min="1900" max="2200"' : ''; ?>>
                                <?php endif; ?>
                                <?php if (! empty($field['hint'])) : ?><div class="form-text"><?= esc($field['hint']); ?></div><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php if (isset($config['upload'])) : ?>
                        <?php $uploadColumn = $config['upload']['column']; ?>
                        <div class="col-md-6">
                            <label class="form-label" for="upload"><?= $config['upload']['kind'] === 'pdf' ? 'File PDF' : 'Foto'; ?><?= ($config['upload']['required'] ?? false) && empty($item) ? ' *' : ''; ?></label>
                            <input class="form-control" type="file" id="upload" name="upload" accept="<?= $config['upload']['kind'] === 'pdf' ? 'application/pdf,.pdf' : 'image/jpeg,image/png,image/webp'; ?>" <?= ($config['upload']['required'] ?? false) && empty($item) ? 'required' : ''; ?>>
                            <div class="form-text">Maksimal <?= $config['upload']['kind'] === 'pdf' ? '10 MB, PDF' : '5 MB, JPG/PNG/WebP'; ?>. <?= empty($item[$uploadColumn]) ? '' : 'Biarkan kosong untuk mempertahankan file saat ini.'; ?></div>
                            <?php if (! empty($item[$uploadColumn])) : ?>
                                <div class="mt-2 small">
                                    File saat ini:
                                    <?php if ($config['upload']['kind'] === 'pdf') : ?>
                                        <a href="<?= site_url('news/downloads/file/' . (int) $item['id']); ?>" target="_blank" rel="noopener"><?= esc(basename($item[$uploadColumn])); ?></a>
                                    <?php else : ?>
                                        <a href="<?= base_url(ltrim($item[$uploadColumn], '/')); ?>" target="_blank" rel="noopener"><?= esc(basename($item[$uploadColumn])); ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-3">
                        <label class="form-label" for="sort_order">Urutan</label>
                        <input class="form-control" type="number" min="0" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $item['sort_order'] ?? 0)); ?>">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="show" name="show" value="1" <?= (string) old('show', $item['show'] ?? '1') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="show">Tampilkan di website</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <?php if (\App\Libraries\ActionAccess::can('website-content.manage')) : ?>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <?php endif; ?>
                        <?php if (! empty($item)) : ?><a class="btn btn-outline-secondary" href="<?= site_url('website-content/' . $sectionKey); ?>">Batal edit</a><?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-semibold"><?= esc($config['label']); ?> yang tersimpan</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr>
                    <?php foreach ($config['columns'] as $column) : ?><th><?= esc($config['fields'][$column]['label'] ?? match ($column) {
                        'event_date' => 'Tanggal',
                        'is_featured' => 'Unggulan',
                        'file_size' => 'Ukuran',
                        default => ucfirst(str_replace('_', ' ', $column)),
                    }); ?></th><?php endforeach; ?>
                    <th>Status</th><th class="text-end">Aksi</th>
                </tr></thead>
                <tbody>
                    <?php if ($items === []) : ?>
                        <tr><td colspan="<?= count($config['columns']) + 2; ?>" class="text-center text-muted py-5">Belum ada data. Isi formulir di atas untuk menambahkannya.</td></tr>
                    <?php else : ?>
                        <?php foreach ($items as $record) : ?>
                            <tr>
                                <?php foreach ($config['columns'] as $column) : ?>
                                    <td class="text-break">
                                        <?php if ($column === 'is_featured') : ?>
                                            <?= (int) $record[$column] === 1 ? 'Ya' : 'Tidak'; ?>
                                        <?php elseif ($column === 'description' || $column === 'quote' || $column === 'answer') : ?>
                                            <?= esc(mb_strimwidth((string) ($record[$column] ?? ''), 0, 90, '…')); ?>
                                        <?php else : ?>
                                            <?= esc($record[$column] ?? '-'); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td><span class="badge <?= (int) $record['show'] === 1 ? 'text-bg-success' : 'text-bg-secondary'; ?>"><?= (int) $record['show'] === 1 ? 'Tampil' : 'Disembunyikan'; ?></span></td>
                                <td class="text-end text-nowrap">
                                    <?php if (\App\Libraries\ActionAccess::can('website-content.manage')) : ?>
                                        <a class="btn btn-sm btn-outline-primary" href="<?= site_url('website-content/' . $sectionKey . '?edit=' . (int) $record['id']); ?>">Edit</a>
                                    <?php endif; ?>
                                    <?php if (\App\Libraries\ActionAccess::can('website-content.delete')) : ?>
                                    <form class="d-inline" method="post" action="<?= site_url('website-content/' . $sectionKey . '/delete/' . (int) $record['id']); ?>" onsubmit="return confirm('Hapus konten ini?')">
                                        <?= csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
