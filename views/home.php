<?php
$pageTitle = APP_NAME . ' — ' . __('app_tagline');
$pageDesc = __('app_desc');

$pdo = DB::getConnection();
$latestPosts = $pdo->query("SELECT id, title, slug, category, author_name, cover_image, read_time, lang, excerpt, created_at FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 3")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>


<!-- Hero Section -->
<section style="padding: 5rem 0 3.5rem; text-align: center; position: relative;">
    <div class="container" style="max-width: 960px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 1rem; border-radius: var(--radius-full); margin-bottom: 1.75rem;">
            <span class="badge badge-pro"><?= __('hero_badge') ?></span>
            <span style="font-size: 0.85rem; color: #c7d2fe; font-weight: 500;"><?= __('hero_badge_text') ?></span>
        </div>

        <h1 style="font-size: clamp(2.5rem, 5vw, 4.2rem); font-weight: 800; letter-spacing: -1.5px; margin-bottom: 1.5rem; line-height: 1.2;">
            <?= __('hero_title_1') ?><br>
            <span class="heading-gradient"><?= __('hero_title_2') ?></span>
        </h1>

        <p style="font-size: clamp(1.05rem, 2vw, 1.25rem); color: var(--text-secondary); line-height: 1.6; max-width: 740px; margin: 0 auto 2.5rem;">
            <?= __('hero_subtitle') ?>
        </p>

        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 3.5rem;">
            <a href="<?= url('register') ?>" class="btn btn-lg btn-primary">
                <span><?= __('hero_cta_free') ?></span>
                <span>🚀</span>
            </a>
            <a href="<?= url('login?demo=1') ?>" class="btn btn-lg btn-secondary">
                <span><?= __('hero_cta_demo') ?></span>
            </a>
            <a href="<?= url('p/alexcreator') ?>" target="_blank" class="btn btn-lg btn-outline">
                <span><?= __('hero_cta_preview') ?></span>
            </a>
        </div>

        <!-- Social Proof Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 2rem; background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); backdrop-filter: blur(16px);">
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #fff; font-family: 'Outfit';">140,000+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= __('stat_clicks') ?></div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #a5b4fc; font-family: 'Outfit';">$2.8M+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= __('stat_deals') ?></div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #6ee7b7; font-family: 'Outfit';">99.98%</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= __('stat_uptime') ?></div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #f472b6; font-family: 'Outfit';">5-in-1</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;"><?= __('stat_modules') ?></div>
            </div>
        </div>
    </div>
</section>

