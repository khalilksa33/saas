<?php
/**
 * Public Blog Index
 * iLoveDigital Creator Hub
 */
$pageTitle = __('nav_blog', 'Creator Blog & Insights');
$pdo = DB::getConnection();

// Language filtering & category filtering
$currentLang = current_lang();
$selectedCategory = trim($_GET['category'] ?? '');
$searchQuery = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM blog_posts WHERE status = 'published'";
$params = [];

if ($selectedCategory) {
    $sql .= " AND category = ?";
    $params[] = $selectedCategory;
}

if ($searchQuery) {
    $sql .= " AND (title LIKE ? OR excerpt LIKE ? OR tags LIKE ?)";
    $like = '%' . $searchQuery . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

// Order by latest
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Get unique categories
$catStmt = $pdo->query("SELECT DISTINCT category FROM blog_posts WHERE status = 'published'");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Separate featured post (latest post) and remaining posts
$featuredPost = null;
$gridPosts = [];
if (!empty($posts)) {
    if (!$selectedCategory && !$searchQuery) {
        $featuredPost = $posts[0];
        $gridPosts = array_slice($posts, 1);
    } else {
        $gridPosts = $posts;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="background: radial-gradient(circle at 50% 0%, var(--primary-glow) 0%, transparent 60%); padding: 4rem 1.5rem 2.5rem; text-align: center; border-bottom: 1px solid var(--border-glass);">
    <div class="container" style="max-width: 850px;">
        <span class="badge badge-pro" style="margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.5rem;">
            <span>💡</span> <?= current_lang() === 'ar' ? 'أفكار واستراتيجيات صناع المحتوى' : 'Creator Insights & Playbooks' ?>
        </span>
        <h1 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; line-height: 1.2; margin-bottom: 1rem;">
            <?= current_lang() === 'ar' ? 'مقالات وأدلة نمو صناع المحتوى' : 'Tactics, Blueprints & Creator Stories' ?>
        </h1>
        <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 650px; margin: 0 auto 2rem; line-height: 1.6;">
            <?= current_lang() === 'ar' 
                ? 'استراتيجيات واقعية مجربة في مضاعفة النقرات، وتسعير عقود الرعاية، وبناء مصادر دخل مستدامة بعيداً عن تقلبات الخوارزميات.'
                : 'Actionable frameworks, sponsorship negotiation teardowns, and bio conversion tactics to help you scale a sustainable, independent creator business.' ?>
        </p>

        <!-- Search Bar -->
        <form method="GET" action="<?= url('blog') ?>" style="max-width: 500px; margin: 0 auto; display: flex; gap: 0.5rem;">
            <input type="text" name="q" class="form-control" placeholder="<?= current_lang() === 'ar' ? 'ابحث في المقالات...' : 'Search articles, funnels, sponsorships...' ?>" value="<?= e($searchQuery) ?>" style="flex: 1; border-radius: 999px; padding: 0.75rem 1.25rem;">
            <button type="submit" class="btn btn-primary" style="border-radius: 999px; padding: 0 1.5rem;">
                🔍 <?= current_lang() === 'ar' ? 'بحث' : 'Search' ?>
            </button>
        </form>
    </div>
</div>

<main class="container" style="padding: 3rem 1.5rem 5rem;">
    <!-- Category Filter Pills -->
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 2.5rem; align-items: center; justify-content: flex-start;">
        <a href="<?= url('blog') ?>" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
            <?= current_lang() === 'ar' ? 'جميع المقالات' : 'All Articles' ?>
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= url('blog?category=' . urlencode($cat)) ?>" class="btn btn-sm <?= $selectedCategory === $cat ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                <?= e($cat) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Featured Post -->
    <?php if ($featuredPost): ?>
        <article class="card card-glow" style="margin-bottom: 3.5rem; padding: 0; overflow: hidden; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 0; align-items: center; border: 1px solid var(--border-glass);">
            <div style="position: relative; height: 100%; min-height: 280px; overflow: hidden;">
                <img src="<?= e($featuredPost['cover_image'] ?: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80') ?>" alt="<?= e($featuredPost['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;">
                <span class="badge badge-pro" style="position: absolute; top: 1rem; left: 1rem; background: rgba(10, 15, 29, 0.85); backdrop-filter: blur(8px);">
                    ⭐ <?= current_lang() === 'ar' ? 'مقال مميز' : 'Featured Blueprint' ?>
                </span>
            </div>
            <div style="padding: 2.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
                    <span style="color: var(--primary); font-weight: 700;"><?= e($featuredPost['category']) ?></span>
                    <span>•</span>
                    <span>⏱️ <?= e($featuredPost['read_time']) ?></span>
                    <span>•</span>
                    <span>👁️ <?= number_format($featuredPost['views_count']) ?> views</span>
                </div>
                <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.3;">
                    <a href="<?= url('blog/' . e($featuredPost['slug'])) ?>" style="color: inherit; text-decoration: none;">
                        <?= e($featuredPost['title']) ?>
                    </a>
                </h2>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <?= e($featuredPost['excerpt']) ?>
                </p>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <span style="font-size: 1.3rem;">✍️</span>
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem;"><?= e($featuredPost['author_name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= date('M j, Y', strtotime($featuredPost['created_at'])) ?></div>
                        </div>
                    </div>
                    <a href="<?= url('blog/' . e($featuredPost['slug'])) ?>" class="btn btn-sm btn-primary">
                        <?= current_lang() === 'ar' ? 'اقرأ الدليل الكامل ←' : 'Read Full Guide →' ?>
                    </a>
                </div>
            </div>
        </article>
    <?php endif; ?>

    <!-- Articles Grid -->
    <?php if (empty($posts)): ?>
        <div style="text-align: center; padding: 4rem 1rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
            <h3 style="font-size: 1.3rem; font-weight: 700;"><?= current_lang() === 'ar' ? 'لم يتم العثور على مقالات' : 'No articles found' ?></h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;"><?= current_lang() === 'ar' ? 'جرب البحث بكلمة مختلفة أو تصفح كل المقالات.' : 'Try adjusting your search terms or filter.' ?></p>
            <a href="<?= url('blog') ?>" class="btn btn-secondary"><?= current_lang() === 'ar' ? 'عرض كل المقالات' : 'View All Articles' ?></a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
            <?php foreach ($gridPosts as $post): ?>
                <article class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s;">
                    <a href="<?= url('blog/' . e($post['slug'])) ?>" style="display: block; position: relative; height: 190px; overflow: hidden;">
                        <img src="<?= e($post['cover_image'] ?: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=600&q=80') ?>" alt="<?= e($post['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <span class="badge badge-pro" style="position: absolute; top: 0.75rem; right: 0.75rem; font-size: 0.7rem;">
                            <?= e($post['category']) ?>
                        </span>
                        <?php if ($post['lang'] === 'ar'): ?>
                            <span class="badge" style="position: absolute; top: 0.75rem; left: 0.75rem; font-size: 0.7rem; background: rgba(0,0,0,0.7);">
                                🇸🇦 بالعربية
                            </span>
                        <?php endif; ?>
                    </a>
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; gap: 0.5rem; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                <span>⏱️ <?= e($post['read_time']) ?></span>
                                <span>•</span>
                                <span><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                            </div>
                            <h3 style="font-size: 1.15rem; font-weight: 700; line-height: 1.4; margin-bottom: 0.75rem;">
                                <a href="<?= url('blog/' . e($post['slug'])) ?>" style="color: inherit; text-decoration: none;">
                                    <?= e($post['title']) ?>
                                </a>
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                                <?= e(mb_strimwidth($post['excerpt'], 0, 130, '...')) ?>
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-glass); padding-top: 1rem;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">✍️ <?= e($post['author_name']) ?></span>
                            <a href="<?= url('blog/' . e($post['slug'])) ?>" style="color: var(--primary); font-weight: 700; font-size: 0.85rem; text-decoration: none;">
                                <?= current_lang() === 'ar' ? 'قراءة المزيد ←' : 'Read Article →' ?>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Newsletter / CTA Card -->
    <div class="card card-glow" style="margin-top: 5rem; padding: 3rem 2rem; text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);">
        <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.75rem;">
            <?= current_lang() === 'ar' ? 'جاهز لتحويل متابعيك إلى إيرادات حقيقية؟' : 'Ready to turn your followers into serious revenue?' ?>
        </h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto 1.5rem; line-height: 1.6;">
            <?= current_lang() === 'ar' 
                ? 'انضم إلى مجتمع صناع المحتوى واستمتع بصفحات بايو فاخرة، وحاسبة أسعار الرعايات، وروابط QR ذكية، واستوديو صور مصغرة مجاناً.'
                : 'Build high-converting bio micro-sites, design viral thumbnails, and calculate fair brand deals all inside iLoveDigital Studio.' ?>
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?= url('register') ?>" class="btn btn-lg btn-primary">
                <?= __('hero_cta_free', 'Start Creating for Free 🚀') ?>
            </a>
            <a href="<?= url('login?demo=1') ?>" class="btn btn-lg btn-secondary">
                <?= __('hero_cta_demo', '⚡ Instant 1-Click Demo') ?>
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
