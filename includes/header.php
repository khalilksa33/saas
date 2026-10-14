<?php
/**
 * Global Header Component
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? APP_NAME . ' — ' . APP_TAGLINE;
$pageDesc = $pageDesc ?? 'Supercharge your digital creator journey with bio-link micro-sites, social thumbnail studio, AI copywriter, and sponsorship calculator.';
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>" data-theme="<?= e(current_theme()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">

    <?php if (defined('GTM_ID') && GTM_ID): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?= e(GTM_ID) ?>');</script>
    <!-- End Google Tag Manager -->
    <?php elseif (defined('GA_MEASUREMENT_ID') && GA_MEASUREMENT_ID): ?>
    <!-- Google Analytics 4 (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(GA_MEASUREMENT_ID) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= e(GA_MEASUREMENT_ID) ?>', { 'anonymize_ip': true });
    </script>
    <?php endif; ?>
    
    <!-- Favicons & Mobile Icons -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('images/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('images/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('images/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= url('favicon.ico') ?>">
    <link rel="manifest" href="<?= url('site.webmanifest') ?>">
    <meta name="theme-color" content="#090d16">
    
    <!-- OpenGraph & Social SEO -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDesc) ?>">
    <meta property="og:image" content="<?= asset('images/apple-touch-icon.png') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(url()) ?>">
    
    <!-- Design System CSS -->
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>?v=<?= APP_VERSION ?>">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= asset('css/' . $css) ?>?v=<?= APP_VERSION ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Global App Constants -->
    <script>
        window.APP_URL = "<?= url() ?>";
        window.CSRF_TOKEN = "<?= csrf_token() ?>";
        window.APP_LANG = "<?= current_lang() ?>";
        window.APP_THEME = "<?= current_theme() ?>";
    </script>
</head>
<body>
    <?php if (defined('GTM_ID') && GTM_ID): ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e(GTM_ID) ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>

    <!-- Top Navigation (Marketing & Public Pages) -->
    <?php if (empty($hideNav)): ?>
    <header class="navbar">
        <div class="container nav-container">
            <a href="<?= url() ?>" class="brand-logo" title="<?= e(APP_NAME) ?>">
                <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" class="brand-logo-img" width="34" height="34">
                <span class="brand-text">iLove<span class="brand-highlight">Digital</span></span>
            </a>

            <ul class="nav-links">
                <li><a href="<?= url('#features') ?>" class="nav-link"><?= __('nav_tools') ?></a></li>
                <li><a href="<?= url('blog') ?>" class="nav-link"><?= __('nav_blog', 'Blog') ?></a></li>
                <li><a href="<?= url('#pricing') ?>" class="nav-link"><?= __('nav_pricing') ?></a></li>
                <li><a href="<?= url('#showcase') ?>" class="nav-link"><?= __('nav_showcase') ?></a></li>
                <li><a href="<?= url('#faq') ?>" class="nav-link"><?= __('nav_faq') ?></a></li>
            </ul>

            <div class="nav-actions">
                <!-- Theme Switcher -->
                <div class="theme-selector-wrap">
                    <button type="button" class="selector-pill theme-dropdown-toggle" title="<?= __('theme_switcher') ?>">
                        <span class="theme-dot <?= e(current_theme()) ?>"></span>
                        <span style="font-size: 0.8rem;">🎨</span>
                    </button>
                    <div class="theme-dropdown">
                        <button type="button" class="theme-opt-btn <?= current_theme() === 'dark' ? 'active' : '' ?>" data-theme="dark">
                            <span class="theme-dot dark"></span> <span><?= __('theme_dark') ?></span>
                        </button>
                        <button type="button" class="theme-opt-btn <?= current_theme() === 'light' ? 'active' : '' ?>" data-theme="light">
                            <span class="theme-dot light"></span> <span><?= __('theme_light') ?></span>
                        </button>
                        <button type="button" class="theme-opt-btn <?= current_theme() === 'cyberpunk' ? 'active' : '' ?>" data-theme="cyberpunk">
                            <span class="theme-dot cyberpunk"></span> <span><?= __('theme_cyberpunk') ?></span>
                        </button>
                        <button type="button" class="theme-opt-btn <?= current_theme() === 'sunset' ? 'active' : '' ?>" data-theme="sunset">
                            <span class="theme-dot sunset"></span> <span><?= __('theme_sunset') ?></span>
                        </button>
                        <button type="button" class="theme-opt-btn <?= current_theme() === 'emerald' ? 'active' : '' ?>" data-theme="emerald">
                            <span class="theme-dot emerald"></span> <span><?= __('theme_emerald') ?></span>
                        </button>
                    </div>
                </div>

                <!-- Language Switcher -->
                <div class="lang-selector-wrap">
                    <?php if (current_lang() === 'ar'): ?>
                        <a href="<?= e(lang_switch_url('en')) ?>" class="selector-pill" title="Switch to English">
                            <span>🇺🇸</span> <span>English</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= e(lang_switch_url('ar')) ?>" class="selector-pill" title="التبديل إلى العربية">
                            <span>🇸🇦</span> <span>العربية</span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($user): ?>
                    <?php if (Auth::isSuperAdmin()): ?>
                        <a href="<?= url('admin') ?>" class="btn btn-sm btn-outline" style="border-color: #f59e0b; color: #f59e0b; font-weight: 700;">
                            ⚡ <?= __('nav_superadmin', 'Superadmin') ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('dashboard') ?>" class="btn btn-sm btn-primary"><?= __('nav_dashboard') ?> →</a>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-sm btn-secondary"><?= __('nav_signin') ?></a>
                    <a href="<?= url('register') ?>" class="btn btn-sm btn-primary"><?= __('nav_signup') ?></a>
                <?php endif; ?>
            </div>

        </div>
    </header>
    <?php endif; ?>
