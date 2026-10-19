<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? $site_name) ?> — <?= esc($site_name) ?></title>
    <link rel="icon" href="<?= base_url('assets/img/favicon/favicon.ico') ?>?v=<?= filemtime(FCPATH . 'assets/img/favicon/favicon.ico') ?>" type="image/x-icon">
    <meta name="description" content="<?= esc($site_description ?? $site_tagline) ?>">
    <meta name="keywords" content="sekolah, pendidikan, kurikulum merdeka, sekolah unggulan, SPMB">
    <meta name="author" content="<?= esc($site_name) ?>">
    <meta property="og:title" content="<?= esc($page_title ?? $site_name) ?> — <?= esc($site_name) ?>">
    <meta property="og:description" content="<?= esc($site_description ?? $site_tagline) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:image" content="<?= base_url('sekolahku/public/assets/img/og-default.jpg') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($page_title ?? $site_name) ?> — <?= esc($site_name) ?>">
    <meta name="twitter:description" content="<?= esc($site_description ?? $site_tagline) ?>">
    <meta name="twitter:image" content="<?= base_url('sekolahku/public/assets/img/og-default.jpg') ?>">
    <link rel="canonical" href="<?= current_url() ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap">
    </noscript>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </noscript>
    <link rel="stylesheet" href="<?= base_url('sekolahku/public/css/style.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('sekolahku/public/css/principal-carousel.css') ?>?v=<?= filemtime(FCPATH . 'sekolahku/public/css/principal-carousel.css') ?>">
    <link rel="stylesheet" href="<?= base_url('sekolahku/public/css/spmb.css'); ?>?v=<?= filemtime(FCPATH . 'sekolahku/public/css/spmb.css'); ?>">
    <?php
    $themeColor = (string) ($theme_color ?? 'default');
    if (preg_match('/^[a-z-]+$/', $themeColor) === 1 && $themeColor !== 'default') :
        $_themeFile = FCPATH . 'sekolahku' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $themeColor . '-theme.css';
        if (is_file($_themeFile)) :
    ?>
            <style>
                <?= file_get_contents($_themeFile) ?>
            </style>
    <?php endif;
    endif; ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "<?= esc($site_name) ?>",
            "url": "<?= base_url('news') ?>",
            "description": "<?= esc($site_description ?? $site_tagline) ?>",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "<?= esc($contact_address) ?>",
                "addressLocality": "",
                "addressRegion": "",
                "postalCode": "",
                "addressCountry": "ID"
            },
            "telephone": "<?= esc($contact_phone) ?>",
            "email": "<?= esc($contact_email) ?>"
        }
    </script>
</head>

<body>

    <!-- ===== SKIP TO CONTENT ===== -->
    <a href="#main-content" class="skip-link" style="position:absolute;left:-9999px;top:0;z-index:9999;background:#0c4a6e;color:#fff;padding:8px 16px;text-decoration:none;font-size:14px">Langsung ke konten utama</a>
    <style>
        .skip-link:focus,
        .skip-link:active {
            left: 8px !important;
            top: 8px !important
        }
    </style>

    <!-- ===== HEADER TOP ===== -->
    <div class="header-top" role="complementary" aria-label="Kontak dan sosial media">
        <div class="container header-top-inner">
            <div class="header-top-left">
                <span><i class="fas fa-phone-alt" aria-hidden="true"></i> <?= esc($contact_phone ?? '(021) 1234-5678') ?></span>
                <span><i class="fas fa-envelope" aria-hidden="true"></i> <?= esc($contact_email ?? 'info@sekolahku.sch.id') ?></span>
            </div>
            <div class="header-top-right">
                <?php if (isset($social_facebook) && $social_facebook): ?>
                    <a href="<?= esc($social_facebook) ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (isset($social_instagram) && $social_instagram): ?>
                    <a href="<?= esc($social_instagram) ?>" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (isset($social_youtube) && $social_youtube): ?>
                    <a href="<?= esc($social_youtube) ?>" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
                <?php if (isset($social_tiktok) && $social_tiktok): ?>
                    <a href="<?= esc($social_tiktok) ?>" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== HEADER MAIN ===== -->
    <header class="header" role="banner">
        <div class="container header-inner">
            <a href="<?= base_url('news') ?>" class="logo" aria-label="Beranda <?= esc($site_name) ?>">
                <i class="<?= esc($site_logo_icon ?? 'fas fa-graduation-cap') ?>" aria-hidden="true"></i>
                <span><?= esc($site_logo_text ?? $site_name) ?></span>
            </a>
            <nav class="nav" id="navMenu" role="navigation" aria-label="Navigasi utama">
                <a href="<?= base_url('news'); ?>">Beranda</a>
                <a href="<?= base_url('news/#profile'); ?>">Profil</a>
                <a href="<?= base_url('news/#programs'); ?>">Program</a>
                <a href="<?= base_url('news/news'); ?>">Berita</a>
                <a href="<?= base_url('news/contact'); ?>">Kontak</a>
                <div class="nav-dropdown">
                    <button class="nav-dd-btn" aria-expanded="false" aria-haspopup="true">Lainnya <i class="fas fa-chevron-down" aria-hidden="true"></i></button>
                    <div class="nav-dd-menu" role="menu">
                        <a href="<?= base_url('news/#teachers'); ?>" role="menuitem">Pengajar</a>
                        <a href="<?= base_url('news/#achievements'); ?>" role="menuitem">Prestasi</a>
                        <a href="<?= base_url('news/#extracurriculars'); ?>" role="menuitem">Ekstrakurikuler</a>
                        <a href="<?= base_url('news/#testimonials'); ?>" role="menuitem">Testimoni</a>
                        <a href="<?= base_url('news/#gallery'); ?>" role="menuitem">Galeri</a>
                        <a href="<?= base_url('news/#events'); ?>" role="menuitem">Agenda</a>
                        <a href="<?= base_url('news/#faq'); ?>" role="menuitem">FAQ</a>
                        <a href="<?= base_url('news/downloads'); ?>" role="menuitem">Download</a>
                    </div>
                </div>
                <a href="<?= esc($spmb_url ?? site_url('spmb')) ?>" class="nav-spmb" aria-label="SPMB Online">SPMB Online</a>
            </nav>
            <div class="header-actions">
                <button class="dark-toggle" id="darkToggle" aria-label="Alihkan tema gelap/terang" title="Alihkan tema">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="nav-toggle" id="navToggle" aria-label="Buka menu navigasi" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>