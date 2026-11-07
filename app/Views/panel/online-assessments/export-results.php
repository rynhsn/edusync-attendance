<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?> - EduSync</title>
    <link rel="stylesheet" href="<?= base_url('metis-1.0.0/dist-modern/assets/main-Kuf3cpUe.css'); ?>">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        .kop-sekolah { border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container my-4 bg-white p-4 p-md-5 rounded shadow-sm border">
        <!-- Print Button -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                &larr; Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                Cetak Laporan / PDF
            </button>
        </div>

        <!-- Kop Surat -->
        <div class="text-center kop-sekolah">
            <h2 class="fw-bold mb-1">REKAPITULASI HASIL UJIAN ONLINE (CBT)</h2>
            <h4 class="mb-1 text-primary fw-bold"><?= esc($ujian['judul']); ?></h4>
            <p class="mb-0 text-muted">
                Mata Pelajaran: <strong><?= esc($ujian['mapel_nama'] ?: 'Semua Mapel'); ?></strong> &bull; 
                Kelas: <strong><?= esc($ujian['kelas_nama'] ?: 'Semua Kelas'); ?></strong> &bull; 
                Durasi: <strong><?= (int) $ujian['durasi_menit']; ?> Menit</strong> &bull;
                Tanggal Cetak: <?= date('d/m/Y H:i'); ?>
            </p>
        </div>

        <!-- Table Results -->
        <table class="table table-bordered align-middle text-center mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th class="text-start">Nama Siswa</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Benar (PG)</th>
                    <th>Salah (PG)</th>
                    <th>Nilai PG</th>
                    <th>Nilai Essay</th>
                    <th class="bg-primary text-white">Nilai Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $idx => $p) : ?>
                    <tr>
                        <td><?= $idx + 1; ?></td>
                        <td class="text-start fw-semibold"><?= esc($p['nama_siswa'] ?: '-'); ?></td>
                        <td><?= esc($p['nis'] ?: '-'); ?></td>
                        <td><?= esc($p['kelas_nama'] ?: '-'); ?></td>
                        <td class="text-success fw-bold"><?= (int) $p['jumlah_benar']; ?></td>
                        <td class="text-danger"><?= (int) $p['jumlah_salah']; ?></td>
                        <td class="fw-semibold"><?= (float) $p['nilai_pg']; ?></td>
                        <td><?= (float) $p['nilai_essay']; ?></td>
                        <td class="fw-bold fs-6 bg-light"><?= (float) $p['nilai_total']; ?></td>
                        <td><?= $p['status_pengerjaan'] === 'selesai' ? 'Selesai' : 'Mengerjakan'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($participants === []) : ?>
                    <tr><td colspan="10" class="text-muted py-4">Belum ada siswa yang mengerjakan ujian ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="row mt-5 text-center">
            <div class="col-6">
                <p class="mb-5">Mengetahui,<br>Kepala Sekolah</p>
                <p class="fw-bold text-decoration-underline mb-0">............................................</p>
                <p class="text-muted small">NIP. ........................................</p>
            </div>
            <div class="col-6">
                <p class="mb-5">Guru Pengampu Mata Pelajaran</p>
                <p class="fw-bold text-decoration-underline mb-0"><?= esc($ujian['guru_nama'] ?: '............................................'); ?></p>
                <p class="text-muted small">NIP. ........................................</p>
            </div>
        </div>
    </div>
</body>
</html>
