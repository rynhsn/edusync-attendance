<!-- Tampilan untuk Konfigurasi Waktu -->
<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Absensi /</span> Konfigurasi Waktu</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card card-action">
        <div class="card-header">
            <div class="card-action-title">Konfigurasi Waktu</div>
            <div class="card-action-element">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="javascript:void(0);" class="card-expand"><i class="tf-icons ti ti-arrows-maximize ti-sm"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">Senin</th>
                        <th class="text-center">Selasa</th>
                        <th class="text-center">Rabu</th>
                        <th class="text-center">Kamis</th>
                        <th class="text-center">Jumat</th>
                        <th class="text-center">Sabtu</th>
                    </tr>
                </thead>
                <!-- tfoot -->
                <tfoot class="table-border-bottom-0">
                    <tr>
                        <th class="text-center rounded-start-bottom">Kelas</th>
                        <th class="text-center">Senin</th>
                        <th class="text-center">Selasa</th>
                        <th class="text-center">Rabu</th>
                        <th class="text-center">Kamis</th>
                        <th class="text-center">Jumat</th>
                        <th class="text-center rounded-end-bottom">Sabtu</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

</div>

<?= $this->endSection(); ?>