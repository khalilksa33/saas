<?php
/**
 * Standalone Public Creator Bio Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pdo = DB::getConnection();
$username = strtolower(trim($routeParam ?? 'alexcreator'));

// Fetch user & profile
$stmt = $pdo->prepare("SELECT u.id as user_id, u.name, u.username, u.avatar_url as user_avatar, u.plan,
                              bp.id as profile_id, bp.theme, bp.display_name, bp.bio_text, bp.avatar_url as profile_avatar,
                              bp.social_links, bp.views_count
                       FROM users u
                       JOIN bio_profiles bp ON u.id = bp.user_id
                       WHERE LOWER(u.username) = ?");
$stmt->execute([$username]);
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    echo "<div style='font-family:sans-serif; text-align:center; padding: 4rem; color:#fff; background:#090d16; min-height:100vh;'>
            <h1>404 - Creator Not Found</h1>
            <p>The creator page you are looking for does not exist.</p>
            <a href='" . url() . "' style='color:#6366f1; text-decoration:none;'>← Back to Home</a>
          </div>";
    exit;
}

// Increment view count
$upd = $pdo->prepare("UPDATE bio_profiles SET views_count = views_count + 1 WHERE id = ?");
$upd->execute([$data['profile_id']]);

// Fetch active links
$linksStmt = $pdo->prepare("SELECT * FROM bio_links WHERE profile_id = ? AND is_active = 1 ORDER BY sort_order ASC, id ASC");
$linksStmt->execute([$data['profile_id']]);
$links = $linksStmt->fetchAll();

$socials = json_decode($data['social_links'] ?? '{}', true) ?: [];
$theme = $data['theme'] ?: 'midnight';
$avatar = $data['profile_avatar'] ?: $data['user_avatar'];
$name = $data['display_name'] ?: $data['name'];
$bio = $data['bio_text'] ?: 'Digital Creator';

$socialIcons = [
    'youtube' => ['icon' => '▶️', 'label' => 'YouTube'],
    'instagram' => ['icon' => '📸', 'label' => 'Instagram'],
    'tiktok' => ['icon' => '🎵', 'label' => 'TikTok'],
    'twitter' => ['icon' => '✖️', 'label' => 'X'],
    'spotify' => ['icon' => '🎧', 'label' => 'Spotify'],
    'github' => ['icon' => '💻', 'label' => 'GitHub']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($name) ?> (@<?= e($data['username']) ?>) — Links & Assets</title>
    <meta name="description" content="<?= e($bio) ?>">

    <!-- Favicons & Mobile Icons -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('images/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('images/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('images/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= url('favicon.ico') ?>">
    <link rel="manifest" href="<?= url('site.webmanifest') ?>">
    <meta name="theme-color" content="#090d16">

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>?v=<?= APP_VERSION ?>">
    <link rel="stylesheet" href="<?= asset('css/bio-page.css') ?>?v=<?= APP_VERSION ?>">
    
    <!-- OpenGraph -->
    <meta property="og:title" content="<?= e($name) ?>">
    <meta property="og:description" content="<?= e($bio) ?>">
    <meta property="og:image" content="<?= e($avatar) ?>">
    <meta property="og:type" content="profile">
</head>
<body class="public-bio-body theme-<?= e($theme) ?>">

    <div class="public-bio-container theme-<?= e($theme) ?>">
        <!-- Profile Header -->
        <header class="bio-header">
            <div class="bio-avatar-wrapper">
                <img src="<?= e($avatar) ?>" alt="<?= e($name) ?>" class="bio-avatar">
                <span class="bio-verified-badge" title="Verified Creator">✓</span>
            </div>
            <h1 class="bio-name">
                <span><?= e($name) ?></span>
            </h1>
            <p class="bio-description"><?= e($bio) ?></p>
        </header>

        <!-- Social Icons Tray -->
        <?php if (!empty($socials)): ?>
            <div class="bio-socials">
                <?php foreach ($socialIcons as $key => $meta): ?>
                    <?php if (!empty($socials[$key])): ?>
                        <a href="<?= e($socials[$key]) ?>" target="_blank" rel="noopener noreferrer" class="bio-social-link" title="<?= e($meta['label']) ?>">
                            <?= $meta['icon'] ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Links Stack -->
        <div class="bio-links-stack">
            <?php foreach ($links as $link): ?>
                <?php 
                    $animClass = ($link['animation'] && $link['animation'] !== 'none') ? 'anim-' . e($link['animation']) : '';
                ?>
                <a href="<?= url('track-click?id=' . $link['id'] . '&to=' . urlencode($link['url'])) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="bio-link-btn <?= $animClass ?>">
                    <div class="bio-link-left">
                        <span class="bio-link-icon"><?= e($link['icon'] ?: '🔗') ?></span>
                        <span class="bio-link-title"><?= e($link['title']) ?></span>
                    </div>
                    <?php if (!empty($link['badge'])): ?>
                        <span class="bio-link-badge badge badge-pro"><?= e($link['badge']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Footer -->
        <div class="bio-branding-footer">
            <a href="<?= url() ?>" target="_blank" style="opacity: 0.85; transition: opacity 0.2s; display: inline-flex; align-items: center; gap: 0.45rem;">
                <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" width="18" height="18" style="vertical-align: middle;">
                <span>Powered by <strong><?= e(APP_NAME) ?></strong></span>
            </a>
        </div>
    </div>

</body>
</html>
