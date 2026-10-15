<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Raport Siswa</title>
    <style>
        @page { size: <?= esc($paperSize); ?> portrait; margin: 8mm 0; }
        html, body { background: #fff; color: #111; margin: 0; padding: 0; }
        .pdf-report .no-print { display: none !important; }
        .pdf-report .container-fluid { margin: 0; padding: 0; width: auto; }
        .pdf-report .pdf-report-frame { margin: 0; padding: 0 14mm; width: auto; }
        .pdf-report .pdf-report-frame.smp { padding-top: 6mm; }
        .pdf-report .report-paper { border: 0; box-sizing: border-box; margin: 0; max-width: none; padding: 0; width: auto; }
    </style>
</head>
<body class="pdf-report">
<?= $this->renderSection('content'); ?>
<?= $this->renderSection('scripts'); ?>
<style>
    <?php
    $rootFontSize = max(7, 15 - ((int) $pdfFitLevel * 0.85));
    $cellPadding = max(0, 4 - ((int) $pdfFitLevel * 0.45));
    $headingHeight = max(50, 112 - ((int) $pdfFitLevel * 7));
    $signatureHeight = max(22, 72 - ((int) $pdfFitLevel * 6));
    ?>
    html { font-size: <?= $rootFontSize; ?>px; }
    .report-paper, .report-paper * { box-sizing: border-box; }
    .report-heading, .smp .report-heading { min-height: <?= $headingHeight; ?>px; }
    .pdf-report .smp .report-separator { margin-top: 40px; }
    .report-identity, .report-grades, .report-assessment-table, .smp-assessment-table { font-size: .75rem; }
    .report-grades { table-layout: fixed; width: 100%; }
    .report-grades th, .report-grades td { overflow-wrap: break-word; word-wrap: break-word; }
    .smk .report-grades { font-size: .68rem; }
    .report-identity th, .report-identity td,
    .report-grades th, .report-grades td,
    .report-assessment-table th, .report-assessment-table td,
    .smp-assessment-table th, .smp-assessment-table td { padding: <?= $cellPadding; ?>px; }
    .report-lower-grid { margin-top: 5px; }
    .report-assessment-section + .report-assessment-section { margin-top: 5px; }
    .report-comment { margin-top: 5px; min-height: 24px; padding: <?= $cellPadding; ?>px; }
    .report-signatures, .smp-signatures { margin-top: 8px; min-height: 0; }
    .signature-line, .smp-signature-line { height: <?= $signatureHeight; ?>px; }
    .smp-homeroom-note > div { min-height: 30px; }
    .report-lower-grid { display: block; position: relative; width: 100%; }
    .report-assessment-column { display: block; width: 54%; }
    .report-attendance-table { display: table; margin: 0; position: absolute; right: 0; top: 0; width: 42%; }
    .report-signatures, .smp-signatures { display: block; min-height: <?= $signatureHeight + 12; ?>px; position: relative; width: 100%; }
    .report-signatures > div, .smp-signatures > div { display: block; float: left; width: 50%; }
    .report-signatures > div:last-child, .smp-signatures > div:last-child { float: right; text-align: left; }
    .report-signatures::after, .smp-signatures::after { clear: both; content: ""; display: block; }
    .report-generated { display: none; }
    .report-heading, .smp-homeroom-note, .report-signatures, .smp-signatures { page-break-inside: avoid; }
</style>
</body>
</html>
