# 🚀 Hostinger Shared Web Hosting CI/CD Deployment Guide: ilovedigital.online

This guide details the exact step-by-step process to auto-deploy **iLoveDigital SaaS** from your GitHub account (**`khalilksa33`**) to your domain **`ilovedigital.online`** on **Hostinger Shared Web Hosting** using GitHub Actions CI/CD.

---

## 1. Retrieve Hostinger FTP Credentials

1. Log into your **[Hostinger hPanel](https://hpanel.hostinger.com/)**.
2. Navigate to **Websites** and select **`ilovedigital.online`**.
3. In the left sidebar, navigate to **Files** ➔ **FTP Accounts**.
4. You will see your connection parameters:
   - **FTP Host / Server IP**: (e.g. `ftp.ilovedigital.online` or server IP `185.xxx.xxx.xxx`)
   - **FTP Username**: (e.g. `u123456789` or your custom FTP account username)
   - **FTP Password**: Click **Change Password** if needed.
   - **Port**: `21`
   - **Target Directory**:
     - If `ilovedigital.online` is your primary hosting website: `public_html`
     - If added as an Addon Domain: usually `domains/ilovedigital.online/public_html` or `public_html`

---

## 2. Configure GitHub Secrets (Repository: `khalilksa33/saas`)

1. Open your repository on GitHub:
   `https://github.com/khalilksa33/saas`
2. Click **Settings** (top tab) ➔ **Secrets and variables** (left sidebar) ➔ **Actions**.
3. Add the following repository secrets:

| Secret Name | Value | Example |
| :--- | :--- | :--- |
| `FTP_SERVER` | Your Hostinger FTP Host or IP | `ftp.ilovedigital.online` |
| `FTP_USERNAME` | Your Hostinger FTP Username | `u123456789` |
| `FTP_PASSWORD` | Your Hostinger FTP Password | `YourSecurePassword!` |
| `FTP_SERVER_DIR` *(Optional)* | Remote web root directory | `public_html` (default) or `domains/ilovedigital.online/public_html` |

---

## 3. Database Options on Hostinger

### Option A: SQLite (Default — Zero-Setup)
- iLoveDigital uses SQLite by default.
- It requires **no database setup** or user configuration on Hostinger!
- The app automatically creates and seeds `data/database.sqlite` on first load.
- It is protected against public downloads via `.htaccess`.

### Option B: Hostinger MySQL / MariaDB (High Traffic)
If you prefer Hostinger's managed MySQL database:
1. In hPanel for `ilovedigital.online`, go to **Databases** ➔ **MySQL Databases**.
2. Create a new database:
   - Database name: `u123456789_ilovedigital`
   - Username: `u123456789_digitaluser`
   - Password: `YourDatabasePassword123`
3. In Hostinger **File Manager**, create or edit `.env` inside `public_html/`:
   ```env
   APP_NAME="iLoveDigital"
   APP_ENV=production
   APP_URL=https://ilovedigital.online
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123456789_ilovedigital
   DB_USERNAME=u123456789_digitaluser
   DB_PASSWORD=YourDatabasePassword123
   ```
4. iLoveDigital auto-creates all tables and seeds the demo creator upon your first visit!

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

1. On your GitHub repo page (`khalilksa33/saas`), click the **Actions** tab.
2. You will see the **🚀 Deploy iLoveDigital SaaS to Hostinger (ilovedigital.online)** workflow running.
3. Once completed (green checkmark), all files are synced directly into Hostinger.
4. Open your domain **`https://ilovedigital.online`** in your browser!

---

## 6. Hostinger Optimization & SSL

1. In hPanel ➔ **Security** ➔ **SSL**, verify that Hostinger's **Free Unlimited SSL** is active (Force HTTPS).
2. CreatorPulse includes built-in Gzip compression and Apache caching rules via `.htaccess` for lightning-fast 99+ PageSpeed scores.
