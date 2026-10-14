<?php
$pageTitle = 'Creator Dashboard';
$currentRoute = 'dashboard';
require_once __DIR__ . '/../includes/app_layout_start.php';

$pdo = DB::getConnection();
$userId = Auth::id();

// Fetch bio profile
$profileStmt = $pdo->prepare("SELECT * FROM bio_profiles WHERE user_id = ?");
$profileStmt->execute([$userId]);
$bioProfile = $profileStmt->fetch();

// Fetch counts
$bioClicksStmt = $pdo->prepare("SELECT SUM(clicks_count) as total FROM bio_links WHERE profile_id = ?");
$bioClicksStmt->execute([$bioProfile['id'] ?? 0]);
$totalBioClicks = $bioClicksStmt->fetchColumn() ?: 0;

$shortLinksStmt = $pdo->prepare("SELECT * FROM short_links WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$shortLinksStmt->execute([$userId]);
$recentLinks = $shortLinksStmt->fetchAll();

$totalShortClicksStmt = $pdo->prepare("SELECT SUM(clicks_count) as total FROM short_links WHERE user_id = ?");
$totalShortClicksStmt->execute([$userId]);
$totalShortClicks = $totalShortClicksStmt->fetchColumn() ?: 0;

$combinedClicks = $totalBioClicks + $totalShortClicks;
$profileViews = $bioProfile['views_count'] ?? 1420;
?>

<!-- Welcome Banner -->
<div class="card card-glow" style="margin-bottom: 2rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.08) 100%);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.85rem; font-weight: 800;"><?= __('dash_welcome_back') ?>, <?= e($user['name']) ?>! ⚡</h1>
                <span class="badge badge-pro"><?= strtoupper(e($user['plan'])) ?> <?= __('dash_plan_tag') ?></span>
            </div>
            <p style="color: var(--text-secondary); font-size: 0.95rem; max-width: 620px;">
                <?= __('dash_welcome_sub') ?>
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="<?= url('tools/bio-link') ?>" class="btn btn-primary">
                <?= __('dash_edit_bio') ?>
            </a>
            <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" class="btn btn-secondary">
                <?= __('dash_view_live') ?>
            </a>
        </div>
    </div>
</div>

<!-- Metrics Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-title"><?= __('metric_bio_views') ?></span>
            <div class="metric-icon-box" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">👁️</div>
        </div>
        <div class="metric-value"><?= number_format($profileViews) ?></div>
        <div class="metric-trend trend-up">
            <span>↑ 18.4%</span>
            <span style="color: var(--text-muted); font-weight: 400;">vs last week</span>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-title">Total Link Clicks</span>
            <div class="metric-icon-box" style="background: rgba(16, 185, 129, 0.15); color: #6ee7b7;">🖱️</div>
        </div>
        <div class="metric-value"><?= number_format($combinedClicks) ?></div>
        <div class="metric-trend trend-up">
            <span>↑ 24.2%</span>
            <span style="color: var(--text-muted); font-weight: 400;">across all channels</span>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-title">Est. Sponsorship Rate</span>
            <div class="metric-icon-box" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">💰</div>
        </div>
        <div class="metric-value">$1,250</div>
        <div class="metric-trend trend-up">
            <span>Based on 50k reach</span>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-header">
            <span class="metric-title">Active Smart Links</span>
            <div class="metric-icon-box" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">🔗</div>
        </div>
        <div class="metric-value"><?= count($recentLinks) ?></div>
        <div class="metric-trend" style="color: var(--text-secondary);">
            <span>With UTM tracking</span>
        </div>
    </div>
</div>

<!-- Quick Launch Studio Modules -->
<div style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.35rem; font-weight: 700;">Creator Studio Modules</h2>
        <span style="font-size: 0.85rem; color: var(--text-muted);">5 Pro Tools Included</span>
    </div>

    <div class="tools-grid">
        <div class="tool-card">
            <div class="tool-icon-wrapper" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">📱</div>
            <div>
                <h3 class="tool-card-title">Bio-Link Builder</h3>
                <p class="tool-card-desc">Bento-style customizable link-in-bio page with real-time phone simulator and custom theme selector.</p>
            </div>
            <a href="<?= url('tools/bio-link') ?>" class="btn btn-sm btn-outline">Launch Builder →</a>
        </div>

        <div class="tool-card">
            <div class="tool-icon-wrapper" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">🎨</div>
            <div>
                <h3 class="tool-card-title">Thumbnail Studio</h3>
                <p class="tool-card-desc">Browser canvas editor for YouTube thumbnails, IG posts, and TikTok banners with creator stickers & gradients.</p>
            </div>
            <a href="<?= url('tools/thumbnail-studio') ?>" class="btn btn-sm btn-outline">Open Canvas →</a>
        </div>

        <div class="tool-card">
            <div class="tool-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #6ee7b7;">✍️</div>
            <div>
                <h3 class="tool-card-title">Viral Hooks & Copy</h3>
                <p class="tool-card-desc">High-retention opening hooks, YouTube title A/B ideas, descriptions with timestamps, and Twitter threads.</p>
            </div>
            <a href="<?= url('tools/copywriter') ?>" class="btn btn-sm btn-outline">Generate Copy →</a>
        </div>

        <div class="tool-card">
            <div class="tool-icon-wrapper" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">🔗</div>
            <div>
                <h3 class="tool-card-title">Smart Links & QR</h3>
                <p class="tool-card-desc">Generate short URLs with UTM parameters and custom-styled vector QR codes for print & stream overlays.</p>
            </div>
            <a href="<?= url('tools/smart-links') ?>" class="btn btn-sm btn-outline">Manage Links →</a>
        </div>

        <div class="tool-card">
            <div class="tool-icon-wrapper" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">💰</div>
            <div>
                <h3 class="tool-card-title">Rates & Invoices</h3>
                <p class="tool-card-desc">Calculate fair sponsorship pricing based on follower reach & niche, then generate professional PDF invoices.</p>
            </div>
            <a href="<?= url('tools/rate-calculator') ?>" class="btn btn-sm btn-outline">Calculate & Invoice →</a>
        </div>
    </div>
</div>

<!-- Recent Links Table -->
<div class="card" style="padding: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h3 style="font-size: 1.15rem; font-weight: 700;">Recent Smart Links</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Tracking your campaign clicks & conversions</p>
        </div>
        <a href="<?= url('tools/smart-links') ?>" class="btn btn-sm btn-secondary">+ New Smart Link</a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title & Short URL</th>
                    <th>Destination</th>
                    <th>Campaign Tags</th>
                    <th>Clicks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentLinks)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            No smart links created yet. Click "+ New Smart Link" to build your first one!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentLinks as $link): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;"><?= e($link['title'] ?: 'Untitled Link') ?></div>
                                <div style="font-size: 0.8rem; color: var(--primary);">
                                    <?= e(url('s/' . $link['code'])) ?>
                                </div>
                            </td>
                            <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.85rem; color: var(--text-secondary);">
                                <?= e($link['target_url']) ?>
                            </td>
                            <td>
                                <?php if (!empty($link['utm_source'])): ?>
                                    <span class="badge badge-primary"><?= e($link['utm_source']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">None</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 700; color: #6ee7b7;">
                                <?= number_format($link['clicks_count']) ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.copyToClipboard('<?= e(url('s/' . $link['code'])) ?>', 'Short link copied!')">
                                        📋 Copy
                                    </button>
                                    <a href="<?= url('s/' . $link['code']) ?>" target="_blank" class="btn btn-sm btn-outline">
                                        ↗ Test
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
