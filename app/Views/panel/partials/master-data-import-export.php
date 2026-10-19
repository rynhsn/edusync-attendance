<?php
$importMessage = session()->getFlashdata('masterDataImportMessage');
$importError = session()->getFlashdata('masterDataImportError');
?>
<?php if (! empty($importMessage)) : ?>
    <div class="alert alert-success" role="status"><?= esc($importMessage); ?></div>
<?php endif; ?>
<?php if (! empty($importError)) : ?>
    <div class="alert alert-danger" role="alert"><?= nl2br(esc($importError)); ?></div>
<?php endif; ?>
<?php if ($canImportMasterData || $canExportMasterData) : ?>
    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 card-title mb-0">Import dan Export Excel</h2></div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php if ($canImportMasterData) : ?>
                    <a class="btn btn-outline-primary" href="<?= site_url($masterDataRoute . '/import-template'); ?>">
                        <i class="bi bi-download me-2" aria-hidden="true"></i>Unduh Template Import
                    </a>
                <?php endif; ?>
                <?php if ($canExportMasterData) : ?>
                    <a class="btn btn-outline-success" href="<?= site_url($masterDataRoute . '/export'); ?>">
                        <i class="bi bi-file-earmark-spreadsheet me-2" aria-hidden="true"></i>Export Data
                    </a>
                <?php endif; ?>
            </div>
            <?php if ($canImportMasterData) : ?>
                <form class="d-flex flex-column flex-md-row align-items-md-end gap-2 mt-3" method="post" action="<?= site_url($masterDataRoute . '/import'); ?>" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="flex-grow-1">
                        <label class="form-label" for="masterDataImportFile">File Excel (.xlsx), CSV, atau TSV</label>
                        <input class="form-control" id="masterDataImportFile" name="master_data_file" type="file" accept=".xlsx,.csv,.tsv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                    <button class="btn btn-primary flex-shrink-0" type="submit">
                        <i class="bi bi-upload me-2" aria-hidden="true"></i>Import Data
                    </button>
                </form>
                <p class="form-text mt-2 mb-0">Gunakan template yang sesuai. Baris contoh otomatis dilewati. Maksimal 5 MB dan 3.000 baris per impor.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
