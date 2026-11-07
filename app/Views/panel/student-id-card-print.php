<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title); ?><?= str_contains($title, 'EduSync') ? '' : ' - EduSync'; ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/img/favicon/favicon.ico'); ?>">
    <style>
        @page {
            size: <?= esc((string) $frontCardSize['width']); ?>mm <?= esc((string) $frontCardSize['height']); ?>mm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            color: #172033;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        .print-actions {
            align-items: center;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            padding: 14px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .card-info-badge {
            align-items: center;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #475569;
            display: inline-flex;
            font-size: 13px;
            font-weight: 500;
            gap: 6px;
            padding: 8px 14px;
        }
        .btn-action {
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
        .btn-jpg {
            background: #10b981;
            color: #ffffff;
        }
        .btn-jpg:hover:not(:disabled) {
            background: #059669;
            color: #ffffff;
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #1d4ed8;
            color: #ffffff;
        }
        .btn-back {
            background: #64748b;
            color: #ffffff;
        }
        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }
        .btn-action:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        .spinner {
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .card-wrapper {
            display: flex;
            justify-content: center;
            padding: 24px 16px;
        }
        .card-page {
            height: var(--card-height);
            overflow: hidden;
            position: relative;
            width: var(--card-width);
            background: #ffffff;
        }
        .id-card {
            border: none;
            height: var(--card-height);
            overflow: hidden;
            position: relative;
            width: var(--card-width);
            background: #ffffff;
        }
        .id-card-template {
            height: 100%;
            left: 0;
            object-fit: fill;
            pointer-events: none;
            position: absolute;
            top: 0;
            width: 100%;
        }
        .card-photo {
            align-items: center;
            background: #e60000;
            border-radius: 50%;
            display: flex;
            height: var(--photo-height);
            justify-content: center;
            left: var(--photo-x);
            overflow: hidden;
            position: absolute;
            top: var(--photo-y);
            width: var(--photo-width);
            z-index: 2;
        }
        .card-photo.is-circle {
            border-radius: 50% !important;
        }
        .card-photo.is-square {
            border-radius: 0% !important;
        }
        .card-photo.is-teacher-photo {
            background: transparent !important;
        }
        .card-code-container {
            align-items: center;
            display: flex;
            justify-content: center;
            overflow: hidden;
            position: absolute;
            z-index: 3;
            pointer-events: none;
        }
        .card-code-container .code-inner {
            align-items: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
        .card-code-container svg {
            display: block;
            max-width: 100%;
            max-height: 100%;
            height: auto;
            width: 100%;
        }
        .card-code-container #print-qrcode-canvas {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 100%;
            max-height: 85%;
        }
        .card-code-container #print-qrcode-canvas canvas,
        .card-code-container #print-qrcode-canvas img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .card-code-container .code-label {
            font-size: 2.2mm;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #111827;
            text-align: center;
            line-height: 1.1;
            margin-top: 0.5mm;
            font-family: monospace, Arial, sans-serif;
        }
        .front-photo {
            display: block;
            flex-shrink: 0;
            height: auto;
            max-height: none !important;
            max-width: none !important;
            min-height: 100%;
            min-width: 100%;
            pointer-events: none;
            transform: translate(var(--photo-pan-x, 0%), var(--photo-pan-y, 0%)) scale(var(--photo-zoom, 1));
            transform-origin: center;
            width: 100%;
        }
        .photo-placeholder {
            align-items: center;
            color: #ffffff;
            display: flex;
            font-size: 7mm;
            font-weight: 700;
            justify-content: center;
        }
        .card-field {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 0.5mm;
            color: #102b58;
            line-height: 1.25;
            max-width: 88%;
            overflow-wrap: anywhere;
            padding: 0.5mm 1mm;
            position: absolute;
            white-space: normal;
            z-index: 3;
        }
        .card-field.name {
            background: rgba(255, 255, 255, 0.92);
            font-weight: 700;
        }
        @media screen {
            body {
                background: #f1f5f9;
            }
            .card-page {
                border-radius: 6px;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            }
        }
        @media print {
            body {
                background: transparent !important;
                height: <?= esc((string) $frontCardSize['height']); ?>mm !important;
                margin: 0 !important;
                padding: 0 !important;
                width: <?= esc((string) $frontCardSize['width']); ?>mm !important;
            }
            .print-actions {
                display: none !important;
            }
            .card-wrapper {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card-page {
                border-radius: 0 !important;
                box-shadow: none !important;
                break-inside: avoid !important;
                height: <?= esc((string) $frontCardSize['height']); ?>mm !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                width: <?= esc((string) $frontCardSize['width']); ?>mm !important;
            }
            .id-card {
                border: none !important;
                border-radius: 0 !important;
                height: <?= esc((string) $frontCardSize['height']); ?>mm !important;
                width: <?= esc((string) $frontCardSize['width']); ?>mm !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" class="btn-action btn-jpg" id="btn-download-jpg" onclick="downloadJpg()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Download Gambar (JPG)
        </button>

        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Cetak ID Card
        </button>

        <?php
            $backUrl = site_url('student-id-cards');
            $sessionUser = session()->get('authUser');
            if (($sessionUser['role'] ?? '') === 'siswa') {
                $backUrl = site_url('profile');
            }
        ?>
        <a class="btn-action btn-back" href="<?= $backUrl; ?>" onclick="if (window.opener) { window.close(); return false; }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali
        </a>

        <div class="card-info-badge">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="18" rx="2"></rect>
                <line x1="2" y1="9" x2="22" y2="9"></line>
                <line x1="2" y1="15" x2="22" y2="15"></line>
                <line x1="9" y1="3" x2="9" y2="21"></line>
            </svg>
            Ukuran Kartu: <?= esc((string) $frontCardSize['width']); ?> × <?= esc((string) $frontCardSize['height']); ?> mm
        </div>
    </div>

    <main class="card-wrapper">
        <?php
        $frontLayout = $layout['front'] ?? $layout;
        $photo = $frontLayout['photo'] ?? null;
        $photoHeight = $photo['height'] ?? null;
        $photoShape = $photo['shape'] ?? 'circle';
        $code = $frontLayout['code'] ?? null;
        $codeType = $code['type'] ?? 'barcode';
        $codeShowText = ! isset($code['showText']) || ! empty($code['showText']);
        ?>
        <section class="card-page" aria-label="ID card depan" style="--card-width: <?= esc((string) $frontCardSize['width'], 'attr'); ?>mm; --card-height: <?= esc((string) $frontCardSize['height'], 'attr'); ?>mm;">
            <div class="id-card" id="id-card" style="--card-width: <?= esc((string) $frontCardSize['width'], 'attr'); ?>mm; --card-height: <?= esc((string) $frontCardSize['height'], 'attr'); ?>mm; <?= $photo !== null ? '--photo-x:' . esc((string) $photo['x'], 'attr') . '%; --photo-y:' . esc((string) $photo['y'], 'attr') . '%; --photo-width:' . esc((string) $photo['width'], 'attr') . '%; --photo-height:' . esc((string) $photoHeight, 'attr') . '%; --photo-zoom:' . esc((string) ($photo['zoom'] ?? 1), 'attr') . '; --photo-pan-x:' . esc((string) ($photo['panX'] ?? 0), 'attr') . '%; --photo-pan-y:' . esc((string) ($photo['panY'] ?? 0), 'attr') . '%;' : ''; ?>">
                <img class="id-card-template" id="id-card-template-img" src="<?= esc($frontTemplate, 'attr'); ?>" alt="" crossorigin="anonymous">
                <?php if ($photo !== null) : ?>
                    <div class="card-photo <?= $photoShape === 'square' ? 'is-square' : 'is-circle'; ?> <?= ($cardCategory ?? '') === 'teacher' ? 'is-teacher-photo' : ''; ?>">
                    <?php if ($photoUrl !== null) : ?>
                        <img class="front-photo" src="<?= esc($photoUrl, 'attr'); ?>" alt="Foto <?= esc($personName); ?>" crossorigin="anonymous" style="transform: translate(<?= esc((string) ($photo['panX'] ?? 0), 'attr'); ?>%, <?= esc((string) ($photo['panY'] ?? 0), 'attr'); ?>%) scale(<?= esc((string) ($photo['zoom'] ?? 1), 'attr'); ?>); transform-origin: center;">
                    <?php else : ?>
                        <div class="photo-placeholder" aria-label="Foto belum tersedia" style="<?= ($cardCategory ?? '') === 'teacher' ? 'background: rgba(0,0,0,0.05); color: #475569;' : ''; ?>"><?= esc(mb_strtoupper(mb_substr($personName, 0, 1))); ?></div>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php foreach ($frontFields as $field) : ?>
                    <?php $position = $frontLayout[$field['key']] ?? null; ?>
                    <?php if ($position === null) { continue; } ?>
                    <div class="card-field <?= $field['key'] === 'name' ? 'name' : ''; ?>"
                        style="left: <?= esc((string) $position['x'], 'attr'); ?>%; top: <?= esc((string) $position['y'], 'attr'); ?>%; font-family: <?= esc($position['fontFamily'], 'attr'); ?>; font-size: <?= esc((string) $position['fontSize'], 'attr'); ?>mm; color: <?= esc($position['fontColor'] ?? '#102b58', 'attr'); ?>"><?= esc($field['value'] ?? '-'); ?></div>
                <?php endforeach; ?>
                <?php if ($code !== null && $codeType !== 'none') : ?>
                    <?php
                    $codeX = isset($code['x']) && is_numeric($code['x']) ? (float) $code['x'] : 25.0;
                    $codeY = isset($code['y']) && is_numeric($code['y']) ? (float) $code['y'] : 84.0;
                    $codeW = isset($code['width']) && is_numeric($code['width']) ? (float) $code['width'] : 50.0;
                    $codeH = isset($code['height']) && is_numeric($code['height']) ? (float) $code['height'] : 12.0;
                    ?>
                    <div class="card-code-container" style="left: <?= esc((string) $codeX); ?>%; top: <?= esc((string) $codeY); ?>%; width: <?= esc((string) $codeW); ?>%; height: <?= esc((string) $codeH); ?>%;">
                        <div class="code-inner" id="print-code-target" data-type="<?= esc($codeType); ?>" data-value="<?= esc($rfidValue); ?>" data-show-text="<?= $codeShowText ? '1' : '0'; ?>">
                            <?php if ($codeType === 'barcode') : ?>
                                <svg id="print-barcode-svg"></svg>
                            <?php elseif ($codeType === 'qrcode') : ?>
                                <div id="print-qrcode-canvas"></div>
                                <?php if ($codeShowText) : ?>
                                    <span class="code-label"><?= esc($rfidValue); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="<?= base_url('assets/js/JsBarcode.all.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/qrcode.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/html2canvas.min.js'); ?>"></script>
    <script>
        async function downloadJpg() {
            const cardEl = document.getElementById('id-card');
            const btn = document.getElementById('btn-download-jpg');
            if (!cardEl) return;

            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="spinner" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg> Memproses JPG...';
            }

            try {
                // Pastikan semua gambar di dalam kartu selesai dimuat
                const imgs = Array.from(cardEl.querySelectorAll('img'));
                await Promise.all(imgs.map((img) => {
                    if (img.complete) return Promise.resolve();
                    return new Promise((resolve) => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                }));

                // Render kartu menggunakan html2canvas dengan skala tinggi (scale 4 = ~300-350 DPI tajam)
                const canvas = await html2canvas(cardEl, {
                    scale: 4,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false
                });

                // Gambar ke canvas dengan latar belakang putih murni untuk kompresi JPEG optimal
                const outputCanvas = document.createElement('canvas');
                outputCanvas.width = canvas.width;
                outputCanvas.height = canvas.height;
                const ctx = outputCanvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, outputCanvas.width, outputCanvas.height);
                ctx.drawImage(canvas, 0, 0);

                outputCanvas.toBlob((blob) => {
                    if (!blob) {
                        alert('Gagal membuat file gambar JPG.');
                        return;
                    }
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const cleanName = <?= json_encode('idcard-' . trim(preg_replace('/[^a-z0-9\-]+/', '-', strtolower($personName)), '-') . '.jpg'); ?>;
                    link.href = url;
                    link.download = cleanName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    setTimeout(() => URL.revokeObjectURL(url), 2000);
                }, 'image/jpeg', 0.95);
            } catch (err) {
                console.error('Export JPG error:', err);
                alert('Terjadi kesalahan saat membuat JPG: ' + (err.message || 'Kesalahan sistem'));
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        function initPrintCodes() {
            const target = document.getElementById('print-code-target');
            if (!target) return;
            const type = target.dataset.type;
            const val = target.dataset.value || '12345678';
            const showText = target.dataset.showText === '1';

            if (type === 'barcode' && typeof JsBarcode !== 'undefined') {
                const svgEl = document.getElementById('print-barcode-svg');
                if (svgEl) {
                    try {
                        JsBarcode(svgEl, val, {
                            format: 'CODE128',
                            width: 1.6,
                            height: 44,
                            displayValue: showText,
                            fontSize: 11,
                            font: 'Arial',
                            textMargin: 2,
                            margin: 0,
                            background: 'transparent'
                        });
                    } catch (e) {
                        console.warn('JsBarcode render error:', e);
                    }
                }
            } else if (type === 'qrcode' && typeof QRCode !== 'undefined') {
                const container = document.getElementById('print-qrcode-canvas');
                if (container) {
                    container.innerHTML = '';
                    try {
                        new QRCode(container, {
                            text: val,
                            width: 100,
                            height: 100,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch (e) {
                        console.warn('QRCode render error:', e);
                    }
                }
            }
        }

        // Inisialisasi barcode/qrcode saat load
        initPrintCodes();

        // Jalankan download otomatis bila parameter query `download=1` atau `format=jpg` disertakan
        window.addEventListener('DOMContentLoaded', () => {
            initPrintCodes();
            const params = new URLSearchParams(window.location.search);
            if (params.get('download') === '1' || params.get('format') === 'jpg') {
                setTimeout(downloadJpg, 350);
            }
        });
    </script>
</body>
</html>
