<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Jadwal SPMB Online</h1>
        <p class="text-muted mb-0">Atur tanggal mulai dan berakhirnya pendaftaran pada website sekolah.</p>
    </div>

    <?php if (! empty($message)) : ?><div class="alert alert-success" role="status"><?= esc($message); ?></div><?php endif; ?>
    <?php if (! empty($error)) : ?><div class="alert alert-danger" role="alert"><?= esc($error); ?></div><?php endif; ?>

    <?php if ($schedule['is_open']) : ?>
        <div class="alert alert-success" role="status">
            <?php if ($schedule['is_configured']) : ?>
                Pendaftaran sedang dibuka sampai <?= esc(date('d/m/Y', strtotime($schedule['end_date']))); ?>.
            <?php else : ?>
                Jadwal belum dibatasi. Pendaftaran SPMB dapat dibuka sampai tanggal mulai dan akhir diatur.
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="alert alert-warning" role="status"><?= esc($schedule['message']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2 class="h5 card-title mb-0">Periode Pendaftaran</h2></div>
        <form action="<?= site_url('website-settings/spmb-schedule'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="spmb-start-date" class="form-label">Tanggal mulai</label>
                        <input id="spmb-start-date" name="spmb_start_date" type="date" class="form-control" value="<?= esc(old('spmb_start_date', $settings['spmb_start_date'])); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="spmb-end-date" class="form-label">Tanggal penutupan</label>
                        <input id="spmb-end-date" name="spmb_end_date" type="date" class="form-control" value="<?= esc(old('spmb_end_date', $settings['spmb_end_date'])); ?>">
                    </div>
                </div>
                <div class="form-text">
                    Tanggal mulai dan tanggal penutupan dihitung inklusif sampai pukul 23.59 WIB. Kosongkan kedua tanggal untuk menonaktifkan pembatasan jadwal. Jika tanggal sudah lewat, formulir ditutup dan pengiriman data ditolak.
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>
