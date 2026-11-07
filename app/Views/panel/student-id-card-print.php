<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title); ?></title>
    <style>
        @page{size:A4 portrait;margin:0}
        *{box-sizing:border-box}
        body{color:#172033;font-family:Arial,sans-serif;margin:0}
        .print-actions{display:flex;gap:8px;justify-content:center;padding:16px}
        .print-actions button,.print-actions a{background:#0d6efd;border:0;border-radius:5px;color:#fff;cursor:pointer;font-size:14px;padding:9px 14px;text-decoration:none}
        .print-actions a{background:#6c757d}
        .card-page{align-items:center;display:flex;height:297mm;justify-content:center;page-break-after:always;position:relative;width:210mm}
        .id-card{border:.2mm solid #d0d5dd;border-radius:2mm;height:var(--card-height);max-height:120mm;max-width:90mm;overflow:hidden;position:relative;width:var(--card-width)}
        .id-card-template{height:100%;left:0;object-fit:fill;position:absolute;top:0;width:100%}
        .card-photo{align-items:center;background:#e60000;border-radius:50%;display:flex;height:var(--photo-height);justify-content:center;left:var(--photo-x);overflow:hidden;position:absolute;top:var(--photo-y);width:var(--photo-width)}
        .front-photo{height:100%;object-fit:cover;transform:translate(var(--photo-pan-x,0%), var(--photo-pan-y,0%)) scale(var(--photo-zoom,1));transform-origin:center;width:100%}
        .photo-placeholder{align-items:center;color:#fff;display:flex;font-size:7mm;font-weight:700;justify-content:center}
        .card-field{background:rgba(255,255,255,.82);border-radius:.5mm;line-height:1.25;max-width:88%;overflow-wrap:anywhere;padding:.5mm 1mm;position:absolute}
        .card-field.name{background:rgba(255,255,255,.9);font-weight:700}
        @media screen{body{background:#e9ecef}.card-page{background:#fff;margin:0 auto 12px}}
        @media print{.print-actions{display:none}}
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Cetak ID Card</button>
        <a href="<?= site_url('student-id-cards'); ?>">Kembali</a>
    </div>
    <main>
        <?php
        $photo = $layout['front']['photo'] ?? null;
        $photoHeight = $photo['height'] ?? null;
        ?>
        <section class="card-page" aria-label="ID card depan">
            <div class="id-card" style="--card-width: <?= esc((string) $frontCardSize['width'], 'attr'); ?>mm; --card-height: <?= esc((string) $frontCardSize['height'], 'attr'); ?>mm; <?= $photo !== null ? '--photo-x:' . esc((string) $photo['x'], 'attr') . '%; --photo-y:' . esc((string) $photo['y'], 'attr') . '%; --photo-width:' . esc((string) $photo['width'], 'attr') . '%; --photo-height:' . esc((string) $photoHeight, 'attr') . '%; --photo-zoom:' . esc((string) ($photo['zoom'] ?? 1), 'attr') . '; --photo-pan-x:' . esc((string) ($photo['panX'] ?? 0), 'attr') . '%; --photo-pan-y:' . esc((string) ($photo['panY'] ?? 0), 'attr') . '%;' : ''; ?>">
                <img class="id-card-template" src="<?= esc($frontTemplate, 'attr'); ?>" alt="">
                <?php if ($photo !== null) : ?>
                    <div class="card-photo">
                    <?php if ($photoUrl !== null) : ?>
                        <img class="front-photo" src="<?= esc($photoUrl, 'attr'); ?>" alt="Foto <?= esc($personName); ?>">
                    <?php else : ?>
                        <div class="photo-placeholder" aria-label="Foto belum tersedia"><?= esc(mb_strtoupper(mb_substr($personName, 0, 1))); ?></div>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php foreach ($frontFields as $field) : ?>
                    <?php $position = $layout['front'][$field['key']] ?? null; ?>
                    <?php if ($position === null) { continue; } ?>
                    <div class="card-field <?= $field['key'] === 'name' ? 'name' : ''; ?>"
                        style="left: <?= esc((string) $position['x'], 'attr'); ?>%; top: <?= esc((string) $position['y'], 'attr'); ?>%; font-family: <?= esc($position['fontFamily'], 'attr'); ?>; font-size: <?= esc((string) $position['fontSize'], 'attr'); ?>mm"><?= esc($field['value'] ?? '-'); ?></div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
