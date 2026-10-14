<?php
/**
 * Superadmin Dashboard Overview
 * Platform Analytics & Quick Management
 */
Auth::requireSuperAdmin();

$pageTitle = 'Superadmin Control Center';
$currentRoute = 'admin_dashboard';
$user = Auth::user();
$pdo = DB::getConnection();

// Metrics aggregation
$totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$freeUsers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'free'")->fetchColumn();
$proUsers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'pro'")->fetchColumn();
$agencyUsers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'agency'")->fetchColumn();

// Estimated Monthly Recurring Revenue (Pro: $15/mo, Agency: $39/mo)
$estimatedMrr = ($proUsers * 15) + ($agencyUsers * 39);

// Bio Profiles & Smart Links
$totalBioViews = (int) $pdo->query("SELECT SUM(views_count) FROM bio_profiles")->fetchColumn();
$totalLinkClicks = (int) $pdo->query("SELECT SUM(clicks_count) FROM short_links")->fetchColumn() 
                 + (int) $pdo->query("SELECT SUM(clicks_count) FROM bio_links")->fetchColumn();

// Blog Posts
$totalPosts = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$publishedPosts = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();

// Recent Users
$recentUsers = $pdo->query("SELECT id, name, username, email, plan, role, created_at FROM users ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Recent Blog Posts
$recentPosts = $pdo->query("SELECT id, title, slug, category, status, views_count, lang, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Handle quick plan update from recent users table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_plan_update'])) {
    if (verify_csrf()) {
        $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
        $newPlan = in_array($_POST['plan'] ?? '', ['free', 'pro', 'agency']) ? $_POST['plan'] : 'pro';
        if ($targetUserId > 0) {
            $stmt = $pdo->prepare("UPDATE users SET plan = ? WHERE id = ?");
            $stmt->execute([$newPlan, $targetUserId]);
            set_flash('success', 'User subscription updated successfully!');
            redirect('admin');
        }
    }
}

require_once __DIR__ . '/../../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
            <span class="badge badge-pro" style="background: linear-gradient(135deg, #f59e0b, #ef4444); color: #fff;">
                ⚡ PLATFORM SUPERADMIN
            </span>
            <span style="font-size: 0.8rem; color: var(--text-muted);">ilovedigital.online</span>
        </div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Platform Operations &amp; Intelligence</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Monitor global creator accounts, subscriptions, MRR metrics, and publish blog articles.
        </p>
    </div>

    <!-- Quick Actions -->
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= url('admin/blog/create') ?>" class="btn btn-primary">
            ✍️ Write New Blog Post
        </a>
        <a href="<?= url('admin/subscriptions') ?>" class="btn btn-secondary">
            👥 Manage Subscriptions
        </a>
        <a href="<?= url('admin/blog') ?>" class="btn btn-secondary">
            📰 Blog Posts CMS
        </a>
    </div>
</div>

<!-- Superadmin KPI Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <!-- MRR Card -->
    <div class="card card-glow" style="border-left: 4px solid #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">ESTIMATED MRR</span>
            <span style="font-size: 1.25rem;">💰</span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; color: #10b981;">
            $<?= number_format($estimatedMrr) ?><span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">/mo</span>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
            <?= $proUsers ?> Pro ($15) • <?= $agencyUsers ?> Agency ($39)
        </div>
    </div>

    <!-- Total Creators -->
    <div class="card card-glow" style="border-left: 4px solid var(--primary);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">TOTAL CREATORS</span>
            <span style="font-size: 1.25rem;">👥</span>
        </div>
        <div style="font-size: 2rem; font-weight: 800;">
            <?= number_format($totalUsers) ?>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
            <?= $freeUsers ?> Free tier accounts
        </div>
    </div>

    <!-- Bio Traffic -->
    <div class="card card-glow" style="border-left: 4px solid #ec4899;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">PLATFORM TRAFFIC</span>
            <span style="font-size: 1.25rem;">📊</span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; color: #ec4899;">
            <?= number_format($totalBioViews + $totalLinkClicks) ?>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
            <?= number_format($totalBioViews) ?> Bio views • <?= number_format($totalLinkClicks) ?> Clicks
        </div>
    </div>

    <!-- Published Content -->
    <div class="card card-glow" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">BLOG ARTICLES</span>
            <span style="font-size: 1.25rem;">📰</span>
        </div>
        <div style="font-size: 2rem; font-weight: 800; color: #f59e0b;">
            <?= $publishedPosts ?>
        </div>
        <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
            <?= $totalPosts ?> Total in database
        </div>
    </div>
</div>

<!-- Main Two-Column Control Section -->
<div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; align-items: flex-start;">
    <!-- Recent Users & Subscription Quick Switch -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                <span>👥</span> Recent Creator Registrations
            </h3>
            <a href="<?= url('admin/subscriptions') ?>" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">
                View All Users →
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-glass); text-align: left; color: var(--text-muted);">
                        <th style="padding: 0.75rem 0.5rem;">User</th>
                        <th style="padding: 0.75rem 0.5rem;">Plan</th>
                        <th style="padding: 0.75rem 0.5rem;">Role</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Quick Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $u): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700;"><?= e($u['name']) ?></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);"><?= e($u['email']) ?></div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <?php if ($u['plan'] === 'agency'): ?>
                                    <span class="badge" style="background: rgba(236,72,153,0.2); color: #ec4899; font-weight: 700;">AGENCY</span>
                                <?php elseif ($u['plan'] === 'pro'): ?>
                                    <span class="badge badge-pro">PRO</span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-secondary);">FREE</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <?php if ($u['role'] === 'superadmin'): ?>
                                    <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; font-weight: 700;">ADMIN</span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">User</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <form method="POST" action="<?= url('admin') ?>" style="display: inline-block;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="quick_plan_update" value="1">
                                    <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                    <select name="plan" onchange="this.form.submit()" style="padding: 0.25rem 0.5rem; border-radius: 6px; background: rgba(255,255,255,0.05); color: var(--text-primary); border: 1px solid var(--border-glass); font-size: 0.8rem; cursor: pointer;">
                                        <option value="free" <?= $u['plan'] === 'free' ? 'selected' : '' ?>>Free</option>
                                        <option value="pro" <?= $u['plan'] === 'pro' ? 'selected' : '' ?>>Pro ($15)</option>
                                        <option value="agency" <?= $u['plan'] === 'agency' ? 'selected' : '' ?>>Agency ($39)</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Blog Posts & CMS Quick View -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                <span>📰</span> Blog Posts CMS
            </h3>
            <a href="<?= url('admin/blog') ?>" style="font-size: 0.85rem; color: var(--primary); text-decoration: none; font-weight: 600;">
                Manage All →
            </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
            <?php foreach ($recentPosts as $p): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-radius: 8px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-glass);">
                    <div style="flex: 1; min-width: 0; padding-right: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem;">
                            <span class="badge" style="font-size: 0.65rem; background: rgba(255,255,255,0.08);">
                                <?= $p['lang'] === 'ar' ? '🇸🇦 AR' : '🇺🇸 EN' ?>
                            </span>
                            <span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;"><?= e($p['category']) ?></span>
                        </div>
                        <h4 style="font-size: 0.9rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 0.2rem;">
                            <a href="<?= url('blog/' . e($p['slug'])) ?>" target="_blank" style="color: inherit; text-decoration: none;">
                                <?= e($p['title']) ?>
                            </a>
                        </h4>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            👁️ <?= number_format($p['views_count']) ?> views • <?= date('M j, Y', strtotime($p['created_at'])) ?>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <a href="<?= url('admin/blog/edit?id=' . $p['id']) ?>" class="btn btn-sm btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" title="Edit Post">
                            ✏️
                        </a>
                        <a href="<?= url('blog/' . e($p['slug'])) ?>" target="_blank" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" title="View Live">
                            ↗
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="<?= url('admin/blog/create') ?>" class="btn btn-primary" style="width: 100%; margin-top: 1.25rem;">
            ➕ Create New Article
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_layout_end.php'; ?>
