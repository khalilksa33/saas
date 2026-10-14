<?php
$pageTitle = 'Bio-Link & Micro-Page Builder';
$currentRoute = 'bio_link';
$extraCss = ['bio-page.css'];
$extraJs = ['bio-builder.js'];
require_once __DIR__ . '/../includes/app_layout_start.php';

$pdo = DB::getConnection();
$userId = Auth::id();

$stmt = $pdo->prepare("SELECT * FROM bio_profiles WHERE user_id = ?");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

$socials = json_decode($profile['social_links'] ?? '{}', true) ?: [];

$linksStmt = $pdo->prepare("SELECT * FROM bio_links WHERE profile_id = ? ORDER BY sort_order ASC, id ASC");
$linksStmt->execute([$profile['id'] ?? 0]);
$links = $linksStmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Bio-Link & Micro-Page Builder</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Design your mobile-first creator landing page with live interactive phone preview
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="<?= url('p/' . e($user['username'])) ?>" target="_blank" class="btn btn-secondary">
            ↗ Open Live Bio (<?= e($user['username']) ?>)
        </a>
        <button type="button" id="saveBioBtn" class="btn btn-primary">
            💾 Save Profile & Links
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.25fr 0.95fr; gap: 2rem; align-items: flex-start;">
    <!-- Left Column: Form & Link Controls -->
    <div id="bioBuilderForm">
        <!-- Theme & Profile Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🎨</span> Visual Theme & Branding
            </h3>

            <div class="form-group">
                <label class="form-label" for="themeSelect">Select Theme</label>
                <select id="themeSelect" class="form-control">
                    <option value="midnight" <?= ($profile['theme'] ?? '') === 'midnight' ? 'selected' : '' ?>>🌌 Midnight Indigo (Glassmorphism & Dark Slate)</option>
                    <option value="cyber" <?= ($profile['theme'] ?? '') === 'cyber' ? 'selected' : '' ?>>⚡ Cyberpunk Neon (Electric Cyan & Magenta)</option>
                    <option value="sunset" <?= ($profile['theme'] ?? '') === 'sunset' ? 'selected' : '' ?>>🌅 Sunset Horizon (Warm Rose & Purple Glow)</option>
                    <option value="gold" <?= ($profile['theme'] ?? '') === 'gold' ? 'selected' : '' ?>>✨ Luxury Gold (Obsidian & Amber Gold)</option>
                    <option value="minimal" <?= ($profile['theme'] ?? '') === 'minimal' ? 'selected' : '' ?>>⚪ Minimalist Clean (Sleek Ivory & Dark Charcoal)</option>
                    <option value="emerald" <?= ($profile['theme'] ?? '') === 'emerald' ? 'selected' : '' ?>>🌿 Emerald Mint (Deep Forest & Mint Green)</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="displayNameInput">Display Name</label>
                    <input type="text" id="displayNameInput" class="form-control" value="<?= e($profile['display_name'] ?? $user['name']) ?>" placeholder="Alex Rivera ⚡">
                </div>
                <div class="form-group">
                    <label class="form-label" for="avatarUrlInput">Avatar Image URL</label>
                    <input type="url" id="avatarUrlInput" class="form-control" value="<?= e($profile['avatar_url'] ?? $user['avatar_url']) ?>" placeholder="https://...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="bioTextInput">Bio Tagline / Value Proposition</label>
                <textarea id="bioTextInput" class="form-control" rows="2" placeholder="Digital Creator • Free presets & gear guide below 👇"><?= e($profile['bio_text'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Social Media Icons Bar -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🌐</span> Social Media Links
            </h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">YouTube URL</label>
                    <input type="url" id="social_youtube" class="form-control social-input" placeholder="https://youtube.com/@yourchannel" value="<?= e($socials['youtube'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">Instagram URL</label>
                    <input type="url" id="social_instagram" class="form-control social-input" placeholder="https://instagram.com/username" value="<?= e($socials['instagram'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">TikTok URL</label>
                    <input type="url" id="social_tiktok" class="form-control social-input" placeholder="https://tiktok.com/@username" value="<?= e($socials['tiktok'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">X / Twitter URL</label>
                    <input type="url" id="social_twitter" class="form-control social-input" placeholder="https://x.com/username" value="<?= e($socials['twitter'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">Spotify / Podcast</label>
                    <input type="url" id="social_spotify" class="form-control social-input" placeholder="https://open.spotify.com/..." value="<?= e($socials['spotify'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label" style="font-size: 0.78rem;">GitHub / Kit URL</label>
                    <input type="url" id="social_github" class="form-control social-input" placeholder="https://github.com/username" value="<?= e($socials['github'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Links List Manager -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🔗</span> Bio Links Stack
                </h3>
                <button type="button" id="addLinkBtn" class="btn btn-sm btn-primary">
                    + Add New Link
                </button>
            </div>

            <div id="linksList">
                <?php if (!empty($links)): ?>
                    <?php foreach ($links as $l): ?>
                        <div class="link-item-row card" style="padding: 1rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem;">
                                    <span>☰</span> Link Item (<?= number_format($l['clicks_count']) ?> Clicks)
                                </span>
                                <button type="button" class="btn btn-sm btn-danger remove-link-btn">Delete</button>
                            </div>
                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                                <input type="text" class="form-control link-title" placeholder="Button Title" value="<?= e($l['title']) ?>">
                                <input type="text" class="form-control link-badge" placeholder="Badge (e.g. HOT, NEW)" value="<?= e($l['badge']) ?>">
                            </div>
                            <div style="margin-bottom: 0.75rem;">
                                <input type="url" class="form-control link-url" placeholder="Destination URL (https://...)" value="<?= e($l['url']) ?>">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label class="form-label" style="font-size: 0.75rem;">Icon Emoji</label>
                                    <select class="form-control link-icon">
                                        <option value="🔗" <?= $l['icon'] === '🔗' ? 'selected' : '' ?>>🔗 Link</option>
                                        <option value="🎥" <?= $l['icon'] === '🎥' ? 'selected' : '' ?>>🎥 Video</option>
                                        <option value="🎙️" <?= $l['icon'] === '🎙️' ? 'selected' : '' ?>>🎙️ Podcast</option>
                                        <option value="🎨" <?= $l['icon'] === '🎨' ? 'selected' : '' ?>>🎨 Design / Assets</option>
                                        <option value="🛍️" <?= $l['icon'] === '🛍️' ? 'selected' : '' ?>>🛍️ Store / Product</option>
                                        <option value="💻" <?= $l['icon'] === '💻' ? 'selected' : '' ?>>💻 Tech Gear</option>
                                        <option value="📬" <?= $l['icon'] === '📬' ? 'selected' : '' ?>>📬 Newsletter</option>
                                        <option value="⭐" <?= $l['icon'] === '⭐' ? 'selected' : '' ?>>⭐ Featured</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size: 0.75rem;">Attention Animation</label>
                                    <select class="form-control link-animation">
                                        <option value="none" <?= $l['animation'] === 'none' ? 'selected' : '' ?>>None</option>
                                        <option value="pulse" <?= $l['animation'] === 'pulse' ? 'selected' : '' ?>>Subtle Pulse</option>
                                        <option value="shake" <?= $l['animation'] === 'shake' ? 'selected' : '' ?>>Gentle Shake</option>
                                        <option value="bounce" <?= $l['animation'] === 'bounce' ? 'selected' : '' ?>>Hover Bounce</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Live Phone Mockup Preview -->
    <div style="position: sticky; top: 90px;">
        <div style="text-align: center; margin-bottom: 1rem;">
            <span class="badge badge-primary">LIVE MOBILE PREVIEW</span>
        </div>

        <div class="phone-mockup-wrapper">
            <div class="phone-mockup">
                <div class="phone-island"></div>
                <div class="phone-screen theme-<?= e($profile['theme'] ?? 'midnight') ?>" id="phoneScreen">
                    <div class="bio-header">
                        <div class="bio-avatar-wrapper">
                            <img src="<?= e($profile['avatar_url'] ?: $user['avatar_url']) ?>" alt="Avatar" class="bio-avatar" id="previewAvatar">
                            <span class="bio-verified-badge" title="Verified Creator">✓</span>
                        </div>
                        <h2 class="bio-name" id="previewName"><?= e($profile['display_name'] ?: $user['name']) ?></h2>
                        <p class="bio-description" id="previewBio"><?= e($profile['bio_text'] ?: 'Digital Creator') ?></p>
                    </div>

                    <div class="bio-socials" id="previewSocials"></div>

                    <div class="bio-links-stack" id="previewLinks"></div>

                    <div class="bio-branding-footer">
                        Powered by <strong><?= e(APP_NAME) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
