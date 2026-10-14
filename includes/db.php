<?php
/**
 * Database Handler & Auto-Migration
 */
require_once __DIR__ . '/../config/config.php';

class DB {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                if (DB_CONNECTION === 'sqlite') {
                    $dbPath = DB_DATABASE;
                    $dbDir = dirname($dbPath);
                    if (!is_dir($dbDir)) {
                        @mkdir($dbDir, 0755, true);
                    }
                    $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;

                    self::$instance = new PDO("sqlite:" . $dbPath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$instance->exec("PRAGMA foreign_keys = ON;");

                    if ($isNew) {
                        self::initializeSchema(self::$instance);
                    }
                } else {
                    // MySQL / Hostinger MariaDB
                    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=utf8mb4";
                    self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);

                    // Check if tables exist, if not initialize
                    $stmt = self::$instance->query("SHOW TABLES LIKE 'users'");
                    if ($stmt->rowCount() === 0) {
                        self::initializeSchema(self::$instance);
                    }
                }

                // Run automatic schema updates and seed blog posts if needed
                self::ensureSchemaUpdates(self::$instance);
            } catch (PDOException $e) {
                die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }


    private static function initializeSchema(PDO $pdo): void {
        $schemaFile = __DIR__ . '/../config/schema.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            
            // If MySQL, adjust AUTOINCREMENT to AUTO_INCREMENT
            if (DB_CONNECTION === 'mysql') {
                $sql = str_replace('AUTOINCREMENT', 'AUTO_INCREMENT', $sql);
            }

            // Split and run statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // ignore if table exists
                    }
                }
            }

            // Seed Demo User & Sample Content
            self::seedDemoData($pdo);
        }
    }

    private static function seedDemoData(PDO $pdo): void {
        try {
            $check = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            if ($check == 0) {
                $demoPassword = password_hash('demo123', PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password_hash, plan, avatar_url, bio) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    'Alex Rivera',
                    'alexcreator',
                    'demo@creatorpulse.com',
                    $demoPassword,
                    'pro',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                    'Digital Creator & Visual Storyteller. Sharing tech insights, creator workflows, and creative assets.'
                ]);
                $userId = $pdo->lastInsertId();

                // Seed Bio Profile
                $socials = json_encode([
                    'youtube' => 'https://youtube.com',
                    'instagram' => 'https://instagram.com',
                    'tiktok' => 'https://tiktok.com',
                    'twitter' => 'https://x.com',
                    'spotify' => 'https://spotify.com'
                ]);

                $profileStmt = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links, is_published, views_count)
                                              VALUES (?, ?, ?, ?, ?, ?, 1, 1420)");
                $profileStmt->execute([
                    $userId,
                    'midnight',
                    'Alex Rivera ⚡',
                    'Building in public • Free Lightroom Presets & Creator Assets below 👇',
                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80',
                    $socials
                ]);
                $profileId = $pdo->lastInsertId();

                // Seed Bio Links
                $links = [
                    ['🎥 Latest YouTube: How I Grew to 100k in 6 Months', 'https://youtube.com', 'video', '🔥 HOT', 'pulse', 684, 1],
                    ['🎨 Free Cinematic Lightroom & LUTs Pack', 'https://gumroad.com', 'gift', 'FREE', 'none', 945, 2],
                    ['🎙️ Creative Pulse Podcast (Episode #42 Out Now)', 'https://spotify.com', 'headphones', 'NEW', 'shake', 312, 3],
                    ['💻 My 2026 Camera & Studio Gear Checklist', 'https://kit.co', 'camera', '', 'none', 520, 4],
                    ['📬 Join The 10k Weekly Creator Newsletter', 'https://substack.com', 'mail', '', 'none', 418, 5]
                ];

                $linkStmt = $pdo->prepare("INSERT INTO bio_links (profile_id, title, url, icon, badge, animation, clicks_count, sort_order)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                foreach ($links as $link) {
                    $linkStmt->execute([
                        $profileId, $link[0], $link[1], $link[2], $link[3], $link[4], $link[5], $link[6]
                    ]);
                }

                // Seed Smart Link
                $shortStmt = $pdo->prepare("INSERT INTO short_links (user_id, code, target_url, title, clicks_count, utm_source, utm_medium, utm_campaign)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $shortStmt->execute([
                    $userId,
                    'lutpack26',
                    'https://gumroad.com/l/cinematic-pack',
                    'Cinematic LUTs Pack 2026',
                    389,
                    'youtube',
                    'video_description',
                    'spring_launch'
                ]);
            }
        } catch (Exception $e) {
            // Keep going if seed encounters issue
        }
    }

    public static function ensureSchemaUpdates(PDO $pdo): void {
        static $migrated = false;
        if ($migrated) return;
        $migrated = true;

        // 1. Ensure role and google_id columns exist on users
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
        } catch (Exception $e) {}

        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(100) DEFAULT NULL");
        } catch (Exception $e) {}

        // 2. Ensure blog_posts table exists
        $autoInc = (DB_CONNECTION === 'mysql') ? 'AUTO_INCREMENT' : 'AUTOINCREMENT';
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
                id INTEGER PRIMARY KEY $autoInc,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                excerpt TEXT,
                content TEXT NOT NULL,
                cover_image VARCHAR(500) DEFAULT '',
                author_name VARCHAR(100) DEFAULT 'iLoveDigital Team',
                category VARCHAR(50) DEFAULT 'Growth',
                tags VARCHAR(255) DEFAULT '',
                read_time VARCHAR(20) DEFAULT '5 min read',
                lang VARCHAR(10) DEFAULT 'en',
                status VARCHAR(20) DEFAULT 'published',
                views_count INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } catch (Exception $e) {}

        // 3. Promote superadmin users
        try {
            $pdo->exec("UPDATE users SET role = 'superadmin' WHERE email = 'khalilksa33@gmail.com' OR username = 'alexcreator'");
        } catch (Exception $e) {}

        // 4. Seed blog posts if table is empty
        self::seedBlogPosts($pdo);
    }

    private static function seedBlogPosts(PDO $pdo): void {
        try {
            $check = $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
            if ($check > 0) {
                return;
            }

            $posts = [
                [
                    'title' => 'The 2026 Digital Creator Blueprint: How We Turned 1,000 True Fans into a $12k/Month Machine',
                    'slug' => '2026-digital-creator-blueprint',
                    'category' => 'Monetization',
                    'author_name' => 'Alex Rivera',
                    'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=1200&q=80',
                    'tags' => 'Creator Economy, Funnels, Bio Links, Revenue',
                    'read_time' => '6 min read',
                    'lang' => 'en',
                    'status' => 'published',
                    'views_count' => 1840,
                    'excerpt' => 'Most creators believe you need 100,000 followers to make a full-time living. Here is the exact math, psychology, and funnel we used to build a sustainable $12k/month studio with under 2,000 engaged subscribers.',
                    'content' => "## The Myth of the 100k Follower Requirement

For years, the creator ecosystem peddled a dangerous fantasy: *post daily, pray to the YouTube or TikTok algorithm gods, hope for 500,000 views, and retire on AdSense.*

If you have tried that path, you already know the brutal reality:
1. **AdSense pays peanuts** for 90% of entertainment and lifestyle niches ($1.50 to $4.00 RPM).
2. **Platform algorithms shift overnight**, leaving creators burned out and bewildered.
3. **You are building on rented land.** You don't own your followers on Instagram, TikTok, or YouTube—the platform does.

Back in 2024, our studio pivoted to Kevin Kelly's timeless philosophy of **1,000 True Fans**. Instead of chasing passive eyeballs, we engineered a high-converting micro-ecosystem centered around three key pillars:

---

### Pillar 1: The 3-Second Bio Microsite Rule
Your bio link is the **single most valuable piece of digital real estate** you own. Traditional link aggregator trees fail because they drop 15 identical, low-contrast buttons on the screen. Visitors experience instant decision paralysis and bounce in under 2 seconds.

Here is what we changed:
- **One Hero Offer (70% visual focus):** We put our flagship resource—a free cinematic Lightroom preset pack—right at the top with a subtle pulsing glow badge.
- **Micro-Social Proof:** Instead of just social icons, we linked our latest YouTube video with dynamic click counts.
- **Direct Engagement:** A 1-click WhatsApp/Telegram booking link for VIP consulting.

The result? Our click-through conversion jumped from **4.2% to 19.8%** in the first 30 days.

---

### Pillar 2: The Direct Sponsorship Pricing Formula
Brands don't pay for vanity follower counts; they pay for **audience attention and trust**. If you wait for brands to email you, you're competing at the bottom. 

We switched to pitch bundling:
- Instead of: *\"I'll do a 60-second video integration for $500\"*
- We pitched: *\"Dedicated 90-second integration + 3 high-contrast social thumbnails + permanent smart link in description with UTM tracking + 30-day paid usage rights for $2,800.\"*

By delivering clean, professional PDF invoices immediately and bundling assets that save brand marketing teams days of work, our average deal size tripled.

---

### Pillar 3: Own Your Smart Links & QR Traffic
Whenever we publish a video or share merchandise, every single URL is routed through a branded Smart Link with UTM parameters (`utm_source`, `utm_medium`, `utm_campaign`). 

We discovered that **73% of our actual sales** were coming from description links on our older evergreen tutorial videos, not our newest viral shorts. Without custom smart link analytics, we would have kept pouring energy into the wrong content.

---

### The Takeaway for Your Studio
You don't need a million followers. You need:
1. A stunning, high-converting public bio page that visitors remember.
2. Branded smart links that track where your money actually comes from.
3. Professional rate calculation and invoicing so brands treat you like an agency, not a hobbyist.

Everything you need to run this entire system is built directly into your **iLoveDigital Studio**."
                ],
                [
                    'title' => 'Why Traditional Linktrees Are Costing You 40% of Your Affiliate Sales (And What to Do Instead)',
                    'slug' => 'why-traditional-linktrees-are-costing-you-affiliate-sales',
                    'category' => 'Growth',
                    'author_name' => 'Maya Chen',
                    'cover_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
                    'tags' => 'Conversion Rate, Bio Links, UX Design, Psychology',
                    'read_time' => '5 min read',
                    'lang' => 'en',
                    'status' => 'published',
                    'views_count' => 1420,
                    'excerpt' => 'If you simply drop 15 plain text links on a generic link page, your visitors freeze from decision paralysis. Here is the psychological anatomy of modern bio micro-sites that 3x your click-through rates.',
                    'content' => "## The Hidden Leak in Your Creator Funnel

You spend 20 hours filming, editing, color grading, and crafting a killer YouTube video or TikTok Reel. In the final call-to-action, you say: *\"Check the link in my bio!\"*

The viewer clicks. And what do they see?

A dull vertical stack of 18 identical gray rectangles with tiny generic text:
- *My Store*
- *Amazon Storefront*
- *My Twitter*
- *My Podcast*
- *Latest Video*
- *Contact Me*
- *Random Affiliate Link 1*
- *Random Affiliate Link 2*

What happens next? **Hick's Law** kicks in. Cognitive load skyrockets. The user hesitates for 1.8 seconds, presses the back button, and returns to doom-scrolling. You just lost an affiliate sale, a newsletter subscriber, or a new client.

---

### The Psychological Anatomy of a 20%+ Converting Bio Page

Modern digital creators treat their bio page not as a bookmark list, but as a **mobile micro-landing page**. Here are the four principles we implemented in the iLoveDigital Bio Builder:

#### 1. Visual Hierarchy & The \"Hero Card\"
Your most important offer must look fundamentally different from everything else. Give it a distinct gradient border, a custom highlight badge (like `🔥 HOT` or `⚡ FREE`), and an attention-grabbing micro-animation (like `pulse`). When the eye lands on the page, the user should intuitively know what action to take first.

#### 2. Embedded Media Previews
Instead of making users guess what a link is, show them. Modern bio microsites allow you to preview YouTube videos, Spotify audio tracks, and digital products right in the viewer's palm. 

#### 3. Custom Theming That Matches Your Brand Identity
Generic link-in-bio services look like everyone else. When visitors transition from your Instagram aesthetic or YouTube channel to your bio page, the visual language should feel seamless. With iLoveDigital's 6 luxury themes—from dark *Midnight* to vibrant *Neon Cyberpunk* and crisp *Snow*—your bio link feels like a $10,000 custom-coded website.

#### 4. Real-Time Click Attribution
If you don't know which link is generating revenue, you are flying blind. With per-link click tracking, you can immediately prune underperforming links and double down on what resonates.

---

### Action Checklist: Audit Your Bio Page Today
1. **Reduce your links:** Cut anything you haven't mentioned in the last 30 days. Aim for 4 to 6 focused links.
2. **Assign badges:** Add clear context (e.g., `⚡ 50% OFF`, `🎧 LISTEN`, `📥 DOWNLOAD`).
3. **Test mobile load speed:** A bloated link tool that takes 3 seconds to load will lose 50% of mobile users on cellular data.

Upgrade your bio page in your iLoveDigital Bio Builder in under 3 minutes."
                ],
                [
                    'title' => 'The Exact Pitch Email That Closed a $3,500 Brand Sponsorship (With Contract & Rate Formulas)',
                    'slug' => 'exact-pitch-email-closed-3500-brand-sponsorship',
                    'category' => 'Sponsorships',
                    'author_name' => 'Alex Rivera',
                    'cover_image' => 'https://images.unsplash.com/photo-1553877522-43269d4ea984?auto=format&fit=crop&w=1200&q=80',
                    'tags' => 'Brand Deals, Pitching, Pricing Calculator, Invoicing',
                    'read_time' => '7 min read',
                    'lang' => 'en',
                    'status' => 'published',
                    'views_count' => 2190,
                    'excerpt' => 'Stop sending generic \"Hey I love your brand, let\'s collaborate\" DMs that get left on read. Here is the authentic value-first email template, follow-up cadence, and rate calculation formulas we use.',
                    'content' => "## Why 98% of Creator Pitch Emails End Up in the Trash

Marketing directors at tech and lifestyle brands receive dozens of influencer pitches every single day. The vast majority read like this:

> *\"Hey [Brand Name], I love your products so much! I have 25,000 followers on Instagram and my engagement is amazing. Can we collaborate? Let me know what you think!\"*

Why does this fail?
- It focuses entirely on the creator, not the brand's business objectives.
- It provides zero demographic data or proof of conversion.
- It puts the burden of coming up with a creative concept on the brand.

---

### The Anatomy of the $3,500 Pitch

Here is the exact framework we used to close a high-paying multi-platform sponsorship with a SaaS productivity app:

```text
Subject: Creative concept for [Brand Name] + [Your Channel Name] (84k tech-focused creators)

Hi [Marketing Manager Name],

I noticed [Brand Name] recently launched your new collaboration feature—congratulations! As someone who builds daily digital workflows, I’ve been using it with my team for the past two weeks.

My audience consists of 84,000+ indie hackers, YouTubers, and freelancers (74% based in US/UK/Canada, aged 22-38 with high commercial intent). 

I've put together a specific 3-part integration concept that demonstrates how your new feature solves remote client handoff:
1. A dedicated 90-second workflow breakdown in next Tuesday’s video: \"How We Manage 5 Client Projects Without Chaos.\"
2. A permanent tracked Smart Link in the description with custom UTM parameters.
3. 3 high-contrast YouTube thumbnail variations featuring your UI for maximum CTR.
4. 30-day paid usage rights so your team can repurpose our integration clip for Meta & TikTok ads.

Based on our recent sponsorship CPM benchmarks, the complete package is $3,500 net-15.

Are you open to a brief 10-minute preview of the storyboard this Thursday?

Best regards,
Alex Rivera
Creator & Producer, iLoveDigital
[Link to Media Kit & Bio Page]
```

---

### The Fair Market Rate Calculation Formula
How do you know what to charge without leaving thousands on the table? In the **iLoveDigital Rate Calculator**, we use a proven 4-variable equation:

$$\text{Base Sponsorship Rate} = (\text{Avg Views} \times \frac{\text{Niche CPM}}{1000}) + \text{Production Fee} + \text{Usage Rights}$$

- **Niche CPM Factor:** Tech, Finance, and B2B SaaS command \$30–\$65 CPMs, while Entertainment and Gaming sit at \$8–\$18 CPMs.
- **Production Fee:** Never give away video production, lighting, and scriptwriting for free (always add \$300–\$800).
- **Ad Usage Rights:** If the brand wants to run your face as a paid Instagram ad, charge an extra 30% to 50% on top of the base fee.

---

### Closing the Deal with Professional Invoicing
Once the brand agrees, don't send a casual PayPal address. Send a clean, itemized invoice with an invoice number, deliverable breakdown, payment terms (Net-15), and wire details.

Use the built-in **iLoveDigital Sponsorship Calculator & Invoices** module to generate professional PDF invoices in under 60 seconds."
                ],
                [
                    'title' => 'دليل صناع المحتوى لعام 2026: كيف تبني مصادر دخل حقيقية ومستدامة من جمهورك الرقمي',
                    'slug' => 'creator-monetization-blueprint-arabic-2026',
                    'category' => 'الربح من المحتوى',
                    'author_name' => 'خليل السعيد',
                    'cover_image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b675?auto=format&fit=crop&w=1200&q=80',
                    'tags' => 'صناع المحتوى, الربح من الإنترنت, الرعايات, المنتجات الرقمية',
                    'read_time' => '6 دقائق قراءة',
                    'lang' => 'ar',
                    'status' => 'published',
                    'views_count' => 1650,
                    'excerpt' => 'الكثير من صناع المحتوى يعتقدون أن الربح يقتصر على إعلانات المشاهدات فقط. في هذا الدليل الواقعي، نكشف كيف تبني نموذج عمل رقمي متكامل يدر عليك دخلاً مستقلاً دون الاعتماد الكلي على خوارزميات المنصات.',
                    'content' => "## وهم المشاهدات المليونية في العالم العربي

لسنوات طويلة، وقع صناع المحتوى في فخٍ كبير: *\"سأقوم بنشر الفيديوهات يومياً حتى أصل إلى نصف مليون متابع، وسأعيش من أرباح المشاهدات.\"*

لكن كل صانع محتوى خاض التجربة يعرف الحقيقة الصادمة:
1. **عائد الألف ظهور (RPM) في منطقتنا العربية** للإعلانات التقليدية يتراوح غالباً بين 0.50 دولار إلى 2.50 دولار فقط في معظم المجالات الترفيهية والعامة.
2. **الخوارزميات تتغير باستمرار**، وأي تحديث في تيك توك أو يوتيوب قد يقلص وصول محتواك بنسبة 60% في ليلة وضحاها.
3. **أنت تبني مشروعك على أرض مستأجرة**؛ فالمتابعون ملكٌ للمنصة وليسوا ملكاً لك.

الحل الذي يتبعه كبار رواد الأعمال الرقميين اليوم هو بناء **استوديو رقمي متكامل** يعتمد على 3 ركائز أساسية:

---

### الركيزة الأولى: صفحة البايو الذكية كواجهة متجر
صفحة البايو في حساباتك ليست مجرد قائمة روابط مهملة، بل هي **أهم صفحة هبوط تمتلكها**.
بدلاً من وضع روابط عشوائية مربكة، احرص على:
- **إبراز العرض الأساسي:** ضعه في أول الصفحة مع تأثير حركي هادئ وشارة مميزة (مثل `🔥 الأكثر طلباً` أو `⚡ مجاني`).
- **تسهيل التواصل السريع:** وفر رابطاً مباشراً لرسائل واتساب للأعمال أو حجز الاستشارات.
- **التصميم الاحترافي المتناسق:** يجب أن تعكس صفحتك هوية بصرية فخمة تليق بعلامتك الشخصية، وهو ما يتيحه استوديو iLoveDigital بـ 5 ثيمات فاخرة وتوافق عربي كامل (RTL).

---

### الركيزة الثانية: الاحترافية في تسعير عقود الرعاية
الشركات والماركات لم تعد تبحث عن الأرقام الضخمة فقط، بل تبحث عن **التأثير الفعلي والثقة**.
عند مراسلة الشركات أو الرد على استفساراتهم:
- لا ترسل سعراً شفهياً عشوائياً.
- استخدم **حاسبة أسعار الرعايات** لحساب العائد العادل بناءً على تخصصك (مجالات التقنية والأعمال والاستثمار تحظى بأسعار أعلى بكثير).
- أرسل فاتورة احترافية واضحة البنود تتضمن تفاصيل التسليم وفترة حقوق الاستخدام.

---

### الركيزة الثالثة: الروابط الذكية وأكواد QR المتبوعة
عند تقديم مراجعة لمنتج أو مشاركة رابط تابع، لا تستخدم الروابط الطويلة المعقدة. استخدم **الروابط الذكية المختصرة مع باركود QR** لتتمكن من معرفة:
- كم نقرة جاءت من يوتيوب؟
- وكم نقرة جاءت من تيك توك أو انستغرام؟
- وأي منتج يحقق لك أعلى عائد؟

بهذه الطريقة، تبني مشروعاً رقمياً متيناً ومستداماً يدر أرباحاً شهرية متصاعدة."
                ],
                [
                    'title' => 'أسرار تحويل المتابعين إلى مبيعات وعملاء عبر صفحات البايو الذكية',
                    'slug' => 'smart-bio-link-conversion-secrets-arabic',
                    'category' => 'النمو الرقمي',
                    'author_name' => 'خليل السعيد',
                    'cover_image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                    'tags' => 'صفحة البايو, انستغرام, تيك توك, تجربة المستخدم, التحويلات',
                    'read_time' => '5 دقائق قراءة',
                    'lang' => 'ar',
                    'status' => 'published',
                    'views_count' => 1290,
                    'excerpt' => 'صفحة البايو ليست مجرد قائمة روابط؛ إنها واجهة متجرك الرقمي وبوابتك الأولى لإقناع الزائر في غضون 3 ثوانٍ فقط. تعرف على أسرار التصميم العصري والأزرار التفاعلية التي تضاعف نقراتك.',
                    'content' => "## قاعدة الـ 3 ثوانٍ على شاشات الهواتف

عندما يقرر متابع النقر على الرابط الموجود في بايو حسابك على انستغرام أو تيك توك، يكون في قمة فضوله واستعداده للتفاعل.

إذا واجهته صفحة بطيئة، أو مكدسة بـ 15 رابطاً متطابقاً دون وضوح، تحدث ظاهرة تسمى **\"شلل اتخاذ القرار\" (Decision Paralysis)**. يغلق الصفحة ويعود للتصفح دون أي إجراء!

---

### أسرار مضاعفة نقرات صفحتك المصغرة:

#### 1. مبدأ \"العرض البطل\" (Hero Offer)
اختر عنصراً واحداً فقط تود من كل زائر أن يراه، واجعله مميزاً بصرياً:
- أضف له شارة توضيحية بارزة.
- اختر حركة نبض ناعمة تلفت الانتباه دون إزعاج.
- اجعل نصه واضحاً يركز على الفائدة المباشرة للزائر (مثال: *\"حمّل حقيبة الخطوط المجانية 2026\"* بدلاً من *\"موقعي\"*).

#### 2. تناسق الهوية البصرية والثيم
عندما ينتقل الزائر من حسابك الأنيق إلى صفحة ذات مظهر رمادي تقليدي باهت، يفقد الشعور بالثقة. استخدام ثيم مظلم فاخر كـ **Midnight** أو ثيم نابض بالحيوية مثل **Cyberpunk** يعطي انطباعاً بأنك تدير استوديو رقمي بملايين الريالات.

#### 3. تتبع النقرات وتحسين الأداء أسبوعياً
لا تترك الروابط دون مراقبة. راجع لوحة تحكم iLoveDigital أسبوعياً:
- الروابط التي لا تنقر احذفها أو غير صياغة عنوانها.
- الروابط الأكثر نجاحاً ارفعها لأعلى الصفحة.

ابدأ الآن بتطبيق هذه الخطوات عبر **منشئ صفحات البايو** المدمج في حسابك."
                ]
            ];

            $stmt = $pdo->prepare("INSERT INTO blog_posts 
                (title, slug, category, author_name, cover_image, tags, read_time, lang, status, views_count, excerpt, content)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($posts as $post) {
                $stmt->execute([
                    $post['title'],
                    $post['slug'],
                    $post['category'],
                    $post['author_name'],
                    $post['cover_image'],
                    $post['tags'],
                    $post['read_time'],
                    $post['lang'],
                    $post['status'],
                    $post['views_count'],
                    $post['excerpt'],
                    $post['content']
                ]);
            }
        } catch (Exception $e) {
            // Keep going if seed encounters issue
        }
    }
}

