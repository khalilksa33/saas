<?php
/**
 * Superadmin Blog Posts CMS List
 * Manage, edit, publish, and delete blog articles
 */
Auth::requireSuperAdmin();

$pageTitle = 'Blog Posts Management';
$currentRoute = 'admin_blog';
$user = Auth::user();
$pdo = DB::getConnection();

// Handle Actions (Toggle status, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Session expired. Please try again.');
        redirect('admin/blog');
    }

    $action = $_POST['action'] ?? '';
    $postId = (int) ($_POST['post_id'] ?? 0);

    if ($postId > 0) {
        if ($action === 'toggle_status') {
            $currentStatus = $pdo->query("SELECT status FROM blog_posts WHERE id = $postId")->fetchColumn();
            $newStatus = ($currentStatus === 'published') ? 'draft' : 'published';
            $stmt = $pdo->prepare("UPDATE blog_posts SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $postId]);
            set_flash('success', 'Article status changed to ' . strtoupper($newStatus) . '.');
            redirect('admin/blog');

        } elseif ($action === 'delete_post') {
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$postId]);
            set_flash('success', 'Blog article deleted successfully.');
            redirect('admin/blog');
        }
    }
}

// Fetch all posts
$statusFilter = trim($_GET['status'] ?? '');
$langFilter = trim($_GET['lang'] ?? '');
$search = trim($_GET['q'] ?? '');

$sql = "SELECT id, title, slug, category, author_name, cover_image, read_time, lang, status, views_count, created_at FROM blog_posts WHERE 1=1";
$params = [];

if ($statusFilter && in_array($statusFilter, ['published', 'draft'])) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if ($langFilter && in_array($langFilter, ['en', 'ar'])) {
    $sql .= " AND lang = ?";
    $params[] = $langFilter;
}

if ($search) {
    $sql .= " AND (title LIKE ? OR category LIKE ? OR tags LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$postsList = $stmt->fetchAll();

// Counts
$totalPosts = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$publishedCount = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'")->fetchColumn();
$draftCount = (int) $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'draft'")->fetchColumn();

require_once __DIR__ . '/../../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Blog Posts &amp; Content Management</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Write, review, publish, and organize creator playbooks in English and Arabic.
        </p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= url('blog') ?>" target="_blank" class="btn btn-secondary">
            👁️ View Public Blog ↗
        </a>
        <a href="<?= url('admin/blog/create') ?>" class="btn btn-primary">
            ➕ Write New Article
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= url('admin/blog') ?>" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="<?= url('admin/blog') ?>" class="btn btn-sm <?= empty($statusFilter) && empty($langFilter) ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                All Posts (<?= $totalPosts ?>)
            </a>
            <a href="<?= url('admin/blog?status=published') ?>" class="btn btn-sm <?= $statusFilter === 'published' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                Published (<?= $publishedCount ?>)
            </a>
            <a href="<?= url('admin/blog?status=draft') ?>" class="btn btn-sm <?= $statusFilter === 'draft' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                Drafts (<?= $draftCount ?>)
            </a>
            <a href="<?= url('admin/blog?lang=en') ?>" class="btn btn-sm <?= $langFilter === 'en' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                🇺🇸 English
            </a>
            <a href="<?= url('admin/blog?lang=ar') ?>" class="btn btn-sm <?= $langFilter === 'ar' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                🇸🇦 العربية
            </a>
        </div>

        <div style="display: flex; gap: 0.5rem; flex: 1; max-width: 360px;">
            <input type="text" name="q" class="form-control" placeholder="Search by title, tag, or topic..." value="<?= e($search) ?>" style="padding: 0.5rem 1rem;">
            <button type="submit" class="btn btn-secondary">Search</button>
            <?php if ($search || $statusFilter || $langFilter): ?>
                <a href="<?= url('admin/blog') ?>" class="btn btn-outline" title="Clear Filters">✕</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Posts Table -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--border-glass); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 1rem 1.25rem;">Article</th>
                    <th style="padding: 1rem;">Category</th>
                    <th style="padding: 1rem;">Lang</th>
                    <th style="padding: 1rem;">Status</th>
                    <th style="padding: 1rem;">Views</th>
                    <th style="padding: 1rem;">Created</th>
                    <th style="padding: 1rem 1.25rem; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($postsList)): ?>
                    <tr>
                        <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            No articles found. <a href="<?= url('admin/blog/create') ?>" style="color: var(--primary);">Write your first article now</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($postsList as $p): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <td style="padding: 1rem 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <?php if ($p['cover_image']): ?>
                                        <img src="<?= e($p['cover_image']) ?>" alt="<?= e($p['title']) ?>" style="width: 52px; height: 38px; border-radius: 6px; object-fit: cover; flex-shrink: 0;">
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.2rem;">
                                            <a href="<?= url('admin/blog/edit?id=' . $p['id']) ?>" style="color: inherit; text-decoration: none;">
                                                <?= e($p['title']) ?>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                                            slug: <code><?= e($p['slug']) ?></code> • <?= e($p['author_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge" style="background: rgba(255,255,255,0.08); font-size: 0.75rem;">
                                    <?= e($p['category']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="font-size: 0.85rem;">
                                    <?= $p['lang'] === 'ar' ? '🇸🇦 Arabic' : '🇺🇸 English' ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($p['status'] === 'published'): ?>
                                    <span class="badge badge-pro" style="font-size: 0.72rem;">PUBLISHED</span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(255,255,255,0.1); color: var(--text-muted); font-size: 0.72rem;">DRAFT</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                👁️ <?= number_format($p['views_count']) ?>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.82rem;">
                                <?= date('M j, Y', strtotime($p['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                    <a href="<?= url('blog/' . e($p['slug'])) ?>" target="_blank" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" title="View Live Article">
                                        ↗
                                    </a>
                                    <a href="<?= url('admin/blog/edit?id=' . $p['id']) ?>" class="btn btn-sm btn-secondary" style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" title="Edit Article">
                                        ✏️ Edit
                                    </a>

                                    <!-- Status Toggle Form -->
                                    <form method="POST" action="<?= url('admin/blog') ?>" style="display: inline-block;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.55rem; font-size: 0.75rem;" title="<?= $p['status'] === 'published' ? 'Unpublish to Draft' : 'Publish Article' ?>">
                                            <?= $p['status'] === 'published' ? 'Unpublish' : 'Publish' ?>
                                        </button>
                                    </form>

                                    <!-- Delete Form -->
                                    <form method="POST" action="<?= url('admin/blog') ?>" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this article?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete_post">
                                        <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; color: #ef4444;" title="Delete Post">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_layout_end.php'; ?>
