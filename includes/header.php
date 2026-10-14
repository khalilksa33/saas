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
    
    <!-- OpenGraph & Social SEO -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDesc) ?>">
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
            <a href="<?= url() ?>" class="brand-logo">
                <div class="brand-badge">⚡</div>
                <span><?= e(APP_NAME) ?></span>
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
