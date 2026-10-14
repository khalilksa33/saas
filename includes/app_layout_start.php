<?php
/**
 * Application Layout Start (Sidebar, Topbar & Shell)
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

Auth::requireAuth();
$user = Auth::user();
$currentRoute = $currentRoute ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>" dir="<?= is_rtl() ? 'rtl' : 'ltr' ?>" data-theme="<?= e(current_theme()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Creator Studio') ?> — <?= e(APP_NAME) ?></title>
    
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

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>?v=<?= APP_VERSION ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>?v=<?= APP_VERSION ?>">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= asset('css/' . $css) ?>?v=<?= APP_VERSION ?>">
        <?php endforeach; ?>
    <?php endif; ?>

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

<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('dashboard') ?>" class="brand-logo" title="<?= e(APP_NAME) ?>">
                <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" class="brand-logo-img" width="34" height="34">
                <span class="brand-text">iLove<span class="brand-highlight">Digital</span></span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-label"><?= __('dash_overview') ?></li>
            <li class="sidebar-item <?= $currentRoute === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= url('dashboard') ?>">
                    <span class="sidebar-icon">📊</span>
                    <span><?= __('dash_overview') ?></span>
                </a>
            </li>

            <li class="sidebar-label"><?= __('dash_creator_studio') ?></li>
            <li class="sidebar-item <?= $currentRoute === 'bio_link' ? 'active' : '' ?>">
                <a href="<?= url('tools/bio-link') ?>">
                    <span class="sidebar-icon">📱</span>
                    <span><?= __('tool_bio_title') ?></span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'thumbnail_studio' ? 'active' : '' ?>">
                <a href="<?= url('tools/thumbnail-studio') ?>">
                    <span class="sidebar-icon">🎨</span>
                    <span><?= __('tool_thumb_title') ?></span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'copywriter' ? 'active' : '' ?>">
                <a href="<?= url('tools/copywriter') ?>">
                    <span class="sidebar-icon">✍️</span>
                    <span><?= __('tool_copy_title') ?></span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'smart_links' ? 'active' : '' ?>">
                <a href="<?= url('tools/smart-links') ?>">
                    <span class="sidebar-icon">🔗</span>
                    <span><?= __('tool_links_title') ?></span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'rate_calculator' ? 'active' : '' ?>">
                <a href="<?= url('tools/rate-calculator') ?>">
                    <span class="sidebar-icon">💰</span>
                    <span><?= __('tool_rates_title') ?></span>
                </a>
            </li>

            <li class="sidebar-label"><?= __('dash_settings') ?></li>
            <li class="sidebar-item <?= $currentRoute === 'profile' ? 'active' : '' ?>">
                <a href="<?= url('profile') ?>">
                    <span class="sidebar-icon">⚙️</span>
                    <span><?= __('dash_account_plan') ?></span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" style="color: #6ee7b7;">
                    <span class="sidebar-icon">↗</span>
                    <span><?= __('dash_view_public') ?></span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-mini-card">
                <img src="<?= e($user['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80') ?>" alt="<?= e($user['name']) ?>" class="user-avatar">
                <div class="user-info">
                    <div class="user-name"><?= e($user['name']) ?></div>
                    <div class="user-plan-tag"><?= strtoupper(e($user['plan'] ?? 'FREE')) ?> PLAN</div>
                </div>
                <a href="<?= url('logout') ?>" title="Log Out" style="color: var(--text-muted); font-size: 1.1rem; padding: 0.2rem;">🚪</a>
            </div>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="app-main">
        <header class="app-topbar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="button" id="sidebarToggle" class="btn btn-sm btn-secondary" style="display: none;">☰</button>
                <div class="app-breadcrumbs">
                    <span><?= e(APP_NAME) ?></span>
                    <span>/</span>
                    <span class="active"><?= e($pageTitle ?? 'Dashboard') ?></span>
                </div>
            </div>

            <div class="topbar-actions">
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

                <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" class="btn btn-sm btn-secondary">
                    <span>👁️ <?= __('dash_preview_bio') ?></span>
                </a>
                <a href="<?= url('profile') ?>" class="badge badge-pro">
                    ⚡ <?= strtoupper(e($user['plan'])) ?>
                </a>
            </div>
        </header>

        <main class="app-content">
