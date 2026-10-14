# ⚡ CreatorPulse — All-in-One Digital Creator SaaS Platform

> Built with **PHP 8+, HTML5 Canvas, Vanilla CSS3 & Modern JavaScript**. Ready for **Hostinger Shared Web Hosting** with **GitHub Actions CI/CD Pipeline**.

---

## 🌟 The 5 Pro Creator Modules

1. **📱 Bio-Link & Micro-Landing Page Builder**
   - Live interactive mobile phone preview simulator.
   - 6 custom themes: *Midnight Indigo*, *Cyberpunk Neon*, *Sunset Horizon*, *Luxury Gold*, *Minimalist Clean*, and *Emerald Mint*.
   - Animated attention buttons (Pulse, Shake, Bounce, Glow), badges (NEW, HOT, FREE), and social icon tray.
   - Public sharable profile (`/p/{username}`) with zero-lag fast loading.

2. **🎨 Social Media Thumbnail & Canvas Studio**
   - In-browser HTML5 Canvas visual editor.
   - Pre-configured aspect ratios: YouTube Thumbnail (16:9), Instagram Post (1:1), TikTok/Story (9:16), and Twitter/X (16:9).
   - Custom background gradients, solid colors, and image file upload.
   - Typography controls: Creator fonts (*Outfit*, *Impact*, *Anton*, *Plus Jakarta Sans*), stroke outlines, drop-shadow glow.
   - Creator badges overlay (*4K*, *VIRAL*, *TOP 10*, *LIVE*, *MUST WATCH*, *SUBSCRIBE*).
   - High-res PNG & JPG export.

3. **✍️ Viral Hooks & Copywriting Studio**
   - Algorithmic high-yield creator copywriting framework (works 100% offline out-of-the-box).
   - Formats: Contrarian Hooks, Curiosity Gaps, YouTube Titles & Timestamp Descriptions, Reel/TikTok Captions, and Twitter/X Viral Threads.
   - Tone selector: Bold & Viral, Educational, Direct Converting, Casual Storyteller.
   - Word counter, character count, one-click clipboard copying.

4. **🔗 Smart Short Links & Branded QR Studio**
   - Clean URL shortener (`/s/{code}`) with custom alias support.
   - Automated UTM Campaign Builder (`utm_source`, `utm_medium`, `utm_campaign`).
   - Dynamic Branded QR Code generator with custom foreground/background colors and PNG download.
   - Click tracking and performance analytics.

5. **💰 Sponsorship Rates & Brand Invoicing**
   - Algorithmic sponsorship pricing calculator based on followers, average views, engagement rate, content format, and niche multiplier.
   - Implied CPM computation and 3-post retainer bundle pricing.
   - 1-Click "Add to Invoice" integration.
   - Print-ready and PDF-exportable creator brand deal invoice maker (`window.print()` formatted for clean sheets).

---

## 🛠️ Technology Stack & Architecture

- **Backend**: Native PHP 8.1+ (zero external vendor bloat, works smoothly on Hostinger shared hosting without Node or daemon requirements).
- **Frontend**: Semantic HTML5, Vanilla CSS3 (Custom Design System with Glassmorphism and Micro-Animations), Modern Vanilla JS.
- **Database**:
  - **SQLite (Default)**: Zero configuration, instant local & remote run.
  - **MySQL / MariaDB**: Hostinger production ready with simple `.env` switch.
- **CI/CD**: GitHub Actions workflow (`.github/workflows/deploy.yml`) synchronizing repository pushes to Hostinger shared web hosting via secure FTPS.

---

## 🚀 Local Development Setup

To run locally using PHP's built-in web server:

```bash
# 1. Navigate to the project directory
cd c:\Users\Khalil\OneDrive\saas

# 2. Start the local server
php -S localhost:8000
```

Open your browser at **[http://localhost:8000](http://localhost:8000)**.

### Demo Account (Pre-Seeded)
- **Email**: `demo@creatorpulse.com`
- **Password**: `demo123`
- Or simply click the **"⚡ 1-Click Demo Login"** button on the Sign-In page!
- Demo public bio page: **`http://localhost:8000/p/alexcreator`**

---

## 📦 Hostinger CI/CD Deployment

For step-by-step instructions on linking your Hostinger shared web hosting and configuring GitHub secrets under `khalilksa33`, see [DEPLOYMENT.md](DEPLOYMENT.md).
