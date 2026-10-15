<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<style>
.gradient-card { position:relative; overflow:hidden; transition:transform .2s, box-shadow .2s; }
.gradient-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
.gradient-card .icon-bg { position:absolute; right:-10px; bottom:-10px; font-size:4rem; opacity:.1; }
.stat-value { font-size:1.75rem; font-weight:700; line-height:1.2; }
.stat-label { font-size:.8rem; opacity:.85; }
.dash-section-title { font-size:.85rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--cui-body-color); }
.quick-action-btn { transition:transform .15s; }
.quick-action-btn:hover { transform:scale(1.03); }
.msg-excerpt { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
@media (max-width:768px) { .stat-value { font-size:1.4rem; } }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div>
    <h4 class="mb-1 fw-bold">Dashboard</h4>
    <p class="text-body-tertiary small mb-0">
      <i class="fas fa-calendar-alt me-1"></i><?= date('l, d F Y') ?> &mdash;
      Selamat datang kembali, <strong><?= esc($user['full_name'] ?? $user['username'] ?? 'Admin') ?></strong>!
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="/admin/news/new" class="btn btn-primary rounded-pill btn-sm px-3">
      <i class="fas fa-plus me-1"></i>News
    </a>
    <a href="/admin/events/new" class="btn btn-outline-primary rounded-pill btn-sm px-3">
      <i class="fas fa-plus me-1"></i>Event
    </a>
  </div>
</div>

<div class="alert alert-info d-flex align-items-start gap-3 py-2 px-3 mb-4 small rounded-3 border-0 shadow-sm" role="alert">
  <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
  <div>
    <strong class="d-block">Cache halaman: ~1 menit</strong>
    Perubahan konten (berita, agenda, dll) membutuhkan waktu hingga 1 menit untuk tercermin di halaman publik karena page cache. Untuk melihat perubahan segera, tambahkan <code>?nocache</code> di URL publik.
  </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
  <?php $stats = [
    ['label' => 'News',        'value' => $total_news ?? 0,           'icon' => 'fa-newspaper',  'gradient' => 'linear-gradient(135deg,#0d6efd,#0a58ca)'],
    ['label' => 'Guru',        'value' => $total_teachers ?? 0,       'icon' => 'fa-chalkboard-user','gradient' => 'linear-gradient(135deg,#198754,#146c43)'],
    ['label' => 'Program',     'value' => $total_programs ?? 0,       'icon' => 'fa-book-open',  'gradient' => 'linear-gradient(135deg,#fd7e14,#d96b0d)'],
    ['label' => 'Ekskul',      'value' => $total_extracurriculars ?? 0,'icon' => 'fa-futbol',    'gradient' => 'linear-gradient(135deg,#6f42c1,#5a32a3)'],
    ['label' => 'Prestasi',    'value' => $total_achievements ?? 0,   'icon' => 'fa-trophy',     'gradient' => 'linear-gradient(135deg,#dc3545,#b82d3b)'],
    ['label' => 'Agenda',      'value' => $total_events ?? 0,         'icon' => 'fa-calendar',  'gradient' => 'linear-gradient(135deg,#0dcaf0,#0aa2c0)'],
  ]; ?>
  <?php foreach ($stats as $s): ?>
  <div class="col-6 col-md-4 col-lg-2">
    <div class="card gradient-card border-0 rounded-3 text-white" style="background:<?= $s['gradient'] ?>">
      <div class="card-body p-3 position-relative">
        <i class="fas <?= $s['icon'] ?> icon-bg"></i>
        <div class="stat-value"><?= $s['value'] ?></div>
        <div class="stat-label"><?= $s['label'] ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Row 2: Secondary stats + slow-moving items -->
<div class="row g-3 mb-4">
  <?php $secondary = [
    ['label' => 'Pesan Masuk',   'value' => $total_messages ?? 0,     'icon' => 'fa-envelope',     'color' => 'danger'],
    ['label' => 'Komentar',      'value' => $total_comments ?? 0,     'icon' => 'fa-comments',     'color' => 'warning'],
    ['label' => 'Galeri',        'value' => $total_galleries ?? 0,    'icon' => 'fa-images',       'color' => 'info'],
    ['label' => 'FAQ',           'value' => $total_faqs ?? 0,         'icon' => 'fa-question-circle','color' => 'secondary'],
    ['label' => 'Pengguna',      'value' => $total_users ?? 0,        'icon' => 'fa-users',        'color' => 'primary'],
  ]; ?>
  <?php foreach ($secondary as $s): ?>
  <div class="col-6 col-md-4 col-lg">
    <div class="card shadow-sm border-0 rounded-3 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-<?= $s['color'] ?> bg-opacity-10 p-2 flex-shrink-0" style="width:44px;height:44px">
          <i class="fas <?= $s['icon'] ?> text-<?= $s['color'] ?>"></i>
        </div>
        <div>
          <div class="fw-bold fs-5"><?= $s['value'] ?></div>
          <small class="text-body-tertiary"><?= $s['label'] ?></small>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Content Sections: News + Messages | Events + Comments -->
<div class="row g-4">
  <!-- Left Column -->
  <div class="col-lg-6 d-flex flex-column gap-4">

    <!-- Recent News -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-newspaper text-primary me-2"></i>Berita Terbaru</h6>
        <a href="/admin/news" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
      </div>
      <div class="list-group list-group-flush">
        <?php if (!empty($recent_news)): ?>
          <?php foreach ($recent_news as $i => $item): ?>
          <a href="/admin/news/edit/<?= $item['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-start gap-3 px-3 py-3 border-0 <?= $i > 0 ? 'border-top' : '' ?>">
            <img src="<?= esc($item['image'] ?? 'https://placehold.co/48/0c4a6e/ffffff?text=N') ?>" alt="" class="rounded flex-shrink-0" width="48" height="48" style="object-fit:cover" loading="lazy">
            <div class="flex-grow-1 min-w-0">
              <div class="fw-medium small text-truncate"><?= esc($item['title']) ?></div>
              <div class="d-flex gap-2 align-items-center mt-1">
                <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size:.65rem"><?= esc($item['category'] ?? 'Berita') ?></span>
                <small class="text-body-tertiary"><i class="far fa-clock me-1"></i><?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></small>
              </div>
            </div>
            <i class="fas fa-chevron-right text-body-tertiary align-self-center flex-shrink-0" style="font-size:.7rem"></i>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-4 text-body-tertiary small">
          <i class="fas fa-newspaper fa-2x mb-2 d-block"></i>Belum ada berita
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Messages -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-envelope text-danger me-2"></i>Pesan Baru</h6>
        <a href="/admin/messages" class="btn btn-sm btn-outline-danger rounded-pill px-3">Lihat Semua</a>
      </div>
      <div class="list-group list-group-flush">
        <?php if (!empty($recent_messages)): ?>
          <?php foreach ($recent_messages as $i => $msg): ?>
          <a href="/admin/messages/<?= $msg['id'] ?>" class="list-group-item list-group-item-action px-3 py-3 border-0 <?= $i > 0 ? 'border-top' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <span class="fw-medium small"><?= esc($msg['name']) ?></span>
                <?php if (empty($msg['is_read'])): ?>
                <span class="badge bg-danger rounded-pill ms-1" style="font-size:.55rem">Baru</span>
                <?php endif; ?>
                <div class="text-body-tertiary small msg-excerpt mt-1"><?= esc($msg['message']) ?></div>
              </div>
              <small class="text-body-tertiary flex-shrink-0 ms-2"><?= date('d M', strtotime($msg['created_at'])) ?></small>
            </div>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-4 text-body-tertiary small">
          <i class="fas fa-envelope fa-2x mb-2 d-block"></i>Tidak ada pesan
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Right Column -->
  <div class="col-lg-6 d-flex flex-column gap-4">

    <!-- Upcoming Events -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-calendar-alt text-info me-2"></i>Agenda Mendatang</h6>
        <a href="/admin/events" class="btn btn-sm btn-outline-info rounded-pill px-3">Lihat Semua</a>
      </div>
      <div class="list-group list-group-flush">
        <?php if (!empty($upcoming_events)): ?>
          <?php foreach ($upcoming_events as $i => $ev): ?>
          <a href="/admin/events/edit/<?= $ev['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-start gap-3 px-3 py-3 border-0 <?= $i > 0 ? 'border-top' : '' ?>">
            <div class="d-flex flex-column align-items-center justify-content-center bg-info bg-opacity-10 rounded-3 flex-shrink-0" style="width:52px;height:52px">
              <span class="fw-bold small lh-1"><?= date('d', strtotime($ev['event_date'])) ?></span>
              <span class="text-body-tertiary" style="font-size:.6rem;text-transform:uppercase"><?= date('M', strtotime($ev['event_date'])) ?></span>
            </div>
            <div class="flex-grow-1 min-w-0">
              <div class="fw-medium small text-truncate"><?= esc($ev['title']) ?></div>
              <div class="d-flex gap-2 align-items-center mt-1">
                <?php if (!empty($ev['location'])): ?>
                <small class="text-body-tertiary"><i class="fas fa-location-dot me-1"></i><?= esc($ev['location']) ?></small>
                <?php endif; ?>
                <?php if (!empty($ev['event_time'])): ?>
                <small class="text-body-tertiary"><i class="far fa-clock me-1"></i><?= esc($ev['event_time']) ?></small>
                <?php endif; ?>
              </div>
            </div>
            <i class="fas fa-chevron-right text-body-tertiary align-self-center flex-shrink-0" style="font-size:.7rem"></i>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-4 text-body-tertiary small">
          <i class="fas fa-calendar-alt fa-2x mb-2 d-block"></i>Belum ada agenda
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Pending Comments -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-comments text-warning me-2"></i>Komentar Menunggu</h6>
        <a href="/admin/comments" class="btn btn-sm btn-outline-warning rounded-pill px-3">Lihat Semua</a>
      </div>
      <div class="list-group list-group-flush">
        <?php if (!empty($pending_comments)): ?>
          <?php foreach ($pending_comments as $i => $c): ?>
          <div class="list-group-item px-3 py-3 border-0 <?= $i > 0 ? 'border-top' : '' ?>">
            <div class="d-flex justify-content-between align-items-start">
              <div class="min-w-0">
                <span class="fw-medium small"><?= esc($c['name']) ?></span>
                <small class="text-body-tertiary ms-2"><?= date('d M H:i', strtotime($c['created_at'])) ?></small>
                <div class="text-body-tertiary small msg-excerpt mt-1"><?= esc($c['comment']) ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center py-4 text-body-tertiary small">
          <i class="fas fa-comments fa-2x mb-2 d-block"></i>Semua komentar sudah disetujui
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-header bg-transparent py-3 border-bottom">
        <h6 class="mb-0 fw-semibold"><i class="fas fa-bolt text-primary me-2"></i>Aksi Cepat</h6>
      </div>
      <div class="card-body p-3">
        <div class="row g-3">
          <div class="col-6">
            <a href="/admin/news/new" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#0d6efd15,#0a58ca08)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#0d6efd,#0a58ca)">
                  <i class="fas fa-newspaper text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Buat Berita</div>
                  <small class="text-body-tertiary">Tambah artikel baru</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="/admin/events/new" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#0dcaf015,#0aa2c008)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#0dcaf0,#0aa2c0)">
                  <i class="fas fa-calendar-plus text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Tambah Agenda</div>
                  <small class="text-body-tertiary">Buat acara baru</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="/admin/gallery/new" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#19875415,#146c4308)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#198754,#146c43)">
                  <i class="fas fa-images text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Upload Galeri</div>
                  <small class="text-body-tertiary">Tambah foto kegiatan</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="/admin/achievements/new" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#dc354515,#b82d3b08)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#dc3545,#b82d3b)">
                  <i class="fas fa-trophy text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Tambah Prestasi</div>
                  <small class="text-body-tertiary">Catat pencapaian siswa</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="/admin/teachers/new" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#6f42c115,#5a32a308)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#6f42c1,#5a32a3)">
                  <i class="fas fa-chalkboard-user text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Tambah Guru</div>
                  <small class="text-body-tertiary">Data staf pengajar</small>
                </div>
              </div>
            </a>
          </div>
          <div class="col-6">
            <a href="/admin/settings" class="text-decoration-none d-block quick-action-btn rounded-3 p-3 h-100" style="background:linear-gradient(135deg,#6c757d15,#5c636a08)">
              <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:42px;height:42px;background:linear-gradient(135deg,#6c757d,#5c636a)">
                  <i class="fas fa-cog text-white"></i>
                </div>
                <div>
                  <div class="fw-semibold small">Pengaturan</div>
                  <small class="text-body-tertiary">Konfigurasi website</small>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