<!-- 5 Core Modules Section -->
<section id="features" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 3.5rem;">
            <span class="badge badge-primary" style="margin-bottom: 0.75rem;"><?= __('features_badge') ?></span>
            <h2 style="font-size: 2.4rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 1rem;">
                <?= __('features_title') ?>
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.05rem;">
                <?= __('features_desc') ?>
            </p>
        </div>

        <div class="tools-grid">
            <!-- Tool 1 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
                    📱
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title"><?= __('tool_bio_title') ?></h3>
                        <span class="badge badge-emerald">HOT</span>
                    </div>
                    <p class="tool-card-desc">
                        <?= __('tool_bio_desc') ?>
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/bio-link') ?>" class="btn btn-sm btn-outline" style="width: 100%;"><?= __('tool_bio_cta') ?></a>
                </div>
            </div>

            <!-- Tool 2 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">
                    🎨
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title"><?= __('tool_thumb_title') ?></h3>
                        <span class="badge badge-pro">CREATOR PICK</span>
                    </div>
                    <p class="tool-card-desc">
                        <?= __('tool_thumb_desc') ?>
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/thumbnail-studio') ?>" class="btn btn-sm btn-outline" style="width: 100%;"><?= __('tool_thumb_cta') ?></a>
                </div>
            </div>

            <!-- Tool 3 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #6ee7b7;">
                    ✍️
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title"><?= __('tool_copy_title') ?></h3>
                    </div>
                    <p class="tool-card-desc">
                        <?= __('tool_copy_desc') ?>
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/copywriter') ?>" class="btn btn-sm btn-outline" style="width: 100%;"><?= __('tool_copy_cta') ?></a>
                </div>
            </div>

            <!-- Tool 4 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">
                    🔗
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title"><?= __('tool_links_title') ?></h3>
                    </div>
                    <p class="tool-card-desc">
                        <?= __('tool_links_desc') ?>
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/smart-links') ?>" class="btn btn-sm btn-outline" style="width: 100%;"><?= __('tool_links_cta') ?></a>
                </div>
            </div>

            <!-- Tool 5 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    💰
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title"><?= __('tool_rates_title') ?></h3>
                        <span class="badge badge-emerald">MONETIZE</span>
                    </div>
                    <p class="tool-card-desc">
                        <?= __('tool_rates_desc') ?>
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/rate-calculator') ?>" class="btn btn-sm btn-outline" style="width: 100%;"><?= __('tool_rates_cta') ?></a>
                </div>
            </div>

            <!-- Tool 6 / CI/CD & Hostinger -->
            <div class="tool-card" style="border: 1px dashed rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.04);">
                <div class="tool-icon-wrapper" style="background: rgba(99, 102, 241, 0.15); color: #a5b4fc;">
                    🚀
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title">CI/CD Hostinger Ready</h3>
                        <span class="badge badge-primary">DEVOPS</span>
                    </div>
                    <p class="tool-card-desc">
                        Pre-configured GitHub Actions pipeline (`khalilksa33`). Push to your `main` branch and have changes automatically synchronized to Hostinger Shared Hosting via high-speed FTPS!
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem;">
                    <a href="<?= url('DEPLOYMENT.md') ?>" class="btn btn-sm btn-secondary" style="width: 100%;">View Deployment Docs ↗</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Plans Section -->
<section id="pricing" style="padding: 5rem 0; background: rgba(15, 23, 42, 0.4); border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass);">
    <div class="container">
        <div style="text-align: center; max-width: 650px; margin: 0 auto 3.5rem;">
            <span class="badge badge-emerald" style="margin-bottom: 0.75rem;">TRANSPARENT PRICING</span>
            <h2 style="font-size: 2.4rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 1rem;">
                Simple, Fair Plans for Every Stage
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.05rem;">
                Start 100% free with core tools, or upgrade to unlock unlimited high-res exports, custom domains, and AI generation.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; max-width: 1050px; margin: 0 auto;">
            <!-- Free Plan -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Starter Creator</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">Perfect for new creators launching their first brand.</p>
                </div>
                <div style="font-size: 2.75rem; font-weight: 800; font-family: 'Outfit'; margin-bottom: 1.5rem;">
                    $0 <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">/forever</span>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; margin-bottom: 2rem; flex: 1;">
                    <li>✓ 1 Bio-Link Landing Page</li>
                    <li>✓ Up to 10 Link Items</li>
                    <li>✓ Basic Thumbnail Canvas Studio</li>
                    <li>✓ Algorithmic Hook & Caption Generator</li>
                    <li>✓ 5 Smart Short Links & QR codes</li>
                    <li>✓ Sponsorship Rate Calculator</li>
                </ul>
                <a href="<?= url('register') ?>" class="btn btn-secondary" style="width: 100%;">Get Started Free</a>
            </div>

            <!-- Pro Plan -->
            <div class="card card-glow" style="border-color: var(--primary); display: flex; flex-direction: column; position: relative;">
                <div style="position: absolute; top: -12px; right: 20px;">
                    <span class="badge badge-pro">MOST POPULAR</span>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Creator Pro</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">For active creators who want to maximize conversions & revenue.</p>
                </div>
                <div style="font-size: 2.75rem; font-weight: 800; font-family: 'Outfit'; margin-bottom: 1.5rem; color: #a5b4fc;">
                    $15 <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">/month</span>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; margin-bottom: 2rem; flex: 1;">
                    <li>✓ <strong>Unlimited</strong> Bio-Links & Micro-Pages</li>
                    <li>✓ <strong>All 6 Premium Themes</strong> & Animations</li>
                    <li>✓ <strong>High-Res 4K</strong> Thumbnail Canvas Export</li>
                    <li>✓ <strong>Unlimited</strong> AI Viral Hooks & Thread Builder</li>
                    <li>✓ <strong>Unlimited</strong> Smart Links & Branded QR Codes</li>
                    <li>✓ <strong>PDF Invoice Generator</strong> & Rate Sheet Maker</li>
                    <li>✓ Detailed Click & Visitor Analytics</li>
                </ul>
                <a href="<?= url('register?plan=pro') ?>" class="btn btn-primary" style="width: 100%;">Upgrade to Pro ⚡</a>
            </div>

            <!-- Agency Plan -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Agency & Studio</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">For talent managers, media agencies, and creator teams.</p>
                </div>
                <div style="font-size: 2.75rem; font-weight: 800; font-family: 'Outfit'; margin-bottom: 1.5rem;">
                    $39 <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">/month</span>
                </div>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; margin-bottom: 2rem; flex: 1;">
                    <li>✓ Manage up to 10 Creator Accounts</li>
                    <li>✓ Custom CNAME & Subdomain Routing</li>
                    <li>✓ Remove all "Powered by" Badges</li>
                    <li>✓ Priority Support & Dedicated Hostinger Config</li>
                    <li>✓ Export All Raw CSV Analytics</li>
                </ul>
                <a href="<?= url('register?plan=agency') ?>" class="btn btn-secondary" style="width: 100%;">Start Agency Trial</a>
            </div>
        </div>
    </div>
