<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">Pengaturan Waktu</h1>
        <p class="text-muted mb-0">Atur jam masuk dan pulang untuk setiap kelas dan hari sekolah.</p>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div>
                <h2 class="h5 card-title mb-1">Jadwal Waktu per Kelas</h2>
                <p class="text-muted small mb-0">Perubahan disimpan saat Anda meninggalkan kolom jam.</p>
            </div>
            <span id="time-setting-status" class="small text-muted" data-csrf-cookie="<?= esc(config('Security')->cookieName); ?>" data-can-manage="<?= \App\Libraries\ActionAccess::can('timesetting.manage') ? '1' : '0'; ?>" role="status" aria-live="polite"></span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap mb-0" id="time-setting-table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Kelas</th>
                        <?php foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day) : ?>
                            <th scope="col" class="text-center"><?= esc($day); ?><br><small class="text-muted">Masuk — Pulang</small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Memuat pengaturan waktu...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer small text-muted">
            Jam masuk ditandai hijau dan jam pulang ditandai merah. Format waktu menggunakan 24 jam.
        </div>
    </div>

    <form class="d-none" aria-hidden="true"><?= csrf_field(); ?></form>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="<?= base_url('assets/vendors/libs/jquery/jquery.js'); ?>"></script>
<script src="<?= base_url('assets/main/tables-konfigurasi-waktu.js'); ?>"></script>
<?= $this->endSection(); ?>
