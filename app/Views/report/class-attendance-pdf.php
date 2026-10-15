<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Siswa</title>
    <style>
        @page { margin: 14mm; }
        body { color: #243247; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        .report-header { border-bottom: 2px solid #2563eb; margin-bottom: 16px; padding-bottom: 10px; }
        h1 { color: #173b73; font-size: 20px; margin: 0 0 3px; }
        .subtitle { color: #64748b; font-size: 9px; margin: 0; }
        .session-info { background: #eff6ff; border: 1px solid #bfdbfe; margin-bottom: 14px; padding: 10px 12px; }
        .info-row { margin: 2px 0; }
        .info-label { color: #64748b; display: inline-block; width: 105px; }
        .info-value { color: #1e3a5f; font-weight: bold; }
        .summary { margin: 0 0 14px; width: 100%; }
        .summary td { border: 1px solid #d7e0ec; padding: 7px; text-align: center; width: 20%; }
        .summary-label { color: #64748b; display: block; font-size: 8px; }
        .summary-value { display: block; font-size: 13px; font-weight: bold; }
        .total { background: #eff6ff; color: #1d4ed8; }
        .present { background: #dcfce7; color: #15803d; }
        .sick { background: #fef3c7; color: #a16207; }
        .leave { background: #cffafe; color: #0e7490; }
        .absent { background: #fee2e2; color: #b91c1c; }
        table.students { border-collapse: collapse; width: 100%; }
        .students th, .students td { border: 1px solid #d7e0ec; padding: 7px 8px; text-align: left; vertical-align: top; }
        .students th { background: #1e3a5f; color: #fff; font-size: 9px; }
        .students tbody tr:nth-child(even) { background: #f3f7fc; }
        .students tbody tr:nth-child(odd) { background: #fff; }
        .students thead { display: table-header-group; }
        .students tr { page-break-inside: avoid; }
        .number { text-align: center !important; width: 7%; }
        .nis { width: 18%; }
        .name { width: 34%; }
        .status-cell { width: 18%; }
        .note { width: 23%; }
        .status { border-radius: 8px; display: inline-block; font-size: 8px; font-weight: bold; padding: 3px 7px; }
        .status-hadir { background: #dcfce7; color: #15803d; }
        .status-sakit { background: #fef3c7; color: #a16207; }
        .status-izin { background: #cffafe; color: #0e7490; }
        .status-alpa { background: #fee2e2; color: #b91c1c; }
        .empty { color: #64748b; padding: 16px !important; text-align: center !important; }
    </style>
</head>
<body>
    <?php $statusLabels = ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpa' => 'Alpa']; ?>
    <div class="report-header">
        <h1>Laporan Absensi Siswa</h1>
        <p class="subtitle">Rekap kehadiran siswa untuk satu sesi pembelajaran.</p>
    </div>

    <div class="session-info">
        <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value"><?= esc($session['tanggal']); ?></span></div>
        <div class="info-row"><span class="info-label">Kelas</span><span class="info-value"><?= esc($session['kelas_nama']); ?></span></div>
        <div class="info-row"><span class="info-label">Mata pelajaran</span><span class="info-value"><?= esc($session['mapel_nama']); ?></span></div>
        <div class="info-row"><span class="info-label">Guru</span><span class="info-value"><?= esc($session['guru_nama']); ?></span></div>
        <div class="info-row"><span class="info-label">Jam pelajaran</span><span class="info-value"><?= esc($session['jam_ke']); ?> (<?= esc(substr($session['jam_mulai'], 0, 5)); ?>–<?= esc(substr($session['jam_selesai'], 0, 5)); ?>)</span></div>
        <div class="info-row"><span class="info-label">Tahun ajaran</span><span class="info-value"><?= esc($session['tahun_ajaran_nama']); ?> - Semester <?= esc($session['semester']); ?></span></div>
    </div>

    <table class="summary">
        <tr>
            <td class="total"><span class="summary-label">Jumlah siswa</span><span class="summary-value"><?= count($students); ?></span></td>
            <td class="present"><span class="summary-label">Hadir</span><span class="summary-value"><?= $summary['hadir']; ?></span></td>
            <td class="sick"><span class="summary-label">Sakit</span><span class="summary-value"><?= $summary['sakit']; ?></span></td>
            <td class="leave"><span class="summary-label">Izin</span><span class="summary-value"><?= $summary['izin']; ?></span></td>
            <td class="absent"><span class="summary-label">Alpa</span><span class="summary-value"><?= $summary['alpa']; ?></span></td>
        </tr>
    </table>

    <table class="students">
        <thead>
            <tr>
                <th class="number">No.</th>
                <th class="nis">NIS</th>
                <th class="name">Nama siswa</th>
                <th class="status-cell">Status</th>
                <th class="note">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $student) : ?>
                <?php $status = $student['status']; ?>
                <tr>
                    <td class="number"><?= $index + 1; ?></td>
                    <td><?= esc($student['nis'] ?? '-'); ?></td>
                    <td><?= esc($student['nama_siswa']); ?></td>
                    <td><span class="status status-<?= esc($status); ?>"><?= esc($statusLabels[$status] ?? $status); ?></span></td>
                    <td><?= esc($student['keterangan'] ?? '-'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($students === []) : ?>
                <tr><td class="empty" colspan="5">Tidak ada data siswa pada sesi absensi ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
