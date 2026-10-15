<?= $this->include('layouts/header') ?>

  <!-- ===== PAGE BANNER ===== -->
  <?php $newsBanner = !empty($page_banners) ? (json_decode($page_banners, true)['news'] ?? []) : []; ?>
  <section class="page-banner" id="main-content">
    <div class="page-banner-shape page-banner-shape--1"></div>
    <div class="page-banner-shape page-banner-shape--2"></div>
    <div class="container page-banner-content">
      <span class="page-banner-badge"><i class="fas fa-newspaper"></i> <?= esc($newsBanner['badge'] ?? '') ?: 'Berita & Artikel' ?></span>
      <h1><?= esc($newsBanner['title'] ?? '') ?: 'Berita' ?> <span class="text-gradient"><?= esc($site_name) ?></span></h1>
      <p><?= esc($newsBanner['subtitle'] ?? '') ?: 'Informasi terkini seputar kegiatan, prestasi, dan pengumuman di lingkungan sekolah' ?></p>
    </div>
  </section>

  <!-- ===== NEWS CONTENT ===== -->
  <section class="section">
    <div class="container">
      <div class="news-layout">
        <div class="news-main">

          <!-- Filter -->
          <div class="news-filter">
            <a href="<?= base_url('news') ?>" class="nf-btn <?= empty($category) ? 'active' : '' ?>">Semua</a>
            <?php foreach($categories as $cat): ?>
            <a href="<?= base_url('news?category='.urlencode($cat['slug'])) ?>" class="nf-btn <?= (isset($category) && $category === $cat['slug']) ? 'active' : '' ?>"><?= esc($cat['name']) ?></a>
            <?php endforeach; ?>
          </div>

          <!-- Grid -->
          <div class="news-grid">
            <?php if(isset($news) && !empty($news)): ?>
              <?php foreach($news as $item): ?>
              <article class="news-card" data-categories="<?= esc(strtolower($item['category'] ?? '')) ?>">
                <div class="bc-img">
                  <img loading="lazy" src="<?= esc($item['image'] ?? 'https://placehold.co/600x400/0c4a6e/ffffff?text=Berita') ?>" alt="<?= esc($item['title']) ?>">
                  <span class="bc-cat"><?= esc(implode(' / ', array_map('trim', explode(',', $item['category'] ?? 'Berita')))) ?></span>
                </div>
                <div class="bc-body">
                  <div class="bc-meta">
                    <span><i class="far fa-calendar"></i> <?= date('j F Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></span>
                    <span><i class="far fa-user"></i> <?= esc($item['author'] ?? 'Admin') ?></span>
                  </div>
                  <h3><?= esc($item['title']) ?></h3>
                  <p><?= esc($item['excerpt']) ?></p>
                  <a href="<?= base_url('news/'.esc($item['slug'])) ?>" class="bc-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                </div>
              </article>
              <?php endforeach; ?>
            <?php else: ?>
              <p>Tidak ada berita saat ini.</p>
            <?php endif; ?>
          </div>

          <!-- Pagination -->
          <?php if(isset($pager) && $pager): ?>
          <div class="news-pagination">
            <?= $pager->links() ?>
          </div>
          <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="news-sidebar">
          <div class="ns-widget">
            <h3>Pencarian</h3>
            <form class="ns-search" action="<?= base_url('news') ?>" method="get">
              <input type="text" name="search" placeholder="Cari berita..." value="<?= esc($search ?? '') ?>">
              <button type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>

          <?php if(isset($categories) && !empty($categories)): ?>
          <div class="ns-widget">
            <h3>Kategori</h3>
            <div class="ns-categories">
              <?php foreach($categories as $cat): ?>
              <a href="<?= base_url('news?category='.esc($cat['slug'] ?? $cat)) ?>" class="<?= (isset($active_category) && $active_category === ($cat['slug'] ?? $cat)) ? 'active' : '' ?>">
                <?= esc($cat['name'] ?? $cat) ?> <span><?= esc($cat['count'] ?? 0) ?></span>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if(isset($recent_news) && !empty($recent_news)): ?>
          <div class="ns-widget">
            <h3>Berita Terbaru</h3>
            <div class="ns-recent">
              <?php foreach($recent_news as $rn): ?>
              <div class="nsr-item">
                <img loading="lazy" src="<?= esc($rn['image'] ?? 'https://placehold.co/70x70/0c4a6e/ffffff?text=N') ?>" alt="thumb">
                <div>
                  <h4><a href="<?= base_url('news/'.esc($rn['slug'])) ?>"><?= esc($rn['title']) ?></a></h4>
                  <span><?= date('j F Y', strtotime($rn['published_at'] ?? $rn['created_at'])) ?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if(isset($archive) && !empty($archive)): ?>
          <div class="ns-widget">
            <h3>Arsip Berita</h3>
            <div class="ns-archive">
              <?php foreach($archive as $a): ?>
              <a href="<?= base_url('news?month='.esc($a['month'])) ?>" class="<?= (isset($month) && $month === $a['month']) ? 'active' : '' ?>"><?= esc($a['label']) ?> <span><?= esc($a['count']) ?></span></a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if(isset($tags) && !empty($tags)): ?>
          <div class="ns-widget">
            <h3>Tags</h3>
            <div class="ns-tags">
              <?php foreach($tags as $tag): ?>
              <a href="<?= base_url('news?tag='.esc($tag['slug'] ?? $tag)) ?>"><?= esc($tag['name'] ?? $tag) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="ns-widget ns-widget-cta">
            <h3>Ikuti Kami</h3>
            <div class="ns-social">
              <?php if(isset($social_facebook) && $social_facebook): ?>
              <a href="<?= esc($social_facebook) ?>"><i class="fab fa-facebook-f"></i></a>
              <?php endif; ?>
              <?php if(isset($social_instagram) && $social_instagram): ?>
              <a href="<?= esc($social_instagram) ?>"><i class="fab fa-instagram"></i></a>
              <?php endif; ?>
              <?php if(isset($social_youtube) && $social_youtube): ?>
              <a href="<?= esc($social_youtube) ?>"><i class="fab fa-youtube"></i></a>
              <?php endif; ?>
              <?php if(isset($social_tiktok) && $social_tiktok): ?>
              <a href="<?= esc($social_tiktok) ?>"><i class="fab fa-tiktok"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

<?= $this->include('layouts/footer') ?>
