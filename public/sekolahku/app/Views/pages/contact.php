<?= $this->include('layouts/header') ?>

  <!-- ===== PAGE BANNER ===== -->
  <?php $contactBanner = !empty($page_banners) ? (json_decode($page_banners, true)['contact'] ?? []) : []; ?>
  <section class="page-banner" id="main-content">
    <div class="page-banner-shape page-banner-shape--1"></div>
    <div class="page-banner-shape page-banner-shape--2"></div>
    <div class="container page-banner-content">
      <span class="page-banner-badge"><i class="fas fa-envelope"></i> <?= esc($contactBanner['badge'] ?? '') ?: 'Hubungi Kami' ?></span>
      <h1><?= esc($contactBanner['title'] ?? '') ?: 'Hubungi' ?> <span class="text-gradient"><?= esc($contactBanner['subtitle'] ?? '') ?: 'Kami' ?></span></h1>
      <p>Informasi lebih lanjut tentang pendaftaran dan program sekolah</p>
    </div>
  </section>

  <!-- ===== KONTAK ===== -->
  <section class="section">
    <div class="container">
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

<?= $this->include('layouts/footer') ?>