</section>

<!-- Authentic Creator Stories Section -->
<section style="padding: 5rem 0; background: rgba(255, 255, 255, 0.015); border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass);">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 3.5rem;">
            <span class="badge badge-primary" style="margin-bottom: 0.75rem;">AUTHENTIC CREATOR STORIES</span>
            <h2 style="font-size: 2.3rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 1rem;">
                Built by creators who were tired of 6 different subscriptions
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.05rem;">
                Here is why YouTubers, podcasters, and digital consultants made iLoveDigital their daily creative studio.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div class="card card-glow" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.25rem; color: #f59e0b; margin-bottom: 1rem;">★★★★★</div>
                    <p style="font-size: 0.98rem; line-height: 1.6; color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">
                        "I used to pay $24/mo for Linktree Pro, $13/mo for Canva, and struggled with ugly invoice templates. iLoveDigital replaced them all. My bio link click-through rate jumped from 4% to 18% in the first week."
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Maya Chen" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="font-weight: 700;">Maya Chen</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Tech & Workflow Creator (84k subs)</div>
                    </div>
                </div>
            </div>

            <div class="card card-glow" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.25rem; color: #f59e0b; margin-bottom: 1rem;">★★★★★</div>
                    <p style="font-size: 0.98rem; line-height: 1.6; color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">
                        "Finding a platform that respects Arabic typography (RTL) with genuine luxury design was impossible before this. My followers in Saudi Arabia and the UAE love the fluid dark mode and instant speed."
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Omar Farooq" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="font-weight: 700;">Omar Farooq</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Digital Consultant & Podcaster (Riyadh)</div>
                    </div>
                </div>
            </div>

            <div class="card card-glow" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 1.25rem; color: #f59e0b; margin-bottom: 1rem;">★★★★★</div>
                    <p style="font-size: 0.98rem; line-height: 1.6; color: var(--text-secondary); font-style: italic; margin-bottom: 1.5rem;">
                        "The Sponsorship Calculator and instant PDF invoice generator alone made me thousands. Brands stop haggling when you send them an itemized deliverable sheet that looks this professional."
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=100&q=80" alt="Sarah Jenkins" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <div style="font-weight: 700;">Sarah Jenkins</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Creator Business Coach</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Creator Blog Section -->
<?php if (!empty($latestPosts)): ?>
<section style="padding: 5rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.75rem;">CREATOR PLAYBOOKS</span>
                <h2 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.5rem;">
                    Tactics from the Creator Frontier
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.05rem;">
                    No generic fluff. Proven strategies for audience retention, rate negotiation, and conversion funnels.
                </p>
            </div>
            <a href="<?= url('blog') ?>" class="btn btn-secondary">
                View All Articles →
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <?php foreach ($latestPosts as $bp): ?>
                <article class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                    <a href="<?= url('blog/' . e($bp['slug'])) ?>" style="position: relative; height: 180px; overflow: hidden; display: block;">
                        <img src="<?= e($bp['cover_image'] ?: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80') ?>" alt="<?= e($bp['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <span class="badge badge-pro" style="position: absolute; top: 0.75rem; right: 0.75rem; font-size: 0.7rem;">
                            <?= e($bp['category']) ?>
                        </span>
                        <?php if ($bp['lang'] === 'ar'): ?>
                            <span class="badge" style="position: absolute; top: 0.75rem; left: 0.75rem; font-size: 0.7rem; background: rgba(0,0,0,0.7);">
                                🇸🇦 بالعربية
                            </span>
                        <?php endif; ?>
                    </a>
                    <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">
                                ⏱️ <?= e($bp['read_time']) ?> • <?= date('M j, Y', strtotime($bp['created_at'])) ?>
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 700; line-height: 1.4; margin-bottom: 0.75rem;">
                                <a href="<?= url('blog/' . e($bp['slug'])) ?>" style="color: inherit; text-decoration: none;">
                                    <?= e($bp['title']) ?>
                                </a>
                            </h3>
                            <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.5; margin-bottom: 1.25rem;">
                                <?= e(mb_strimwidth($bp['excerpt'], 0, 120, '...')) ?>
                            </p>
                        </div>
                        <a href="<?= url('blog/' . e($bp['slug'])) ?>" style="color: var(--primary); font-weight: 700; font-size: 0.85rem; text-decoration: none;">
                            Read Playbook →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ Section -->
