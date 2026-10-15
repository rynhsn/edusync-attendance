<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Ekstrakurikuler</title>
    <style>
        @page { margin: 14mm; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        h1 { color: #173b73; font-size: 18px; margin: 0 0 4px; }
        h2 { color: #1e3a5f; font-size: 12px; margin: 18px 0 7px; }
        .subtitle { color: #64748b; margin: 0 0 14px; }
        .identity { background: #eff6ff; border: 1px solid #bfdbfe; margin-bottom: 14px; padding: 9px 11px; }
        .identity-item { display: inline-block; margin-right: 26px; }
        .label { color: #64748b; font-size: 8px; }
        .value { color: #1e3a5f; font-weight: bold; }
        .summary { border-collapse: collapse; margin-bottom: 15px; width: 100%; }
        .summary th, .summary td { border: 1px solid #d7e0ec; padding: 7px; text-align: center; }
        .summary th { background: #1e3a5f; color: #fff; font-size: 9px; }
        .summary td { font-size: 11px; font-weight: bold; }
        table.records { border-collapse: collapse; width: 100%; }
        .records th, .records td { border: 1px solid #d7e0ec; padding: 6px; text-align: left; vertical-align: top; }
        .records th { background: #1e3a5f; color: #fff; font-size: 8px; }
        .records tbody tr:nth-child(even) { background: #f3f7fc; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .center { text-align: center !important; }
        .empty { color: #64748b; padding: 14px !important; text-align: center !important; }
    </style>
</head>
<body>
    <h1>Laporan Absensi Ekstrakurikuler</h1>
    <p class="subtitle">Rekap kehadiran ekstrakurikuler per siswa dan per bulan.</p>

    <div class="identity">
        <span class="identity-item">
            <span class="label">Nama Siswa<br></span>
            <span class="value"><?= esc($student['nama_siswa']); ?></span>
        </span>
        <span class="identity-item">
            <span class="label">NIS<br></span>
            <span class="value"><?= esc($student['nis'] ?: '-'); ?></span>
        </span>
        <span class="identity-item">
            <span class="label">Kelas<br></span>
            <span class="value"><?= esc($student['kelas_nama'] ?: '-'); ?></span>
        </span>
        <span class="identity-item">
            <span class="label">Bulan<br></span>
            <span class="value"><?= esc($monthLabel); ?></span>
        </span>
    </div>

    <h2>Rekap Status Kehadiran</h2>
    <table class="summary">
        <thead>
            <tr>
                <?php foreach ($statuses as $label) : ?>
                    <th><?= esc($label); ?></th>
                <?php endforeach; ?>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php foreach ($summary as $count) : ?>
                    <td><?= (int) $count; ?></td>
                <?php endforeach; ?>
                <td><?= array_sum($summary); ?></td>
            </tr>
        </tbody>
    </table>

    <h2>Riwayat Kehadiran</h2>
    <table class="records">
        <thead>
            <tr><th class="center">No.</th><th>Tanggal</th><th>Ekstrakurikuler</th><th>Status</th><th>Keterangan</th></tr>
        </thead>
        <tbody>
            <?php foreach ($records as $index => $record) : ?>
                <tr>
                    <td class="center"><?= $index + 1; ?></td>
                    <td><?= esc($record['tanggal']); ?></td>
                    <td><?= esc($record['nama_kegiatan']); ?></td>
                    <td><?= esc($statuses[$record['status']] ?? $record['status']); ?></td>
                    <td><?= esc($record['keterangan'] ?: '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($records === []) : ?>
                <tr><td class="empty" colspan="5">Tidak ada data absensi ekstrakurikuler untuk siswa pada bulan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
