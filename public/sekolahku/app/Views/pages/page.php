<?= $this->include('layouts/header') ?>

  <!-- ===== PAGE BANNER ===== -->
  <?php $pageBanner = !empty($page_banners) ? (json_decode($page_banners, true)['pages'] ?? []) : []; ?>
  <section class="page-banner" id="main-content">
    <div class="page-banner-shape page-banner-shape--1"></div>
    <div class="page-banner-shape page-banner-shape--2"></div>
    <div class="container page-banner-content">
      <span class="page-banner-badge"><i class="fas fa-file-alt"></i> <?= esc($pageBanner['badge'] ?? '') ?: 'Halaman' ?></span>
      <h1><?= esc($pageBanner['title'] ?? '') ?: 'Baca Halaman' ?> <span class="text-gradient"><?= esc($site_name) ?></span></h1>
      <p><?= esc($pageBanner['subtitle'] ?? '') ?: 'Informasi lengkap seputar halaman ini' ?></p>
    </div>
  </section>

  <!-- ===== PAGE CONTENT ===== -->
  <section class="section">
    <div class="container">
      <div class="news-layout">
        <div class="news-main">

          <!-- Breadcrumb -->
          <nav class="sp-breadcrumb">
            <a href="<?= base_url() ?>">Beranda</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= esc($page['title']) ?></span>
          </nav>

          <article class="sp-article">
            <div class="sp-top">
              <h1><?= esc($page['title']) ?></h1>
              <?php if ($page['excerpt']): ?>
              <p class="text-body-secondary lead mt-2"><?= esc($page['excerpt']) ?></p>
              <?php endif; ?>
              <div class="sp-meta">
                <span><i class="far fa-calendar"></i> <?= date('j F Y', strtotime($page['created_at'] ?? $page['updated_at'])) ?></span>
              </div>
            </div>

            <?php if (!empty($page['image'])): ?>
            <div class="sp-cover">
              <img loading="lazy" src="<?= esc($page['image']) ?>" alt="<?= esc($page['title']) ?>">
            </div>
            <?php endif; ?>

            <div class="sp-body">
              <?= $page['content'] ?>
            </div>

            <!-- Share -->
            <div class="sp-share">
              <span>Bagikan:</span>
              <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
              <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode(esc($page['title'])) ?>" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
              <a href="https://wa.me/?text=<?= urlencode(esc($page['title']).' '.current_url()) ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
              <a href="https://t.me/share/url?url=<?= urlencode(current_url()) ?>&text=<?= urlencode(esc($page['title'])) ?>" target="_blank" rel="noopener"><i class="fab fa-telegram"></i></a>
              <a href="#" onclick="navigator.clipboard.writeText(window.location.href);alert('Link disalin!');return false;"><i class="fas fa-link"></i></a>
            </div>
          </article>

        </div>

        <!-- SIDEBAR -->
        <aside class="news-sidebar">
          <div class="ns-widget">
            <h3>Pencarian</h3>
            <form class="ns-search" action="<?= base_url('news') ?>" method="get">
              <input type="text" name="search" placeholder="Cari berita..." value="">
              <button type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>

          <?php if (isset($all_pages) && count($all_pages) > 1): ?>
          <div class="ns-widget">
            <h3>Halaman Lainnya</h3>
            <div class="ns-categories">
              <?php foreach ($all_pages as $p): ?>
                <?php if ($p['id'] === $page['id']) continue; ?>
                <a href="<?= base_url('pages/'.esc($p['slug'])) ?>">
                  <?= esc($p['title']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (isset($categories) && !empty($categories)): ?>
          <div class="ns-widget">
            <h3>Kategori</h3>
            <div class="ns-categories">
              <?php foreach ($categories as $cat): ?>
              <a href="<?= base_url('news?category='.esc($cat['slug'])) ?>">
                <?= esc($cat['name']) ?> <span><?= esc($cat['count']) ?></span>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (isset($recent_news) && !empty($recent_news)): ?>
          <div class="ns-widget">
            <h3>Berita Terbaru</h3>
            <div class="ns-recent">
              <?php foreach ($recent_news as $rn): ?>
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

          <?php if (isset($archive) && !empty($archive)): ?>
          <div class="ns-widget">
            <h3>Arsip Berita</h3>
            <div class="ns-archive">
              <?php foreach ($archive as $a): ?>
              <a href="<?= base_url('news?month='.esc($a['month'])) ?>"><?= esc($a['label']) ?> <span><?= esc($a['count']) ?></span></a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <?php if (isset($tags) && !empty($tags)): ?>
          <div class="ns-widget">
            <h3>Tags</h3>
            <div class="ns-tags">
              <?php foreach ($tags as $tag): ?>
              <a href="<?= base_url('news?tag='.esc($tag['slug'])) ?>"><?= esc($tag['name']) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="ns-widget ns-widget-cta">
            <h3>Ikuti Kami</h3>
            <div class="ns-social">
              <?php if (isset($social_facebook) && $social_facebook): ?>
              <a href="<?= esc($social_facebook) ?>"><i class="fab fa-facebook-f"></i></a>
              <?php endif; ?>
              <?php if (isset($social_instagram) && $social_instagram): ?>
              <a href="<?= esc($social_instagram) ?>"><i class="fab fa-instagram"></i></a>
              <?php endif; ?>
              <?php if (isset($social_youtube) && $social_youtube): ?>
              <a href="<?= esc($social_youtube) ?>"><i class="fab fa-youtube"></i></a>
              <?php endif; ?>
              <?php if (isset($social_tiktok) && $social_tiktok): ?>
              <a href="<?= esc($social_tiktok) ?>"><i class="fab fa-tiktok"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </section>

<?= $this->include('layouts/footer') ?>
