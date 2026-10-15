<?= $this->include('layouts/header') ?>

  <!-- ===== PAGE BANNER ===== -->
  <?php $banner = !empty($page_banners) ? (json_decode($page_banners, true)['downloads'] ?? []) : []; ?>
  <section class="page-banner" id="main-content">
    <div class="page-banner-shape page-banner-shape--1"></div>
    <div class="page-banner-shape page-banner-shape--2"></div>
    <div class="container page-banner-content">
      <span class="page-banner-badge"><i class="fas fa-download"></i> <?= esc($banner['badge'] ?? '') ?: 'Download Center' ?></span>
      <h1><?= esc($banner['title'] ?? '') ?: 'Pusat Unduhan' ?> <span class="text-gradient"><?= esc($site_name) ?></span></h1>
      <p><?= esc($banner['subtitle'] ?? '') ?: 'Unduh berkas-berkas penting seputar akademik, kurikulum, dan administrasi sekolah' ?></p>
    </div>
  </section>

  <!-- ===== DOWNLOADS CONTENT ===== -->
  <section class="section">
    <div class="container">
      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category => $items): ?>
        <div class="download-group">
          <div class="download-group-header">
            <i class="fas fa-folder-open"></i>
            <h2><?= esc($category) ?></h2>
            <span class="download-group-count"><?= count($items) ?> berkas</span>
          </div>
          <div class="download-list">
            <?php foreach ($items as $item): ?>
            <a href="<?= esc($item['url']) ?>" target="_blank" rel="noopener" class="download-card">
              <div class="dc-icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <div class="dc-body">
                <h3><?= esc($item['title']) ?></h3>
                <?php if (!empty($item['description'])): ?>
                <p><?= esc($item['description']) ?></p>
                <?php endif; ?>
              </div>
              <div class="dc-meta">
                <?php if (!empty($item['file_size'])): ?>
                <span class="dc-size"><i class="fas fa-weight"></i> <?= esc($item['file_size']) ?></span>
                <?php endif; ?>
                <span class="dc-download"><i class="fas fa-download"></i></span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
      <div class="text-center py-5">
        <i class="fas fa-download fa-4x text-body-tertiary opacity-50 mb-3" style="display:block;color:var(--text-muted)"></i>
        <p style="color:var(--text-soft)">Belum ada berkas download tersedia.</p>
      </div>
      <?php endif; ?>
    </div>
  </section>

<?= $this->include('layouts/footer') ?>
