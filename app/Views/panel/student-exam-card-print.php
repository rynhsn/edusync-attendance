<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title); ?><?= str_contains($title, 'EduSync') ? '' : ' - EduSync'; ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        <?php
            $layout = $settings['student_exam_card_layout'] ?? 'landscape_4';
            if ($layout === 'portrait_2') {
                $pageSize = 'A4 portrait';
                $gridCols = '1fr';
                $cardsPerPage = 2;
                $cardHeight = '120mm';
            } elseif ($layout === 'landscape_2') {
                $pageSize = 'A4 landscape';
                $gridCols = 'repeat(2, 1fr)';
                $cardsPerPage = 2;
                $cardHeight = '160mm';
            } elseif ($layout === 'single') {
                $pageSize = 'A5 landscape';
                $gridCols = '1fr';
                $cardsPerPage = 1;
                $cardHeight = '120mm';
            } else { // landscape_4 (default)
                $pageSize = 'A4 landscape';
                $gridCols = 'repeat(2, 1fr)';
                $cardsPerPage = 4;
                $cardHeight = '88mm';
            }
        ?>

        @page {
            size: <?= esc($pageSize); ?>;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            color: #000000;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Top Action Bar (Screen Only) */
        .print-toolbar {
            align-items: center;
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: space-between;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badge-count {
            background: #e2e8f0;
            border-radius: 6px;
            color: #1e293b;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 12px;
        }

        .btn-toolbar {
            align-items: center;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            font-weight: 600;
            gap: 8px;
            padding: 8px 18px;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }

        /* Print Container */
        .print-sheet {
            margin: 20px auto;
            max-width: 297mm;
            padding: 0 10px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: <?= esc($gridCols); ?>;
            gap: 6mm;
            margin-bottom: 6mm;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* The Exam Card Box - Exact replica of Image 1 */
        .exam-card-item {
            background: #ffffff;
            border: 2px solid #000000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: <?= esc($cardHeight); ?>;
            padding: 12px 18px;
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
        }

        .card-header-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .card-logo-col {
            flex-shrink: 0;
            width: 58px;
            text-align: center;
        }

        .card-school-logo {
            display: block;
            margin: 0 auto;
            max-height: 56px;
            max-width: 56px;
            object-fit: contain;
        }

        .card-title-col {
            flex-grow: 1;
            text-align: center;
        }

        .card-title-1 {
            color: #000000;
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 0.3px;
            line-height: 1.25;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .card-title-2 {
            color: #000000;
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 0.5px;
            line-height: 1.25;
            text-transform: uppercase;
        }

        /* Kop surat double line separator: thick top, thin bottom */
        .card-double-divider {
            border-top: 2.5px solid #000000;
            border-bottom: 1px solid #000000;
            box-sizing: border-box;
            height: 3px;
            margin-bottom: 10px;
            margin-top: 8px;
        }

        /* Student Identity Table */
        .card-body-table {
            border-collapse: collapse;
            color: #000000;
            font-size: 11pt;
            line-height: 1.35;
            margin-bottom: 6px;
            width: 100%;
        }

        .card-body-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        .col-label {
            font-weight: 700;
            white-space: nowrap;
            width: 135px;
        }

        .col-colon {
            font-weight: 700;
            text-align: center;
            width: 18px;
        }

        .col-value {
            font-weight: 700;
            word-break: break-word;
        }

        /* Footer / Tanda Tangan */
        .card-footer-row {
            display: flex;
            justify-content: flex-end;
            margin-top: auto;
            padding-top: 4px;
        }

        .signature-block {
            color: #000000;
            font-size: 10.5pt;
            font-weight: 700;
            line-height: 1.3;
            min-width: 210px;
            text-align: left;
        }

        .sig-city-date {
            margin-bottom: 2px;
        }

        .sig-space {
            height: 40px;
        }

        .sig-barcode-wrap {
            display: flex;
            align-items: center;
            height: 12mm;
            margin: 1mm 0;
        }

        .sig-barcode-img {
            max-height: 12mm;
            max-width: 12mm;
            object-fit: contain;
            display: block;
        }

        .sig-signer-name {
            font-weight: 700;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: transparent !important;
            }
            .print-toolbar {
                display: none !important;
            }
            .print-sheet {
                margin: 0 !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .exam-card-item {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Sticky Toolbar (Screen only) -->
    <div class="print-toolbar">
        <div class="toolbar-info">
            <a href="<?= site_url('student-exam-cards'); ?>" class="btn-toolbar btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <span class="badge-count">
                <i class="bi bi-card-heading me-1"></i> Total: <?= count($students); ?> Kartu
            </span>
            <span style="font-size: 13px; color: #64748b;">
                (Layout: <strong><?= esc($layout); ?></strong> &bull; Ukuran Kertas: <strong><?= esc($pageSize); ?></strong>)
            </span>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button type="button" class="btn-toolbar btn-print" onclick="window.print()">
                <i class="bi bi-printer-fill"></i> Cetak Sekarang
            </button>
        </div>
    </div>

    <!-- Main Cards Container -->
    <div class="print-sheet">
        <?php
            $chunks = array_chunk($students, $cardsPerPage);
            $totalChunks = count($chunks);
        ?>
        <?php foreach ($chunks as $chunkIndex => $pageStudents) : ?>
            <div class="card-grid <?= ($chunkIndex < $totalChunks - 1) ? 'page-break' : ''; ?>">
                <?php foreach ($pageStudents as $s) : ?>
                    <div class="exam-card-item">
                        <div>
                            <!-- Header Row: Logo & Centered Titles -->
                            <div class="card-header-row">
                                <div class="card-logo-col">
                                    <img src="<?= esc($logoUrl, 'attr'); ?>" alt="Logo Sekolah" class="card-school-logo">
                                </div>
                                <div class="card-title-col">
                                    <div class="card-title-1"><?= esc($settings['student_exam_card_title']); ?></div>
                                    <div class="card-title-2"><?= esc($settings['student_exam_card_subtitle']); ?></div>
                                </div>
                            </div>

                            <!-- Double Horizontal Rule -->
                            <div class="card-double-divider"></div>

                            <!-- Student Identity Details -->
                            <table class="card-body-table">
                                <tr>
                                    <td class="col-label">No. Peserta</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value"><?= esc($s['display_no_peserta']); ?></td>
                                </tr>
                                <tr>
                                    <td class="col-label">Nama</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value"><?= esc($s['nama_siswa']); ?></td>
                                </tr>
                                <tr>
                                    <td class="col-label">Tempat, Tgl Lahir</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value"><?= esc($s['formatted_ttl']); ?></td>
                                </tr>
                                <tr>
                                    <td class="col-label">NIS</td>
                                    <td class="col-colon">:</td>
                                    <td class="col-value"><?= esc($s['display_nis']); ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Footer: Signature & Date -->
                        <div class="card-footer-row">
                            <div class="signature-block">
                                <div class="sig-city-date"><?= esc($settings['student_exam_card_city_date']); ?></div>
                                <div class="sig-role"><?= esc($settings['student_exam_card_signer_role']); ?></div>
                                <?php if (! empty($barcodeUrl)) : ?>
                                    <div class="sig-barcode-wrap">
                                        <img src="<?= esc($barcodeUrl, 'attr'); ?>" alt="Barcode TTD" class="sig-barcode-img">
                                    </div>
                                <?php else : ?>
                                    <div class="sig-space"></div>
                                <?php endif; ?>
                                <div class="sig-signer-name"><?= esc($settings['student_exam_card_signer_name']); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
