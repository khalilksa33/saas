<?php
/**
 * Single Blog Post Reader View
 * iLoveDigital Creator Hub
 */

$slug = $routeParam ?? '';
$pdo = DB::getConnection();

$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ?");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Article Not Found';
    require_once __DIR__ . '/../includes/header.php';
    echo "<div class='container' style='padding: 6rem 1.5rem; text-align: center;'>
            <div style='font-size: 3rem; margin-bottom: 1rem;'>📄</div>
            <h1 style='font-size: 2rem; font-weight: 800; margin-bottom: 1rem;'>Article Not Found</h1>
            <p style='color: var(--text-muted); margin-bottom: 2rem;'>The article you are looking for does not exist or has been moved.</p>
            <a href='" . url('blog') . "' class='btn btn-primary'>← Return to Creator Blog</a>
          </div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// If draft, only superadmin can view
if ($post['status'] !== 'published' && !Auth::isSuperAdmin()) {
    http_response_code(403);
    echo "This article is currently an unpublished draft.";
    exit;
}

// Increment view count
$upd = $pdo->prepare("UPDATE blog_posts SET views_count = views_count + 1 WHERE id = ?");
$upd->execute([$post['id']]);

// Fetch related posts (same category or latest, excluding current)
$relStmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id != ? AND status = 'published' ORDER BY (category = ?) DESC, created_at DESC LIMIT 3");
$relStmt->execute([$post['id'], $post['category']]);
$relatedPosts = $relStmt->fetchAll();

$pageTitle = $post['title'];
$pageDescription = $post['excerpt'];
$isPostRtl = ($post['lang'] === 'ar');

// Simple Markdown parser for clean formatting
function render_markdown(string $text): string {
    $html = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Headings
    $html = preg_replace('/^### (.*?)$/m', '<h3 style="font-size: 1.35rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--text-primary);">$1</h3>', $html);
    $html = preg_replace('/^## (.*?)$/m', '<h2 style="font-size: 1.7rem; font-weight: 800; margin: 2.5rem 0 1rem; color: var(--text-primary); border-bottom: 1px solid var(--border-glass); padding-bottom: 0.5rem;">$1</h2>', $html);
    $html = preg_replace('/^# (.*?)$/m', '<h1 style="font-size: 2.1rem; font-weight: 800; margin: 2rem 0 1rem; color: var(--text-primary);">$1</h1>', $html);

    // Blockquotes
    $html = preg_replace('/^> (.*?)$/m', '<blockquote style="border-left: 4px solid var(--primary); padding: 0.85rem 1.25rem; margin: 1.5rem 0; background: rgba(99, 102, 241, 0.05); border-radius: 0 8px 8px 0; font-style: italic; color: var(--text-secondary);">$1</blockquote>', $html);

    // Horizontal Rule
    $html = preg_replace('/^---$/m', '<hr style="border: none; border-top: 1px solid var(--border-glass); margin: 2.5rem 0;">', $html);

    // Bold & Italic
    $html = preg_replace('/\*\*(.*?)\*\*/s', '<strong style="color: var(--text-primary);">$1</strong>', $html);
    $html = preg_replace('/\*([^*]+)\*/', '<em style="color: var(--text-secondary);">$1</em>', $html);

    // Code blocks
    $html = preg_replace('/```([a-zA-Z]*)\n(.*?)\n```/s', '<pre style="background: rgba(10, 15, 29, 0.9); border: 1px solid var(--border-glass); border-radius: 8px; padding: 1.25rem; overflow-x: auto; font-family: monospace; font-size: 0.88rem; margin: 1.5rem 0;"><code>$2</code></pre>', $html);
    $html = preg_replace('/`([^`]+)`/', '<code style="background: rgba(255,255,255,0.08); padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; font-size: 0.9em; color: var(--primary);">$1</code>', $html);

    // Math formulas
    $html = preg_replace('/\$\$(.*?)\$\$/s', '<div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); border-radius: 8px; padding: 1rem; text-align: center; margin: 1.5rem 0; font-weight: 700; color: #6ee7b7; font-family: monospace;">$1</div>', $html);

    // Bullet points
    $html = preg_replace('/^- (.*?)$/m', '<li style="margin-bottom: 0.5rem; line-height: 1.6;">$1</li>', $html);
    $html = preg_replace('/((?:<li[^>]*>.*?<\/li>\s*)+)/s', '<ul style="padding-left: 1.5rem; margin: 1rem 0 1.5rem;">$1</ul>', $html);

    // Paragraphs
    $paragraphs = preg_split('/\n\s*\n/', $html);
    $formatted = [];
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if ($p && !preg_match('/^<(h[1-6]|ul|blockquote|pre|hr|div)/i', $p)) {
            $formatted[] = '<p style="line-height: 1.8; margin-bottom: 1.35rem; color: var(--text-secondary); font-size: 1.05rem;">' . nl2br($p) . '</p>';
        } else {
            $formatted[] = $p;
        }
    }

    return implode("\n", $formatted);
}

require_once __DIR__ . '/../includes/header.php';
?>

