<?php
/**
 * Superadmin Blog Post Editor
 * Create or Edit Articles with Live Slug & Preview
 */
Auth::requireSuperAdmin();

$pdo = DB::getConnection();
$user = Auth::user();

$postId = (int) ($_GET['id'] ?? 0);
$isEdit = ($postId > 0);
$post = null;

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch();
    if (!$post) {
        set_flash('error', 'Article not found.');
        redirect('admin/blog');
    }
    $pageTitle = 'Edit Article: ' . $post['title'];
} else {
    $pageTitle = 'Write New Creator Article';
    $post = [
        'title' => '',
        'slug' => '',
        'category' => 'Growth',
        'author_name' => $user['name'] ?? 'iLoveDigital Team',
        'cover_image' => '',
        'read_time' => '5 min read',
        'tags' => 'Creator Economy, Growth',
        'lang' => current_lang(),
        'status' => 'published',
        'excerpt' => '',
        'content' => ''
    ];
}

$currentRoute = 'admin_blog';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Session expired. Please try again.');
        redirect($isEdit ? 'admin/blog/edit?id=' . $postId : 'admin/blog/create');
    }

    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'Growth');
    $authorName = trim($_POST['author_name'] ?? ($user['name'] ?? 'iLoveDigital Team'));
    $coverImage = filter_var($_POST['cover_image'] ?? '', FILTER_SANITIZE_URL);
    $readTime = trim($_POST['read_time'] ?? '5 min read');
    $tags = trim($_POST['tags'] ?? '');
    $lang = in_array($_POST['lang'] ?? '', ['en', 'ar']) ? $_POST['lang'] : 'en';
    $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Slug sanitize
    if (!$slug) {
        $slug = preg_replace('/[^a-zA-Z0-9_\-]+/', '-', strtolower($title));
        $slug = trim($slug, '-');
    }

    if (empty($title) || empty($slug) || empty($content)) {
        set_flash('error', 'Please fill in Title, Slug, and Article Content.');
    } else {
        // Check slug uniqueness
        $checkStmt = $pdo->prepare("SELECT id FROM blog_posts WHERE slug = ? AND id != ?");
        $checkStmt->execute([$slug, $postId]);
        if ($checkStmt->fetch()) {
            set_flash('error', 'The slug "' . htmlspecialchars($slug) . '" is already taken by another article.');
        } else {
            if ($isEdit) {
                $upd = $pdo->prepare("UPDATE blog_posts SET title = ?, slug = ?, category = ?, author_name = ?, cover_image = ?, read_time = ?, tags = ?, lang = ?, status = ?, excerpt = ?, content = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $upd->execute([$title, $slug, $category, $authorName, $coverImage, $readTime, $tags, $lang, $status, $excerpt, $content, $postId]);
                set_flash('success', 'Article updated successfully!');
            } else {
                $ins = $pdo->prepare("INSERT INTO blog_posts (title, slug, category, author_name, cover_image, read_time, tags, lang, status, excerpt, content) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $ins->execute([$title, $slug, $category, $authorName, $coverImage, $readTime, $tags, $lang, $status, $excerpt, $content]);
                set_flash('success', 'Article published successfully!');
            }
            redirect('admin/blog');
        }
    }
}

require_once __DIR__ . '/../../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <a href="<?= url('admin/blog') ?>" style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;">
            ← Back to Blog List
        </a>
        <h1 style="font-size: 1.85rem; font-weight: 800;"><?= $isEdit ? 'Edit Article' : 'Write New Article' ?></h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Craft comprehensive guides, case studies, and creator playbooks with live slug and markdown formatting.
        </p>
    </div>

    <?php if ($isEdit && $post['slug']): ?>
        <a href="<?= url('blog/' . e($post['slug'])) ?>" target="_blank" class="btn btn-secondary">
            👁️ Preview Live Article ↗
        </a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= $isEdit ? url('admin/blog/edit?id=' . $postId) : url('admin/blog/create') ?>">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: flex-start;">
        <!-- Left: Main Content Fields -->
        <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="post_title" style="font-weight: 700;">Article Title *</label>
                <input type="text" id="post_title" name="title" class="form-control" value="<?= e($post['title']) ?>" placeholder="e.g. The 2026 Digital Creator Blueprint..." required style="font-size: 1.15rem; font-weight: 700;">
            </div>

            <div class="form-group">
                <label class="form-label" for="post_slug" style="font-weight: 700;">URL Slug *</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted); font-size: 0.85rem; font-family: monospace;"><?= url('blog/') ?></span>
                    <input type="text" id="post_slug" name="slug" class="form-control" value="<?= e($post['slug']) ?>" placeholder="the-2026-digital-creator-blueprint" required style="font-family: monospace;">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="post_excerpt" style="font-weight: 700;">Short Excerpt / Meta Description *</label>
                <textarea id="post_excerpt" name="excerpt" class="form-control" rows="3" placeholder="A compelling 1-2 sentence hook for social media preview cards and Google SEO search results..."><?= e($post['excerpt']) ?></textarea>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" for="post_content" style="font-weight: 700; margin: 0;">Article Content (Markdown &amp; HTML supported) *</label>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Use ## for headings, - for bullets, ``` for code</span>
                </div>
                <textarea id="post_content" name="content" class="form-control" rows="18" placeholder="Write your full guide here...&#10;&#10;## Introduction&#10;Your authentic creator hook...&#10;&#10;### Key Takeaways&#10;- Point 1&#10;- Point 2" required style="font-family: inherit; font-size: 1rem; line-height: 1.6;"><?= e($post['content']) ?></textarea>
            </div>
        </div>

        <!-- Right Sidebar: Publishing Settings -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Publish Actions Card -->
            <div class="card card-glow">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Publishing Controls</h3>

                <div class="form-group">
                    <label class="form-label" for="post_status">Publishing Status</label>
                    <select id="post_status" name="status" class="form-control">
                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>🟢 Published (Live)</option>
                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>🟡 Draft (Hidden)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="post_lang">Language</label>
                    <select id="post_lang" name="lang" class="form-control">
                        <option value="en" <?= $post['lang'] === 'en' ? 'selected' : '' ?>>🇺🇸 English (LTR)</option>
                        <option value="ar" <?= $post['lang'] === 'ar' ? 'selected' : '' ?>>🇸🇦 العربية (Arabic RTL)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; justify-content: center; font-weight: 700; padding: 0.85rem;">
                    <?= $isEdit ? '💾 Save & Update Article' : '🚀 Publish Article Now' ?>
                </button>
            </div>

            <!-- Metadata Card -->
            <div class="card">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Metadata &amp; Categorization</h3>

                <div class="form-group">
                    <label class="form-label" for="post_category">Category</label>
                    <input type="text" id="post_category" name="category" class="form-control" value="<?= e($post['category']) ?>" list="category_options" placeholder="e.g. Growth, Monetization...">
                    <datalist id="category_options">
                        <option value="Growth">
                        <option value="Monetization">
                        <option value="Sponsorships">
                        <option value="Studio Workflows">
                        <option value="الربح من المحتوى">
                        <option value="النمو الرقمي">
                    </datalist>
                </div>

                <div class="form-group">
                    <label class="form-label" for="post_author">Author Name</label>
                    <input type="text" id="post_author" name="author_name" class="form-control" value="<?= e($post['author_name']) ?>" placeholder="Alex Rivera">
                </div>

                <div class="form-group">
                    <label class="form-label" for="post_read_time">Estimated Read Time</label>
                    <input type="text" id="post_read_time" name="read_time" class="form-control" value="<?= e($post['read_time']) ?>" placeholder="e.g. 5 min read">
                </div>

                <div class="form-group">
                    <label class="form-label" for="post_tags">Tags (Comma-separated)</label>
                    <input type="text" id="post_tags" name="tags" class="form-control" value="<?= e($post['tags']) ?>" placeholder="Funnels, Bio Links, Ads">
                </div>

                <div class="form-group">
                    <label class="form-label" for="post_cover">Cover Image URL</label>
                    <input type="url" id="post_cover" name="cover_image" class="form-control" value="<?= e($post['cover_image']) ?>" placeholder="https://images.unsplash.com/..." onchange="updateCoverPreview(this.value)">
                </div>

                <!-- Live Cover Preview -->
                <div id="cover_preview_box" style="margin-top: 0.5rem; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-glass); <?= empty($post['cover_image']) ? 'display: none;' : '' ?>">
                    <img id="cover_preview_img" src="<?= e($post['cover_image']) ?>" alt="Cover preview" style="width: 100%; height: 140px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Auto slug generation if creating new post
const titleInput = document.getElementById('post_title');
const slugInput = document.getElementById('post_slug');
const isEditMode = <?= $isEdit ? 'true' : 'false' ?>;

if (!isEditMode && titleInput && slugInput) {
    titleInput.addEventListener('input', () => {
        if (!slugInput.dataset.manual) {
            const slug = titleInput.value
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });

    slugInput.addEventListener('input', () => {
        slugInput.dataset.manual = 'true';
    });
}

function updateCoverPreview(url) {
    const box = document.getElementById('cover_preview_box');
    const img = document.getElementById('cover_preview_img');
    if (url && url.startsWith('http')) {
        img.src = url;
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/app_layout_end.php'; ?>
