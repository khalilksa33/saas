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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Creator Studio') ?> — <?= e(APP_NAME) ?></title>
    
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
    </script>
</head>
<body>

<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="app-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('dashboard') ?>" class="brand-logo">
                <div class="brand-badge">⚡</div>
                <span><?= e(APP_NAME) ?></span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-label">Overview</li>
            <li class="sidebar-item <?= $currentRoute === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= url('dashboard') ?>">
                    <span class="sidebar-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="sidebar-label">Creator Studio</li>
            <li class="sidebar-item <?= $currentRoute === 'bio_link' ? 'active' : '' ?>">
                <a href="<?= url('tools/bio-link') ?>">
                    <span class="sidebar-icon">📱</span>
                    <span>Bio-Link Builder</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'thumbnail_studio' ? 'active' : '' ?>">
                <a href="<?= url('tools/thumbnail-studio') ?>">
                    <span class="sidebar-icon">🎨</span>
                    <span>Thumbnail Studio</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'copywriter' ? 'active' : '' ?>">
                <a href="<?= url('tools/copywriter') ?>">
                    <span class="sidebar-icon">✍️</span>
                    <span>Viral Hooks & Copy</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'smart_links' ? 'active' : '' ?>">
                <a href="<?= url('tools/smart-links') ?>">
                    <span class="sidebar-icon">🔗</span>
                    <span>Smart Links & QR</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentRoute === 'rate_calculator' ? 'active' : '' ?>">
                <a href="<?= url('tools/rate-calculator') ?>">
                    <span class="sidebar-icon">💰</span>
                    <span>Rates & Invoices</span>
                </a>
            </li>

            <li class="sidebar-label">Settings</li>
            <li class="sidebar-item <?= $currentRoute === 'profile' ? 'active' : '' ?>">
                <a href="<?= url('profile') ?>">
                    <span class="sidebar-icon">⚙️</span>
                    <span>Account & Plan</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" style="color: #6ee7b7;">
                    <span class="sidebar-icon">↗</span>
                    <span>View Public Bio</span>
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
                    <span>CreatorPulse</span>
                    <span>/</span>
                    <span class="active"><?= e($pageTitle ?? 'Dashboard') ?></span>
                </div>
            </div>

            <div class="topbar-actions">
                <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" class="btn btn-sm btn-secondary">
                    <span>👁️ Preview Bio Page</span>
                </a>
                <a href="<?= url('profile') ?>" class="badge badge-pro">
                    ⚡ <?= strtoupper(e($user['plan'])) ?>
                </a>
            </div>
        </header>

        <main class="app-content">
