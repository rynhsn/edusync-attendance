<!-- Tampilan untuk Status Absensi -->
<?= $this->extend('layouts/template'); ?>
<?= $this->section('content'); ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Absensi /</span> Status</h4>

    <div class="row d-flex align-items-stretch">
        <div class="col-md-6 mb-4 d-flex align-items-stretch">
            <div class="card card-action h-100 w-100">
                <div class="card-header">
                    <div class="card-action-title">Status Absensi</div>
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
                                <th class="w-20">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absenceStatus as $status) : ?>
                                <tr>
                                    <td><span class="badge bg-<?= $status['status_absensi_warna'] ?>"><?= $status['status_absensi'] ?></span></td>
                                    <td><?= $status['status_absensi_keterangan'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-border-bottom-0">
                            <tr>
                                <th class="rounded-start-bottom">Status</th>
                                <th class="rounded-end-bottom">Keterangan</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4 d-flex align-items-stretch">
            <div class="card card-action h-100 w-100">
                <div class="card-header">
                    <div class="card-action-title">Status Kehadiran</div>
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
                                <th class="w-20">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceStatus as $status) : ?>
                                <tr>
                                    <td><span class="badge bg-<?= $status['status_kehadiran_warna'] ?>"><?= $status['status_kehadiran'] ?></span></td>
                                    <td><?= $status['status_kehadiran_keterangan'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-border-bottom-0">
                            <tr>
                                <th class="rounded-start-bottom">Status</th>
                                <th class="rounded-end-bottom">Keterangan</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>