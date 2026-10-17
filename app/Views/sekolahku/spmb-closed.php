<?= $this->include('layouts/sekolahku/header') ?>

<main id="main-content" class="spmb-closed-page">
  <div class="spmb-closed-backdrop">
    <section class="spmb-closed-dialog" role="dialog" aria-modal="true" aria-labelledby="spmb-closed-title">
      <div class="spmb-closed-icon"><i class="fas fa-calendar-times" aria-hidden="true"></i></div>
      <span class="page-banner-badge">SPMB Online</span>
      <h1 id="spmb-closed-title">Pendaftaran belum tersedia</h1>
      <p><?= esc($schedule['message']); ?></p>
      <?php if (! empty($schedule['start_date']) && ! empty($schedule['end_date'])) : ?>
        <p class="spmb-closed-period">Periode pendaftaran: <?= esc(date('d/m/Y', strtotime($schedule['start_date']))); ?> – <?= esc(date('d/m/Y', strtotime($schedule['end_date']))); ?></p>
      <?php endif; ?>
      <a class="btn btn-primary" href="<?= site_url('news'); ?>">Kembali ke website</a>
    </section>
  </div>
</main>

<style>
  .spmb-closed-backdrop{position:fixed;inset:0;z-index:10000;display:grid;place-items:center;padding:24px;background:rgba(10,26,44,.72)}
  .spmb-closed-dialog{width:min(100%,520px);padding:clamp(28px,5vw,48px);border-radius:24px;background:#fff;text-align:center;box-shadow:0 24px 80px rgba(0,0,0,.25)}
  .spmb-closed-dialog h1{margin:18px 0 12px;font-size:clamp(1.6rem,4vw,2.25rem)}
  .spmb-closed-dialog p{color:#536273;line-height:1.7}
  .spmb-closed-icon{display:grid;place-items:center;width:68px;height:68px;margin:0 auto 18px;border-radius:50%;background:#fff1f0;color:#d92d20;font-size:28px}
  .spmb-closed-period{font-weight:600}
</style>

<?= $this->include('layouts/sekolahku/footer') ?>
