<?php
$pageTitle = 'Smart Short Links & QR Studio';
$currentRoute = 'smart_links';
$extraJs = ['smart-links.js'];
require_once __DIR__ . '/../includes/app_layout_start.php';

$pdo = DB::getConnection();
$userId = Auth::id();

$linksStmt = $pdo->prepare("SELECT * FROM short_links WHERE user_id = ? ORDER BY id DESC");
$linksStmt->execute([$userId]);
$links = $linksStmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Smart Short Links & Branded QR Studio</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Shorten URLs, auto-attach campaign UTM parameters, and generate high-res branded QR codes
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.35fr 0.95fr; gap: 2rem; margin-bottom: 2.5rem; align-items: flex-start;">
    <!-- Shortener & UTM Form -->
    <div class="card">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🔗</span> Create New Smart Link
        </h3>

        <form id="shortenerForm">
            <div class="form-group">
                <label class="form-label" for="targetUrlInput">Destination URL (Where should this link go?)</label>
                <input type="url" id="targetUrlInput" class="form-control" placeholder="https://gumroad.com/l/my-preset-pack" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="linkTitleInput">Link Title (For your reference)</label>
                    <input type="text" id="linkTitleInput" class="form-control" placeholder="Spring Preset Launch">
                </div>
                <div class="form-group">
                    <label class="form-label" for="customCodeInput">Custom Slug (Optional)</label>
                    <div style="display: flex; align-items: center; position: relative;">
                        <span style="position: absolute; left: 0.75rem; color: var(--text-muted); font-size: 0.85rem;">/s/</span>
                        <input type="text" id="customCodeInput" class="form-control" style="padding-left: 2.2rem;" placeholder="springsale">
                    </div>
                </div>
            </div>

            <!-- UTM Parameters Accordion -->
            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-glass); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.85rem; font-weight: 700; color: #a5b4fc;">📊 UTM Campaign Tracking (Optional)</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Source</label>
                        <input type="text" id="utmSource" class="form-control" placeholder="youtube" style="font-size: 0.85rem;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Medium</label>
                        <input type="text" id="utmMedium" class="form-control" placeholder="video_desc" style="font-size: 0.85rem;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.75rem;">Campaign</label>
                        <input type="text" id="utmCampaign" class="form-control" placeholder="march_drop" style="font-size: 0.85rem;">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem;">
                Generate Smart Link 🚀
            </button>
        </form>
    </div>

    <!-- Branded QR Code Studio -->
    <div class="card" style="text-align: center;">
        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <span>📱</span> Branded QR Code Studio
        </h3>

        <div style="display: inline-block; padding: 1rem; background: rgba(0, 0, 0, 0.4); border: 1px solid var(--border-glass); border-radius: var(--radius-lg); margin-bottom: 1.25rem;">
            <canvas id="qrCanvas" width="260" height="260" style="display: block; border-radius: var(--radius-md);"></canvas>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label class="form-label" style="font-size: 0.75rem;">QR Color</label>
                <input type="color" id="qrFgColor" value="#6366f1" style="width: 44px; height: 34px; border-radius: var(--radius-sm); cursor: pointer;">
            </div>
            <div>
                <label class="form-label" style="font-size: 0.75rem;">Background</label>
                <input type="color" id="qrBgColor" value="#ffffff" style="width: 44px; height: 34px; border-radius: var(--radius-sm); cursor: pointer;">
            </div>
        </div>

        <button type="button" id="downloadQrBtn" class="btn btn-secondary" style="width: 100%;">
            📥 Download QR Code (PNG)
        </button>
    </div>
</div>

<!-- All Links Table -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <div>
            <h3 style="font-size: 1.15rem; font-weight: 700;">Your Smart Links</h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary);">Manage and track performance across all campaigns</p>
        </div>
        <span class="badge badge-primary"><?= count($links) ?> Links Total</span>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title & Short URL</th>
                    <th>Destination URL</th>
                    <th>UTM Source / Medium</th>
                    <th>Clicks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($links)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                            You have no smart links yet. Create one above to start tracking clicks!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700;"><?= e($link['title'] ?: 'Smart Link') ?></div>
                                <div style="font-size: 0.82rem; color: var(--primary);">
                                    <?= e(url('s/' . $link['code'])) ?>
                                </div>
                            </td>
                            <td style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.85rem; color: var(--text-secondary);">
                                <?= e($link['target_url']) ?>
                            </td>
                            <td>
                                <?php if (!empty($link['utm_source'])): ?>
                                    <span class="badge badge-primary"><?= e($link['utm_source']) ?></span>
                                    <?php if (!empty($link['utm_medium'])): ?>
                                        <span class="badge badge-emerald"><?= e($link['utm_medium']) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">Direct</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 700; color: #6ee7b7; font-size: 1rem;">
                                <?= number_format($link['clicks_count']) ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.copyToClipboard('<?= e(url('s/' . $link['code'])) ?>', 'Copied short link!')">
                                        📋 Copy
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline" onclick="window.renderQrCode('<?= e(url('s/' . $link['code'])) ?>')">
                                        📱 QR
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
