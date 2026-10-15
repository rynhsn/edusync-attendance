<?= $this->include('layouts/header') ?>

  <?php $ss = is_array($section_settings) ? $section_settings : (!empty($section_settings) ? json_decode($section_settings, true) : []); ?>

  <!-- ===== HERO ===== -->
  <section class="hero" id="main-content" aria-label="Selamat datang">
    <div class="hero-shape hero-shape--1" aria-hidden="true"></div>
    <div class="hero-shape hero-shape--2" aria-hidden="true"></div>
    <div class="hero-shape hero-shape--3" aria-hidden="true"></div>
    <div class="hero-shape hero-shape--4" aria-hidden="true"></div>
    <div class="container">
      <div class="hero-grid">
        <div class="hero-content">
          <?php if(isset($hero_badge) && $hero_badge): ?>
          <span class="hero-badge"><i class="fas fa-star" aria-hidden="true"></i> <?= esc($hero_badge) ?></span>
          <?php endif; ?>
          <h1><?= isset($hero_title) ? $hero_title : 'Selamat Datang di <span class="text-gradient">'.esc($site_name).'</span>' ?></h1>
          <p><?= esc($hero_subtitle ?? 'Membangun Generasi Cerdas, Berkarakter, dan Berprestasi menuju Masa Depan Gemilang') ?></p>
          <div class="hero-buttons">
            <?php if(isset($hero_btn_primary_text) && $hero_btn_primary_text): ?>
            <a href="<?= esc($hero_btn_primary_url ?? '#profile') ?>" class="btn btn-primary"><?= esc($hero_btn_primary_text) ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            <?php endif; ?>
            <?php if(isset($hero_btn_secondary_text) && $hero_btn_secondary_text): ?>
            <a href="<?= esc($hero_btn_secondary_url ?? '#contact') ?>" class="btn btn-outline"><?= esc($hero_btn_secondary_text) ?></a>
            <?php endif; ?>
          </div>
          <?php
          $stats = [];
          if(isset($hero_stats) && $hero_stats) {
            $stats = is_string($hero_stats) ? json_decode($hero_stats, true) : $hero_stats;
          }
          ?>
          <?php if(!empty($stats)): ?>
          <div class="hero-meta">
            <?php foreach($stats as $stat): ?>
            <div class="hero-meta-item">
              <span class="hm-number"><?= esc($stat['value'] ?? $stat['number'] ?? '0') ?></span>
              <span class="hm-label"><?= esc($stat['label'] ?? '') ?></span>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="hero-visual">
          <?php
          $principal_data = [];
          if(isset($principal) && $principal) {
            $principal_data = is_string($principal) ? json_decode($principal, true) : $principal;
          }
          ?>
          <?php if(!empty($principal_data)): ?>
          <div class="hero-card hero-card-principal">
            <div class="hc-img">
              <span class="hc-badge">Kata Sambutan</span>
              <img src="<?= esc($principal_data['photo'] ?? '/images/default-principal.jpg') ?>" alt="<?= esc($principal_data['name'] ?? 'Kepala Sekolah') ?>" loading="lazy">
            </div>
            <div class="hc-body">
              <h4><?= esc($principal_data['name'] ?? '') ?></h4>
              <span class="hc-role"><?= esc($principal_data['role'] ?? 'Kepala Sekolah') ?></span>
              <p><?= esc($principal_data['quote'] ?? $principal_data['sambutan'] ?? '') ?></p>
              <?php if(isset($principal_data['meta']) && is_array($principal_data['meta'])): ?>
              <div class="hc-principal-meta">
                <?php foreach($principal_data['meta'] as $m): ?>
                <span><i class="fas fa-<?= esc($m['icon'] ?? 'graduation-cap') ?>" aria-hidden="true"></i> <?= esc($m['text'] ?? $m) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== COUNTER ===== -->
  <?php
  $counterItems = [];
  if (isset($counter_stats) && $counter_stats) {
    $counterItems = is_string($counter_stats) ? json_decode($counter_stats, true) : $counter_stats;
  }
  if (empty($counterItems)):
    $counterItems = [
      ['icon' => 'layer-group', 'number' => '40', 'suffix' => '+', 'label' => 'Rombongan Belajar'],
      ['icon' => 'calendar-alt', 'number' => '25', 'suffix' => '+', 'label' => 'Tahun Berdiri'],
      ['icon' => 'book-open', 'number' => '24', 'suffix' => '+', 'label' => 'Program Studi'],
      ['icon' => 'user-friends', 'number' => '3000', 'suffix' => '+', 'label' => 'Alumni'],
    ];
  endif;
  ?>
  <section class="counter-section" aria-label="Statistik sekolah">
    <div class="container">
      <div class="counter-grid">
        <?php foreach ($counterItems as $c): ?>
        <div class="counter-card">
          <div class="cc-icon"><i class="fas fa-<?= esc($c['icon'] ?? 'layer-group') ?>" aria-hidden="true"></i></div>
          <div class="cc-content">
            <h3><span class="counter" data-target="<?= esc($c['number'] ?? '0') ?>">0</span><?= esc($c['suffix'] ?? '+') ?></h3>
            <p><?= esc($c['label'] ?? '') ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===== PROFIL ===== -->
  <?php
  $about_data = [];
  if(isset($about) && $about) {
    $about_data = is_string($about) ? json_decode($about, true) : $about;
  }
  ?>
  <?php if(!empty($about_data)): ?>
  <?php $profileSs = $ss['profile'] ?? []; ?>
  <section class="section" id="profile" aria-label="Profil sekolah">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($profileSs['title'] ?? 'Tentang Kami') ?></span>
        <h2>Mengenal <span class="text-gradient"><?= esc($site_name) ?></span></h2>
        <p><?= esc($about_data['content_title'] ?? $profileSs['subtitle'] ?? 'Sekolah unggulan yang berkomitmen mencetak generasi penerus bangsa yang berkualitas') ?></p>
      </div>
      <div class="about-grid">
        <div class="about-image">
          <div class="ai-main">
            <img src="<?= esc($about_data['image'] ?? 'https://placehold.co/600x400/0c4a6e/ffffff?text='.urlencode($site_name)) ?>" alt="Gedung <?= esc($site_name) ?>" loading="lazy">
          </div>
          <?php if(isset($about_data['accreditation']) && $about_data['accreditation']): ?>
          <div class="ai-card">
            <i class="fas fa-medal" aria-hidden="true"></i>
            <h4>Akreditasi <?= esc($about_data['accreditation']) ?></h4>
            <p><?= esc($about_data['accreditation_label'] ?? 'Standar Nasional') ?></p>
          </div>
          <?php endif; ?>
        </div>
        <div class="about-content">
          <h3><?= esc($about_data['content_title'] ?? 'Mewujudkan Pendidikan Berkualitas untuk Masa Depan') ?></h3>
          <?php if(!empty($about_data['visi'])): ?>
          <div class="about-visi">
            <strong>Visi:</strong>
            <p><?= esc($about_data['visi']) ?></p>
          </div>
          <?php endif; ?>
          <?php if(!empty($about_data['misi'])): ?>
          <div class="about-misi">
            <strong>Misi:</strong>
            <p><?= esc($about_data['misi']) ?></p>
          </div>
          <?php endif; ?>
          <?php if(isset($about_data['content_1']) && $about_data['content_1']): ?>
          <p><?= esc($about_data['content_1']) ?></p>
          <?php endif; ?>
          <?php if(isset($about_data['content_2']) && $about_data['content_2']): ?>
          <p><?= esc($about_data['content_2']) ?></p>
          <?php endif; ?>
          <?php if(isset($about_data['highlights']) && is_array($about_data['highlights'])): ?>
          <div class="about-list">
            <?php foreach($about_data['highlights'] as $hl): ?>
            <div class="al-item"><i class="fas fa-check-circle" aria-hidden="true"></i> <?= esc($hl['text'] ?? $hl) ?></div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== PROGRAM ===== -->
  <?php $pSs = $ss['programs'] ?? []; $showPrograms = $ss['programs']['show'] ?? 1; ?>
  <?php if(($showPrograms || !isset($ss['programs'])) && !empty($programs)): ?>
  <section class="section bg-surface" id="programs" aria-label="Program unggulan">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($pSs['title'] ?? 'Program Unggulan') ?></span>
        <h2><?= esc($pSs['title'] ?? 'Program') ?> <span class="text-gradient"><?= esc($site_name) ?></span></h2>
        <p><?= esc($pSs['subtitle'] ?? 'Berbagai program unggulan untuk mengembangkan potensi dan bakat siswa') ?></p>
      </div>
      <div class="programs-grid">
        <?php foreach($programs as $program): ?>
        <div class="programs-card">
          <div class="pc-icon"><i class="<?= esc($program['icon'] ?? 'fas fa-book') ?>" aria-hidden="true"></i></div>
          <h3><?= esc($program['title']) ?></h3>
          <p><?= esc($program['description']) ?></p>
          <?php if(isset($program['link_url']) && $program['link_url']): ?>
          <a href="<?= esc($program['link_url']) ?>" class="pc-link"><?= esc($program['link_text'] ?? 'Selengkapnya') ?> <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== EKSTRAKURIKULER ===== -->
  <?php $exSs = $ss['extracurriculars'] ?? []; $showEx = $ss['extracurriculars']['show'] ?? 1; ?>
  <?php if(($showEx || !isset($ss['extracurriculars'])) && isset($extracurriculars) && !empty($extracurriculars)): ?>
  <section class="section" id="extracurriculars" aria-label="Ekstrakurikuler">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($exSs['title'] ?? 'Kegiatan Siswa') ?></span>
        <h2><?= esc($exSs['title'] ?? 'Ekstrakurikuler') ?> <span class="text-gradient"><?= esc($site_name) ?></span></h2>
        <p><?= esc($exSs['subtitle'] ?? 'Wadah pengembangan minat dan bakat siswa di luar kegiatan akademik') ?></p>
      </div>
      <div class="extracurricular-grid">
        <?php foreach($extracurriculars as $ex): ?>
        <div class="extracurricular-card">
          <div class="ec-icon" style="--ec-color:<?= esc($ex['icon_color'] ?? '#14b8a6') ?>"><i class="<?= esc($ex['icon'] ?? 'fas fa-microchip') ?>"></i></div>
          <h3><?= esc($ex['title']) ?></h3>
          <p><?= esc($ex['description']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== TENAGA PENGAJAR ===== -->
  <?php $tSs = $ss['teachers'] ?? []; $showT = $ss['teachers']['show'] ?? 1; ?>
  <?php if(($showT || !isset($ss['teachers'])) && isset($teachers) && !empty($teachers)): ?>
  <section class="section bg-surface" id="teachers" aria-label="Tenaga pengajar">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($tSs['title'] ?? 'Tenaga Pengajar') ?></span>
        <h2><?= esc($tSs['title'] ?? 'Guru') ?> <span class="text-gradient"><?= esc($tSs['subtitle'] ?? 'Berpengalaman') ?></span></h2>
        <p><?= esc($tSs['subtitle'] ?? 'Dibimbing oleh tenaga pengajar profesional dan berpengalaman di bidangnya') ?></p>
      </div>
      <div class="carousel-wrap">
        <div class="carousel-track" id="teacherTrack">
          <?php foreach($teachers as $teacher): ?>
          <div class="teacher-card">
            <div class="tc-img">
              <img src="<?= !empty($teacher['photo']) ? (preg_match('#^https?://#', $teacher['photo']) ? esc($teacher['photo']) : base_url('uploads/teachers/' . esc($teacher['photo']))) : 'https://placehold.co/400x400/0c4a6e/ffffff?text='.urlencode(substr($teacher['name'], 0, 1)) ?>" alt="<?= esc($teacher['name']) ?>" loading="lazy">
              <div class="tc-social">
                <?php if(isset($teacher['social_facebook']) && $teacher['social_facebook']): ?>
                <a href="<?= esc($teacher['social_facebook']) ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if(isset($teacher['social_instagram']) && $teacher['social_instagram']): ?>
                <a href="<?= esc($teacher['social_instagram']) ?>" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if(isset($teacher['social_linkedin']) && $teacher['social_linkedin']): ?>
                <a href="<?= esc($teacher['social_linkedin']) ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
              </div>
            </div>
            <div class="tc-body">
              <h4><?= esc($teacher['name']) ?></h4>
              <span class="tc-role"><?= esc($teacher['role']) ?></span>
              <div class="tc-meta">
                <?php if(isset($teacher['experience']) && $teacher['experience']): ?>
                <span><i class="fas fa-book" aria-hidden="true"></i> <?= esc($teacher['experience']) ?></span>
                <?php endif; ?>
                <?php if(isset($teacher['education']) && $teacher['education']): ?>
                <span><i class="fas fa-graduation-cap" aria-hidden="true"></i> <?= esc($teacher['education']) ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-btn carousel-prev" id="teacherPrev" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
        <button class="carousel-btn carousel-next" id="teacherNext" aria-label="Selanjutnya"><i class="fas fa-chevron-right"></i></button>
        <div class="carousel-dots" id="teacherDots" role="tablist"></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== SISWA BERPRESTASI ===== -->
  <?php $achSs = $ss['achievements'] ?? []; $showAch = $ss['achievements']['show'] ?? 1; ?>
  <?php if(($showAch || !isset($ss['achievements'])) && isset($achievements) && !empty($achievements)): ?>
  <section class="section" id="achievements" aria-label="Prestasi siswa">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($achSs['title'] ?? 'Siswa Berprestasi') ?></span>
        <h2><?= esc($achSs['title'] ?? 'Prestasi') ?> <span class="text-gradient"><?= esc($achSs['subtitle'] ?? 'Siswa') ?></span></h2>
        <p><?= esc($achSs['subtitle'] ?? 'Kebanggaan sekolah atas prestasi yang diraih siswa di berbagai bidang dan tingkatan') ?></p>
      </div>
      <div class="achievement-grid">
        <?php foreach($achievements as $ach): ?>
        <div class="achievement-card">
          <?php
          $medal_icon = 'fas fa-trophy';
          $medal = $ach['medal'] ?? '';
          if($medal === 'gold' || stripos($medal, 'emas') !== false) $medal_icon = 'fas fa-trophy';
          elseif($medal === 'silver' || stripos($medal, 'perak') !== false) $medal_icon = 'fas fa-medal';
          elseif($medal === 'bronze' || stripos($medal, 'perunggu') !== false) $medal_icon = 'fas fa-award';
          ?>
          <div class="pc-medal"><i class="<?= $medal_icon ?>" aria-hidden="true"></i></div>
          <div class="pc-photo">
            <img src="<?= esc($ach['photo'] ?? 'https://placehold.co/200x200/0c4a6e/ffffff?text='.urlencode(substr($ach['student_name'], 0, 1))) ?>" alt="<?= esc($ach['student_name']) ?>" loading="lazy">
          </div>
          <h4><?= esc($ach['student_name']) ?></h4>
          <span class="pc-class"><?= esc($ach['class_name'] ?? '') ?></span>
          <span class="pc-achieve"><?= esc($ach['achievement']) ?></span>
          <div class="pc-bottom">
            <?php
            $level_class = 'pc-level-gold';
            $lvl = strtolower($ach['level'] ?? '');
            if(stripos($lvl, 'provinsi') !== false) $level_class = 'pc-level-silver';
            elseif(stripos($lvl, 'kota') !== false || stripos($lvl, 'kabupaten') !== false) $level_class = 'pc-level-bronze';
            ?>
            <span class="pc-level <?= $level_class ?>"><?= esc($ach['level']) ?></span>
            <span class="pc-year"><?= esc($ach['year'] ?? date('Y')) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== TESTIMONI ===== -->
  <?php $tmSs = $ss['testimonials'] ?? []; $showTm = $ss['testimonials']['show'] ?? 1; ?>
  <?php if(($showTm || !isset($ss['testimonials'])) && isset($testimonials) && !empty($testimonials)): ?>
  <section class="section bg-surface" id="testimonials" aria-label="Testimoni">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($tmSs['title'] ?? 'Kata Mereka') ?></span>
        <h2><?= esc($tmSs['title'] ?? 'Testimoni') ?> <span class="text-gradient"><?= esc($site_name) ?></span></h2>
        <p>Apa kata siswa, alumni, dan orang tua tentang pengalaman mereka di <?= esc($site_name) ?></p>
      </div>
      <div class="testimonial-grid">
        <?php foreach($testimonials as $tm): ?>
        <div class="testimonial-card">
          <div class="tc-quote"><i class="fas fa-quote-left" aria-hidden="true"></i></div>
          <p class="tc-text"><?= esc($tm['quote']) ?></p>
          <div class="tc-author">
            <img src="<?= esc($tm['photo'] ?? 'https://placehold.co/60x60/0c4a6e/ffffff?text='.urlencode(substr($tm['name'], 0, 1))) ?>" alt="<?= esc($tm['name']) ?>" loading="lazy">
            <div>
              <strong><?= esc($tm['name']) ?></strong>
              <span><?= esc($tm['role'] ?? '') ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== BERITA ===== -->
  <?php if(isset($news) && !empty($news)): ?>
  <section class="section" id="news" aria-label="Berita dan artikel">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($ss['news']['title'] ?? 'Berita & Artikel') ?></span>
        <h2><?= esc($ss['news']['title'] ?? 'Berita') ?> <span class="text-gradient"><?= esc($ss['news']['subtitle'] ?? 'Terbaru') ?></span></h2>
        <p><?= esc($ss['news']['subtitle'] ?? 'Informasi terkini seputar kegiatan dan prestasi di lingkungan sekolah') ?></p>
      </div>
      <div class="news-grid">
        <?php foreach($news as $item): ?>
        <article class="news-card">
          <div class="bc-img">
            <img src="<?= esc($item['image'] ?? 'https://placehold.co/600x400/0c4a6e/ffffff?text=Berita') ?>" alt="<?= esc($item['title']) ?>" loading="lazy">
            <span class="bc-cat"><?= esc($item['category'] ?? 'Berita') ?></span>
          </div>
          <div class="bc-body">
            <div class="bc-meta">
              <span><i class="far fa-calendar" aria-hidden="true"></i> <?= date('j F Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></span>
              <span><i class="far fa-user" aria-hidden="true"></i> <?= esc($item['author'] ?? 'Admin') ?></span>
            </div>
            <h3><?= esc($item['title']) ?></h3>
            <p><?= esc($item['excerpt']) ?></p>
            <a href="<?= base_url('news/'.esc($item['slug'])) ?>" class="bc-link">Baca Selengkapnya <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="section-cta">
        <a href="<?= base_url('news') ?>" class="btn btn-outline-dark">Lihat Semua Berita <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== EVENT / AGENDA ===== -->
  <?php $showEvents = $ss['events']['show'] ?? 1; if(($showEvents || !isset($ss['events'])) && isset($events) && !empty($events)): ?>
  <section class="section bg-surface" id="events" aria-label="Agenda kegiatan">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($ss['events']['title'] ?? 'Kegiatan Mendatang') ?></span>
        <h2>Agenda <span class="text-gradient"><?= esc($site_name) ?></span></h2>
        <p><?= esc($ss['events']['subtitle'] ?? 'Jadwal kegiatan dan acara mendatang di lingkungan sekolah') ?></p>
      </div>
      <div class="event-grid">
        <?php foreach($events as $ev): ?>
          <div class="event-card">
              <?php
              $ev_date = strtotime($ev['event_date'] ?? '');
              $day = $ev_date ? date('j', $ev_date) : '';
              $month = $ev_date ? date('F', $ev_date) : '';
              ?>
              <?php if($ev_date): ?>
              <div class="ac-date">
                <span class="acd-day"><?= $day ?></span>
                <span class="acd-month"><?= $month ?></span>
              </div>
              <?php endif; ?>
              <div class="ac-body">
                <h3><?= esc($ev['title']) ?></h3>
                <div class="ac-meta">
                  <?php if(isset($ev['location']) && $ev['location']): ?>
                  <span><i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= esc($ev['location']) ?></span>
                  <?php endif; ?>
                  <?php if(isset($ev['event_time']) && $ev['event_time']): ?>
                  <span><i class="fas fa-clock" aria-hidden="true"></i> <?= esc($ev['event_time']) ?></span>
                  <?php endif; ?>
                </div>
                <p><?= esc($ev['description']) ?></p>
              </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== GALERI ===== -->
  <?php $showGallery = $ss['gallery']['show'] ?? 1; if(($showGallery || !isset($ss['gallery'])) && isset($galleries) && !empty($galleries)): ?>
  <section class="section" id="gallery" aria-label="Galeri dokumentasi">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($ss['gallery']['title'] ?? 'Dokumentasi') ?></span>
        <h2>Galeri <span class="text-gradient"><?= esc($ss['gallery']['subtitle'] ?? 'Sekolah') ?></span></h2>
        <p><?= esc($ss['gallery']['subtitle'] ?? 'Dokumentasi kegiatan dan momen berharga di lingkungan sekolah') ?></p>
      </div>
      <div class="gallery-grid">
        <?php foreach($galleries as $i => $g): ?>
        <a href="<?= esc($g['image']) ?>" class="gallery-item <?= $i === 0 ? 'gi-tall' : '' ?>" data-lightbox>
          <img src="<?= esc($g['image']) ?>" alt="<?= esc($g['caption'] ?? 'Galeri '.($i+1)) ?>" loading="lazy">
          <span class="gi-overlay"><i class="fas fa-plus" aria-hidden="true"></i></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== FAQ ===== -->
  <?php $showFaq = $ss['faq']['show'] ?? 1; if(($showFaq || !isset($ss['faq'])) && isset($faq) && !empty($faq)): ?>
  <section class="section bg-surface" id="faq" aria-label="Pertanyaan umum">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($ss['faq']['title'] ?? 'Tanya Jawab') ?></span>
        <h2>Pertanyaan <span class="text-gradient"><?= esc($ss['faq']['subtitle'] ?? 'Umum') ?></span></h2>
        <p><?= esc($ss['faq']['subtitle'] ?? 'Informasi yang sering ditanyakan seputar ' . $site_name) ?></p>
      </div>
      <div class="faq-list">
        <?php foreach($faq as $f): ?>
        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            <span><?= esc($f['question']) ?></span>
            <i class="fas fa-chevron-down" aria-hidden="true"></i>
          </button>
          <div class="faq-answer">
            <p><?= esc($f['answer']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ===== KONTAK ===== -->
  <?php $cSs = $ss['contact'] ?? []; $showC = $ss['contact']['show'] ?? 1; ?>
  <?php if($showC || !isset($ss['contact'])): ?>
  <section class="section" id="contact" aria-label="Kontak">
    <div class="container">
      <div class="section-title">
        <span class="st-badge"><?= esc($cSs['title'] ?? 'Hubungi Kami') ?></span>
        <h2>Silakan <span class="text-gradient"><?= esc($cSs['title'] ?? 'Hubungi') ?></span> Kami</h2>
        <p><?= esc($cSs['subtitle'] ?? 'Untuk informasi lebih lanjut tentang pendaftaran dan program sekolah') ?></p>
      </div>
      <div class="contact-grid">
        <div class="contact-info">
          <div class="ki-card">
            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
            <div>
              <h4>Alamat</h4>
              <p><?= esc($contact_address ?? 'Jl. Pendidikan No. 123, Kelurahan Cerdas, Kecamatan Unggul, Kota Pelajar 12345') ?></p>
            </div>
          </div>
          <div class="ki-card">
            <i class="fas fa-phone-alt" aria-hidden="true"></i>
            <div>
              <h4>Telepon</h4>
              <p><?= esc($contact_phone ?? '(021) 1234-5678') ?></p>
            </div>
          </div>
          <div class="ki-card">
            <i class="fas fa-envelope" aria-hidden="true"></i>
            <div>
              <h4>Email</h4>
              <p><?= esc($contact_email ?? 'info@sekolahku.sch.id') ?></p>
            </div>
          </div>
          <div class="ki-card">
            <i class="fas fa-clock" aria-hidden="true"></i>
            <div>
              <h4>Jam Kerja</h4>
              <p><?= esc($contact_hours ?? 'Senin - Jumat: 07.00 - 16.00 WIB') ?></p>
            </div>
          </div>
        </div>
        <form class="contact-form" id="contactForm" action="<?= base_url('contact/send') ?>" method="post" novalidate>
          <?= csrf_field() ?>
          <div style="position:absolute;left:-9999px" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>
          <div class="form-row">
            <input type="text" name="name" placeholder="Nama Lengkap" required aria-label="Nama lengkap">
          </div>
          <div class="form-row">
            <input type="email" name="email" placeholder="Email" required aria-label="Alamat email">
          </div>
          <div class="form-row">
            <input type="text" name="subject" placeholder="Subjek" aria-label="Subjek pesan">
          </div>
          <div class="form-row">
            <textarea name="message" placeholder="Pesan" rows="5" required aria-label="Isi pesan"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Kirim Pesan <i class="fas fa-paper-plane" aria-hidden="true"></i></button>
        </form>
      </div>
    </div>
  </section>
  <?php endif; ?>

<?= $this->include('layouts/footer') ?>
