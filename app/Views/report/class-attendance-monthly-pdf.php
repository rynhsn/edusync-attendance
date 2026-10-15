<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Kelas</title>
    <style>
        @page { margin: 12mm 10mm; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 8px; line-height: 1.4; }
        .report-header { border-bottom: 2px solid #2563eb; margin-bottom: 12px; padding-bottom: 8px; }
        h1 { color: #173b73; font-size: 18px; margin: 0 0 3px; }
        .subtitle { color: #64748b; font-size: 8px; margin: 0; }
        .meta { background: #eff6ff; border: 1px solid #bfdbfe; margin-bottom: 10px; padding: 7px 9px; }
        .meta-item { display: inline-block; margin-right: 22px; }
        .meta-label { color: #64748b; font-size: 7px; }
        .meta-value { color: #1e3a5f; font-weight: bold; }
        h2 { color: #1e3a5f; font-size: 11px; margin: 12px 0 6px; }
        table { border-collapse: collapse; table-layout: fixed; width: 100%; }
        th, td { border: 1px solid #d7e0ec; padding: 4px; text-align: left; vertical-align: top; }
        th { background: #1e3a5f; border-color: #1e3a5f; color: #fff; font-size: 7px; }
        tbody tr:nth-child(even) { background: #f3f7fc; }
        tbody tr:nth-child(odd) { background: #fff; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .number, .center { text-align: center; }
        .summary-no { width: 4%; }
        .summary-name { width: 27%; }
        .summary-nis { width: 16%; }
        .summary-count { text-align: center; width: 8%; }
        .summary-total { background: #eff6ff; text-align: center; width: 9%; }
        .detail-date { width: 9%; }
        .detail-name { width: 17%; }
        .detail-nis { width: 10%; }
        .detail-subject { width: 17%; }
        .detail-teacher { width: 14%; }
        .detail-period { width: 12%; }
        .detail-status { width: 10%; }
        .detail-note { width: 11%; }
        .status { border-radius: 7px; display: inline-block; font-size: 7px; font-weight: bold; padding: 2px 5px; }
        .status-hadir { background: #dcfce7; color: #15803d; }
        .status-sakit { background: #fef3c7; color: #a16207; }
        .status-izin { background: #cffafe; color: #0e7490; }
        .status-alpa { background: #fee2e2; color: #b91c1c; }
        .empty { color: #64748b; padding: 10px; text-align: center; }
    </style>
</head>
<body>
    <?php $statusLabels = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa']; ?>
    <div class="report-header">
        <h1>Laporan Absensi Kelas</h1>
        <p class="subtitle">Rekap dan riwayat absensi siswa berdasarkan kelas dan bulan.</p>
    </div>
    <div class="meta">
        <span class="meta-item"><span class="meta-label">Kelas<br></span><span class="meta-value"><?= esc($selectedClass['kelas_nama']); ?></span></span>
        <span class="meta-item"><span class="meta-label">Bulan<br></span><span class="meta-value"><?= esc($monthLabel); ?></span></span>
        <span class="meta-item"><span class="meta-label">Jumlah siswa tercatat<br></span><span class="meta-value"><?= count($students); ?> siswa</span></span>
        <span class="meta-item"><span class="meta-label">Jumlah catatan<br></span><span class="meta-value"><?= count($records); ?> catatan</span></span>
    </div>

    <h2>Rekap Absensi per Siswa</h2>
    <table>
        <thead>
            <tr>
                <th class="summary-no">No.</th>
                <th class="summary-name">Nama Siswa</th>
                <th class="summary-nis">NIS</th>
                <th class="summary-count">Hadir</th>
                <th class="summary-count">Sakit</th>
                <th class="summary-count">Izin</th>
                <th class="summary-count">Alpa</th>
                <th class="summary-total">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $student) : ?>
                <tr>
                    <td class="number"><?= $index + 1; ?></td>
                    <td><?= esc($student['nama_siswa']); ?></td>
                    <td><?= esc($student['nis'] ?? '-'); ?></td>
                    <td class="center"><?= (int) $student['hadir']; ?></td>
                    <td class="center"><?= (int) $student['sakit']; ?></td>
                    <td class="center"><?= (int) $student['izin']; ?></td>
                    <td class="center"><?= (int) $student['alpa']; ?></td>
                    <td class="center"><?= (int) $student['total']; ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($students === []) : ?>
                <tr><td class="empty" colspan="8">Belum ada data absensi untuk kelas dan bulan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Riwayat Absensi</h2>
    <table>
        <thead>
            <tr>
                <th class="detail-date">Tanggal</th>
                <th class="detail-name">Nama Siswa</th>
                <th class="detail-nis">NIS</th>
                <th class="detail-subject">Mapel</th>
                <th class="detail-teacher">Guru</th>
                <th class="detail-period">Jam</th>
                <th class="detail-status">Status</th>
                <th class="detail-note">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record) : ?>
                <tr>
                    <td><?= esc($record['tanggal']); ?></td>
                    <td><?= esc($record['nama_siswa']); ?></td>
                    <td><?= esc($record['nis'] ?? '-'); ?></td>
                    <td><?= esc($record['mapel_nama']); ?></td>
                    <td><?= esc($record['guru_nama']); ?></td>
                    <td><?= esc($record['jam_ke']); ?> (<?= esc(substr($record['jam_mulai'], 0, 5)); ?>–<?= esc(substr($record['jam_selesai'], 0, 5)); ?>)</td>
                    <td><span class="status status-<?= esc($record['status']); ?>"><?= esc($statusLabels[$record['status']] ?? $record['status']); ?></span></td>
                    <td><?= esc($record['keterangan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($records === []) : ?>
                <tr><td class="empty" colspan="8">Belum ada data absensi untuk kelas dan bulan ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
