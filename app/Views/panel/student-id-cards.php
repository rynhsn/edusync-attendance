<?= $this->extend('layouts/panel'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid p-4 p-lg-4">
    <div class="mb-4">
        <h1 class="h3 mb-1">ID Card Siswa dan Guru</h1>
        <p class="text-muted mb-0">Atur template terpisah untuk SMK, SMP, dan Guru, lalu cetak kartu dari data yang tersimpan.</p>
    </div>

    <?php if (! empty($message)) : ?>
        <div class="alert alert-success" role="status"><?= esc($message); ?></div>
    <?php endif; ?>
    <?php if (! empty($error)) : ?>
        <div class="alert alert-danger" role="alert"><?= esc($error); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Template Kartu</h2>
        </div>
        <div class="card-body">
            <?php if ($canSaveTemplates) : ?>
                <form action="<?= site_url('student-id-cards/templates'); ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <div class="mb-3">
                        <label for="id-card-template-category" class="form-label">Pilih kategori template</label>
                        <select id="id-card-template-category" class="form-select id-card-template-category-select">
                            <?php foreach ($categories as $category => $categoryLabel) : ?>
                                <option value="<?= esc($category); ?>" <?= $category === 'smp' ? 'selected' : ''; ?>><?= esc($categoryLabel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php foreach ($categories as $category => $categoryLabel) : ?>
                        <section class="mb-4 id-card-template-category" data-template-category="<?= esc($category); ?>" <?= $category !== 'smp' ? 'hidden' : ''; ?>>
                            <h3 class="h6"><?= esc($categoryLabel); ?></h3>
                            <div class="row g-4">
                                <?php foreach (['front' => 'Depan'] as $side => $label) : ?>
                                    <div class="col-md-12">
                                        <label class="form-label" for="id-card-<?= esc($category . '-' . $side); ?>"><?= esc($label); ?></label>
                                        <input class="form-control" id="id-card-<?= esc($category . '-' . $side); ?>" name="<?= esc($category . '_' . $side); ?>_template" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                        <div class="form-text">JPG/PNG, maksimal 5 MB. Kosongkan untuk mempertahankan template saat ini.</div>
                                        <div class="id-card-template-preview mt-3">
                                            <?php if ($templates[$category][$side] !== null) : ?>
                                                <img src="<?= esc($templates[$category][$side], 'attr'); ?>" alt="Template <?= esc($categoryLabel); ?> sisi <?= esc(strtolower($label)); ?>">
                                            <?php else : ?>
                                                <span class="text-muted">Template <?= esc(strtolower($label)); ?> belum diunggah.</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-upload me-2" aria-hidden="true"></i>Simpan Template
                    </button>
                </form>
            <?php else : ?>
                <div class="alert alert-info mb-0" role="status">Anda dapat mencetak ID card. Hubungi administrator untuk mengunggah atau mengganti template.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5 card-title mb-0">Atur Posisi Data pada Template</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">Posisi dan font disimpan terpisah untuk setiap desain. Pastikan kategori yang diatur sama dengan kategori yang dipilih pada baris siswa saat mencetak. Geser lingkaran untuk memindahkan posisinya pada kartu; setelah foto diperbesar, drag langsung pada foto untuk mengatur crop di dalam lingkaran. Gunakan scroll mouse atau slider untuk zoom, dan tarik handel untuk mengubah ukuran lingkaran.</p>
            <?php if ($canSaveLayout) : ?>
                <?php
                $previewStudent = $students[0] ?? [];
                $samples = [
                    'smk' => [
                        'front' => [
                            'name' => $previewStudent['nama_siswa'] ?? 'Nama Siswa',
                            'nis' => $previewStudent['nis'] ?? '123456',
                            'nisn' => $previewStudent['nisn'] ?? '0012345678',
                            'birth' => trim(($previewStudent['tempat_lahir'] ?? 'Bandung') . ', ' . ($previewStudent['tanggal_lahir'] ?? '01-01-2010'), ', '),
                            'address' => $previewStudent['alamat'] ?? 'Jl. Contoh No. 1',
                        ],
                    ],
                    'smp' => [
                        'front' => [
                            'name' => $previewStudent['nama_siswa'] ?? 'Nama Siswa',
                            'nis' => $previewStudent['nis'] ?? '123456',
                            'nisn' => $previewStudent['nisn'] ?? '0012345678',
                            'birth' => trim(($previewStudent['tempat_lahir'] ?? 'Bandung') . ', ' . ($previewStudent['tanggal_lahir'] ?? '01-01-2010'), ', '),
                            'address' => $previewStudent['alamat'] ?? 'Jl. Contoh No. 1',
                        ],
                    ],
                    'teacher' => [
                        'front' => [
                            'name' => $previewTeacher['guru_nama'] ?? 'Nama Guru',
                            'nip' => 'NIP: ' . ($previewTeacher['guru_nip'] ?? 'Nomor Induk'),
                            'email' => 'Email: ' . ($previewTeacher['guru_email'] ?? 'Alamat email'),
                        ],
                    ],
                ];
                ?>
                <form action="<?= site_url('student-id-cards/layout'); ?>" method="post" id="id-card-layout-form" novalidate>
                    <?= csrf_field(); ?>
                    <input type="hidden" name="layout" id="id-card-layout-value" value="<?= esc(json_encode($layout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), 'attr'); ?>">
                    <div class="mb-3">
                        <label for="id-card-layout-category" class="form-label">Desain yang diatur</label>
                        <select id="id-card-layout-category" class="form-select id-card-layout-category-select">
                            <?php foreach ($categories as $category => $categoryLabel) : ?>
                                <option value="<?= esc($category); ?>" <?= $category === 'smp' ? 'selected' : ''; ?>><?= esc($categoryLabel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php foreach ($categories as $category => $categoryLabel) : ?>
                        <div class="id-card-layout-category" data-layout-category="<?= esc($category); ?>" <?= $category !== 'smp' ? 'hidden' : ''; ?>>
                            <h3 class="h6 mb-3"><?= esc($categoryLabel); ?></h3>
                            <div class="row g-4">
                                <?php foreach (['front' => 'Depan'] as $side => $sideLabel) : ?>
                                    <div class="col-lg-12">
                                        <h4 class="h6 mb-3">Sisi <?= esc(strtolower($sideLabel)); ?></h4>
                                        <?php if ($templates[$category][$side] !== null) : ?>
                                            <?php $stageMaxWidth = min(360, 360 * $templateRatios[$category][$side]); ?>
                                            <div class="id-card-layout-stage mx-auto"
                                                data-category="<?= esc($category); ?>"
                                                data-side="<?= esc($side); ?>"
                                                style="--template-ratio: <?= esc((string) $templateRatios[$category][$side], 'attr'); ?>; --stage-max-width: <?= esc((string) $stageMaxWidth, 'attr'); ?>px; --card-width-mm: <?= esc((string) $templateCardWidths[$category][$side], 'attr'); ?>">
                                                <img class="id-card-layout-background" src="<?= esc($templates[$category][$side], 'attr'); ?>" alt="Pratinjau template <?= esc($categoryLabel); ?> <?= esc(strtolower($sideLabel)); ?>">
                                                <?php foreach ($samples[$category][$side] as $field => $sample) : ?>
                                                    <?php $position = $layout[$category][$side][$field]; ?>
                                                    <span class="id-card-layout-field <?= $field === 'name' ? 'is-name' : ''; ?>"
                                                        data-field="<?= esc($field); ?>"
                                                        style="left: <?= esc((string) $position['x'], 'attr'); ?>%; top: <?= esc((string) $position['y'], 'attr'); ?>%; color: <?= esc($position['fontColor'] ?? '#102b58', 'attr'); ?>;"
                                                        tabindex="0" role="button"
                                                        aria-label="Geser posisi <?= esc($sample); ?>"><?= esc($sample); ?></span>
                                                <?php endforeach; ?>
                                                <?php if (isset($layout[$category][$side]['photo'])) : ?>
                                                    <?php $photoPosition = $layout[$category][$side]['photo']; ?>
                                                    <?php $photoHeight = $photoPosition['width'] * $templateRatios[$category][$side]; ?>
                                                    <div class="id-card-layout-photo <?= ($photoPosition['shape'] ?? 'circle') === 'square' ? 'is-square' : 'is-circle'; ?> <?= $category === 'teacher' ? 'is-teacher-photo' : ''; ?>"
                                                        data-field="photo"
                                                        data-drag-mode="circle"
                                                        data-shape="<?= esc($photoPosition['shape'] ?? 'circle', 'attr'); ?>"
                                                        style="left: <?= esc((string) $photoPosition['x'], 'attr'); ?>%; top: <?= esc((string) $photoPosition['y'], 'attr'); ?>%; width: <?= esc((string) $photoPosition['width'], 'attr'); ?>%; height: <?= esc((string) $photoHeight, 'attr'); ?>%; --photo-zoom: <?= esc((string) ($photoPosition['zoom'] ?? 1), 'attr'); ?>; --photo-pan-x: <?= esc((string) ($photoPosition['panX'] ?? 0), 'attr'); ?>%; --photo-pan-y: <?= esc((string) ($photoPosition['panY'] ?? 0), 'attr'); ?>%;"
                                                        tabindex="0" role="button" aria-label="Geser atau ubah ukuran foto" title="Drag langsung untuk menggeser posisinya di kartu. Drag handel kanan bawah untuk mengubah ukuran.">
                                                        <?php if ($category === 'teacher') : ?>
                                                            <?php 
                                                                $teacherPreviewImg = $previewTeacherPhotoUrl ?? (! empty($previewTeacher['foto']) ? \App\Controllers\StudentIdCardController::studentPhotoUrl((string) $previewTeacher['foto']) : null);
                                                            ?>
                                                            <?php if ($teacherPreviewImg !== null) : ?>
                                                                <img src="<?= esc($teacherPreviewImg, 'attr'); ?>" alt="Pratinjau foto guru" style="transform: translate(<?= esc((string) ($photoPosition['panX'] ?? 0), 'attr'); ?>%, <?= esc((string) ($photoPosition['panY'] ?? 0), 'attr'); ?>%) scale(<?= esc((string) ($photoPosition['zoom'] ?? 1), 'attr'); ?>); transform-origin: center;">
                                                            <?php else : ?>
                                                                <div class="p-2 text-center" style="font-size: 11px; color: #374151;">
                                                                    <i class="bi bi-person-bounding-box d-block fs-3 text-primary mb-1"></i>
                                                                    <span class="fw-semibold">Foto Guru</span>
                                                                    <div class="text-muted" style="font-size: 9px;">Tanpa Latar Merah</div>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php else : ?>
                                                            <?php if ($previewPhotoUrl !== null) : ?>
                                                                <img src="<?= esc($previewPhotoUrl, 'attr'); ?>" alt="Pratinjau foto berlatar merah" style="transform: translate(<?= esc((string) ($photoPosition['panX'] ?? 0), 'attr'); ?>%, <?= esc((string) ($photoPosition['panY'] ?? 0), 'attr'); ?>%) scale(<?= esc((string) ($photoPosition['zoom'] ?? 1), 'attr'); ?>); transform-origin: center;">
                                                            <?php else : ?>
                                                                <span>Foto siswa<br>latar merah</span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <span class="id-card-photo-move-handle" title="Tarik untuk geser posisi foto di kartu" aria-hidden="true"><i class="bi bi-arrows-move"></i></span>
                                                        <span class="id-card-photo-resize-handle" title="Tarik untuk ubah ukuran foto" aria-hidden="true"></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php
                                                $codeSettings = $layout[$category][$side]['code'] ?? [
                                                    'x' => 25.0,
                                                    'y' => 84.0,
                                                    'width' => 50.0,
                                                    'height' => 12.0,
                                                    'type' => 'barcode',
                                                    'showText' => true,
                                                ];
                                                $sampleCodeVal = ($category === 'teacher') ? ($previewTeacherRfid ?? '19850101-001') : ($previewStudentRfid ?? '202409001');
                                                ?>
                                                <div class="id-card-layout-code <?= ($codeSettings['type'] ?? 'barcode') === 'none' ? 'd-none' : ''; ?>"
                                                    data-field="code"
                                                    data-code-type="<?= esc($codeSettings['type'] ?? 'barcode', 'attr'); ?>"
                                                    data-show-text="<?= ! empty($codeSettings['showText']) ? '1' : '0'; ?>"
                                                    data-code-val="<?= esc($sampleCodeVal, 'attr'); ?>"
                                                    style="left: <?= esc((string) $codeSettings['x'], 'attr'); ?>%; top: <?= esc((string) $codeSettings['y'], 'attr'); ?>%; width: <?= esc((string) $codeSettings['width'], 'attr'); ?>%; height: <?= esc((string) $codeSettings['height'], 'attr'); ?>%;"
                                                    tabindex="0" role="button" aria-label="Geser posisi Barcode / QR Code" title="Drag langsung untuk menggeser posisi Barcode / QR Code di kartu">
                                                    <div class="code-preview-wrap" data-category="<?= esc($category); ?>">
                                                        <svg class="preview-barcode-svg <?= ($codeSettings['type'] ?? 'barcode') === 'qrcode' ? 'd-none' : ''; ?>" id="preview-barcode-<?= esc($category); ?>" data-val="<?= esc($sampleCodeVal, 'attr'); ?>"></svg>
                                                        <div class="preview-qrcode-box <?= ($codeSettings['type'] ?? 'barcode') === 'barcode' ? 'd-none' : ''; ?>" id="preview-qrcode-<?= esc($category); ?>" data-val="<?= esc($sampleCodeVal, 'attr'); ?>"></div>
                                                        <span class="preview-code-text <?= empty($codeSettings['showText']) ? 'd-none' : ''; ?>"><?= esc($sampleCodeVal); ?></span>
                                                    </div>
                                                    <span class="id-card-code-move-handle" title="Tarik untuk geser posisi kode" aria-hidden="true"><i class="bi bi-arrows-move"></i></span>
                                                </div>
                                            </div>
                                            <?php if (isset($layout[$category][$side]['photo'])) : ?>
                                                <div class="card bg-light border p-3 mt-3 mb-3 shadow-sm rounded-3">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-3">
                                                        <div>
                                                            <h5 class="h6 mb-0 text-dark fw-bold d-flex align-items-center">
                                                                <i class="bi <?= $category === 'teacher' ? 'bi-person-bounding-box text-primary' : 'bi-circle-fill text-danger'; ?> me-2"></i>
                                                                <?= $category === 'teacher' ? 'Pengaturan Posisi &amp; Ukuran Foto Guru' : 'Pengaturan Bulatan Latar Merah &amp; Foto Siswa'; ?>
                                                            </h5>
                                                            <span class="text-muted" style="font-size: 11px;">
                                                                <?= $category === 'teacher' ? 'Sesuaikan posisi dan ukuran foto guru (tanpa latar belakang merah) pada template kartu.' : 'Sesuaikan posisi bulatan merah terhadap lingkaran template serta posisi foto di dalamnya.'; ?>
                                                            </span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button type="button" class="btn btn-sm btn-success fw-semibold id-card-photo-autofit shadow-sm" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" title="<?= $category === 'teacher' ? 'Sesuaikan area foto ke frame template' : 'Otomatis sesuaikan lingkaran merah agar tepat berada di garis bulatan putih template'; ?>">
                                                                <i class="bi bi-bullseye me-1"></i><?= $category === 'teacher' ? 'Pas-kan ke Frame Template' : 'Pas-kan ke Bulatan Putih Template'; ?>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-secondary id-card-photo-reset" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" title="Reset zoom dan posisi foto di dalam frame">
                                                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Foto
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Pilihan Bentuk Foto: Bulat vs Kotak -->
                                                    <div class="bg-white p-2 border rounded-2 mb-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="small fw-bold text-dark">
                                                                <i class="bi bi-crop text-primary me-1"></i>Format / Bentuk Foto Kartu:
                                                            </span>
                                                            <span class="badge bg-secondary-subtle text-dark border small" style="font-size: 10px;">Semua Template</span>
                                                        </div>
                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                            <div class="btn-group btn-group-sm" role="group" aria-label="Bentuk foto">
                                                                <button type="button" class="btn <?= ($photoPosition['shape'] ?? 'circle') === 'circle' ? 'btn-primary' : 'btn-outline-primary'; ?> id-card-photo-shape-btn" data-category="<?= esc($category); ?>" data-shape="circle">
                                                                    <i class="bi bi-circle me-1"></i>Bulat (Lingkaran)
                                                                </button>
                                                                <button type="button" class="btn <?= ($photoPosition['shape'] ?? 'circle') === 'square' ? 'btn-primary' : 'btn-outline-primary'; ?> id-card-photo-shape-btn" data-category="<?= esc($category); ?>" data-shape="square">
                                                                    <i class="bi bi-square me-1"></i>Kotak (Persegi)
                                                                </button>
                                                            </div>
                                                            <span class="text-muted small" style="font-size: 11px;">
                                                                Pilih apakah foto ditampilkan berbentuk <strong>lingkaran</strong> atau <strong>kotak persegi</strong>.
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Mode Drag Pilihan Mouse/Sentuhan -->
                                                    <div class="bg-white p-2 border rounded-2 mb-3 d-flex flex-wrap align-items-center gap-2">
                                                        <span class="small fw-bold text-dark me-1"><i class="bi bi-arrows-move text-primary me-1"></i>Mode Drag Mouse:</span>
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Mode drag di kartu">
                                                            <button type="button" class="btn btn-primary id-card-drag-mode-btn" data-category="<?= esc($category); ?>" data-mode="circle">
                                                                <i class="bi bi-arrows-fullscreen me-1"></i><?= $category === 'teacher' ? 'Geser Frame Foto di Kartu' : 'Geser Bulatan di Kartu'; ?>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-primary id-card-drag-mode-btn" data-category="<?= esc($category); ?>" data-mode="photo">
                                                                <i class="bi bi-person-bounding-box me-1"></i><?= $category === 'teacher' ? 'Geser Foto di Dalam Frame' : 'Geser Foto di Dalam Latar'; ?>
                                                            </button>
                                                        </div>
                                                        <span class="text-muted small ms-md-auto" style="font-size: 11px;">
                                                            <span class="id-card-drag-mode-hint" data-category="<?= esc($category); ?>">
                                                                Mode aktif: <strong><?= $category === 'teacher' ? 'Geser Frame Foto di Kartu' : 'Geser Bulatan di Kartu'; ?></strong> (Klik &amp; tarik untuk memindahkan posisinya).
                                                            </span>
                                                        </span>
                                                    </div>

                                                    <!-- Bagian 1: Posisi & Ukuran Foto di Kartu -->
                                                    <div class="bg-white border rounded-2 p-2 mb-3">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="small fw-bold <?= $category === 'teacher' ? 'text-primary' : 'text-danger'; ?>">
                                                                <i class="bi <?= $category === 'teacher' ? 'bi-person-bounding-box' : 'bi-record-circle'; ?> me-1"></i>
                                                                <?= $category === 'teacher' ? '1. Posisi &amp; Ukuran Area Foto Guru' : '1. Posisi &amp; Diameter Bulatan Merah (Latar di Kartu)'; ?>
                                                            </span>
                                                            <span class="badge <?= $category === 'teacher' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'; ?> small" style="font-size: 10px;">
                                                                <?= $category === 'teacher' ? 'Area Foto Guru' : 'Garis Lingkaran'; ?>
                                                            </span>
                                                        </div>
                                                        <div class="row g-2">
                                                            <!-- Diameter / Lebar Bulatan -->
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-circle-width-<?= esc($category); ?>">
                                                                        <?= $category === 'teacher' ? 'Lebar Area Foto' : 'Lebar / Diameter Bulatan'; ?>
                                                                    </label>
                                                                    <output class="badge <?= $category === 'teacher' ? 'bg-primary' : 'bg-danger'; ?>" data-circle-width-value="<?= esc($category); ?>"><?= esc(number_format((float) ($photoPosition['width'] ?? 50.87), 2, '.', '')); ?>%</output>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <button type="button" class="btn btn-sm <?= $category === 'teacher' ? 'btn-outline-primary' : 'btn-outline-danger'; ?> py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="width" data-delta="-0.5" title="Kecilkan (-0.5%)"><i class="bi bi-dash"></i></button>
                                                                    <input class="form-range id-card-circle-width flex-grow-1" id="id-card-circle-width-<?= esc($category); ?>" type="range" min="15" max="80" step="0.1" value="<?= esc((string) ($photoPosition['width'] ?? 50.87), 'attr'); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                                    <button type="button" class="btn btn-sm <?= $category === 'teacher' ? 'btn-outline-primary' : 'btn-outline-danger'; ?> py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="width" data-delta="0.5" title="Besarkan (+0.5%)"><i class="bi bi-plus"></i></button>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 10px;">
                                                                    <span>Kecil</span>
                                                                    <span class="text-danger fw-medium">Tarik Handel Kanan Bawah ↘</span>
                                                                    <span>Besar</span>
                                                                </div>
                                                            </div>

                                                            <!-- Posisi X Bulatan -->
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-circle-x-<?= esc($category); ?>">Posisi X Bulatan (Kiri / Kanan)</label>
                                                                    <output class="badge bg-secondary" data-circle-x-value="<?= esc($category); ?>"><?= esc(number_format((float) ($photoPosition['x'] ?? 22.10), 2, '.', '')); ?>%</output>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="x" data-delta="-0.5" title="Geser ke Kiri (-0.5%)"><i class="bi bi-chevron-left"></i></button>
                                                                    <input class="form-range id-card-circle-x flex-grow-1" id="id-card-circle-x-<?= esc($category); ?>" type="range" min="0" max="80" step="0.1" value="<?= esc((string) ($photoPosition['x'] ?? 22.10), 'attr'); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="x" data-delta="0.5" title="Geser ke Kanan (+0.5%)"><i class="bi bi-chevron-right"></i></button>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 10px;">
                                                                    <span>◀ Kiri</span>
                                                                    <span>Kanan ▶</span>
                                                                </div>
                                                            </div>

                                                            <!-- Posisi Y Bulatan -->
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-circle-y-<?= esc($category); ?>">Posisi Y Bulatan (Atas / Bawah)</label>
                                                                    <output class="badge bg-secondary" data-circle-y-value="<?= esc($category); ?>"><?= esc(number_format((float) ($photoPosition['y'] ?? 17.87), 2, '.', '')); ?>%</output>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="y" data-delta="-0.5" title="Geser Naik (-0.5%)"><i class="bi bi-chevron-up"></i></button>
                                                                    <input class="form-range id-card-circle-y flex-grow-1" id="id-card-circle-y-<?= esc($category); ?>" type="range" min="0" max="80" step="0.1" value="<?= esc((string) ($photoPosition['y'] ?? 17.87), 'attr'); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-circle-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-prop="y" data-delta="0.5" title="Geser Turun (+0.5%)"><i class="bi bi-chevron-down"></i></button>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 10px;">
                                                                    <span>▲ Naik (Atas)</span>
                                                                    <span>Turun (Bawah) ▼</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Bagian 2: Posisi & Zoom Foto di Dalam Bulatan -->
                                                    <div class="bg-white border rounded-2 p-2">
                                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                            <span class="small fw-bold text-primary">
                                                                <i class="bi bi-person-bounding-box me-1"></i>2. Pengaturan Foto Siswa di Dalam Latar Merah
                                                            </span>
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle small" style="font-size: 10px;">Crop &amp; Zoom</span>
                                                        </div>
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-photo-zoom-<?= esc($category); ?>">Zoom Foto Siswa</label>
                                                                    <output class="badge bg-secondary" data-photo-zoom-value="<?= esc($category); ?>"><?= esc(number_format((float) ($photoPosition['zoom'] ?? 1), 2, '.', '')); ?>x</output>
                                                                </div>
                                                                <input class="form-range id-card-photo-zoom" id="id-card-photo-zoom-<?= esc($category); ?>" type="range" min="0.5" max="2.5" step="0.05" value="<?= esc((string) ($photoPosition['zoom'] ?? 1), 'attr'); ?>" aria-label="Zoom foto <?= esc($categoryLabel); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-photo-pany-<?= esc($category); ?>">Geser Vertikal Foto (Tarik Bawah/Atas)</label>
                                                                    <?php
                                                                    $currentPanY = (float) ($photoPosition['panY'] ?? 0);
                                                                    $panYText = ($currentPanY >= 0 ? '+' : '') . number_format($currentPanY, 0, '.', '') . '%';
                                                                    if ($currentPanY > 0) $panYText .= ' (Turun)';
                                                                    elseif ($currentPanY < 0) $panYText .= ' (Naik)';
                                                                    else $panYText .= ' (Tengah)';
                                                                    ?>
                                                                    <output class="badge bg-primary" data-photo-pany-value="<?= esc($category); ?>"><?= esc($panYText); ?></output>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1 id-card-photo-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-axis="y" data-delta="-5" title="Tarik ke Atas (-5%)"><i class="bi bi-chevron-up"></i></button>
                                                                    <input class="form-range id-card-photo-pany flex-grow-1" id="id-card-photo-pany-<?= esc($category); ?>" type="range" min="-60" max="60" step="1" value="<?= esc((string) ($photoPosition['panY'] ?? 0), 'attr'); ?>" aria-label="Geser vertikal foto <?= esc($categoryLabel); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-1 id-card-photo-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-axis="y" data-delta="5" title="Tarik ke Bawah (+5%)"><i class="bi bi-chevron-down"></i></button>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 10px;">
                                                                    <span>▲ Naik</span>
                                                                    <span class="text-primary fw-medium">Tarik Turun ▼</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <label class="form-label small mb-0 fw-medium" for="id-card-photo-panx-<?= esc($category); ?>">Geser Horizontal Foto (Kiri/Kanan)</label>
                                                                    <?php
                                                                    $currentPanX = (float) ($photoPosition['panX'] ?? 0);
                                                                    $panXText = ($currentPanX >= 0 ? '+' : '') . number_format($currentPanX, 0, '.', '') . '%';
                                                                    ?>
                                                                    <output class="badge bg-info text-dark" data-photo-panx-value="<?= esc($category); ?>"><?= esc($panXText); ?></output>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-photo-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-axis="x" data-delta="-5" title="Geser ke Kiri (-5%)"><i class="bi bi-chevron-left"></i></button>
                                                                    <input class="form-range id-card-photo-panx flex-grow-1" id="id-card-photo-panx-<?= esc($category); ?>" type="range" min="-50" max="50" step="1" value="<?= esc((string) ($photoPosition['panX'] ?? 0), 'attr'); ?>" aria-label="Geser horizontal foto <?= esc($categoryLabel); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-1 id-card-photo-nudge" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-axis="x" data-delta="5" title="Geser ke Kanan (+5%)"><i class="bi bi-chevron-right"></i></button>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted mt-1" style="font-size: 10px;">
                                                                    <span>◀ Kiri</span>
                                                                    <span>Kanan ▶</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Pengaturan Barcode / QR Code dari RFID Tag -->
                                            <div class="card bg-light border p-3 mb-3 shadow-sm rounded-3">
                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2 mb-3">
                                                    <div>
                                                        <h5 class="h6 mb-0 text-dark fw-bold d-flex align-items-center">
                                                            <i class="bi bi-upc-scan text-primary me-2"></i>Pengaturan Barcode / QR Code (RFID Tag)
                                                        </h5>
                                                        <span class="text-muted" style="font-size: 11px;">
                                                            Data otomatis mengambil <strong>RFID Tag</strong> masing-masing <?= $category === 'teacher' ? 'guru' : 'siswa'; ?> yang sudah digenerate (fallback ke <?= $category === 'teacher' ? 'NIP/ID' : 'NIS/NISN'; ?> jika belum diisi).
                                                        </span>
                                                    </div>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small" style="font-size: 10px;">
                                                        Sampel: <?= esc($sampleCodeVal); ?>
                                                    </span>
                                                </div>

                                                <!-- Pilihan Tipe: Barcode vs QR Code vs Sembunyikan -->
                                                <div class="bg-white border rounded-2 p-2 mb-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                                        <span class="small fw-bold text-dark">
                                                            <i class="bi bi-qr-code text-primary me-1"></i>Pilihan Tampilan Kode
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Tipe Kode">
                                                            <button type="button" class="btn <?= ($codeSettings['type'] ?? 'barcode') === 'barcode' ? 'btn-primary' : 'btn-outline-primary'; ?> id-card-code-type-btn" data-category="<?= esc($category); ?>" data-type="barcode">
                                                                <i class="bi bi-upc me-1"></i>Barcode
                                                            </button>
                                                            <button type="button" class="btn <?= ($codeSettings['type'] ?? 'barcode') === 'qrcode' ? 'btn-primary' : 'btn-outline-primary'; ?> id-card-code-type-btn" data-category="<?= esc($category); ?>" data-type="qrcode">
                                                                <i class="bi bi-qr-code me-1"></i>QR Code
                                                            </button>
                                                            <button type="button" class="btn <?= ($codeSettings['type'] ?? 'barcode') === 'none' ? 'btn-secondary' : 'btn-outline-secondary'; ?> id-card-code-type-btn" data-category="<?= esc($category); ?>" data-type="none">
                                                                <i class="bi bi-eye-slash me-1"></i>Sembunyikan
                                                            </button>
                                                        </div>
                                                        <div class="form-check form-switch mb-0">
                                                            <input class="form-check-input id-card-code-showtext" type="checkbox" id="code-showtext-<?= esc($category); ?>" data-category="<?= esc($category); ?>" <?= ! empty($codeSettings['showText']) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small fw-medium" for="code-showtext-<?= esc($category); ?>">Tampilkan nomor di bawah kode</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Slider Ukuran & Posisi Barcode / QR Code -->
                                                <div class="bg-white border rounded-2 p-2" id="code-controls-box-<?= esc($category); ?>" style="<?= ($codeSettings['type'] ?? 'barcode') === 'none' ? 'opacity: 0.5; pointer-events: none;' : ''; ?>">
                                                    <div class="row g-2">
                                                        <div class="col-md-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label small mb-0 fw-medium" for="id-card-code-width-<?= esc($category); ?>">Lebar Kode</label>
                                                                <output class="badge bg-secondary" data-code-width-value="<?= esc($category); ?>"><?= esc(number_format((float) ($codeSettings['width'] ?? 50), 2, '.', '')); ?>%</output>
                                                            </div>
                                                            <input class="form-range id-card-code-width" id="id-card-code-width-<?= esc($category); ?>" type="range" min="15" max="85" step="1" value="<?= esc((string) ($codeSettings['width'] ?? 50)); ?>" data-category="<?= esc($category); ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label small mb-0 fw-medium" for="id-card-code-height-<?= esc($category); ?>">Tinggi Kode</label>
                                                                <output class="badge bg-secondary" data-code-height-value="<?= esc($category); ?>"><?= esc(number_format((float) ($codeSettings['height'] ?? 12), 2, '.', '')); ?>%</output>
                                                            </div>
                                                            <input class="form-range id-card-code-height" id="id-card-code-height-<?= esc($category); ?>" type="range" min="5" max="30" step="0.5" value="<?= esc((string) ($codeSettings['height'] ?? 12)); ?>" data-category="<?= esc($category); ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label small mb-0 fw-medium" for="id-card-code-x-<?= esc($category); ?>">Posisi X</label>
                                                                <output class="badge bg-secondary" data-code-x-value="<?= esc($category); ?>"><?= esc(number_format((float) ($codeSettings['x'] ?? 25), 2, '.', '')); ?>%</output>
                                                            </div>
                                                            <input class="form-range id-card-code-x" id="id-card-code-x-<?= esc($category); ?>" type="range" min="0" max="80" step="0.5" value="<?= esc((string) ($codeSettings['x'] ?? 25)); ?>" data-category="<?= esc($category); ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <label class="form-label small mb-0 fw-medium" for="id-card-code-y-<?= esc($category); ?>">Posisi Y</label>
                                                                <output class="badge bg-secondary" data-code-y-value="<?= esc($category); ?>"><?= esc(number_format((float) ($codeSettings['y'] ?? 84), 2, '.', '')); ?>%</output>
                                                            </div>
                                                            <input class="form-range id-card-code-y" id="id-card-code-y-<?= esc($category); ?>" type="range" min="0" max="95" step="0.5" value="<?= esc((string) ($codeSettings['y'] ?? 84)); ?>" data-category="<?= esc($category); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted mt-2" style="font-size: 10px;">
                                                        <span><i class="bi bi-arrows-move me-1"></i>Anda juga dapat menggeser langsung Barcode/QR Code pada kartu di atas</span>
                                                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none id-card-code-reset" data-category="<?= esc($category); ?>">Reset Posisi Kode</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <div class="id-card-layout-empty">Unggah template sisi <?= esc(strtolower($sideLabel)); ?> untuk menampilkan pratinjau.</div>
                                        <?php endif; ?>
                                        <div class="row g-2 mt-2">
                                            <?php foreach ($samples[$category][$side] as $field => $sample) : ?>
                                                <?php $position = $layout[$category][$side][$field]; ?>
                                                <div class="col-md-6">
                                                    <label class="form-label mb-1 small fw-medium"><?= esc($sample); ?> — font &amp; warna</label>
                                                    <select class="form-select form-select-sm id-card-font-family" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-field="<?= esc($field); ?>">
                                                        <?php foreach ($fontOptions as $fontValue => $fontLabel) : ?>
                                                            <option value="<?= esc($fontValue); ?>" <?= $position['fontFamily'] === $fontValue ? 'selected' : ''; ?>><?= esc($fontLabel); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="input-group input-group-sm mt-1">
                                                        <span class="input-group-text py-0 px-2 text-muted" style="font-size: 11px; min-width: 60px;">Size</span>
                                                        <input class="form-control form-control-sm id-card-font-size" type="number" min="1" max="8" step="0.01" value="<?= esc((string) $position['fontSize'], 'attr'); ?>" placeholder="Ukuran mm" aria-label="Ukuran font <?= esc($sample); ?> dalam mm" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-field="<?= esc($field); ?>">
                                                        <span class="input-group-text py-0 px-2 text-muted" style="font-size: 11px;">mm</span>
                                                    </div>
                                                    <div class="input-group input-group-sm mt-1">
                                                        <span class="input-group-text py-0 px-2 text-muted" style="font-size: 11px; min-width: 60px;">Warna</span>
                                                        <input class="form-control form-control-color form-control-sm id-card-font-color" type="color" value="<?= esc($position['fontColor'] ?? '#102b58', 'attr'); ?>" title="Pilih warna teks <?= esc($sample); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-field="<?= esc($field); ?>" style="height: 31px; width: 44px; padding: 2px; flex: 0 0 44px;">
                                                        <input class="form-control form-control-sm text-uppercase id-card-font-color-hex" type="text" maxlength="7" value="<?= esc($position['fontColor'] ?? '#102b58', 'attr'); ?>" placeholder="#102b58" aria-label="Kode warna heksadesimal <?= esc($sample); ?>" data-category="<?= esc($category); ?>" data-side="<?= esc($side); ?>" data-field="<?= esc($field); ?>" style="font-family: monospace; font-size: 12px;">
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-2" aria-hidden="true"></i>Simpan Posisi, Font &amp; Warna</button>
                        <span class="form-text">Ukuran font dalam milimeter dan pilihan warna teks pada kartu cetak.</span>
                    </div>
                </form>
            <?php else : ?>
                <div class="alert alert-info mb-0">Hubungi administrator untuk mengatur posisi dan font template.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $studentClasses = [];
    foreach ($students as $st) {
        $className = trim((string) ($st['kelas'] ?? ''));
        if ($className !== '' && ! in_array($className, $studentClasses, true)) {
            $studentClasses[] = $className;
        }
    }
    sort($studentClasses);
    ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 card-title mb-0">Daftar ID Card Siswa</h2>
                <small class="text-muted">Pilih kategori SMK atau SMP pada setiap siswa sebelum mencetak.</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm" id="btn-export-zip-student" title="Cetak dan kompres semua ID Card siswa ke file ZIP">
                    <i class="bi bi-file-earmark-zip-fill"></i> Cetak Semua (.ZIP)
                </button>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle id-card-student-count-badge" data-total="<?= count($students); ?>"><?= count($students); ?> siswa</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter & Search Siswa -->
            <div class="row g-2 mb-3">
                <div class="col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control border-start-0 ps-0" id="id-card-student-search" placeholder="Cari nama siswa, NIS, atau kelas..." aria-label="Cari nama siswa atau NIS" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="id-card-student-search-clear" title="Bersihkan pencarian" style="display: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <?php if ($studentClasses !== []) : ?>
                    <div class="col-md-4 col-lg-3">
                        <select class="form-select" id="id-card-student-class-filter" aria-label="Filter berdasarkan kelas">
                            <option value="">Semua Kelas (<?= count($students); ?>)</option>
                            <?php foreach ($studentClasses as $cls) : ?>
                                <option value="<?= esc(strtolower($cls)); ?>">Kelas <?= esc($cls); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="id-card-student-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No.</th>
                            <th>Nama Siswa</th>
                            <th style="width: 140px;">NIS</th>
                            <th style="width: 90px;" class="text-center">Kelas</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 230px;" class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student) : ?>
                            <tr class="id-card-student-row"
                                data-name="<?= esc(strtolower((string) ($student['nama_siswa'] ?? ''))); ?>"
                                data-nis="<?= esc(strtolower((string) ($student['nis'] ?? ''))); ?>"
                                data-nisn="<?= esc(strtolower((string) ($student['nisn'] ?? ''))); ?>"
                                data-class="<?= esc(strtolower(trim((string) ($student['kelas'] ?? '')))); ?>"
                                data-status="<?= esc(strtolower((string) ($student['status'] ?? ''))); ?>">
                                <td class="text-center id-card-student-num"><?= $index + 1; ?></td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($student['nama_siswa'] ?? '-'); ?></div>
                                </td>
                                <td><?= esc($student['nis'] ?? '-'); ?></td>
                                <td class="text-center"><?= esc($student['kelas'] ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php if (($student['status'] ?? '') === 'aktif') : ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1 flex-nowrap">
                                        <select class="form-select form-select-sm id-card-student-category" style="width: 76px; font-size: 12px; padding: 0.2rem 1.25rem 0.2rem 0.45rem; height: 30px;" aria-label="Kategori ID card <?= esc($student['nama_siswa'] ?? 'siswa'); ?>">
                                            <option value="smk">SMK</option>
                                            <option value="smp" selected>SMP</option>
                                        </select>
                                        <a class="btn btn-sm btn-success text-nowrap id-card-student-jpg <?= $templateReady['smp'] ? '' : 'disabled'; ?>"
                                            style="font-size: 12px; padding: 0.2rem 0.55rem; height: 30px; line-height: 1.5;"
                                            data-student-id="<?= (int) $student['siswa_id']; ?>"
                                            href="<?= $templateReady['smp'] ? site_url('student-id-cards/print/' . (int) $student['siswa_id'] . '/smp?download=1') : '#'; ?>"
                                            target="_blank"
                                            title="Unduh ID Card format JPG"
                                            aria-disabled="<?= $templateReady['smp'] ? 'false' : 'true'; ?>">
                                            <i class="bi bi-file-earmark-image me-1" aria-hidden="true"></i>JPG
                                        </a>
                                        <a class="btn btn-sm btn-outline-primary text-nowrap id-card-student-print <?= $templateReady['smp'] ? '' : 'disabled'; ?>"
                                            style="font-size: 12px; padding: 0.2rem 0.55rem; height: 30px; line-height: 1.5;"
                                            data-student-id="<?= (int) $student['siswa_id']; ?>"
                                            href="<?= $templateReady['smp'] ? site_url('student-id-cards/print/' . (int) $student['siswa_id'] . '/smp') : '#'; ?>"
                                            target="_blank"
                                            title="Buka Pratinjau & Cetak ID Card"
                                            aria-disabled="<?= $templateReady['smp'] ? 'false' : 'true'; ?>">
                                            <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="id-card-student-search-empty" hidden>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-search me-2"></i>Tidak ada siswa yang cocok dengan pencarian "<span class="search-query fw-bold"></span>".
                            </td>
                        </tr>
                        <?php if ($students === []) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data siswa.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 card-title mb-0">Daftar ID Card Guru</h2>
                <small class="text-muted">Kartu guru menggunakan nama, NIP, email, dan telepon dari data guru.</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 shadow-sm" id="btn-export-zip-teacher" title="Cetak dan kompres semua ID Card guru ke file ZIP">
                    <i class="bi bi-file-earmark-zip-fill"></i> Cetak Semua (.ZIP)
                </button>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle id-card-teacher-count-badge" data-total="<?= count($teachers); ?>"><?= count($teachers); ?> guru</span>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter & Search Guru -->
            <div class="row g-2 mb-3">
                <div class="col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control border-start-0 ps-0" id="id-card-teacher-search" placeholder="Cari nama guru, NIP, email, atau telepon..." aria-label="Cari nama guru atau NIP" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="id-card-teacher-search-clear" title="Bersihkan pencarian" style="display: none;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="id-card-teacher-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No.</th>
                            <th>Nama Guru</th>
                            <th style="width: 140px;">NIP</th>
                            <th>Email</th>
                            <th style="width: 140px;">Telepon</th>
                            <th style="width: 170px;" class="text-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $index => $teacher) : ?>
                            <tr class="id-card-teacher-row"
                                data-name="<?= esc(strtolower((string) ($teacher['guru_nama'] ?? ''))); ?>"
                                data-nip="<?= esc(strtolower((string) ($teacher['guru_nip'] ?? ''))); ?>"
                                data-email="<?= esc(strtolower((string) ($teacher['guru_email'] ?? ''))); ?>"
                                data-phone="<?= esc(strtolower((string) ($teacher['guru_telepon'] ?? ''))); ?>">
                                <td class="text-center id-card-teacher-num"><?= $index + 1; ?></td>
                                <td><div class="fw-medium text-dark"><?= esc($teacher['guru_nama'] ?? '-'); ?></div></td>
                                <td><?= esc($teacher['guru_nip'] ?? '-'); ?></td>
                                <td><?= esc($teacher['guru_email'] ?? '-'); ?></td>
                                <td><?= esc($teacher['guru_telepon'] ?? '-'); ?></td>
                                <td class="text-nowrap text-center">
                                    <?php if ($templateReady['teacher']) : ?>
                                        <div class="d-inline-flex align-items-center justify-content-center gap-1 flex-nowrap">
                                            <a class="btn btn-sm btn-success text-nowrap" style="font-size: 12px; padding: 0.2rem 0.55rem; height: 30px; line-height: 1.5;" href="<?= site_url('student-id-cards/teacher/print/' . (int) $teacher['guru_id'] . '?download=1'); ?>" target="_blank" title="Unduh ID Card format JPG">
                                                <i class="bi bi-file-earmark-image me-1" aria-hidden="true"></i>JPG
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary text-nowrap" style="font-size: 12px; padding: 0.2rem 0.55rem; height: 30px; line-height: 1.5;" href="<?= site_url('student-id-cards/teacher/print/' . (int) $teacher['guru_id']); ?>" target="_blank" title="Buka Pratinjau & Cetak ID Card">
                                                <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <button class="btn btn-sm btn-outline-secondary text-nowrap" style="font-size: 12px; padding: 0.2rem 0.75rem; height: 30px; line-height: 1.5;" type="button" disabled>Cetak</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="id-card-teacher-search-empty" hidden>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-search me-2"></i>Tidak ada guru yang cocok dengan pencarian "<span class="search-query fw-bold"></span>".
                            </td>
                        </tr>
                        <?php if ($teachers === []) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data guru.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ekspor Bulk ZIP ID Card -->
<div class="modal fade" id="bulkZipModal" tabindex="-1" aria-labelledby="bulkZipModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-file-earmark-zip-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="bulkZipModalLabel">Cetak Semua ID Card (.ZIP)</h5>
                        <small class="text-muted" id="bulkZipModalSubLabel">Kompres semua kartu ID ke format gambar JPG dalam satu file ZIP</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" id="btn-close-bulk-modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- VIEW 1: CONFIG VIEW -->
                <div id="bulk-zip-config-view">
                    <div class="alert alert-primary-subtle border border-primary-subtle text-primary-emphasis d-flex align-items-start gap-2 py-2 px-3 mb-3 small rounded-3">
                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 mt-1"></i>
                        <div>Setiap kartu akan dirender sesuai dengan tata letak (layout), foto, warna font, barcode/QR code, dan disimpan sebagai file JPG beresolusi tinggi di dalam file ZIP.</div>
                    </div>

                    <!-- Pilihan Target / Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-2">1. Pilih Data yang Ingin Dicetak</label>
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="bulk-scope" id="bulk-scope-all" value="all" checked>
                                <label class="form-check-label fw-medium text-dark" for="bulk-scope-all" id="bulk-scope-all-label">
                                    Semua Data (0)
                                </label>
                            </div>
                            <div class="form-check mb-0" id="bulk-scope-filtered-wrapper">
                                <input class="form-check-input" type="radio" name="bulk-scope" id="bulk-scope-filtered" value="filtered">
                                <label class="form-check-label fw-medium text-dark" for="bulk-scope-filtered" id="bulk-scope-filtered-label">
                                    Hanya yang Sedang Tampil / Terfilter (0)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Template (Hanya untuk Siswa) -->
                    <div class="mb-3" id="bulk-student-category-group">
                        <label class="form-label fw-semibold mb-2">2. Pilihan Kategori Template</label>
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="bulk-category-mode" id="bulk-cat-table" value="table" checked>
                                <label class="form-check-label text-dark" for="bulk-cat-table">
                                    <strong>Sesuai Pilihan di Tabel</strong> (SMP / SMK per siswa)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="bulk-category-mode" id="bulk-cat-smp" value="smp">
                                <label class="form-check-label text-dark" for="bulk-cat-smp">
                                    Paksa Semua Menggunakan Template <strong>SMP</strong>
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="radio" name="bulk-category-mode" id="bulk-cat-smk" value="smk">
                                <label class="form-check-label text-dark" for="bulk-cat-smk">
                                    Paksa Semua Menggunakan Template <strong>SMK</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Kualitas Gambar -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-2" for="bulk-quality-select">3. Kualitas Gambar</label>
                        <select class="form-select" id="bulk-quality-select">
                            <option value="3" selected>Kualitas Tinggi / 300 DPI (Sangat Tajam &amp; Siap Cetak - Rekomendasi)</option>
                            <option value="2">Kualitas Standar / 150 DPI (Ukuran Lebih Ringan &amp; Cepat)</option>
                        </select>
                    </div>
                </div>

                <!-- VIEW 2: PROGRESS VIEW -->
                <div id="bulk-zip-progress-view" class="d-none text-center py-2">
                    <div class="spinner-border text-success mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="fw-bold mb-1" id="bulk-zip-current-action">Sedang Merender ID Card...</h5>
                    <p class="text-muted small mb-3" id="bulk-zip-current-person">Mempersiapkan template &amp; data...</p>

                    <div class="progress mb-2" style="height: 24px; border-radius: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success fw-bold" id="bulk-zip-progress-bar" role="progressbar" style="width: 0%;">0%</div>
                    </div>

                    <div class="d-flex justify-content-between small text-muted px-1">
                        <span id="bulk-zip-progress-count">0 / 0 kartu diproses</span>
                        <span id="bulk-zip-progress-percent">0%</span>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mt-3 mb-0 text-start rounded-3">
                        <i class="bi bi-info-circle-fill me-1"></i> Jangan menutup halaman ini sampai proses selesai. File ZIP akan otomatis diunduh ke komputer Anda.
                    </div>
                </div>

                <!-- VIEW 3: DONE VIEW -->
                <div id="bulk-zip-done-view" class="d-none text-center py-3">
                    <div class="text-success mb-3">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Kompresi Selesai!</h4>
                    <p class="text-muted mb-4" id="bulk-zip-done-message">Semua file ID Card telah berhasil dirender dan diunduh dalam file ZIP.</p>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <div id="bulk-footer-config" class="w-100 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm" id="btn-start-bulk-zip">
                        <i class="bi bi-file-earmark-zip-fill"></i> Mulai Cetak &amp; Kompres (.ZIP)
                    </button>
                </div>
                <div id="bulk-footer-progress" class="w-100 d-flex justify-content-center d-none">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-cancel-bulk-zip">
                        <i class="bi bi-x-circle me-1"></i> Hentikan Proses
                    </button>
                </div>
                <div id="bulk-footer-done" class="w-100 d-flex justify-content-end d-none">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offscreen Stage untuk Bulk Render ID Card ke JPG -->
<div id="bulk-card-stage-wrapper" style="position: fixed; left: -99999px; top: 0; pointer-events: none; z-index: -9999; opacity: 1;">
    <div id="bulk-card-page" class="card-page" style="--card-width: 85.6mm; --card-height: 54mm; width: var(--card-width); height: var(--card-height); overflow: hidden; position: relative; background: #ffffff;">
        <div id="bulk-card-el" class="id-card" style="--card-width: 85.6mm; --card-height: 54mm; width: var(--card-width); height: var(--card-height); position: relative; overflow: hidden; background: #ffffff;">
            <img id="bulk-card-template-img" class="id-card-template" src="" alt="" crossorigin="anonymous" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: fill; pointer-events: none;">
            
            <div id="bulk-card-photo-box" class="card-photo is-circle" style="position: absolute; display: flex; align-items: center; justify-content: center; overflow: hidden; z-index: 2; background: #e60000;">
                <img id="bulk-card-photo-img" class="front-photo" src="" alt="Foto" crossorigin="anonymous" style="display: block; width: 100%; min-width: 100%; min-height: 100%; object-fit: cover; flex-shrink: 0; pointer-events: none;">
                <div id="bulk-card-photo-placeholder" class="photo-placeholder" style="display: none; font-size: 7mm; font-weight: 700; color: #ffffff;">A</div>
            </div>

            <!-- Text Fields -->
            <div id="bulk-card-field-name" class="card-field name" style="position: absolute; z-index: 3;"></div>
            <div id="bulk-card-field-nis" class="card-field" style="position: absolute; z-index: 3;"></div>
            <div id="bulk-card-field-nisn" class="card-field" style="position: absolute; z-index: 3;"></div>
            <div id="bulk-card-field-birth" class="card-field" style="position: absolute; z-index: 3;"></div>
            <div id="bulk-card-field-address" class="card-field" style="position: absolute; z-index: 3;"></div>
            <div id="bulk-card-field-nip" class="card-field" style="position: absolute; z-index: 3; display: none;"></div>
            <div id="bulk-card-field-email" class="card-field" style="position: absolute; z-index: 3; display: none;"></div>

            <!-- Code Container (Barcode / QR Code) -->
            <div id="bulk-card-code-container" class="card-code-container" style="position: absolute; z-index: 3; display: flex; align-items: center; justify-content: center; overflow: hidden; pointer-events: none;">
                <div class="code-inner" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;">
                    <svg id="bulk-barcode-svg" style="display: none; max-width: 100%; max-height: 100%;"></svg>
                    <div id="bulk-qrcode-canvas" style="display: none; max-width: 100%; max-height: 85%;"></div>
                    <span id="bulk-code-label" class="code-label" style="display: none; font-size: 2.2mm; font-weight: 700; color: #111827; margin-top: 0.5mm; font-family: monospace, Arial, sans-serif;"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Bulk Render Offscreen Stage Styles */
    #bulk-card-stage-wrapper * {
        box-sizing: border-box;
    }
    #bulk-card-stage-wrapper .card-page {
        height: var(--card-height);
        overflow: hidden;
        position: relative;
        width: var(--card-width);
        background: #ffffff;
    }
    #bulk-card-stage-wrapper .id-card {
        border: none;
        height: var(--card-height);
        overflow: hidden;
        position: relative;
        width: var(--card-width);
        background: #ffffff;
    }
    #bulk-card-stage-wrapper .id-card-template {
        height: 100%;
        left: 0;
        object-fit: fill;
        pointer-events: none;
        position: absolute;
        top: 0;
        width: 100%;
    }
    #bulk-card-stage-wrapper .card-photo {
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
    #bulk-card-stage-wrapper .card-photo.is-circle {
        border-radius: 50% !important;
    }
    #bulk-card-stage-wrapper .card-photo.is-square {
        border-radius: 0% !important;
    }
    #bulk-card-stage-wrapper .card-photo.is-teacher-photo {
        background: transparent !important;
    }
    #bulk-card-stage-wrapper .card-code-container {
        align-items: center;
        display: flex;
        justify-content: center;
        overflow: hidden;
        position: absolute;
        z-index: 3;
        pointer-events: none;
    }
    #bulk-card-stage-wrapper .card-code-container .code-inner {
        align-items: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    #bulk-card-stage-wrapper .card-code-container svg {
        display: block;
        max-width: 100%;
        max-height: 100%;
        height: auto;
        width: 100%;
    }
    #bulk-card-stage-wrapper .card-code-container #bulk-qrcode-canvas {
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 100%;
        max-height: 85%;
    }
    #bulk-card-stage-wrapper .card-code-container #bulk-qrcode-canvas canvas,
    #bulk-card-stage-wrapper .card-code-container #bulk-qrcode-canvas img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    #bulk-card-stage-wrapper .card-code-container .code-label {
        font-size: 2.2mm;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #111827;
        text-align: center;
        line-height: 1.1;
        margin-top: 0.5mm;
        font-family: monospace, Arial, sans-serif;
    }
    #bulk-card-stage-wrapper .front-photo {
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
    #bulk-card-stage-wrapper .photo-placeholder {
        align-items: center;
        color: #ffffff;
        display: flex;
        font-size: 7mm;
        font-weight: 700;
        justify-content: center;
    }
    #bulk-card-stage-wrapper .card-field {
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
    #bulk-card-stage-wrapper .card-field.name {
        background: rgba(255, 255, 255, 0.92);
        font-weight: 700;
    }
    .id-card-template-preview {
        align-items: center;
        background: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: .5rem;
        display: flex;
        height: 150px;
        justify-content: center;
        overflow: hidden;
        padding: .5rem
    }

    .id-card-template-preview img {
        height: 100%;
        max-width: 100%;
        object-fit: contain
    }

    .id-card-layout-stage {
        background: #f8f9fa;
        border: 1px dashed #adb5bd;
        max-width: 100%;
        overflow: hidden;
        position: relative;
        touch-action: none;
        width: min(100%, var(--stage-max-width));
        aspect-ratio: var(--template-ratio)
    }

    .id-card-layout-background {
        height: 100%;
        inset: 0;
        object-fit: fill;
        pointer-events: none;
        position: absolute;
        width: 100%
    }

    .id-card-layout-field {
        background: rgba(255, 255, 255, .82);
        border: 1px dashed #0d6efd;
        border-radius: 3px;
        color: #102b58;
        cursor: grab;
        line-height: 1.25;
        max-width: 88%;
        padding: 2px 4px;
        position: absolute;
        user-select: none;
        white-space: normal;
        z-index: 1
    }

    .id-card-layout-field.is-name {
        font-weight: 700
    }

    .id-card-layout-photo {
        align-items: center;
        aspect-ratio: 1;
        background: #e60000;
        border: 2px dashed #0d6efd;
        border-radius: 50%;
        color: #fff;
        cursor: move;
        display: flex;
        font-size: clamp(8px, 2.4vw, 13px);
        justify-content: center;
        min-height: 12px;
        min-width: 12px;
        overflow: hidden;
        position: absolute;
        touch-action: none;
        user-select: none;
        z-index: 2;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.4);
    }

    .id-card-layout-photo.is-teacher-photo {
        background: transparent !important;
        box-shadow: none !important;
        color: #1f2937 !important;
    }

    .id-card-layout-photo.is-teacher-photo:hover {
        background: rgba(13, 110, 253, 0.04) !important;
        box-shadow: 0 0 0 2px #fff, 0 0 10px rgba(13, 110, 253, 0.4) !important;
    }

    .id-card-layout-photo.is-circle {
        border-radius: 50% !important;
    }

    .id-card-layout-photo.is-square {
        border-radius: 4px !important;
    }

    .id-card-layout-code {
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        border: 1.5px dashed #0d6efd;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
        cursor: move;
        display: flex;
        justify-content: center;
        overflow: hidden;
        padding: 3px 5px;
        position: absolute;
        touch-action: none;
        user-select: none;
        z-index: 2;
    }

    .id-card-layout-code:hover {
        box-shadow: 0 0 0 2px #fff, 0 0 8px rgba(13, 110, 253, 0.6);
    }

    .id-card-layout-code:active {
        cursor: grabbing;
    }

    .code-preview-wrap {
        align-items: center;
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: center;
        overflow: hidden;
        width: 100%;
    }

    .preview-barcode-svg {
        height: 100%;
        max-height: 80%;
        max-width: 100%;
        width: 100%;
    }

    .preview-qrcode-box {
        align-items: center;
        display: flex;
        height: 100%;
        justify-content: center;
        max-height: 100%;
        max-width: 100%;
    }

    .preview-qrcode-box img,
    .preview-qrcode-box canvas {
        height: auto;
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .preview-code-text {
        color: #111827;
        font-family: monospace;
        font-size: 8px;
        font-weight: 600;
        letter-spacing: 0.5px;
        line-height: 1;
        margin-top: 2px;
        text-align: center;
    }

    .id-card-code-move-handle {
        align-items: center;
        background: #0d6efd;
        border: 1px solid #fff;
        border-radius: 3px;
        color: #fff;
        cursor: move;
        display: flex;
        font-size: 9px;
        height: 15px;
        justify-content: center;
        left: 2px;
        position: absolute;
        top: 2px;
        width: 15px;
        z-index: 3;
    }

    .id-card-layout-photo[data-drag-mode="photo"] {
        cursor: grab;
    }

    .id-card-layout-photo:hover {
        box-shadow: 0 0 0 2px #fff, 0 0 10px rgba(13, 110, 253, 0.6);
    }

    .id-card-layout-photo:active,
    .id-card-layout-field:active {
        cursor: grabbing;
    }

    .id-card-layout-photo img {
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

    .id-card-layout-photo>span:not(.id-card-photo-resize-handle):not(.id-card-photo-move-handle) {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .id-card-photo-move-handle {
        align-items: center;
        background: #0d6efd;
        border: 1.5px solid #fff;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
        color: #fff;
        cursor: move;
        display: flex;
        font-size: 10px;
        height: 18px;
        justify-content: center;
        left: 4px;
        line-height: 1;
        position: absolute;
        top: 4px;
        width: 18px;
        z-index: 4;
    }

    .id-card-photo-resize-handle {
        background: #0d6efd;
        border: 1.5px solid #fff;
        border-radius: 3px;
        bottom: 2px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
        cursor: nwse-resize;
        height: 14px;
        position: absolute;
        right: 2px;
        width: 14px;
        z-index: 4;
    }

    .id-card-layout-field:focus-visible {
        outline: 2px solid #084298;
        outline-offset: 2px
    }

    .id-card-layout-empty {
        align-items: center;
        background: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: .5rem;
        color: #6c757d;
        display: flex;
        justify-content: center;
        min-height: 150px;
        padding: 1rem;
        text-align: center
    }

    .id-card-student-category {
        max-width: 110px
    }
</style>
<script src="<?= base_url('assets/js/JsBarcode.all.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/qrcode.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/html2canvas.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/jszip.min.js'); ?>"></script>
<script>
    (() => {
        const layout = <?= json_encode($layout, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const layoutInput = document.getElementById('id-card-layout-value');
        const categorySelect = document.getElementById('id-card-layout-category');
        const templateCategorySelect = document.getElementById('id-card-template-category');
        const updateInput = () => {
            if (layoutInput) layoutInput.value = JSON.stringify(layout);
        };
        const syncLayoutFromEditor = () => {
            document.querySelectorAll('.id-card-layout-stage').forEach((stage) => {
                const {
                    category,
                    side
                } = stage.dataset;
                stage.querySelectorAll('.id-card-layout-field, .id-card-layout-photo, .id-card-layout-code').forEach((field) => {
                    const settings = layout[category]?.[side]?.[field.dataset.field];
                    if (!settings) return;
                    const x = Number.parseFloat(field.style.left);
                    const y = Number.parseFloat(field.style.top);
                    if (Number.isFinite(x)) settings.x = x;
                    if (Number.isFinite(y)) settings.y = y;
                    if (field.dataset.field === 'photo') {
                        settings.shape = field.dataset.shape || settings.shape || 'circle';
                        const width = Number.parseFloat(field.style.width);
                        const widthControl = document.querySelector(`.id-card-circle-width[data-category="${category}"]`);
                        const xControl = document.querySelector(`.id-card-circle-x[data-category="${category}"]`);
                        const yControl = document.querySelector(`.id-card-circle-y[data-category="${category}"]`);
                        const zoomControl = document.querySelector(`.id-card-photo-zoom[data-category="${category}"]`);
                        const panXControl = document.querySelector(`.id-card-photo-panx[data-category="${category}"]`);
                        const panYControl = document.querySelector(`.id-card-photo-pany[data-category="${category}"]`);

                        if (widthControl && Number.isFinite(Number(widthControl.value))) settings.width = Number(widthControl.value);
                        else if (Number.isFinite(width)) settings.width = width;

                        if (xControl && Number.isFinite(Number(xControl.value))) settings.x = Number(xControl.value);
                        if (yControl && Number.isFinite(Number(yControl.value))) settings.y = Number(yControl.value);

                        const ratio = Number(stage.style.getPropertyValue('--template-ratio')) || 0.6246;
                        settings.height = Math.round(settings.width * ratio * 100) / 100;

                        if (zoomControl && Number.isFinite(Number(zoomControl.value))) settings.zoom = Number(zoomControl.value);
                        if (panXControl && Number.isFinite(Number(panXControl.value))) settings.panX = Number(panXControl.value);
                        else settings.panX = Number.parseFloat(field.style.getPropertyValue('--photo-pan-x')) || settings.panX || 0;
                        if (panYControl && Number.isFinite(Number(panYControl.value))) settings.panY = Number(panYControl.value);
                        else settings.panY = Number.parseFloat(field.style.getPropertyValue('--photo-pan-y')) || settings.panY || 0;
                    } else if (field.dataset.field === 'code') {
                        const widthCtrl = document.getElementById(`id-card-code-width-${category}`);
                        const heightCtrl = document.getElementById(`id-card-code-height-${category}`);
                        const xCtrl = document.getElementById(`id-card-code-x-${category}`);
                        const yCtrl = document.getElementById(`id-card-code-y-${category}`);
                        const showTextCtrl = document.getElementById(`code-showtext-${category}`);

                        if (widthCtrl && Number.isFinite(Number(widthCtrl.value))) settings.width = Number(widthCtrl.value);
                        if (heightCtrl && Number.isFinite(Number(heightCtrl.value))) settings.height = Number(heightCtrl.value);
                        if (xCtrl && Number.isFinite(Number(xCtrl.value))) settings.x = Number(xCtrl.value);
                        if (yCtrl && Number.isFinite(Number(yCtrl.value))) settings.y = Number(yCtrl.value);
                        if (showTextCtrl) settings.showText = showTextCtrl.checked;
                        settings.type = field.dataset.codeType || settings.type || 'barcode';
                    }
                });
            });
            document.querySelectorAll('.id-card-font-family, .id-card-font-size, .id-card-font-color').forEach((control) => {
                const {
                    category,
                    side,
                    field
                } = control.dataset;
                const settings = layout[category]?.[side]?.[field];
                if (!settings) return;
                if (control.classList.contains('id-card-font-family')) settings.fontFamily = control.value;
                else if (control.classList.contains('id-card-font-color')) {
                    settings.fontColor = control.value;
                    const hexInput = document.querySelector(`.id-card-font-color-hex[data-category="${category}"][data-side="${side}"][data-field="${field}"]`);
                    if (hexInput) hexInput.value = control.value.toUpperCase();
                }
                else if (control.value !== '' && Number.isFinite(Number(control.value))) settings.fontSize = Number(control.value);
            });
            updateInput();
        };
        const clamp = (value, max) => Math.min(Math.max(value, 0), max);
        const clampPan = (value) => Math.min(Math.max(value, -80), 80);
        const dragMode = {};
        const syncPhotoZoom = (field, settings) => {
            const zoom = Number(settings.zoom ?? 1);
            const panX = Number(settings.panX ?? 0);
            const panY = Number(settings.panY ?? 0);

            field.style.setProperty('--photo-zoom', String(zoom));
            field.style.setProperty('--photo-pan-x', `${panX.toFixed(2)}%`);
            field.style.setProperty('--photo-pan-y', `${panY.toFixed(2)}%`);
            const img = field.querySelector('img');
            if (img) {
                img.style.transform = `translate(${panX.toFixed(2)}%, ${panY.toFixed(2)}%) scale(${zoom})`;
            }
            const stageCategory = field.closest('.id-card-layout-stage')?.dataset.category;
            if (stageCategory) {
                const zoomValue = document.querySelector(`[data-photo-zoom-value="${stageCategory}"]`);
                if (zoomValue) zoomValue.textContent = `${zoom.toFixed(2)}x`;
                const zoomControl = document.querySelector(`.id-card-photo-zoom[data-category="${stageCategory}"]`);
                if (zoomControl) zoomControl.value = String(zoom);

                const panXValue = document.querySelector(`[data-photo-panx-value="${stageCategory}"]`);
                if (panXValue) panXValue.textContent = `${panX >= 0 ? '+' : ''}${panX.toFixed(0)}%`;
                const panXControl = document.querySelector(`.id-card-photo-panx[data-category="${stageCategory}"]`);
                if (panXControl) panXControl.value = String(panX);

                const panYValue = document.querySelector(`[data-photo-pany-value="${stageCategory}"]`);
                if (panYValue) {
                    let label = `${panY >= 0 ? '+' : ''}${panY.toFixed(0)}%`;
                    if (panY > 0) label += ' (Turun)';
                    else if (panY < 0) label += ' (Naik)';
                    else label += ' (Tengah)';
                    panYValue.textContent = label;
                }
                const panYControl = document.querySelector(`.id-card-photo-pany[data-category="${stageCategory}"]`);
                if (panYControl) panYControl.value = String(panY);
            }
        };
        const syncCircleVisuals = (stage, field, settings) => {
            const ratio = Number(stage.style.getPropertyValue('--template-ratio')) || 0.6246;
            settings.width = Math.min(80, Math.max(10, Math.round(Number(settings.width ?? 50.87) * 100) / 100));
            settings.height = Math.round(settings.width * ratio * 100) / 100;
            settings.x = Math.round(clamp(Number(settings.x ?? 22.10), 100 - settings.width) * 100) / 100;
            settings.y = Math.round(clamp(Number(settings.y ?? 17.87), 100 - settings.height) * 100) / 100;

            const shape = settings.shape || 'circle';
            field.dataset.shape = shape;
            field.classList.toggle('is-circle', shape === 'circle');
            field.classList.toggle('is-square', shape === 'square');

            field.style.left = `${settings.x.toFixed(2)}%`;
            field.style.top = `${settings.y.toFixed(2)}%`;
            field.style.width = `${settings.width.toFixed(2)}%`;
            field.style.height = `${settings.height.toFixed(2)}%`;

            const category = stage.dataset.category;
            if (category) {
                const widthValue = document.querySelector(`[data-circle-width-value="${category}"]`);
                if (widthValue) widthValue.textContent = `${settings.width.toFixed(2)}%`;
                const widthControl = document.querySelector(`.id-card-circle-width[data-category="${category}"]`);
                if (widthControl) widthControl.value = String(settings.width);

                const xValue = document.querySelector(`[data-circle-x-value="${category}"]`);
                if (xValue) xValue.textContent = `${settings.x.toFixed(2)}%`;
                const xControl = document.querySelector(`.id-card-circle-x[data-category="${category}"]`);
                if (xControl) xControl.value = String(settings.x);

                const yValue = document.querySelector(`[data-circle-y-value="${category}"]`);
                if (yValue) yValue.textContent = `${settings.y.toFixed(2)}%`;
                const yControl = document.querySelector(`.id-card-circle-y[data-category="${category}"]`);
                if (yControl) yControl.value = String(settings.y);
            }

            syncPhotoZoom(field, settings);
        };
        const setPhotoCircle = (field, settings, stage) => {
            syncCircleVisuals(stage, field, settings);
        };
        const syncCodeVisuals = (stage, codeEl, settings) => {
            const category = stage.dataset.category;
            const type = settings.type || 'barcode';
            const showText = Boolean(settings.showText);

            codeEl.style.left = `${Number(settings.x ?? 25).toFixed(2)}%`;
            codeEl.style.top = `${Number(settings.y ?? 84).toFixed(2)}%`;
            codeEl.style.width = `${Number(settings.width ?? 50).toFixed(2)}%`;
            codeEl.style.height = `${Number(settings.height ?? 12).toFixed(2)}%`;

            codeEl.dataset.codeType = type;
            codeEl.dataset.showText = showText ? '1' : '0';

            const barcodeSvg = codeEl.querySelector('.preview-barcode-svg');
            const qrcodeBox = codeEl.querySelector('.preview-qrcode-box');
            const codeText = codeEl.querySelector('.preview-code-text');

            if (type === 'none') {
                codeEl.classList.add('d-none');
            } else {
                codeEl.classList.remove('d-none');
                if (type === 'barcode') {
                    barcodeSvg?.classList.remove('d-none');
                    qrcodeBox?.classList.add('d-none');
                } else {
                    barcodeSvg?.classList.add('d-none');
                    qrcodeBox?.classList.remove('d-none');
                }
            }

            if (codeText) {
                codeText.classList.toggle('d-none', !showText);
            }

            const widthVal = document.querySelector(`[data-code-width-value="${category}"]`);
            if (widthVal) widthVal.textContent = `${Number(settings.width ?? 50).toFixed(0)}%`;
            const widthCtrl = document.getElementById(`id-card-code-width-${category}`);
            if (widthCtrl) widthCtrl.value = String(settings.width ?? 50);

            const heightVal = document.querySelector(`[data-code-height-value="${category}"]`);
            if (heightVal) heightVal.textContent = `${Number(settings.height ?? 12).toFixed(1)}%`;
            const heightCtrl = document.getElementById(`id-card-code-height-${category}`);
            if (heightCtrl) heightCtrl.value = String(settings.height ?? 12);

            const xVal = document.querySelector(`[data-code-x-value="${category}"]`);
            if (xVal) xVal.textContent = `${Number(settings.x ?? 25).toFixed(1)}%`;
            const xCtrl = document.getElementById(`id-card-code-x-${category}`);
            if (xCtrl) xCtrl.value = String(settings.x ?? 25);

            const yVal = document.querySelector(`[data-code-y-value="${category}"]`);
            if (yVal) yVal.textContent = `${Number(settings.y ?? 84).toFixed(1)}%`;
            const yCtrl = document.getElementById(`id-card-code-y-${category}`);
            if (yCtrl) yCtrl.value = String(settings.y ?? 84);

            const controlsBox = document.getElementById(`code-controls-box-${category}`);
            if (controlsBox) {
                controlsBox.style.opacity = type === 'none' ? '0.5' : '1';
                controlsBox.style.pointerEvents = type === 'none' ? 'none' : 'auto';
            }
        };
        const renderCodesInStages = () => {
            document.querySelectorAll('.id-card-layout-stage').forEach((stage) => {
                const category = stage.dataset.category;
                const side = stage.dataset.side;
                const codeEl = stage.querySelector('.id-card-layout-code');
                if (!codeEl) return;
                const codeVal = codeEl.dataset.codeVal || '1234567890';
                const settings = layout[category]?.[side]?.code || {
                    type: 'barcode',
                    showText: true,
                    width: 50,
                    height: 12,
                    x: 25,
                    y: 84
                };

                const barcodeSvg = document.getElementById(`preview-barcode-${category}`);
                if (barcodeSvg && typeof JsBarcode === 'function') {
                    try {
                        barcodeSvg.innerHTML = '';
                        JsBarcode(barcodeSvg, codeVal, {
                            format: "CODE128",
                            displayValue: false,
                            margin: 0,
                            background: "transparent",
                            lineColor: "#111827"
                        });
                    } catch (e) {
                        console.warn('JsBarcode error:', e);
                    }
                }

                const qrcodeBox = document.getElementById(`preview-qrcode-${category}`);
                if (qrcodeBox && typeof QRCode === 'function') {
                    qrcodeBox.innerHTML = '';
                    try {
                        new QRCode(qrcodeBox, {
                            text: codeVal,
                            width: 90,
                            height: 90,
                            colorDark: "#111827",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } catch (e) {
                        console.warn('QRCode error:', e);
                    }
                }

                syncCodeVisuals(stage, codeEl, settings);
            });
        };
        const styleText = (category, side, key) => {
            const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
            const field = stage?.querySelector(`.id-card-layout-field[data-field="${key}"]`);
            if (!stage || !field) return;
            const settings = layout[category][side][key];
            const pixelsPerMillimeter = stage.clientWidth / Number(stage.style.getPropertyValue('--card-width-mm'));
            field.style.fontFamily = settings.fontFamily;
            field.style.fontSize = `${settings.fontSize * pixelsPerMillimeter}px`;
            field.style.color = settings.fontColor || '#102b58';
            field.style.padding = `${0.5 * pixelsPerMillimeter}px ${pixelsPerMillimeter}px`;
            field.style.borderRadius = `${0.5 * pixelsPerMillimeter}px`;
        };
        const showCategory = () => {
            document.querySelectorAll('.id-card-layout-category').forEach((group) => {
                group.hidden = group.dataset.layoutCategory !== categorySelect.value;
            });
            requestAnimationFrame(() => {
                document.querySelectorAll(`.id-card-layout-stage[data-category="${categorySelect.value}"] .id-card-layout-field`).forEach((field) => {
                    const stage = field.closest('.id-card-layout-stage');
                    styleText(stage.dataset.category, stage.dataset.side, field.dataset.field);
                });
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${categorySelect.value}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (stage && photo) {
                    const settings = layout[categorySelect.value]?.front?.photo;
                    if (settings) syncCircleVisuals(stage, photo, settings);
                }
                renderCodesInStages();
            });
        };
        document.querySelectorAll('.id-card-layout-field').forEach((field) => {
            const stage = field.closest('.id-card-layout-stage');
            styleText(stage.dataset.category, stage.dataset.side, field.dataset.field);
        });
        categorySelect?.addEventListener('change', showCategory);
        templateCategorySelect?.addEventListener('change', () => {
            document.querySelectorAll('.id-card-template-category').forEach((group) => {
                group.hidden = group.dataset.templateCategory !== templateCategorySelect.value;
            });
        });
        document.querySelectorAll('.id-card-font-family, .id-card-font-size, .id-card-font-color').forEach((control) => {
            const update = () => {
                const {
                    category,
                    side,
                    field
                } = control.dataset;
                const settings = layout[category][side][field];
                if (control.classList.contains('id-card-font-family')) {
                    settings.fontFamily = control.value;
                } else if (control.classList.contains('id-card-font-color')) {
                    settings.fontColor = control.value;
                    const hexInput = document.querySelector(`.id-card-font-color-hex[data-category="${category}"][data-side="${side}"][data-field="${field}"]`);
                    if (hexInput) hexInput.value = control.value.toUpperCase();
                } else {
                    const size = Number(control.value);
                    if (!Number.isFinite(size) || size < 1 || size > 8) {
                        control.setCustomValidity('Masukkan ukuran antara 1 hingga 8 mm.');
                        return;
                    }
                    control.setCustomValidity('');
                    settings.fontSize = Math.round(size * 100) / 100;
                }
                styleText(category, side, field);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });

        document.querySelectorAll('.id-card-font-color-hex').forEach((hexInput) => {
            const handleHex = () => {
                const { category, side, field } = hexInput.dataset;
                const settings = layout[category]?.[side]?.[field];
                if (!settings) return;
                let val = hexInput.value.trim();
                if (val !== '' && !val.startsWith('#')) {
                    val = '#' + val;
                }
                if (/^#[0-9a-fA-F]{6}$/i.test(val)) {
                    settings.fontColor = val;
                    const colorPicker = document.querySelector(`.id-card-font-color[data-category="${category}"][data-side="${side}"][data-field="${field}"]`);
                    if (colorPicker) colorPicker.value = val;
                    styleText(category, side, field);
                    updateInput();
                }
            };
            hexInput.addEventListener('input', handleHex);
            hexInput.addEventListener('change', handleHex);
        });

        // Mode Drag Toggle
        document.querySelectorAll('.id-card-drag-mode-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category, mode } = btn.dataset;
                dragMode[category] = mode;
                document.querySelectorAll(`.id-card-drag-mode-btn[data-category="${category}"]`).forEach((b) => {
                    if (b.dataset.mode === mode) {
                        b.classList.remove('btn-outline-primary');
                        b.classList.add('btn-primary');
                    } else {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline-primary');
                    }
                });
                const hint = document.querySelector(`.id-card-drag-mode-hint[data-category="${category}"]`);
                if (hint) {
                    hint.innerHTML = mode === 'circle'
                        ? 'Mode aktif: <strong>Geser Bulatan di Kartu</strong> (Klik &amp; tarik lingkaran untuk memindahkan posisinya).'
                        : 'Mode aktif: <strong>Geser Foto di Dalam Latar</strong> (Klik &amp; tarik foto ke bawah/atas untuk menyesuaikan crop).';
                }
                const photo = document.querySelector(`.id-card-layout-stage[data-category="${category}"] .id-card-layout-photo`);
                if (photo) photo.setAttribute('data-drag-mode', mode);
            });
        });

        // Autofit Button
        document.querySelectorAll('.id-card-photo-autofit').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category, side } = btn.dataset;
                const settings = layout[category]?.[side]?.photo;
                if (!settings) return;
                if (category === 'smp') {
                    settings.x = 22.10;
                    settings.y = 17.87;
                    settings.width = 50.87;
                } else {
                    settings.width = 44.0;
                    settings.x = 28.0;
                    settings.y = 20.0;
                }
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (stage && photo) {
                    syncCircleVisuals(stage, photo, settings);
                }
                updateInput();
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Sudah Pas!';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                setTimeout(() => {
                    btn.innerHTML = origHtml;
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');
                }, 2000);
            });
        });

        // Circle Width Control
        document.querySelectorAll('.id-card-circle-width').forEach((control) => {
            const update = () => {
                const { category, side } = control.dataset;
                const settings = layout[category][side].photo;
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                settings.width = Math.round(Number(control.value) * 100) / 100;
                if (stage && photo) syncCircleVisuals(stage, photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });

        // Circle X Control
        document.querySelectorAll('.id-card-circle-x').forEach((control) => {
            const update = () => {
                const { category, side } = control.dataset;
                const settings = layout[category][side].photo;
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                settings.x = Math.round(Number(control.value) * 100) / 100;
                if (stage && photo) syncCircleVisuals(stage, photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });

        // Circle Y Control
        document.querySelectorAll('.id-card-circle-y').forEach((control) => {
            const update = () => {
                const { category, side } = control.dataset;
                const settings = layout[category][side].photo;
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                settings.y = Math.round(Number(control.value) * 100) / 100;
                if (stage && photo) syncCircleVisuals(stage, photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });

        // Circle Nudge (width, x, y)
        document.querySelectorAll('.id-card-circle-nudge').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category, side, prop, delta } = btn.dataset;
                const settings = layout[category][side].photo;
                const change = Number(delta) || 0;
                if (prop === 'width') {
                    settings.width = Math.round((settings.width + change) * 100) / 100;
                } else if (prop === 'x') {
                    settings.x = Math.round((settings.x + change) * 100) / 100;
                } else if (prop === 'y') {
                    settings.y = Math.round((settings.y + change) * 100) / 100;
                }
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (stage && photo) syncCircleVisuals(stage, photo, settings);
                updateInput();
            });
        });

        document.querySelectorAll('.id-card-photo-zoom').forEach((control) => {
            const update = () => {
                const {
                    category,
                    side
                } = control.dataset;
                const settings = layout[category][side].photo;
                settings.zoom = Math.round(Number(control.value) * 100) / 100;
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) syncPhotoZoom(photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });
        document.querySelectorAll('.id-card-photo-pany').forEach((control) => {
            const update = () => {
                const {
                    category,
                    side
                } = control.dataset;
                const settings = layout[category][side].photo;
                settings.panY = clampPan(Number(control.value));
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) syncPhotoZoom(photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });
        document.querySelectorAll('.id-card-photo-panx').forEach((control) => {
            const update = () => {
                const {
                    category,
                    side
                } = control.dataset;
                const settings = layout[category][side].photo;
                settings.panX = clampPan(Number(control.value));
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) syncPhotoZoom(photo, settings);
                updateInput();
            };
            control.addEventListener('input', update);
            control.addEventListener('change', update);
        });
        document.querySelectorAll('.id-card-photo-nudge').forEach((btn) => {
            btn.addEventListener('click', () => {
                const {
                    category,
                    side,
                    axis,
                    delta
                } = btn.dataset;
                const settings = layout[category][side].photo;
                const change = Number(delta) || 0;
                if (axis === 'y') {
                    settings.panY = clampPan((settings.panY ?? 0) + change);
                } else if (axis === 'x') {
                    settings.panX = clampPan((settings.panX ?? 0) + change);
                }
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) syncPhotoZoom(photo, settings);
                updateInput();
            });
        });
        document.querySelectorAll('.id-card-photo-reset').forEach((btn) => {
            btn.addEventListener('click', () => {
                const {
                    category,
                    side
                } = btn.dataset;
                const settings = layout[category][side].photo;
                settings.zoom = 1.0;
                settings.panX = 0;
                settings.panY = 15;
                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"][data-side="${side}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) syncPhotoZoom(photo, settings);
                updateInput();
            });
        });

        // Pilihan Bentuk Foto Bulat vs Kotak
        document.querySelectorAll('.id-card-photo-shape-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category, shape } = btn.dataset;
                const settings = layout[category]?.front?.photo;
                if (!settings) return;
                settings.shape = shape;

                document.querySelectorAll(`.id-card-photo-shape-btn[data-category="${category}"]`).forEach((b) => {
                    const isSelected = b.dataset.shape === shape;
                    b.classList.toggle('btn-primary', isSelected);
                    b.classList.toggle('btn-outline-primary', !isSelected);
                });

                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"]`);
                const photo = stage?.querySelector('.id-card-layout-photo');
                if (photo) {
                    photo.dataset.shape = shape;
                    photo.classList.toggle('is-circle', shape === 'circle');
                    photo.classList.toggle('is-square', shape === 'square');
                }
                updateInput();
            });
        });

        // Kontrol Tipe Barcode / QR Code
        document.querySelectorAll('.id-card-code-type-btn').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category, type } = btn.dataset;
                const settings = layout[category]?.front?.code;
                if (!settings) return;
                settings.type = type;

                document.querySelectorAll(`.id-card-code-type-btn[data-category="${category}"]`).forEach((b) => {
                    const isSelected = b.dataset.type === type;
                    b.classList.toggle(type === 'none' ? 'btn-secondary' : 'btn-primary', isSelected);
                    b.classList.toggle(type === 'none' ? 'btn-outline-secondary' : 'btn-outline-primary', !isSelected);
                });

                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"]`);
                const codeEl = stage?.querySelector('.id-card-layout-code');
                if (stage && codeEl) {
                    syncCodeVisuals(stage, codeEl, settings);
                }
                updateInput();
            });
        });

        // Switch Tampilkan Nomor RFID di Bawah Kode
        document.querySelectorAll('.id-card-code-showtext').forEach((sw) => {
            sw.addEventListener('change', () => {
                const { category } = sw.dataset;
                const settings = layout[category]?.front?.code;
                if (!settings) return;
                settings.showText = sw.checked;

                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"]`);
                const codeEl = stage?.querySelector('.id-card-layout-code');
                if (stage && codeEl) {
                    syncCodeVisuals(stage, codeEl, settings);
                }
                updateInput();
            });
        });

        // Slider Dimensi & Posisi Kode
        ['width', 'height', 'x', 'y'].forEach((prop) => {
            document.querySelectorAll(`.id-card-code-${prop}`).forEach((slider) => {
                const update = () => {
                    const { category } = slider.dataset;
                    const settings = layout[category]?.front?.code;
                    if (!settings) return;
                    settings[prop] = Number(slider.value);

                    const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"]`);
                    const codeEl = stage?.querySelector('.id-card-layout-code');
                    if (stage && codeEl) {
                        syncCodeVisuals(stage, codeEl, settings);
                    }
                    updateInput();
                };
                slider.addEventListener('input', update);
                slider.addEventListener('change', update);
            });
        });

        // Reset Posisi Kode
        document.querySelectorAll('.id-card-code-reset').forEach((btn) => {
            btn.addEventListener('click', () => {
                const { category } = btn.dataset;
                const settings = layout[category]?.front?.code;
                if (!settings) return;
                settings.x = 25.0;
                settings.y = 84.0;
                settings.width = 50.0;
                settings.height = 12.0;

                const stage = document.querySelector(`.id-card-layout-stage[data-category="${category}"]`);
                const codeEl = stage?.querySelector('.id-card-layout-code');
                if (stage && codeEl) {
                    syncCodeVisuals(stage, codeEl, settings);
                }
                updateInput();
            });
        });

        window.addEventListener('resize', () => {
            document.querySelectorAll('.id-card-layout-field').forEach((field) => {
                const stage = field.closest('.id-card-layout-stage');
                styleText(stage.dataset.category, stage.dataset.side, field.dataset.field);
            });
        });
        document.querySelectorAll('.id-card-layout-field, .id-card-layout-photo, .id-card-layout-code').forEach((field) => {
            const stage = field.closest('.id-card-layout-stage');
            const {
                category,
                side
            } = stage.dataset;
            const key = field.dataset.field;
            const settings = layout[category][side][key];
            if (key === 'photo') setPhotoCircle(field, settings, stage);
            let drag = null;
            field.addEventListener('pointerdown', (event) => {
                const resizing = event.target.closest('.id-card-photo-resize-handle') !== null;
                const movingCircleHandle = event.target.closest('.id-card-photo-move-handle') !== null;
                const activeMode = dragMode[category] || 'circle';
                const effectiveMode = event.altKey ? (activeMode === 'circle' ? 'photo' : 'circle') : activeMode;
                const movingCircle = key === 'photo' ? (movingCircleHandle || (!resizing && effectiveMode === 'circle')) : false;
                const isPhotoPan = key === 'photo' && !resizing && !movingCircleHandle && effectiveMode === 'photo';
                const movingCode = key === 'code';

                const rect = stage.getBoundingClientRect();
                const box = field.getBoundingClientRect();
                drag = {
                    resizing,
                    movingCircle,
                    isPhotoPan,
                    movingCode,
                    startX: event.clientX,
                    startY: event.clientY,
                    x: layout[category][side][key].x,
                    y: layout[category][side][key].y,
                    width: box.width / rect.width * 100,
                    height: box.height / rect.height * 100,
                    rect,
                    photoRect: key === 'photo' ? field.getBoundingClientRect() : null,
                    panX: layout[category][side][key].panX ?? 0,
                    panY: layout[category][side][key].panY ?? 0,
                };
                if (key === 'photo' || key === 'code') {
                    drag.width = layout[category][side][key].width;
                    drag.height = layout[category][side][key].height;
                }
                field.setPointerCapture(event.pointerId);
                event.preventDefault();
            });
            field.addEventListener('pointermove', (event) => {
                if (!drag) return;
                const settings = layout[category][side][key];
                const deltaX = (event.clientX - drag.startX) / drag.rect.width * 100;
                const deltaY = (event.clientY - drag.startY) / drag.rect.height * 100;
                if (drag.resizing && key === 'photo') {
                    const ratio = drag.rect.width / drag.rect.height;
                    const diameterDelta = Math.max(deltaX, deltaY / ratio);
                    const maxWidth = Math.min(100 - settings.x, (100 - settings.y) / ratio);
                    settings.width = Math.min(maxWidth, Math.max(10, Math.round((drag.width + diameterDelta) * 100) / 100));
                    syncCircleVisuals(stage, field, settings);
                } else if (drag.isPhotoPan && key === 'photo') {
                    const deltaPhotoX = (event.clientX - drag.startX) / drag.photoRect.width * 100;
                    const deltaPhotoY = (event.clientY - drag.startY) / drag.photoRect.height * 100;
                    settings.panX = clampPan(drag.panX + deltaPhotoX);
                    settings.panY = clampPan(drag.panY + deltaPhotoY);
                    syncPhotoZoom(field, settings);
                } else if (drag.movingCircle && key === 'photo') {
                    settings.x = Math.round(clamp(drag.x + deltaX, 100 - drag.width) * 100) / 100;
                    settings.y = Math.round(clamp(drag.y + deltaY, 100 - drag.height) * 100) / 100;
                    syncCircleVisuals(stage, field, settings);
                } else if (drag.movingCode && key === 'code') {
                    settings.x = Math.round(clamp(drag.x + deltaX, 100 - drag.width) * 100) / 100;
                    settings.y = Math.round(clamp(drag.y + deltaY, 100 - drag.height) * 100) / 100;
                    syncCodeVisuals(stage, field, settings);
                } else {
                    settings.x = clamp(drag.x + deltaX, 100 - drag.width);
                    settings.y = clamp(drag.y + deltaY, 100 - drag.height);
                    field.style.left = `${settings.x}%`;
                    field.style.top = `${settings.y}%`;
                }
                updateInput();
            });
            const finishDrag = () => {
                drag = null;
            };
            field.addEventListener('pointerup', finishDrag);
            field.addEventListener('pointercancel', finishDrag);
            field.addEventListener('wheel', (event) => {
                if (key !== 'photo') return;
                event.preventDefault();
                const nextZoom = Number((settings.zoom ?? 1).toFixed(2)) + (event.deltaY < 0 ? 0.05 : -0.05);
                settings.zoom = Math.min(2.5, Math.max(0.5, Math.round(nextZoom * 100) / 100));
                syncPhotoZoom(field, settings);
                updateInput();
            }, {
                passive: false
            });
            field.addEventListener('keydown', (event) => {
                const step = event.shiftKey ? 1 : 0.2;
                const settings = layout[category][side][key];
                if (key === 'photo' && event.altKey) {
                    // Alt + arrows adjusts photo pan
                    if (event.key === 'ArrowUp') settings.panY = clampPan((settings.panY ?? 0) - 1);
                    else if (event.key === 'ArrowDown') settings.panY = clampPan((settings.panY ?? 0) + 1);
                    else if (event.key === 'ArrowLeft') settings.panX = clampPan((settings.panX ?? 0) - 1);
                    else if (event.key === 'ArrowRight') settings.panX = clampPan((settings.panX ?? 0) + 1);
                    else return;
                    syncPhotoZoom(field, settings);
                    event.preventDefault();
                    updateInput();
                    return;
                }
                const maxX = 100 - (key === 'photo' || key === 'code' ? settings.width : field.offsetWidth / stage.clientWidth * 100);
                const maxY = 100 - (key === 'photo' || key === 'code' ? settings.height : field.offsetHeight / stage.clientHeight * 100);
                if (event.key === 'ArrowLeft') settings.x = clamp(settings.x - step, maxX);
                else if (event.key === 'ArrowRight') settings.x = clamp(settings.x + step, maxX);
                else if (event.key === 'ArrowUp') settings.y = clamp(settings.y - step, maxY);
                else if (event.key === 'ArrowDown') settings.y = clamp(settings.y + step, maxY);
                else if (key === 'photo' && (event.key === '+' || event.key === '=')) {
                    settings.zoom = Math.min(2.5, Math.round((Number(settings.zoom ?? 1) + 0.05) * 100) / 100);
                    syncPhotoZoom(field, settings);
                    event.preventDefault();
                    updateInput();
                    return;
                } else if (key === 'photo' && (event.key === '-' || event.key === '_')) {
                    settings.zoom = Math.max(0.5, Math.round((Number(settings.zoom ?? 1) - 0.05) * 100) / 100);
                    syncPhotoZoom(field, settings);
                    event.preventDefault();
                    updateInput();
                    return;
                } else return;
                event.preventDefault();
                if (key === 'code') {
                    syncCodeVisuals(stage, field, settings);
                } else {
                    field.style.left = `${settings.x}%`;
                    field.style.top = `${settings.y}%`;
                }
                updateInput();
            });
        });
        document.querySelectorAll('.id-card-student-category').forEach((select) => {
            const printLink = select.parentElement.querySelector('.id-card-student-print');
            const jpgLink = select.parentElement.querySelector('.id-card-student-jpg');
            const studentId = printLink?.dataset.studentId;
            const ready = <?= json_encode($templateReady); ?>;
            const updatePrintLink = () => {
                const category = select.value;
                const enabled = ready[category] === true;
                if (printLink) {
                    printLink.href = enabled ? `<?= site_url('student-id-cards/print/'); ?>${studentId}/${category}` : '#';
                    printLink.classList.toggle('disabled', !enabled);
                    printLink.setAttribute('aria-disabled', enabled ? 'false' : 'true');
                    printLink.tabIndex = enabled ? 0 : -1;
                }
                if (jpgLink) {
                    jpgLink.href = enabled ? `<?= site_url('student-id-cards/print/'); ?>${studentId}/${category}?download=1` : '#';
                    jpgLink.classList.toggle('disabled', !enabled);
                    jpgLink.setAttribute('aria-disabled', enabled ? 'false' : 'true');
                    jpgLink.tabIndex = enabled ? 0 : -1;
                }
            };
            select.addEventListener('change', updatePrintLink);
        });

        // ============================================
        // PENCARIAN & FILTER SISWA
        // ============================================
        const studentSearchInput = document.getElementById('id-card-student-search');
        const studentSearchClear = document.getElementById('id-card-student-search-clear');
        const studentClassFilter = document.getElementById('id-card-student-class-filter');
        const studentRows = document.querySelectorAll('.id-card-student-row');
        const studentEmptyRow = document.getElementById('id-card-student-search-empty');
        const studentCountBadge = document.querySelector('.id-card-student-count-badge');
        const totalStudents = Number(studentCountBadge?.dataset.total ?? 0);

        const filterStudents = () => {
            const query = (studentSearchInput?.value || '').trim().toLowerCase();
            const selectedClass = (studentClassFilter?.value || '').trim().toLowerCase();

            if (studentSearchClear) {
                studentSearchClear.style.display = query !== '' ? 'inline-block' : 'none';
            }

            let visibleCount = 0;
            studentRows.forEach((row) => {
                const name = row.dataset.name || '';
                const nis = row.dataset.nis || '';
                const nisn = row.dataset.nisn || '';
                const cls = row.dataset.class || '';

                const matchesQuery = query === '' || name.includes(query) || nis.includes(query) || nisn.includes(query) || cls.includes(query);
                const matchesClass = selectedClass === '' || cls === selectedClass;
                const isMatch = matchesQuery && matchesClass;

                row.style.display = '';
                row.hidden = !isMatch;

                if (isMatch) {
                    visibleCount++;
                    const numCell = row.querySelector('.id-card-student-num');
                    if (numCell) numCell.textContent = String(visibleCount);
                }
            });

            if (studentEmptyRow) {
                const showEmpty = visibleCount === 0 && studentRows.length > 0 && (query !== '' || selectedClass !== '');
                studentEmptyRow.hidden = !showEmpty;
                studentEmptyRow.style.display = '';
                if (showEmpty) {
                    const qSpan = studentEmptyRow.querySelector('.search-query');
                    if (qSpan) qSpan.textContent = query || (selectedClass ? `Kelas ${selectedClass.toUpperCase()}` : '');
                }
            }

            if (studentCountBadge) {
                if (query !== '' || selectedClass !== '') {
                    studentCountBadge.textContent = `${visibleCount} dari ${totalStudents} siswa`;
                    studentCountBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle id-card-student-count-badge';
                } else {
                    studentCountBadge.textContent = `${totalStudents} siswa`;
                    studentCountBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle id-card-student-count-badge';
                }
            }
        };

        studentSearchInput?.addEventListener('input', filterStudents);
        studentClassFilter?.addEventListener('change', filterStudents);
        studentSearchClear?.addEventListener('click', () => {
            if (studentSearchInput) {
                studentSearchInput.value = '';
                studentSearchInput.focus();
            }
            filterStudents();
        });

        // ============================================
        // PENCARIAN GURU
        // ============================================
        const teacherSearchInput = document.getElementById('id-card-teacher-search');
        const teacherSearchClear = document.getElementById('id-card-teacher-search-clear');
        const teacherRows = document.querySelectorAll('.id-card-teacher-row');
        const teacherEmptyRow = document.getElementById('id-card-teacher-search-empty');
        const teacherCountBadge = document.querySelector('.id-card-teacher-count-badge');
        const totalTeachers = Number(teacherCountBadge?.dataset.total ?? 0);

        const filterTeachers = () => {
            const query = (teacherSearchInput?.value || '').trim().toLowerCase();

            if (teacherSearchClear) {
                teacherSearchClear.style.display = query !== '' ? 'inline-block' : 'none';
            }

            let visibleCount = 0;
            teacherRows.forEach((row) => {
                const name = row.dataset.name || '';
                const nip = row.dataset.nip || '';
                const email = row.dataset.email || '';
                const phone = row.dataset.phone || '';

                const isMatch = query === '' || name.includes(query) || nip.includes(query) || email.includes(query) || phone.includes(query);

                row.style.display = '';
                row.hidden = !isMatch;

                if (isMatch) {
                    visibleCount++;
                    const numCell = row.querySelector('.id-card-teacher-num');
                    if (numCell) numCell.textContent = String(visibleCount);
                }
            });

            if (teacherEmptyRow) {
                const showEmpty = visibleCount === 0 && teacherRows.length > 0 && query !== '';
                teacherEmptyRow.hidden = !showEmpty;
                teacherEmptyRow.style.display = '';
                if (showEmpty) {
                    const qSpan = teacherEmptyRow.querySelector('.search-query');
                    if (qSpan) qSpan.textContent = query;
                }
            }

            if (teacherCountBadge) {
                if (query !== '') {
                    teacherCountBadge.textContent = `${visibleCount} dari ${totalTeachers} guru`;
                    teacherCountBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle id-card-teacher-count-badge';
                } else {
                    teacherCountBadge.textContent = `${totalTeachers} guru`;
                    teacherCountBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle id-card-teacher-count-badge';
                }
            }
        };

        teacherSearchInput?.addEventListener('input', filterTeachers);
        teacherSearchClear?.addEventListener('click', () => {
            if (teacherSearchInput) {
                teacherSearchInput.value = '';
                teacherSearchInput.focus();
            }
            filterTeachers();
        });

        // ============================================
        // BULK EXPORT ZIP (SISWA & GURU)
        // ============================================
        const allStudents = <?= json_encode($students, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const allTeachers = <?= json_encode($teachers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const templateDataUrls = <?= json_encode($templateDataUrls ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const templateCardSizes = <?= json_encode($templateCardSizes ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
        const templateReady = <?= json_encode($templateReady); ?>;

        const bulkZipModalEl = document.getElementById('bulkZipModal');
        const bulkZipModal = bulkZipModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(bulkZipModalEl) : null;
        const btnExportZipStudent = document.getElementById('btn-export-zip-student');
        const btnExportZipTeacher = document.getElementById('btn-export-zip-teacher');
        const btnStartBulkZip = document.getElementById('btn-start-bulk-zip');
        const btnCancelBulkZip = document.getElementById('btn-cancel-bulk-zip');
        const btnCloseBulkModal = document.getElementById('btn-close-bulk-modal');

        const bulkConfigView = document.getElementById('bulk-zip-config-view');
        const bulkProgressView = document.getElementById('bulk-zip-progress-view');
        const bulkDoneView = document.getElementById('bulk-zip-done-view');
        const bulkFooterConfig = document.getElementById('bulk-footer-config');
        const bulkFooterProgress = document.getElementById('bulk-footer-progress');
        const bulkFooterDone = document.getElementById('bulk-footer-done');

        const bulkZipModalLabel = document.getElementById('bulkZipModalLabel');
        const bulkZipModalSubLabel = document.getElementById('bulkZipModalSubLabel');
        const bulkScopeAllLabel = document.getElementById('bulk-scope-all-label');
        const bulkScopeFilteredLabel = document.getElementById('bulk-scope-filtered-label');
        const bulkScopeFilteredWrapper = document.getElementById('bulk-scope-filtered-wrapper');
        const bulkStudentCatGroup = document.getElementById('bulk-student-category-group');
        const bulkQualitySelect = document.getElementById('bulk-quality-select');

        const bulkZipCurrentAction = document.getElementById('bulk-zip-current-action');
        const bulkZipCurrentPerson = document.getElementById('bulk-zip-current-person');
        const bulkZipProgressBar = document.getElementById('bulk-zip-progress-bar');
        const bulkZipProgressCount = document.getElementById('bulk-zip-progress-count');
        const bulkZipProgressPercent = document.getElementById('bulk-zip-progress-percent');
        const bulkZipDoneMessage = document.getElementById('bulk-zip-done-message');

        // Offscreen Stage Elements
        const bulkCardPage = document.getElementById('bulk-card-page');
        const bulkCardEl = document.getElementById('bulk-card-el');
        const bulkTemplateImg = document.getElementById('bulk-card-template-img');
        const bulkPhotoBox = document.getElementById('bulk-card-photo-box');
        const bulkPhotoImg = document.getElementById('bulk-card-photo-img');
        const bulkPhotoPlaceholder = document.getElementById('bulk-card-photo-placeholder');

        const bulkFieldName = document.getElementById('bulk-card-field-name');
        const bulkFieldNis = document.getElementById('bulk-card-field-nis');
        const bulkFieldNisn = document.getElementById('bulk-card-field-nisn');
        const bulkFieldBirth = document.getElementById('bulk-card-field-birth');
        const bulkFieldAddress = document.getElementById('bulk-card-field-address');
        const bulkFieldNip = document.getElementById('bulk-card-field-nip');
        const bulkFieldEmail = document.getElementById('bulk-card-field-email');

        const bulkCodeContainer = document.getElementById('bulk-card-code-container');
        const bulkBarcodeSvg = document.getElementById('bulk-barcode-svg');
        const bulkQrcodeCanvas = document.getElementById('bulk-qrcode-canvas');
        const bulkCodeLabel = document.getElementById('bulk-code-label');

        let bulkContext = {
            type: 'student', // 'student' or 'teacher'
            isProcessing: false,
            isCancelled: false
        };

        const resetBulkModalViews = () => {
            bulkConfigView.classList.remove('d-none');
            bulkProgressView.classList.add('d-none');
            bulkDoneView.classList.add('d-none');
            bulkFooterConfig.classList.remove('d-none');
            bulkFooterProgress.classList.add('d-none');
            bulkFooterDone.classList.add('d-none');
            if (btnCloseBulkModal) btnCloseBulkModal.disabled = false;
            bulkZipProgressBar.style.width = '0%';
            bulkZipProgressBar.textContent = '0%';
            bulkZipProgressPercent.textContent = '0%';
            bulkContext.isProcessing = false;
            bulkContext.isCancelled = false;
        };

        bulkZipModalEl?.addEventListener('hidden.bs.modal', resetBulkModalViews);

        btnExportZipStudent?.addEventListener('click', () => {
            if (!templateReady.smp && !templateReady.smk) {
                alert('Silakan unggah template ID card (SMP atau SMK) terlebih dahulu sebelum mencetak.');
                return;
            }

            bulkContext.type = 'student';
            bulkZipModalLabel.textContent = 'Cetak Semua ID Card Siswa (.ZIP)';
            bulkZipModalSubLabel.textContent = 'Kompres kartu ID siswa ke format JPG dalam satu file arsip ZIP';
            bulkStudentCatGroup.style.display = 'block';

            // Cek status filter saat ini
            const query = (studentSearchInput?.value || '').trim();
            const selectedClass = (studentClassFilter?.value || '').trim();
            const hasFilter = query !== '' || selectedClass !== '';
            let visibleCount = 0;
            studentRows.forEach((r) => { if (!r.hidden && r.style.display !== 'none') visibleCount++; });

            bulkScopeAllLabel.textContent = `Semua Siswa (${allStudents.length} siswa)`;
            if (hasFilter && visibleCount > 0 && visibleCount < allStudents.length) {
                bulkScopeFilteredWrapper.style.display = 'block';
                const filterDesc = selectedClass ? `Kelas ${selectedClass.toUpperCase()}` : (query ? `Pencarian "${query}"` : '');
                bulkScopeFilteredLabel.textContent = `Siswa Sesuai Filter Saat Ini (${visibleCount} siswa ${filterDesc ? '- ' + filterDesc : ''})`;
                document.getElementById('bulk-scope-filtered').checked = true;
            } else {
                bulkScopeFilteredWrapper.style.display = 'none';
                document.getElementById('bulk-scope-all').checked = true;
            }

            resetBulkModalViews();
            bulkZipModal?.show();
        });

        btnExportZipTeacher?.addEventListener('click', () => {
            if (!templateReady.teacher) {
                alert('Silakan unggah template ID card Guru terlebih dahulu sebelum mencetak.');
                return;
            }

            bulkContext.type = 'teacher';
            bulkZipModalLabel.textContent = 'Cetak Semua ID Card Guru (.ZIP)';
            bulkZipModalSubLabel.textContent = 'Kompres kartu ID guru ke format JPG dalam satu file arsip ZIP';
            bulkStudentCatGroup.style.display = 'none';

            // Cek pencarian guru saat ini
            const query = (teacherSearchInput?.value || '').trim();
            let visibleCount = 0;
            teacherRows.forEach((r) => { if (!r.hidden && r.style.display !== 'none') visibleCount++; });

            bulkScopeAllLabel.textContent = `Semua Guru (${allTeachers.length} guru)`;
            if (query !== '' && visibleCount > 0 && visibleCount < allTeachers.length) {
                bulkScopeFilteredWrapper.style.display = 'block';
                bulkScopeFilteredLabel.textContent = `Guru Sesuai Pencarian Saat Ini (${visibleCount} guru - "${query}")`;
                document.getElementById('bulk-scope-filtered').checked = true;
            } else {
                bulkScopeFilteredWrapper.style.display = 'none';
                document.getElementById('bulk-scope-all').checked = true;
            }

            resetBulkModalViews();
            bulkZipModal?.show();
        });

        btnCancelBulkZip?.addEventListener('click', () => {
            if (!bulkContext.isProcessing) return;
            bulkContext.isCancelled = true;
            btnCancelBulkZip.disabled = true;
            btnCancelBulkZip.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Membatalkan...';
            bulkZipCurrentAction.textContent = 'Menghentikan proses...';
        });

        // Helper membersihkan nama file
        const sanitizeFilename = (str) => {
            return (str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '') || 'idcard';
        };

        // Render satu kartu ke Offscreen Stage
        const renderCardToStage = async (person, isTeacher, forcedCategory) => {
            let category = forcedCategory;
            if (!isTeacher) {
                if (forcedCategory === 'table') {
                    // Ambil kategori dari dropdown baris siswa di tabel
                    const row = document.querySelector(`.id-card-student-row[data-name="${(person.nama_siswa || '').toLowerCase()}"]`);
                    const select = row?.querySelector('.id-card-student-category');
                    category = select ? select.value : 'smp';
                }
                if (!templateReady[category]) {
                    category = templateReady.smp ? 'smp' : 'smk';
                }
            } else {
                category = 'teacher';
            }

            const cardSize = (templateCardSizes[category] && templateCardSizes[category].front) ? templateCardSizes[category].front : { width: 85.6, height: 54.0 };
            const frontLayout = layout[category]?.front || {};

            // 1. Terapkan Dimensi Kartu
            bulkCardPage.style.setProperty('--card-width', `${cardSize.width}mm`);
            bulkCardPage.style.setProperty('--card-height', `${cardSize.height}mm`);
            bulkCardEl.style.setProperty('--card-width', `${cardSize.width}mm`);
            bulkCardEl.style.setProperty('--card-height', `${cardSize.height}mm`);

            // 2. Terapkan Template Background
            const bgUrl = templateDataUrls[category] || '';
            bulkTemplateImg.src = bgUrl;

            // 3. Terapkan Foto
            const photoSettings = frontLayout.photo;
            if (photoSettings) {
                bulkPhotoBox.style.display = 'flex';
                bulkPhotoBox.style.left = `${photoSettings.x}%`;
                bulkPhotoBox.style.top = `${photoSettings.y}%`;
                bulkPhotoBox.style.width = `${photoSettings.width}%`;
                bulkPhotoBox.style.height = `${photoSettings.height}%`;
                bulkPhotoBox.className = `card-photo ${photoSettings.shape === 'square' ? 'is-square' : 'is-circle'} ${isTeacher ? 'is-teacher-photo' : ''}`;

                if (person.photo_url) {
                    bulkPhotoImg.style.display = 'block';
                    bulkPhotoPlaceholder.style.display = 'none';
                    bulkPhotoImg.src = person.photo_url;
                    bulkPhotoImg.style.transform = `translate(${photoSettings.panX || 0}%, ${photoSettings.panY || 0}%) scale(${photoSettings.zoom || 1})`;
                } else {
                    bulkPhotoImg.style.display = 'none';
                    bulkPhotoPlaceholder.style.display = 'flex';
                    const initial = (isTeacher ? (person.guru_nama || 'G') : (person.nama_siswa || 'S')).charAt(0).toUpperCase();
                    bulkPhotoPlaceholder.textContent = initial;
                    if (isTeacher) {
                        bulkPhotoPlaceholder.style.background = 'rgba(0,0,0,0.06)';
                        bulkPhotoPlaceholder.style.color = '#475569';
                    } else {
                        bulkPhotoPlaceholder.style.background = '#e60000';
                        bulkPhotoPlaceholder.style.color = '#ffffff';
                    }
                }
            } else {
                bulkPhotoBox.style.display = 'none';
            }

            // 4. Terapkan Teks Fields
            const applyField = (el, key, val) => {
                const pos = frontLayout[key];
                if (pos && val !== null && val !== undefined) {
                    el.style.display = 'block';
                    el.style.left = `${pos.x}%`;
                    el.style.top = `${pos.y}%`;
                    el.style.fontFamily = pos.fontFamily || 'Arial';
                    el.style.fontSize = `${pos.fontSize || 2.5}mm`;
                    el.style.color = pos.fontColor || '#102b58';
                    el.textContent = String(val);
                } else {
                    el.style.display = 'none';
                }
            };

            if (isTeacher) {
                applyField(bulkFieldName, 'name', person.guru_nama);
                applyField(bulkFieldNip, 'nip', 'NIP: ' + (person.guru_nip || '-'));
                applyField(bulkFieldEmail, 'email', 'Email: ' + (person.guru_email || '-'));
                bulkFieldNis.style.display = 'none';
                bulkFieldNisn.style.display = 'none';
                bulkFieldBirth.style.display = 'none';
                bulkFieldAddress.style.display = 'none';
            } else {
                applyField(bulkFieldName, 'name', person.nama_siswa);
                applyField(bulkFieldNis, 'nis', person.nis || '-');
                applyField(bulkFieldNisn, 'nisn', person.nisn || '-');
                const birthStr = [person.tempat_lahir, person.tanggal_lahir].filter(Boolean).join(', ');
                applyField(bulkFieldBirth, 'birth', birthStr || '-');
                applyField(bulkFieldAddress, 'address', person.alamat || '-');
                bulkFieldNip.style.display = 'none';
                bulkFieldEmail.style.display = 'none';
            }

            // 5. Terapkan Barcode / QR Code
            const codeSettings = frontLayout.code;
            let rfidVal = (person.rfid_tag || '').trim();
            if (!rfidVal) {
                rfidVal = isTeacher ? (person.guru_nip || String(person.guru_id)) : (person.nis || person.nisn || String(person.siswa_id));
            }

            if (codeSettings && codeSettings.type !== 'none') {
                bulkCodeContainer.style.display = 'flex';
                bulkCodeContainer.style.left = `${codeSettings.x ?? 25}%`;
                bulkCodeContainer.style.top = `${codeSettings.y ?? 84}%`;
                bulkCodeContainer.style.width = `${codeSettings.width ?? 50}%`;
                bulkCodeContainer.style.height = `${codeSettings.height ?? 12}%`;

                const showText = codeSettings.showText !== false;
                if (codeSettings.type === 'barcode' && typeof JsBarcode === 'function') {
                    bulkBarcodeSvg.style.display = 'block';
                    bulkQrcodeCanvas.style.display = 'none';
                    bulkCodeLabel.style.display = 'none';
                    bulkBarcodeSvg.innerHTML = '';
                    try {
                        JsBarcode(bulkBarcodeSvg, rfidVal, {
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
                        console.warn('JsBarcode bulk error:', e);
                    }
                } else if (codeSettings.type === 'qrcode' && typeof QRCode === 'function') {
                    bulkBarcodeSvg.style.display = 'none';
                    bulkQrcodeCanvas.style.display = 'flex';
                    bulkQrcodeCanvas.innerHTML = '';
                    try {
                        new QRCode(bulkQrcodeCanvas, {
                            text: rfidVal,
                            width: 100,
                            height: 100,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.M
                        });
                        if (showText) {
                            bulkCodeLabel.style.display = 'block';
                            bulkCodeLabel.textContent = rfidVal;
                        } else {
                            bulkCodeLabel.style.display = 'none';
                        }
                    } catch (e) {
                        console.warn('QRCode bulk error:', e);
                    }
                }
            } else {
                bulkCodeContainer.style.display = 'none';
            }

            // 6. Tunggu semua gambar termuat
            const imgs = Array.from(bulkCardEl.querySelectorAll('img')).filter((im) => im.style.display !== 'none' && im.src);
            await Promise.all(imgs.map((im) => {
                if (im.complete && im.naturalWidth !== 0) return Promise.resolve();
                return new Promise((resolve) => {
                    const timer = setTimeout(resolve, 1200);
                    im.onload = () => { clearTimeout(timer); resolve(); };
                    im.onerror = () => { clearTimeout(timer); resolve(); };
                });
            }));

            // Pause singkat agar rendering DOM stabil
            await new Promise((r) => setTimeout(r, 35));
        };

        // Eksekusi Bulk Export ZIP
        btnStartBulkZip?.addEventListener('click', async () => {
            if (typeof JSZip === 'undefined') {
                alert('Library JSZip belum siap. Silakan muat ulang halaman.');
                return;
            }
            if (typeof html2canvas === 'undefined') {
                alert('Library html2canvas belum siap. Silakan muat ulang halaman.');
                return;
            }

            const isTeacher = bulkContext.type === 'teacher';
            const scopeVal = document.querySelector('input[name="bulk-scope"]:checked')?.value || 'all';
            const catMode = document.querySelector('input[name="bulk-category-mode"]:checked')?.value || 'table';
            const scaleFactor = Number(bulkQualitySelect?.value || 3);

            // Tentukan target data
            let targetList = [];
            let zipNamePrefix = isTeacher ? 'idcard-guru' : 'idcard-siswa';

            if (isTeacher) {
                if (scopeVal === 'filtered') {
                    const query = (teacherSearchInput?.value || '').trim().toLowerCase();
                    targetList = allTeachers.filter((t) => {
                        const name = (t.guru_nama || '').toLowerCase();
                        const nip = (t.guru_nip || '').toLowerCase();
                        const email = (t.guru_email || '').toLowerCase();
                        const phone = (t.guru_telepon || '').toLowerCase();
                        return query === '' || name.includes(query) || nip.includes(query) || email.includes(query) || phone.includes(query);
                    });
                    zipNamePrefix += '-terfilter';
                } else {
                    targetList = [...allTeachers];
                    zipNamePrefix += '-semua';
                }
            } else {
                if (scopeVal === 'filtered') {
                    const query = (studentSearchInput?.value || '').trim().toLowerCase();
                    const selectedClass = (studentClassFilter?.value || '').trim().toLowerCase();
                    targetList = allStudents.filter((st) => {
                        const name = (st.nama_siswa || '').toLowerCase();
                        const nis = (st.nis || '').toLowerCase();
                        const nisn = (st.nisn || '').toLowerCase();
                        const cls = (st.kelas || '').trim().toLowerCase();
                        const matchQ = query === '' || name.includes(query) || nis.includes(query) || nisn.includes(query) || cls.includes(query);
                        const matchC = selectedClass === '' || cls === selectedClass;
                        return matchQ && matchC;
                    });
                    if (selectedClass) {
                        zipNamePrefix += `-kelas-${sanitizeFilename(selectedClass)}`;
                    } else {
                        zipNamePrefix += '-terfilter';
                    }
                } else {
                    targetList = [...allStudents];
                    zipNamePrefix += '-semua';
                }
            }

            if (targetList.length === 0) {
                alert('Tidak ada data yang dapat diproses.');
                return;
            }

            // Ganti ke tampilan progress
            bulkConfigView.classList.add('d-none');
            bulkProgressView.classList.remove('d-none');
            bulkDoneView.classList.add('d-none');
            bulkFooterConfig.classList.add('d-none');
            bulkFooterProgress.classList.remove('d-none');
            bulkFooterDone.classList.add('d-none');
            if (btnCloseBulkModal) btnCloseBulkModal.disabled = true;

            bulkContext.isProcessing = true;
            bulkContext.isCancelled = false;
            btnCancelBulkZip.disabled = false;
            btnCancelBulkZip.innerHTML = '<i class="bi bi-x-circle me-1"></i> Hentikan Proses';

            const total = targetList.length;
            const zip = new JSZip();
            let successCount = 0;

            for (let i = 0; i < total; i++) {
                if (bulkContext.isCancelled) {
                    break;
                }

                const person = targetList[i];
                const personName = isTeacher ? (person.guru_nama || 'Guru') : (person.nama_siswa || 'Siswa');
                const personId = isTeacher ? (person.guru_nip || person.guru_id) : (person.nis || person.siswa_id);
                const percent = Math.round((i / total) * 100);

                // Update UI Progress
                bulkZipProgressBar.style.width = `${percent}%`;
                bulkZipProgressBar.textContent = `${percent}%`;
                bulkZipProgressPercent.textContent = `${percent}%`;
                bulkZipProgressCount.textContent = `${i} / ${total} kartu`;
                bulkZipCurrentAction.textContent = `Merender kartu ${i + 1} dari ${total}...`;
                bulkZipCurrentPerson.textContent = personName;

                try {
                    // Render ke DOM
                    await renderCardToStage(person, isTeacher, catMode);

                    // Ambil canvas html2canvas
                    const canvas = await html2canvas(bulkCardEl, {
                        scale: scaleFactor,
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: '#ffffff',
                        logging: false
                    });

                    // Konversi ke canvas output dengan background putih bersih
                    const outCanvas = document.createElement('canvas');
                    outCanvas.width = canvas.width;
                    outCanvas.height = canvas.height;
                    const ctx = outCanvas.getContext('2d');
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, outCanvas.width, outCanvas.height);
                    ctx.drawImage(canvas, 0, 0);

                    // Konversi ke JPEG Blob
                    const blob = await new Promise((resolve) => outCanvas.toBlob(resolve, 'image/jpeg', 0.92));
                    if (blob) {
                        const padIndex = String(i + 1).padStart(3, '0');
                        const cleanName = sanitizeFilename(personName);
                        const cleanId = sanitizeFilename(String(personId));
                        const itemFilename = `${padIndex}_${cleanName}_${cleanId}.jpg`;
                        zip.file(itemFilename, blob);
                        successCount++;
                    }
                } catch (err) {
                    console.error(`Gagal merender kartu ${personName}:`, err);
                }
            }

            if (bulkContext.isCancelled) {
                bulkContext.isProcessing = false;
                bulkZipCurrentAction.textContent = 'Proses Dibatalkan';
                bulkZipCurrentPerson.textContent = `Proses dihentikan oleh pengguna. ${successCount} dari ${total} kartu sempat diproses.`;
                bulkZipProgressBar.className = 'progress-bar bg-warning';
                bulkFooterProgress.classList.add('d-none');
                bulkFooterDone.classList.remove('d-none');
                if (btnCloseBulkModal) btnCloseBulkModal.disabled = false;
                return;
            }

            // Selesai render semua, sekarang kompres ZIP
            bulkZipCurrentAction.textContent = 'Sedang Mengompresi ke File ZIP...';
            bulkZipCurrentPerson.textContent = 'Mengemas semua gambar menjadi file .zip (mohon tunggu)...';
            bulkZipProgressBar.style.width = '100%';
            bulkZipProgressBar.textContent = '100%';
            bulkZipProgressPercent.textContent = '100%';
            bulkZipProgressCount.textContent = `${total} / ${total} kartu`;

            try {
                const zipBlob = await zip.generateAsync({
                    type: 'blob',
                    compression: 'DEFLATE',
                    compressionOptions: { level: 6 }
                }, (metadata) => {
                    if (metadata.percent) {
                        const p = Math.round(metadata.percent);
                        bulkZipProgressBar.textContent = `${p}%`;
                        bulkZipCurrentAction.textContent = `Mengompresi File ZIP (${p}%)...`;
                    }
                });

                // Download File ZIP
                const today = new Date().toISOString().split('T')[0];
                const finalZipName = `${zipNamePrefix}-${today}.zip`;
                const downloadUrl = URL.createObjectURL(zipBlob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = finalZipName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(() => URL.revokeObjectURL(downloadUrl), 5000);

                // Tampilkan Done View
                bulkProgressView.classList.add('d-none');
                bulkDoneView.classList.remove('d-none');
                bulkFooterProgress.classList.add('d-none');
                bulkFooterDone.classList.remove('d-none');
                if (btnCloseBulkModal) btnCloseBulkModal.disabled = false;
                bulkZipDoneMessage.innerHTML = `Sebanyak <strong>${successCount} ID Card</strong> berhasil dirender dan dikompres ke file <code>${finalZipName}</code>. File telah diunduh otomatis ke folder download Anda.`;
            } catch (zipErr) {
                console.error('Error saat membuat ZIP:', zipErr);
                alert('Gagal membuat file ZIP: ' + (zipErr.message || 'Kesalahan sistem'));
                resetBulkModalViews();
            } finally {
                bulkContext.isProcessing = false;
            }
        });

        document.getElementById('id-card-layout-form')?.addEventListener('submit', syncLayoutFromEditor, true);
        renderCodesInStages();
        updateInput();
    })();
</script>
<?= $this->endSection(); ?>