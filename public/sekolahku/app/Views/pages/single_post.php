<?= $this->include('layouts/header') ?>

  <!-- ===== PAGE BANNER ===== -->
  <?php $detailBanner = !empty($page_banners) ? (json_decode($page_banners, true)['single_post'] ?? []) : []; ?>
  <section class="page-banner" id="main-content">
    <div class="page-banner-shape page-banner-shape--1"></div>
    <div class="page-banner-shape page-banner-shape--2"></div>
    <div class="container page-banner-content">
      <span class="page-banner-badge"><i class="fas fa-newspaper"></i> <?= esc($detailBanner['badge'] ?? '') ?: 'Detail Berita' ?></span>
      <h1><?= esc($detailBanner['title'] ?? '') ?: 'Baca Berita' ?> <span class="text-gradient"><?= esc($site_name) ?></span></h1>
      <p><?= esc($detailBanner['subtitle'] ?? '') ?: 'Informasi lengkap seputar kegiatan dan prestasi di lingkungan sekolah' ?></p>
    </div>
  </section>

  <!-- ===== SINGLE POST ===== -->
  <section class="section">
    <div class="container">
      <div class="news-layout">
        <div class="news-main">

          <!-- Breadcrumb -->
          <nav class="sp-breadcrumb">
            <a href="<?= base_url() ?>">Beranda</a>
            <i class="fas fa-chevron-right"></i>
            <a href="<?= base_url('news') ?>">Berita</a>
            <i class="fas fa-chevron-right"></i>
            <span><?= esc($post['title'] ?? $news_item['title'] ?? '') ?></span>
          </nav>

          <?php
          $article = isset($post) ? $post : ($news_item ?? []);
          ?>

          <article class="sp-article">
            <div class="sp-top">
              <span class="bc-cat"><?= esc(implode(' / ', array_map('trim', explode(',', $article['category'] ?? 'Berita')))) ?></span>
              <h1><?= esc($article['title']) ?></h1>
              <div class="sp-meta">
                <span><i class="far fa-calendar"></i> <?= date('j F Y', strtotime($article['published_at'] ?? $article['created_at'])) ?></span>
                <span><i class="far fa-user"></i> <?= esc($article['author'] ?? 'Admin') ?></span>
                <?php if(isset($article['views']) && $article['views']): ?>
                <span><i class="far fa-eye"></i> <?= esc(number_format($article['views'])) ?> dilihat</span>
                <?php endif; ?>
              </div>
            </div>

            <?php if(isset($article['image']) && $article['image']): ?>
            <div class="sp-cover">
              <img loading="lazy" src="<?= esc($article['image']) ?>" alt="<?= esc($article['title']) ?>">
            </div>
            <?php endif; ?>

            <div class="sp-body">
              <?= $article['content'] ?? '' ?>
            </div>

            <!-- Share -->
            <div class="sp-share">
              <span>Bagikan:</span>
              <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
              <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode(esc($article['title'])) ?>" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
              <a href="https://wa.me/?text=<?= urlencode(esc($article['title']).' '.current_url()) ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
              <a href="https://t.me/share/url?url=<?= urlencode(current_url()) ?>&text=<?= urlencode(esc($article['title'])) ?>" target="_blank" rel="noopener"><i class="fab fa-telegram"></i></a>
              <a href="#" onclick="navigator.clipboard.writeText(window.location.href);alert('Link disalin!');return false;"><i class="fas fa-link"></i></a>
            </div>
            </article>

            <!-- Related Posts -->
            <?php if(isset($related) && !empty($related)): ?>
            <div class="sp-related">
              <h3><i class="fas fa-newspaper"></i> Berita Terkait</h3>
              <div class="sp-related-grid">
                <?php foreach($related as $rp): ?>
                <a href="<?= base_url('news/'.esc($rp['slug'])) ?>" class="sp-related-card">
                  <?php if(!empty($rp['image'])): ?>
                  <img loading="lazy" src="<?= esc($rp['image']) ?>" alt="<?= esc($rp['title']) ?>">
                  <?php endif; ?>
                  <div>
                    <h4><?= esc($rp['title']) ?></h4>
                    <span><i class="far fa-calendar"></i> <?= date('j F Y', strtotime($rp['published_at'] ?? $rp['created_at'])) ?></span>
                  </div>
                </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>

           <!-- Comments -->
          <div class="sp-comments">
            <div class="sp-comments-header">
              <h3><i class="far fa-comments"></i> <?= isset($comments) ? count($comments) : '0' ?> Komentar</h3>
              <span>Bergabunglah dalam diskusi</span>
            </div>

            <?php if(isset($comments) && !empty($comments)): ?>
            <div class="sp-comment-list">
              <?php foreach($comments as $cm): ?>
              <div class="spc-item <?= isset($cm['parent_id']) ? 'spc-reply-item' : '' ?>" data-comment-id="<?= $cm['id'] ?>">
                <img loading="lazy" src="<?= esc($cm['avatar'] ?? 'https://placehold.co/44x44/0c4a6e/ffffff?text='.urlencode(substr($cm['name'], 0, 1))) ?>" alt="avatar">
                <div>
                  <div class="spc-head">
                    <strong><?= esc($cm['name']) ?></strong>
                    <span><?= date('j F Y', strtotime($cm['created_at'])) ?></span>
                  </div>
                  <p><?= esc($cm['comment'] ?? $cm['message']) ?></p>
                  <button class="spc-reply"><i class="fas fa-reply"></i> Balas</button>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="sp-comment-form">
              <h3><i class="far fa-edit"></i> Tinggalkan Komentar</h3>
              <form action="<?= base_url('news/'.esc($article['slug']).'/comment') ?>" method="post">
                <?= csrf_field() ?>
                <div style="position:absolute;left:-9999px" aria-hidden="true">
                  <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <input type="hidden" name="parent_id" id="comment_parent_id" value="">
                <input type="hidden" name="slug" value="<?= esc($article['slug']) ?>">
                <div class="spcf-row">
                  <input type="text" name="name" placeholder="Nama Lengkap *" required>
                  <input type="email" name="email" placeholder="Email *" required>
                </div>
                <textarea name="message" rows="5" placeholder="Tulis komentar Anda... *" required></textarea>
                <div class="spcf-check">
                  <label>
                    <input type="checkbox" name="save_info">
                    <span>Simpan nama dan email saya untuk komentar berikutnya</span>
                  </label>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Komentar <i class="fas fa-paper-plane"></i></button>
              </form>
            </div>
          </div>

          <!-- Nav Post -->
          <?php if(isset($prev_post) || isset($next_post)): ?>
          <div class="sp-nav">
            <?php if(isset($prev_post) && $prev_post): ?>
            <a href="<?= base_url('news/'.esc($prev_post['slug'])) ?>" class="sp-nav-prev">
              <i class="fas fa-arrow-left"></i>
              <div>
                <span>Berita Sebelumnya</span>
                <h4><?= esc($prev_post['title']) ?></h4>
              </div>
            </a>
            <?php endif; ?>
            <?php if(isset($next_post) && $next_post): ?>
            <a href="<?= base_url('news/'.esc($next_post['slug'])) ?>" class="sp-nav-next">
              <div>
                <span>Berita Selanjutnya</span>
                <h4><?= esc($next_post['title']) ?></h4>
              </div>
              <i class="fas fa-arrow-right"></i>
            </a>
            <?php endif; ?>
          </div>
          <?php endif; ?>

        </div>

        <!-- SIDEBAR -->
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
              <a href="<?= base_url('news?category='.esc($cat['slug'] ?? $cat)) ?>">
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

<script>
document.querySelectorAll('.spc-reply').forEach(btn => {
  btn.addEventListener('click', function() {
    const parentItem = this.closest('.spc-item');
    const parentId = parentItem.dataset.commentId;
    document.getElementById('comment_parent_id').value = parentId;
    document.querySelector('.sp-comment-form').scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.querySelector('.sp-comment-form input[name="name"]').focus();
  });
});
</script>
