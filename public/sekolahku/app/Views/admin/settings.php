<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<style>
.card-header[data-bs-toggle="collapse"]{cursor:pointer}
.card-header[data-bs-toggle="collapse"] .collapse-icon{transition:transform .2s ease}
.card-header[data-bs-toggle="collapse"].collapsed .collapse-icon{transform:rotate(-90deg)}

</style>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Pengaturan</h4>
        <p class="text-body-tertiary small mb-0">Kelola konfigurasi website sekolah</p>
    </div>
    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2"><?= count($settings ?? []) ?> pengaturan</span>
</div>

<form action="/admin/settings/save" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseIdentitas" role="button">
                    <h6 class="mb-0"><i class="fas fa-globe me-2 text-primary"></i>Identitas Situs</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseIdentitas">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama Situs</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-signature text-body-tertiary"></i></span>
                                <input type="text" name="site_name" class="form-control border-start-0 ps-0" value="<?= $settings['site_name'] ?? '' ?>" placeholder="Mis. SMP Negeri 1 Jakarta">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Teks Logo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-font text-body-tertiary"></i></span>
                                <input type="text" name="site_logo_text" class="form-control border-start-0 ps-0" value="<?= $settings['site_logo_text'] ?? '' ?>" placeholder="Mis. SMPN 1 JKT">
                            </div>
                            <div class="form-text small">Teks pendek yang ditampilkan di area navbar/brand</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Ikon Logo (Font Awesome)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-icons text-body-tertiary"></i></span>
                                <input type="text" name="site_logo_icon" class="form-control border-start-0 ps-0" value="<?= $settings['site_logo_icon'] ?? '' ?>" placeholder="Mis. fa-school">
                            </div>
                            <div class="form-text small">Nama class Font Awesome 6 <a href="https://fontawesome.com/search" target="_blank" class="text-decoration-none">cari ikon <i class="fas fa-external-link-alt fa-xs"></i></a></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Tagline</label>
                            <input type="text" name="site_tagline" class="form-control" value="<?= $settings['site_tagline'] ?? '' ?>" placeholder="Mis. Unggul dalam Prestasi, Berkarakter Mulia">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea name="site_description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang sekolah untuk SEO dan meta tag..."><?= $settings['site_description'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseKontak" role="button">
                    <h6 class="mb-0"><i class="fas fa-phone me-2 text-primary"></i>Informasi Kontak</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseKontak">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-phone text-body-tertiary"></i></span>
                                <input type="text" name="contact_phone" class="form-control border-start-0 ps-0" value="<?= $settings['contact_phone'] ?? '' ?>" placeholder="Mis. (021) 12345678">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-envelope text-body-tertiary"></i></span>
                                <input type="email" name="contact_email" class="form-control border-start-0 ps-0" value="<?= $settings['contact_email'] ?? '' ?>" placeholder="Mis. info@sekolah.sch.id">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Alamat</label>
                            <textarea name="contact_address" class="form-control" rows="2" placeholder="Mis. Jl. Merdeka No. 123, Jakarta Pusat"><?= $settings['contact_address'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Jam Operasional</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-clock text-body-tertiary"></i></span>
                                <input type="text" name="contact_hours" class="form-control border-start-0 ps-0" value="<?= $settings['contact_hours'] ?? '' ?>" placeholder="Mis. Sen-Jum 07:00-16:00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseSosial" role="button">
                    <h6 class="mb-0"><i class="fas fa-share-alt me-2 text-primary"></i>Media Sosial</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseSosial">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Facebook</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-facebook-f text-primary"></i></span>
                                    <input type="text" name="social_facebook" class="form-control border-start-0 ps-0" value="<?= $settings['social_facebook'] ?? '' ?>" placeholder="https://facebook.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-instagram text-danger"></i></span>
                                    <input type="text" name="social_instagram" class="form-control border-start-0 ps-0" value="<?= $settings['social_instagram'] ?? '' ?>" placeholder="https://instagram.com/...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">YouTube</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-youtube text-danger"></i></span>
                                    <input type="text" name="social_youtube" class="form-control border-start-0 ps-0" value="<?= $settings['social_youtube'] ?? '' ?>" placeholder="https://youtube.com/@...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">TikTok</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fab fa-tiktok text-dark"></i></span>
                                    <input type="text" name="social_tiktok" class="form-control border-start-0 ps-0" value="<?= $settings['social_tiktok'] ?? '' ?>" placeholder="https://tiktok.com/@...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseHeroStats" role="button">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Statistik Hero</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseHeroStats">
                    <div class="card-body">
                        <p class="small text-body-tertiary mb-3">Angka statistik di hero section — icon cukup nama class Font Awesome tanpa <code>fa-</code></p>
                        <?php $heroStats = !empty($settings['hero_stats']) ? json_decode($settings['hero_stats'], true) : []; ?>
                        <?php for ($i = 0; $i < 3; $i++): ?>
                        <?php $item = $heroStats[$i] ?? ['icon' => '', 'value' => '', 'label' => '']; ?>
                        <div class="row g-2 mb-3 align-items-end border rounded-3 p-3 bg-body-tertiary">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium">Ikon</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-icons text-body-tertiary"></i></span>
                                    <input type="text" name="hero_stats[<?= $i ?>][icon]" class="form-control border-start-0 ps-0" value="<?= $item['icon'] ?>" placeholder="user-graduate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-medium">Nilai</label>
                                <input type="text" name="hero_stats[<?= $i ?>][value]" class="form-control form-control-sm" value="<?= $item['value'] ?>" placeholder="1200+">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-medium">Label</label>
                                <input type="text" name="hero_stats[<?= $i ?>][label]" class="form-control form-control-sm" value="<?= $item['label'] ?>" placeholder="Siswa">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseTema" role="button">
                    <h6 class="mb-0"><i class="fas fa-palette me-2 text-primary"></i>Warna Tema</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseTema">
                    <div class="card-body">
                        <p class="small text-body-tertiary mb-3">Pilih skema warna untuk tampilan website</p>
                            <?php $current = $settings['theme_color'] ?? 'default'; ?>
                            <?php $themes = [
                                'default' => ['label' => 'Default', 'color' => 'linear-gradient(135deg, #0c4a6e 0%, #14b8a6 50%, #f59e0b 100%)'],
                                'blue'    => ['label' => 'Biru',    'color' => '#203e77'],
                                'green'   => ['label' => 'Hijau',   'color' => '#065f46'],
                                'red'     => ['label' => 'Merah',   'color' => '#7c1d2e'],
                                'gray'    => ['label' => 'Abu',     'color' => '#475569'],
                                'purple'  => ['label' => 'Ungu',    'color' => '#5b21b6'],
                                'navy'    => ['label' => 'Navy',    'color' => '#1e3a5f'],
                                'brown'   => ['label' => 'Cokelat', 'color' => '#78350f'],
                                'pink'    => ['label' => 'Pink',    'color' => '#9f1239'],
                                'teal'    => ['label' => 'Teal',    'color' => '#115e59'],
                            ]; ?>
                            <div id="theme-picker">
                            <?php foreach (array_chunk($themes, 5, true) as $row): ?>
                            <div class="row g-2 mb-2">
                            <?php foreach ($row as $val => $theme): ?>
                            <div class="col">
                                <div class="theme-option <?= $current === $val ? 'theme-selected' : '' ?>" data-value="<?= $val ?>">
                                    <input type="radio" name="theme_color" value="<?= $val ?>" class="d-none" <?= $current === $val ? 'checked' : '' ?>>
                                    <div class="rounded-3 border p-2 text-center" style="cursor:pointer">
                                        <div class="rounded-2 mb-1" style="height:32px;background:<?= $theme['color'] ?>"></div>
                                        <span class="small fw-medium"><?= $theme['label'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                            </div>
                        <script>
                        document.getElementById('theme-picker').addEventListener('click', function(e) {
                            const opt = e.target.closest('.theme-option');
                            if (!opt) return;
                            opt.querySelector('input[type="radio"]').checked = true;
                            this.querySelectorAll('.theme-option').forEach(el => el.classList.remove('theme-selected'));
                            opt.classList.add('theme-selected');
                        });
                        </script>
                    </div>
                </div>
            </div>
            <style>
            .theme-option .rounded-3 { transition: box-shadow .15s, border-color .15s; }
            .theme-option:hover .rounded-3 { border-color: var(--cui-primary) !important; }
            .theme-option.theme-selected .rounded-3 { border-color: var(--cui-primary) !important; box-shadow: 0 0 0 2px var(--cui-primary); }
            </style>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapsePpdb" role="button">
                    <h6 class="mb-0"><i class="fas fa-graduation-cap me-2 text-primary"></i>SPMB</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapsePpdb">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">URL SPMB</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-external-link-alt text-body-tertiary"></i></span>
                                <input type="text" name="spmb_url" class="form-control border-start-0 ps-0" value="<?= $settings['spmb_url'] ?? '' ?>" placeholder="Mis. https://spmb.sekolah.sch.id">
                            </div>
                            <div class="form-text small">Tautan ke portal pendaftaran SPMB</div>
                        </div>
                        <?php if (!empty($settings['spmb_url'])): ?>
                        <a href="<?= $settings['spmb_url'] ?>" target="_blank" class="btn btn-success btn-sm rounded-pill">
                            <i class="fas fa-external-link-alt me-1"></i>Uji Tautan
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php $principalData = !empty($settings['principal']) ? json_decode($settings['principal'], true) : []; ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseKepsek" role="button">
                    <h6 class="mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Kepala Sekolah</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseKepsek">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Foto</label>
                            <div class="mb-2">
                                <img src="<?= $principalData['photo'] ?? '/images/default-principal.jpg' ?>" alt="preview" style="max-height:120px;width:auto" class="rounded border">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-image text-body-tertiary"></i></span>
                                <input type="file" name="principal_photo" class="form-control border-start-0 ps-0" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <input type="hidden" name="principal[current_image]" value="<?= $principalData['photo'] ?? '' ?>">
                            <div class="form-text small">Upload foto kepala sekolah. Maks 5MB, JPG/PNG/WebP. Kosongkan jika tidak diubah.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="principal[name]" class="form-control" value="<?= $principalData['name'] ?? '' ?>" placeholder="Mis. Drs. H. Ahmad Fauzi, M.Pd.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Jabatan</label>
                            <input type="text" name="principal[role]" class="form-control" value="<?= $principalData['role'] ?? '' ?>" placeholder="Mis. Kepala Sekolah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Sambutan / Kutipan</label>
                            <textarea name="principal[quote]" class="form-control" rows="3" placeholder="Sambutan atau kutipan dari kepala sekolah..."><?= $principalData['quote'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseHeroSection" role="button">
                    <h6 class="mb-0"><i class="fas fa-star me-2 text-primary"></i>Bagian Hero</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseHeroSection">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Lencana</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-tag text-body-tertiary"></i></span>
                                <input type="text" name="hero_badge" class="form-control border-start-0 ps-0" value="<?= $settings['hero_badge'] ?? '' ?>" placeholder="Mis. Terakreditasi A">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Judul</label>
                            <input type="text" name="hero_title" class="form-control" value="<?= $settings['hero_title'] ?? '' ?>" placeholder="Mis. Selamat Datang di Sekolah Kami">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Subjudul</label>
                            <textarea name="hero_subtitle" class="form-control" rows="2" placeholder="Mis. Mencerdaskan kehidupan bangsa melalui pendidikan berkualitas..."><?= $settings['hero_subtitle'] ?? '' ?></textarea>
                        </div>
                        <hr class="my-3">
                        <h6 class="small fw-semibold text-body-tertiary mb-2"><i class="fas fa-link me-1"></i>Tombol Hero</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Teks Tombol Utama</label>
                                <input type="text" name="hero_btn_primary_text" class="form-control" value="<?= $settings['hero_btn_primary_text'] ?? '' ?>" placeholder="Mis. Informasi Pendaftaran">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">URL Tombol Utama</label>
                                <input type="text" name="hero_btn_primary_url" class="form-control" value="<?= $settings['hero_btn_primary_url'] ?? '' ?>" placeholder="Mis. /spmb">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Teks Tombol Sekunder</label>
                                <input type="text" name="hero_btn_secondary_text" class="form-control" value="<?= $settings['hero_btn_secondary_text'] ?? '' ?>" placeholder="Mis. Tentang Kami">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">URL Tombol Sekunder</label>
                                <input type="text" name="hero_btn_secondary_url" class="form-control" value="<?= $settings['hero_btn_secondary_url'] ?? '' ?>" placeholder="Mis. /tentang">
                            </div>
                        </div>
                        <div class="border rounded-3 bg-body-tertiary p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill fs-6"><?= $settings['hero_badge'] ?? 'Lencana' ?></span>
                                <span class="small text-body-tertiary"><i class="fas fa-mobile-screen me-1"></i>Pratinjau</span>
                            </div>
                            <h5 class="mb-1"><?= $settings['hero_title'] ?? 'Judul Hero' ?></h5>
                            <p class="small text-body-tertiary mb-2"><?= $settings['hero_subtitle'] ?? 'Subtitle hero akan muncul di sini...' ?></p>
                            <div class="d-flex gap-2">
                                <span class="btn btn-primary btn-sm disabled rounded-pill"><?= $settings['hero_btn_primary_text'] ?? 'Utama' ?></span>
                                <span class="btn btn-outline-secondary btn-sm disabled rounded-pill"><?= $settings['hero_btn_secondary_text'] ?? 'Sekunder' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseCounter" role="button">
                    <h6 class="mb-0"><i class="fas fa-chart-simple me-2 text-primary"></i>Statistik Counter</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseCounter">
                    <div class="card-body">
                        <p class="small text-body-tertiary mb-3">Angka statistik di section counter (animasi naik) — icon cukup nama class Font Awesome tanpa <code>fa-</code></p>
                        <?php $counterStats = !empty($settings['counter_stats']) ? json_decode($settings['counter_stats'], true) : []; ?>
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <?php $item = $counterStats[$i] ?? ['icon' => '', 'number' => '', 'suffix' => '+', 'label' => '']; ?>
                        <div class="row g-2 mb-3 align-items-end border rounded-3 p-3 bg-body-tertiary">
                            <div class="col-md-3">
                                <label class="form-label small fw-medium">Ikon</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-icons text-body-tertiary"></i></span>
                                    <input type="text" name="counter_stats[<?= $i ?>][icon]" class="form-control border-start-0 ps-0" value="<?= $item['icon'] ?>" placeholder="layer-group">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-medium">Angka</label>
                                <input type="number" name="counter_stats[<?= $i ?>][number]" class="form-control form-control-sm" value="<?= $item['number'] ?>" placeholder="40">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-medium">Akhiran</label>
                                <input type="text" name="counter_stats[<?= $i ?>][suffix]" class="form-control form-control-sm" value="<?= $item['suffix'] ?>" placeholder="+">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-medium">Label</label>
                                <input type="text" name="counter_stats[<?= $i ?>][label]" class="form-control form-control-sm" value="<?= $item['label'] ?>" placeholder="Rombongan Belajar">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <?php $banners = !empty($settings['page_banners']) ? json_decode($settings['page_banners'], true) : []; ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseBanners" role="button">
                    <h6 class="mb-0"><i class="fas fa-image me-2 text-primary"></i>Banner Halaman</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseBanners">
                    <div class="card-body">
                        <p class="small text-body-tertiary mb-3">Atur badge, judul, dan subtitle banner di setiap halaman</p>
                        <?php $pages = ['news' => 'Berita', 'single_post' => 'Detail Berita', 'pages' => 'Halaman Statis', 'contact' => 'Kontak', 'downloads' => 'Download Center']; ?>
                        <?php foreach ($pages as $key => $label): ?>
                        <?php $b = $banners[$key] ?? ['badge' => '', 'title' => '', 'subtitle' => '']; ?>
                        <div class="border rounded-3 p-3 bg-body-tertiary mb-3">
                            <h6 class="small fw-semibold mb-2"><?= $label ?></h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Lencana</label>
                                    <input type="text" name="page_banners[<?= $key ?>][badge]" class="form-control form-control-sm" value="<?= $b['badge'] ?>" placeholder="Mis. Berita Terkini">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Judul</label>
                                    <input type="text" name="page_banners[<?= $key ?>][title]" class="form-control form-control-sm" value="<?= $b['title'] ?>" placeholder="Mis. Baca Berita">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Subtitle</label>
                                    <input type="text" name="page_banners[<?= $key ?>][subtitle]" class="form-control form-control-sm" value="<?= $b['subtitle'] ?>" placeholder="Informasi terbaru...">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php $aboutData = !empty($settings['about']) ? json_decode($settings['about'], true) : []; ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseAbout" role="button">
                    <h6 class="mb-0"><i class="fas fa-building-columns me-2 text-primary"></i>Tentang Kami / Profil Sekolah</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseAbout">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Gambar</label>
                            <?php if (!empty($aboutData['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= $aboutData['image'] ?>" alt="preview" style="max-height:100px;width:auto" class="rounded border">
                            </div>
                            <?php endif; ?>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-image text-body-tertiary"></i></span>
                                <input type="file" name="about_image" class="form-control border-start-0 ps-0" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <input type="hidden" name="about[current_image]" value="<?= $aboutData['image'] ?? '' ?>">
                            <div class="form-text small">Upload gambar (akan diubah ke lebar 1200px). Maks 5MB, JPG/PNG/WebP. Kosongkan jika tidak diubah.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Judul Konten</label>
                            <input type="text" name="about[content_title]" class="form-control" value="<?= $aboutData['content_title'] ?? '' ?>" placeholder="Mis. Mewujudkan Pendidikan Berkualitas untuk Masa Depan">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Visi</label>
                                <textarea name="about[visi]" class="form-control" rows="3" placeholder="Visi sekolah..."><?= $aboutData['visi'] ?? '' ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium">Misi</label>
                                <textarea name="about[misi]" class="form-control" rows="3" placeholder="Misi sekolah..."><?= $aboutData['misi'] ?? '' ?></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Paragraf 1</label>
                            <textarea name="about[content_1]" class="form-control" rows="3" placeholder="Paragraf pertama..."><?= $aboutData['content_1'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Paragraf 2</label>
                            <textarea name="about[content_2]" class="form-control" rows="3" placeholder="Paragraf kedua (opsional)..."><?= $aboutData['content_2'] ?? '' ?></textarea>
                        </div>
                        <hr class="my-3">
                        <h6 class="small fw-semibold text-body-tertiary mb-2"><i class="fas fa-medal me-1"></i>Akreditasi</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium">Nilai</label>
                                <input type="text" name="about[accreditation]" class="form-control" value="<?= $aboutData['accreditation'] ?? '' ?>" placeholder="Mis. A">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-medium">Label</label>
                                <input type="text" name="about[accreditation_label]" class="form-control" value="<?= $aboutData['accreditation_label'] ?? '' ?>" placeholder="Mis. Standar Nasional">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Sorotan <span class="text-body-tertiary fw-normal">(satu per baris)</span></label>
                            <textarea name="about[highlights_text]" class="form-control" rows="4" placeholder="Akreditasi A&#10;Kurikulum Merdeka&#10;Lulusan 100% Terserap"><?php
                                $hlItems = $aboutData['highlights'] ?? [];
                                if (is_array($hlItems)) {
                                    $lines = [];
                                    foreach ($hlItems as $hl) {
                                        $lines[] = is_array($hl) ? ($hl['text'] ?? '') : $hl;
                                    }
                                    echo implode("\n", $lines);
                                }
                            ?></textarea>
                            <div class="form-text small">Setiap baris menjadi item centang di bagian tentang</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $sectionSettings = !empty($settings['section_settings']) ? json_decode($settings['section_settings'], true) : []; ?>
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseSectionSettings" role="button">
                    <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-primary"></i>Pengaturan Section</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseSectionSettings">
                    <div class="card-body">
                        <p class="small text-body-tertiary mb-3">Ubah judul dan subtitle setiap section di halaman beranda</p>
                        <?php $sections = [
                            'hero' => 'Hero',
                            'counter' => 'Counter Statistik',
                            'profile' => 'Profil Sekolah',
                            'programs' => 'Program Unggulan',
                            'extracurriculars' => 'Ekstrakurikuler',
                            'teachers' => 'Tenaga Pengajar',
                            'achievements' => 'Prestasi Siswa',
                            'testimonials' => 'Testimoni',
                            'news' => 'Berita',
                            'gallery' => 'Galeri',
                            'events' => 'Agenda',
                            'faq' => 'FAQ',
                            'downloads' => 'Download Center',
                            'contact' => 'Kontak',
                        ]; ?>
                        <?php foreach ($sections as $key => $label): ?>
                        <?php $ss = $sectionSettings[$key] ?? ['title' => '', 'subtitle' => '', 'show' => 1]; ?>
                        <div class="border rounded-3 p-3 bg-body-tertiary mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="small fw-semibold mb-0"><?= $label ?></h6>
                                <input type="hidden" name="section_settings[<?= $key ?>][show]" value="0">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="section_settings[<?= $key ?>][show]" value="1" <?= ($ss['show'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label small">Tampilkan</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium">Judul</label>
                                    <input type="text" name="section_settings[<?= $key ?>][title]" class="form-control form-control-sm" value="<?= esc($ss['title'] ?? '') ?>" placeholder="Judul <?= $label ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-medium">Subtitle</label>
                                    <input type="text" name="section_settings[<?= $key ?>][subtitle]" class="form-control form-control-sm" value="<?= esc($ss['subtitle'] ?? '') ?>" placeholder="Subtitle <?= $label ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#collapseFooter" role="button">
                    <h6 class="mb-0"><i class="fas fa-info me-2 text-primary"></i>Footer</h6>
                    <i class="fas fa-chevron-down fa-xs text-body-tertiary collapse-icon"></i>
                </div>
                <div class="collapse" id="collapseFooter">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea name="footer_description" class="form-control" rows="2" placeholder="Tentang sekolah, visi, atau pengantar singkat untuk footer..."><?= $settings['footer_description'] ?? '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Teks Copyright</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fas fa-copyright text-body-tertiary"></i></span>
                                <input type="text" name="footer_copyright" class="form-control border-start-0 ps-0" value="<?= $settings['footer_copyright'] ?? '' ?>" placeholder="Mis. 2026 SMP Negeri 1 Jakarta. Hak Cipta Dilindungi.">
                            </div>
                        </div>
                        <hr class="my-3">
                        <?php $footerServices = !empty($settings['footer_services']) ? json_decode($settings['footer_services'], true) : []; ?>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Program</label>
                            <div class="form-text small text-body-tertiary mb-2">Tautan kolom "Program" di footer (maks. 10 item)</div>
                            <div id="footerServicesList">
                                <?php if (!empty($footerServices)): ?>
                                <?php foreach ($footerServices as $idx => $svc): ?>
                                <div class="row g-2 mb-2 align-items-center footer-service-item">
                                    <div class="col">
                                        <input type="text" name="footer_services[<?= $idx ?>][label]" class="form-control form-control-sm" value="<?= esc($svc['label'] ?? '') ?>" placeholder="Label (mis. Sains & Teknologi)" maxlength="50">
                                    </div>
                                    <div class="col">
                                        <input type="text" name="footer_services[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= esc($svc['url'] ?? '') ?>" placeholder="URL (mis. #programs)">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-footer-service" title="Hapus" style="width:32px;height:32px"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-1 add-footer-service" data-max="10"><i class="fas fa-plus me-1"></i>Tambah Program</button>
                        </div>
                        <hr class="my-3">
                        <?php $footerLinks = !empty($settings['footer_links']) ? json_decode($settings['footer_links'], true) : []; ?>
                        <div class="mb-0">
                            <label class="form-label small fw-medium">Layanan</label>
                            <div class="form-text small text-body-tertiary mb-2">Tautan kolom "Layanan" di footer (maks. 10 item)</div>
                            <div id="footerLinksList">
                                <?php if (!empty($footerLinks)): ?>
                                <?php foreach ($footerLinks as $idx => $lnk): ?>
                                <div class="row g-2 mb-2 align-items-center footer-link-item">
                                    <div class="col">
                                        <input type="text" name="footer_links[<?= $idx ?>][label]" class="form-control form-control-sm" value="<?= esc($lnk['label'] ?? '') ?>" placeholder="Label (mis. SPMB Online)" maxlength="50">
                                    </div>
                                    <div class="col">
                                        <input type="text" name="footer_links[<?= $idx ?>][url]" class="form-control form-control-sm" value="<?= esc($lnk['url'] ?? '') ?>" placeholder="URL (mis. https://spmb.sekolah.sch.id)">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-footer-link" title="Hapus" style="width:32px;height:32px"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-1 add-footer-link" data-max="10"><i class="fas fa-plus me-1"></i>Tambah Layanan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 pt-4 border-top mt-4">
        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-1"></i>Simpan Pengaturan</button>
        <a href="/admin/dashboard" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-times me-1"></i>Batal</a>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.card-header[data-bs-toggle="collapse"]').forEach(function(header) {
    header.addEventListener('click', function() {
        var target = document.querySelector(this.getAttribute('data-bs-target'));
        if (target) {
            var expanded = target.classList.contains('show');
            target.classList.toggle('show');
            this.classList.toggle('collapsed');
            this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        }
    });
});

function addFooterItem(listId, itemClass, namePrefix) {
    var list = document.getElementById(listId);
    var count = list.querySelectorAll('.' + itemClass).length;
    var max = 10;
    if (count >= max) return;
    var html = '<div class="row g-2 mb-2 align-items-center ' + itemClass + '">'
        + '<div class="col"><input type="text" name="' + namePrefix + '[' + count + '][label]" class="form-control form-control-sm" placeholder="Label" maxlength="50"></div>'
        + '<div class="col"><input type="text" name="' + namePrefix + '[' + count + '][url]" class="form-control form-control-sm" placeholder="URL"></div>'
        + '<div class="col-auto"><button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-' + itemClass + '" title="Hapus" style="width:32px;height:32px"><i class="fas fa-times"></i></button></div>'
        + '</div>';
    list.insertAdjacentHTML('beforeend', html);
}

function bindRemoveBtns(itemClass, listId, namePrefix) {
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.remove-' + itemClass);
        if (!btn) return;
        btn.closest('.' + itemClass).remove();
        var list = document.getElementById(listId);
        var items = list.querySelectorAll('.' + itemClass);
        items.forEach(function(row, i) {
            row.querySelector('[name]').name = namePrefix + '[' + i + '][label]';
            row.querySelectorAll('input')[1].name = namePrefix + '[' + i + '][url]';
        });
    });
}

document.querySelector('.add-footer-service').addEventListener('click', function() {
    addFooterItem('footerServicesList', 'footer-service-item', 'footer_services');
});
document.querySelector('.add-footer-link').addEventListener('click', function() {
    addFooterItem('footerLinksList', 'footer-link-item', 'footer_links');
});
bindRemoveBtns('footer-service-item', 'footerServicesList', 'footer_services');
bindRemoveBtns('footer-link-item', 'footerLinksList', 'footer_links');
</script>
<?= $this->endSection() ?>