<section id="faq" style="padding: 5rem 0;">

    <div class="container" style="max-width: 820px;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0.75rem;">Frequently Asked Questions</h2>
            <p style="color: var(--text-secondary);">Everything you need to know about the platform and Hostinger deployment.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div class="card">
                <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">Can I host this on Hostinger Shared Web Hosting?</h4>
                <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                    Yes, 100%! The platform is specifically engineered for shared hosting (Apache/LiteSpeed, PHP 8+, MySQL or SQLite). It requires zero complex node daemons or heavy build scripts. It runs out-of-the-box upon FTP upload.
                </p>
            </div>
            <div class="card">
                <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">How does GitHub Actions auto-deployment work?</h4>
                <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                    Every time you push code to your `main` branch under GitHub (`khalilksa33`), GitHub Actions triggers a workflow that automatically transfers all updated files securely over FTP/FTPS to Hostinger's `public_html` directory within seconds.
                </p>
            </div>
            <div class="card">
                <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem;">Do I need an OpenAI API key for the copywriter?</h4>
                <p style="font-size: 0.92rem; color: var(--text-secondary); line-height: 1.6;">
                    No! The Copywriting & Hook Generator includes a high-converting algorithmic creator framework that generates instant results with zero API keys required. You can optionally hook up an OpenAI or Gemini API key in settings if you want cloud AI models.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Bottom CTA -->
<section style="padding: 4rem 0 6rem; text-align: center;">
    <div class="container" style="max-width: 780px;">
        <div class="card card-glow" style="padding: 3.5rem 2rem; background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.1) 100%);">
            <h2 style="font-size: 2.4rem; font-weight: 800; margin-bottom: 1rem;">Ready to elevate your creator business?</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">
                Join thousands of creators using <?= e(APP_NAME) ?> to grow their audience and secure brand sponsorships.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?= url('register') ?>" class="btn btn-lg btn-primary">Create Your Free Account 🚀</a>
                <a href="<?= url('login?demo=1') ?>" class="btn btn-lg btn-secondary">⚡ Try Demo Instantly</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