<article style="padding: 2.5rem 1.5rem 5rem;" class="<?= $isPostRtl ? 'post-rtl' : '' ?>">
    <div class="container" style="max-width: 820px;">
        <!-- Breadcrumb & Back Link -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <a href="<?= url('blog') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                <span>←</span> <?= current_lang() === 'ar' ? 'العودة لجميع المقالات' : 'Back to Creator Blog' ?>
            </a>
            <?php if (Auth::isSuperAdmin()): ?>
                <a href="<?= url('admin/blog/edit?id=' . $post['id']) ?>" class="btn btn-sm btn-outline">
                    ✏️ <?= current_lang() === 'ar' ? 'تعديل المقال' : 'Edit Post' ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- Post Header -->
        <header style="margin-bottom: 2.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <span class="badge badge-pro"><?= e($post['category']) ?></span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">⏱️ <?= e($post['read_time']) ?></span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">•</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);"><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">•</span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">👁️ <?= number_format($post['views_count']) ?> views</span>
            </div>

            <h1 style="font-size: clamp(2rem, 3.5vw, 2.75rem); font-weight: 800; line-height: 1.25; margin-bottom: 1.25rem;">
                <?= e($post['title']) ?>
            </h1>

            <p style="font-size: 1.2rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
                <?= e($post['excerpt']) ?>
            </p>

            <!-- Author & Share Meta Bar -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass); flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--primary-glow); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid var(--primary);">
                        ✍️
                    </div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.95rem;"><?= e($post['author_name']) ?></div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?= current_lang() === 'ar' ? 'فريق محتوى iLoveDigital' : 'iLoveDigital Creator Team' ?></div>
                    </div>
                </div>

                <!-- Social Share Buttons -->
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.85rem; color: var(--text-muted); margin-right: 0.25rem;"><?= current_lang() === 'ar' ? 'مشاركة:' : 'Share:' ?></span>
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode(url('blog/' . $post['slug'])) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Share on X">
                        𝕏
                    </a>
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . url('blog/' . $post['slug'])) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Share on WhatsApp">
                        💬
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-copy="<?= e(url('blog/' . $post['slug'])) ?>" title="Copy Article URL">
                        🔗 <?= current_lang() === 'ar' ? 'نسخ الرابط' : 'Copy' ?>
                    </button>
                </div>
            </div>
        </header>

        <!-- Cover Image -->
        <?php if ($post['cover_image']): ?>
            <div style="margin-bottom: 3rem; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-glass); box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <img src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title']) ?>" style="width: 100%; max-height: 440px; object-fit: cover;">
            </div>
        <?php endif; ?>

        <!-- Post Content -->
        <div class="blog-body" style="line-height: 1.8; font-size: 1.05rem;">
            <?= render_markdown($post['content']) ?>
        </div>

        <!-- Tags List -->
        <?php if ($post['tags']): ?>
            <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--border-glass); display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <span style="font-size: 0.85rem; color: var(--text-muted);">🏷️ <?= current_lang() === 'ar' ? 'الوسوم:' : 'Tags:' ?></span>
                <?php foreach (explode(',', $post['tags']) as $tag): ?>
                    <span class="badge" style="background: rgba(255,255,255,0.06); font-weight: 500; font-size: 0.8rem;">
                        #<?= trim(e($tag)) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Author Bio Card -->
        <div class="card card-glow" style="margin-top: 3.5rem; padding: 2rem; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;">
                🚀
            </div>
            <div style="flex: 1;">
                <h4 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.4rem;"><?= e($post['author_name']) ?></h4>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5; margin-bottom: 0;">
                    <?= current_lang() === 'ar' 
                        ? 'كاتب وباحث في اقتصاد صناع المحتوى واستراتيجيات النمو الرقمي في منصة iLoveDigital.'
                        : 'Researcher and practitioner in the creator economy, monetization architectures, and bio-link conversion systems at iLoveDigital.' ?>
                </p>
            </div>
        </div>

        <!-- Related Posts Section -->
        <?php if (!empty($relatedPosts)): ?>
            <div style="margin-top: 5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1.75rem;">
                    <?= current_lang() === 'ar' ? 'مقالات ذات صلة قد تهمك' : 'Related Articles' ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
                    <?php foreach ($relatedPosts as $rel): ?>
                        <div class="card" style="padding: 1.25rem;">
                            <span class="badge badge-pro" style="font-size: 0.7rem; margin-bottom: 0.75rem; display: inline-block;">
                                <?= e($rel['category']) ?>
                            </span>
                            <h4 style="font-size: 1rem; font-weight: 700; line-height: 1.4; margin-bottom: 0.75rem;">
                                <a href="<?= url('blog/' . e($rel['slug'])) ?>" style="color: inherit; text-decoration: none;">
                                    <?= e($rel['title']) ?>
                                </a>
                            </h4>
                            <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.75rem;">
                                <?= e(mb_strimwidth($rel['excerpt'], 0, 90, '...')) ?>
                            </p>
                            <a href="<?= url('blog/' . e($rel['slug'])) ?>" style="color: var(--primary); font-size: 0.8rem; font-weight: 700; text-decoration: none;">
                                <?= current_lang() === 'ar' ? 'قراءة ←' : 'Read Now →' ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
