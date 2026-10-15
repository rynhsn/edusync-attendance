<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        @page { margin: 12mm 10mm; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.4; }
        .report-header { border-bottom: 2px solid #2563eb; margin-bottom: 14px; padding: 0 0 10px; }
        h1 { color: #173b73; font-size: 20px; margin: 0 0 3px; }
        .subtitle { color: #64748b; font-size: 9px; margin: 0; }
        .meta { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 4px; margin-bottom: 14px; padding: 8px 10px; }
        .meta-item { display: inline-block; margin-right: 18px; }
        .meta-label { color: #64748b; font-size: 8px; }
        .meta-value { color: #1e3a5f; font-weight: bold; }
        .record-count { background: #dbeafe; border-radius: 10px; color: #1d4ed8; font-weight: bold; padding: 3px 8px; }
        table { border-collapse: collapse; table-layout: fixed; width: 100%; }
        th, td { border: 1px solid #d7e0ec; padding: 6px 5px; text-align: left; vertical-align: top; }
        th { background: #1e3a5f; border-color: #1e3a5f; color: #fff; font-size: 8px; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f3f7fc; }
        tbody tr:nth-child(odd) { background: #fff; }
        td { overflow-wrap: break-word; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .col-no { width: 4%; }
        .col-name { width: 15%; }
        .col-class { width: 7%; }
        .col-date { width: 10%; }
        .col-time { width: 11%; }
        .col-attendance { width: 10%; }
        .col-schedule { width: 11%; }
        .col-note { width: 10%; }
        .status { border-radius: 8px; display: inline-block; font-size: 7px; font-weight: bold; margin-top: 2px; padding: 2px 5px; }
        .status-primary { background: #dbeafe; color: #1d4ed8; }
        .status-secondary { background: #e2e8f0; color: #475569; }
        .status-success { background: #dcfce7; color: #15803d; }
        .status-danger { background: #fee2e2; color: #b91c1c; }
        .status-warning { background: #fef3c7; color: #a16207; }
        .status-info { background: #cffafe; color: #0e7490; }
        .status-light { background: #f8fafc; color: #475569; }
        .status-dark { background: #e2e8f0; color: #1e293b; }
    </style>
</head>
<body>
    <?php
    $statusColorMap = [
        'primary' => 'primary',
        'secondary' => 'secondary',
        'success' => 'success',
        'danger' => 'danger',
        'warning' => 'warning',
        'info' => 'info',
        'light' => 'light',
        'dark' => 'dark',
    ];
    ?>
    <div class="report-header">
        <h1>Laporan Absensi</h1>
        <p class="subtitle">Riwayat absensi diurutkan berdasarkan nama siswa.</p>
    </div>
    <div class="meta">
        <span class="meta-item"><span class="meta-label">Nama siswa<br></span><span class="meta-value"><?= esc($studentName !== '' ? $studentName : 'Semua siswa'); ?></span></span>
        <span class="meta-item"><span class="meta-label">Tanggal mulai<br></span><span class="meta-value"><?= esc($dateFrom !== '' ? $dateFrom : 'Semua tanggal'); ?></span></span>
        <span class="meta-item"><span class="meta-label">Tanggal akhir<br></span><span class="meta-value"><?= esc($dateTo !== '' ? $dateTo : 'Semua tanggal'); ?></span></span>
        <span class="meta-item"><span class="meta-label">Jumlah data<br></span><span class="record-count"><?= count($attendance); ?> catatan</span></span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-name">Nama Siswa</th>
                <th class="col-class">Kelas</th>
                <th class="col-date">Tanggal</th>
                <th class="col-time">Jam Masuk</th>
                <th class="col-time">Jam Pulang</th>
                <th class="col-attendance">Kehadiran</th>
                <th class="col-schedule">Jadwal Masuk</th>
                <th class="col-schedule">Jadwal Pulang</th>
                <th class="col-note">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendance as $index => $row) : ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= esc($row['nama_siswa'] ?? 'Siswa tidak ditemukan'); ?></td>
                    <td><?= esc($row['kelas_nama'] ?? '-'); ?></td>
                    <td><?= esc($row['tanggal'] ?? '-'); ?></td>
                    <td>
                        <?= esc($row['waktu_masuk_aktual'] ?: '-'); ?>
                        <?php if (! empty($row['status_masuk'])) : ?>
                            <br><span class="status status-<?= esc($statusColorMap[$row['status_masuk_warna'] ?? 'secondary'] ?? 'secondary'); ?>"><?= esc($row['status_masuk']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= esc($row['waktu_pulang_aktual'] ?: '-'); ?>
                        <?php if (! empty($row['status_pulang'])) : ?>
                            <br><span class="status status-<?= esc($statusColorMap[$row['status_pulang_warna'] ?? 'secondary'] ?? 'secondary'); ?>"><?= esc($row['status_pulang']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (! empty($row['status_kehadiran'])) : ?>
                            <span class="status status-<?= esc($statusColorMap[$row['status_kehadiran_warna'] ?? 'secondary'] ?? 'secondary'); ?>"><?= esc($row['status_kehadiran']); ?></span>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row['waktu_masuk'] ?? '-'); ?></td>
                    <td><?= esc($row['waktu_pulang'] ?? '-'); ?></td>
                    <td><?= esc($row['keterangan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($attendance === []) : ?>
                <tr><td colspan="10" class="center">Tidak ada data absensi untuk filter ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
