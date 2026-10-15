<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= base_url() ?></loc>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?= base_url('news') ?></loc>
    <priority>0.8</priority>
  </url>
  <url>
    <loc><?= base_url('contact') ?></loc>
    <priority>0.6</priority>
  </url>
<?php foreach ($news as $n): ?>
  <url>
    <loc><?= base_url('news/' . esc($n['slug'])) ?></loc>
    <lastmod><?= esc($n['updated_at'] ?? $n['published_at'] ?? date('Y-m-d')) ?></lastmod>
    <priority>0.7</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($events as $e): ?>
  <url>
    <loc><?= base_url('#events') ?></loc>
    <lastmod><?= esc($e['updated_at'] ?? $e['event_date'] ?? date('Y-m-d')) ?></lastmod>
    <priority>0.5</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($galleries as $g): ?>
  <url>
    <loc><?= base_url('#gallery') ?></loc>
    <lastmod><?= esc($g['updated_at'] ?? date('Y-m-d')) ?></lastmod>
    <priority>0.4</priority>
  </url>
<?php endforeach; ?>
</urlset>
