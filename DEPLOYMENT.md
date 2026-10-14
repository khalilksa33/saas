# 🚀 Hostinger Shared Web Hosting CI/CD Deployment Guide

This guide details the exact step-by-step process to auto-deploy **CreatorPulse SaaS** from your GitHub account (**`khalilksa33`**) to **Hostinger Shared Web Hosting** using GitHub Actions CI/CD.

---

## 1. Retrieve Hostinger FTP Credentials

1. Log into your **[Hostinger hPanel](https://hpanel.hostinger.com/)**.
2. Navigate to **Websites** and click **Manage** next to your domain.
3. In the left sidebar, navigate to **Files** ➔ **FTP Accounts**.
4. You will see your connection parameters:
   - **FTP Host / Server IP**: (e.g. `ftp.yourdomain.com` or `185.xxx.xxx.xxx`)
   - **FTP Username**: (e.g. `u123456789` or custom user)
   - **FTP Password**: Click **Change Password** if you do not remember it.
   - **Port**: `21`
   - **Default Directory**: `/public_html`

---

## 2. Configure GitHub Secrets (Repository: `khalilksa33`)

1. Open your repository on GitHub:
   `https://github.com/khalilksa33/<your-saas-repo-name>`
2. Click **Settings** (top tab) ➔ **Secrets and variables** (left sidebar) ➔ **Actions**.
3. Click the green button: **New repository secret**.
4. Add the following 3 secrets:

| Secret Name | Value | Example |
| :--- | :--- | :--- |
| `FTP_SERVER` | Your Hostinger FTP Host or IP | `ftp.yourdomain.com` |
| `FTP_USERNAME` | Your Hostinger FTP Username | `u123456789` |
| `FTP_PASSWORD` | Your Hostinger FTP Password | `YourSecurePassword!` |

---

## 3. Database Options on Hostinger

### Option A: SQLite (Default — Zero-Setup)
- CreatorPulse uses SQLite by default.
- It requires **no database setup** or user configuration on Hostinger!
- The app automatically creates and seeds `data/database.sqlite` on first load.
- It is protected against public downloads via `.htaccess`.

### Option B: Hostinger MySQL / MariaDB (High Traffic)
If you prefer Hostinger's managed MySQL database:
1. In hPanel, go to **Databases** ➔ **MySQL Databases**.
2. Create a new database:
   - Database name: `u123456789_creatorpulse`
   - Username: `u123456789_creator`
   - Password: `YourDatabasePassword123`
3. In Hostinger **File Manager**, create or edit `.env` inside `public_html/`:
   ```env
   APP_NAME="CreatorPulse"
   APP_ENV=production
   APP_URL=https://yourdomain.com
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123456789_creatorpulse
   DB_USERNAME=u123456789_creator
   DB_PASSWORD=YourDatabasePassword123
   ```
4. CreatorPulse auto-creates all tables and seeds the demo creator upon your first visit!

---

## 4. Initialize & Push from Your Local Machine

In your local project directory (`c:\Users\Khalil\OneDrive\saas`):

```bash
# 1. Initialize git (if not already done)
git init

# 2. Stage all files
git add .

# 3. Commit
git commit -m "Initial commit: CreatorPulse SaaS with full creator tools suite"

# 4. Link your GitHub repository under khalilksa33
git remote add origin https://github.com/khalilksa33/<your-repo-name>.git

# 5. Push to main
git branch -M main
git push -u origin main
```

---

## 5. Verify Automatic Deployment

1. On your GitHub repo page, click the **Actions** tab.
2. You will see the **🚀 Deploy CreatorPulse SaaS to Hostinger** workflow running.
3. Once completed (green checkmark), all files are synced directly into Hostinger's `public_html/`.
4. Open your domain (e.g. `https://yourdomain.com`) in your browser!

---

## 6. Hostinger Optimization & SSL

1. In hPanel ➔ **Security** ➔ **SSL**, verify that Hostinger's **Free Unlimited SSL** is active (Force HTTPS).
2. CreatorPulse includes built-in Gzip compression and Apache caching rules via `.htaccess` for lightning-fast 99+ PageSpeed scores.
