<?php
$pageTitle = APP_NAME . ' — All-in-One Growth & Content Studio for Digital Creators';
$pageDesc = 'Empower your digital creator business with bio-link pages, social thumbnail canvas studio, viral copywriting tools, branded QR short links, and sponsorship pricing calculators.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section style="padding: 5rem 0 3.5rem; text-align: center; position: relative;">
    <div class="container" style="max-width: 960px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); padding: 0.35rem 1rem; border-radius: var(--radius-full); margin-bottom: 1.75rem;">
            <span class="badge badge-pro">NEW 2026</span>
            <span style="font-size: 0.85rem; color: #c7d2fe; font-weight: 500;">The All-in-One Creator Growth Suite is here</span>
        </div>

        <h1 style="font-size: clamp(2.5rem, 5vw, 4.2rem); font-weight: 800; letter-spacing: -1.5px; margin-bottom: 1.5rem; line-height: 1.1;">
            Stop juggling 5 different apps.<br>
            <span class="heading-gradient">One Studio. Pure Creator Power.</span>
        </h1>

        <p style="font-size: clamp(1.05rem, 2vw, 1.25rem); color: var(--text-secondary); line-height: 1.6; max-width: 740px; margin: 0 auto 2.5rem;">
            Supercharge your monetization and content workflow. Build high-converting bio-link micro-sites, design viral social thumbnails, generate killer video hooks, track branded QR links, and close brand sponsorships—all from a single, unified studio.
        </p>

        <div style="display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 3.5rem;">
            <a href="<?= url('register') ?>" class="btn btn-lg btn-primary">
                <span>Start Creating for Free</span>
                <span>🚀</span>
            </a>
            <a href="<?= url('login?demo=1') ?>" class="btn btn-lg btn-secondary">
                <span>⚡ Instant 1-Click Demo</span>
            </a>
            <a href="<?= url('p/alexcreator') ?>" target="_blank" class="btn btn-lg btn-outline">
                <span>👁️ View Live Bio Demo</span>
            </a>
        </div>

        <!-- Social Proof Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 2rem; background: var(--bg-card); border: 1px solid var(--border-glass); border-radius: var(--radius-xl); backdrop-filter: blur(16px);">
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #fff; font-family: 'Outfit';">140,000+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">LINKS CLICKED DAILY</div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #a5b4fc; font-family: 'Outfit';">$2.8M+</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">BRAND DEALS CALCULATED</div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #6ee7b7; font-family: 'Outfit';">99.98%</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">UPTIME ON HOSTINGER</div>
            </div>
            <div>
                <div style="font-size: 2.2rem; font-weight: 800; color: #f472b6; font-family: 'Outfit';">5-in-1</div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">UNIFIED CREATOR MODULES</div>
            </div>
        </div>
    </div>
</section>

<!-- 5 Core Modules Section -->
<section id="features" style="padding: 5rem 0;">
    <div class="container">
        <div style="text-align: center; max-width: 680px; margin: 0 auto 3.5rem;">
            <span class="badge badge-primary" style="margin-bottom: 0.75rem;">POWERFUL MODULES</span>
            <h2 style="font-size: 2.4rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 1rem;">
                Engineered for the Modern Digital Creator
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.05rem;">
                Everything you need to grow your audience, produce viral assets, and monetize your influence with zero technical headaches.
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
                        <h3 class="tool-card-title">Bio-Link & Landing Pages</h3>
                        <span class="badge badge-emerald">HOT</span>
                    </div>
                    <p class="tool-card-desc">
                        Replace boring linktrees with gorgeous Bento-style mobile landing pages. Customize 6 dark & neon themes, animated CTA buttons, social docks, and custom domains.
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/bio-link') ?>" class="btn btn-sm btn-outline" style="width: 100%;">Launch Bio Builder →</a>
                </div>
            </div>

            <!-- Tool 2 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(236, 72, 153, 0.15); color: #f472b6;">
                    🎨
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title">Thumbnail & Canvas Studio</h3>
                        <span class="badge badge-pro">CREATOR PICK</span>
                    </div>
                    <p class="tool-card-desc">
                        Create high-CTR YouTube thumbnails, Instagram posts, and TikTok banners right inside your browser. Drag-and-drop layers, creator font presets, glowing borders, and badges.
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/thumbnail-studio') ?>" class="btn btn-sm btn-outline" style="width: 100%;">Open Canvas Studio →</a>
                </div>
            </div>

            <!-- Tool 3 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #6ee7b7;">
                    ✍️
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title">Viral Hooks & Caption AI</h3>
                    </div>
                    <p class="tool-card-desc">
                        Never stare at a blank screen again. Generate high-retention video opening hooks, YouTube title A/B ideas, timestamp descriptions, viral Twitter threads, and multi-tier hashtags.
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/copywriter') ?>" class="btn btn-sm btn-outline" style="width: 100%;">Generate Hooks →</a>
                </div>
            </div>

            <!-- Tool 4 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(6, 182, 212, 0.15); color: #22d3ee;">
                    🔗
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title">Smart Links & Branded QR</h3>
                    </div>
                    <p class="tool-card-desc">
                        Shorten links with clean custom aliases, auto-attach UTM tracking tags for sponsors, and generate custom-colored QR codes ready for merch, print, and stream overlays.
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/smart-links') ?>" class="btn btn-sm btn-outline" style="width: 100%;">Create Smart Link →</a>
                </div>
            </div>

            <!-- Tool 5 -->
            <div class="tool-card card-glow">
                <div class="tool-icon-wrapper" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                    💰
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                        <h3 class="tool-card-title">Rates & Brand Invoicing</h3>
                        <span class="badge badge-emerald">MONETIZE</span>
                    </div>
                    <p class="tool-card-desc">
                        Know your worth. Calculate fair market sponsorship rates based on views, niche multipliers, and deliverable types. Generate and download sleek, professional PDF invoices in seconds.
                    </p>
                </div>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-glass);">
                    <a href="<?= url('tools/rate-calculator') ?>" class="btn btn-sm btn-outline" style="width: 100%;">Calculate Deal Rate →</a>
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
                Join thousands of creators using CreatorPulse to grow their audience and secure brand sponsorships.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?= url('register') ?>" class="btn btn-lg btn-primary">Create Your Free Account 🚀</a>
                <a href="<?= url('login?demo=1') ?>" class="btn btn-lg btn-secondary">⚡ Try Demo Instantly</a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
