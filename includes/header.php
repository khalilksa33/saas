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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">
    
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
    </script>
</head>
<body>
    <!-- Top Navigation (Marketing & Public Pages) -->
    <?php if (empty($hideNav)): ?>
    <header class="navbar">
        <div class="container nav-container">
            <a href="<?= url() ?>" class="brand-logo" title="<?= e(APP_NAME) ?>">
                <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" class="brand-logo-img" width="34" height="34">
                <span class="brand-text">iLove<span class="brand-highlight">Digital</span></span>
            </a>

            <ul class="nav-links">
                <li><a href="<?= url('#features') ?>" class="nav-link">Creator Tools</a></li>
                <li><a href="<?= url('#pricing') ?>" class="nav-link">Pricing Plans</a></li>
                <li><a href="<?= url('#showcase') ?>" class="nav-link">Live Showcase</a></li>
                <li><a href="<?= url('#faq') ?>" class="nav-link">FAQ</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($user): ?>
                    <a href="<?= url('dashboard') ?>" class="btn btn-sm btn-primary">Go to Dashboard →</a>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="btn btn-sm btn-secondary">Sign In</a>
                    <a href="<?= url('register') ?>" class="btn btn-sm btn-primary">Start Free 🚀</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php endif; ?>
