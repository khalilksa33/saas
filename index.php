<?php
/**
 * CreatorPulse - Front Controller & Router
 * Hostinger Shared Hosting & Localhost Compatible
 */

// If running with PHP's built-in CLI server and the requested static file exists, return false to let PHP serve it
if (php_sapi_name() === 'cli-server') {
    $reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $fullPath = __DIR__ . $reqPath;
    if ($reqPath !== '/' && is_file($fullPath)) {
        return false;
    }
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

// Parse request path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove basePath if application is installed in subfolder
if (defined('BASE_PATH') && BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
    $path = substr($path, strlen(BASE_PATH));
}

$path = trim($path, '/');

// Router
if ($path === '' || $path === 'home') {
    require __DIR__ . '/views/home.php';
    exit;
}

if ($path === 'login') {
    require __DIR__ . '/views/login.php';
    exit;
}

if ($path === 'register') {
    require __DIR__ . '/views/register.php';
    exit;
}

if ($path === 'logout') {
    Auth::logout();
    set_flash('success', 'You have been logged out.');
    redirect('');
    exit;
}

if ($path === 'dashboard') {
    Auth::requireAuth();
    require __DIR__ . '/views/dashboard.php';
    exit;
}

if ($path === 'tools/bio-link') {
    Auth::requireAuth();
    require __DIR__ . '/views/bio_link.php';
    exit;
}

if ($path === 'tools/thumbnail-studio') {
    Auth::requireAuth();
    require __DIR__ . '/views/thumbnail_studio.php';
    exit;
}

if ($path === 'tools/copywriter') {
    Auth::requireAuth();
    require __DIR__ . '/views/copywriter.php';
    exit;
}

if ($path === 'tools/smart-links') {
    Auth::requireAuth();
    require __DIR__ . '/views/smart_links.php';
    exit;
}

if ($path === 'tools/rate-calculator') {
    Auth::requireAuth();
    require __DIR__ . '/views/rate_calculator.php';
    exit;
}

if ($path === 'profile') {
    Auth::requireAuth();
    require __DIR__ . '/views/profile.php';
    exit;
}

// Public Blog Hub: /blog
if ($path === 'blog') {
    require __DIR__ . '/views/blog_index.php';
    exit;
}

// Public Single Blog Post: /blog/{slug}
if (preg_match('~^blog/([a-zA-Z0-9_\-]+)$~', $path, $matches)) {
    $routeParam = $matches[1];
    require __DIR__ . '/views/blog_single.php';
    exit;
}

// Superadmin Dashboard: /admin or /superadmin
if ($path === 'admin' || $path === 'superadmin') {
    Auth::requireSuperAdmin();
    require __DIR__ . '/views/admin/dashboard.php';
    exit;
}

// Superadmin Subscriptions: /admin/subscriptions
if ($path === 'admin/subscriptions') {
    Auth::requireSuperAdmin();
    require __DIR__ . '/views/admin/subscriptions.php';
    exit;
}

// Superadmin Blog CMS List: /admin/blog
if ($path === 'admin/blog') {
    Auth::requireSuperAdmin();
    require __DIR__ . '/views/admin/blog_list.php';
    exit;
}

// Superadmin Create Blog: /admin/blog/create
if ($path === 'admin/blog/create') {
    Auth::requireSuperAdmin();
    require __DIR__ . '/views/admin/blog_edit.php';
    exit;
}

// Superadmin Edit Blog: /admin/blog/edit
if ($path === 'admin/blog/edit') {
    Auth::requireSuperAdmin();
    require __DIR__ . '/views/admin/blog_edit.php';
    exit;
}


// Public Bio Page: /p/{username}
if (preg_match('~^p/([a-zA-Z0-9_\-]+)$~', $path, $matches)) {
    $routeParam = $matches[1];
    require __DIR__ . '/views/public_bio.php';
    exit;
}

// Smart Link Redirection: /s/{code}
if (preg_match('~^s/([a-zA-Z0-9_\-]+)$~', $path, $matches)) {
    $code = $matches[1];
    $pdo = DB::getConnection();
    $stmt = $pdo->prepare("SELECT id, target_url FROM short_links WHERE code = ?");
    $stmt->execute([$code]);
    $link = $stmt->fetch();

    if ($link) {
        // Record click
        $upd = $pdo->prepare("UPDATE short_links SET clicks_count = clicks_count + 1 WHERE id = ?");
        $upd->execute([$link['id']]);

        // Log detailed click
        $log = $pdo->prepare("INSERT INTO link_clicks (link_id, link_type, user_agent, referer, device) VALUES (?, 'short_link', ?, ?, ?)");
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $device = (preg_match('/Mobile|Android|iPhone/i', $ua)) ? 'Mobile' : 'Desktop';
        $log->execute([$link['id'], substr($ua, 0, 255), substr($ref, 0, 255), $device]);

        header("Location: " . $link['target_url'], true, 302);
        exit;
    } else {
        http_response_code(404);
        echo "<div style='font-family:sans-serif; text-align:center; padding: 4rem; color:#fff; background:#090d16; min-height:100vh;'>
                <h1>404 - Link Not Found</h1>
                <p>This smart link has expired or does not exist.</p>
                <a href='" . url() . "' style='color:#6366f1; text-decoration:none;'>← Go to " . APP_NAME . "</a>
              </div>";
        exit;
    }
}

// Track bio link click: /track-click?id=X&to=Y
if ($path === 'track-click') {
    $id = intval($_GET['id'] ?? 0);
    $to = filter_var($_GET['to'] ?? '', FILTER_SANITIZE_URL);
    if ($id > 0) {
        $pdo = DB::getConnection();
        $upd = $pdo->prepare("UPDATE bio_links SET clicks_count = clicks_count + 1 WHERE id = ?");
        $upd->execute([$id]);
    }
    if ($to && filter_var($to, FILTER_VALIDATE_URL)) {
        header("Location: " . $to, true, 302);
        exit;
    }
    redirect('');
}

// 404 Fallback
http_response_code(404);
echo "<div style='font-family:sans-serif; text-align:center; padding: 4rem; color:#fff; background:#090d16; min-height:100vh;'>
        <h1>404 - Page Not Found</h1>
        <p>The page you requested could not be found.</p>
        <a href='" . url() . "' style='color:#6366f1; text-decoration:none;'>← Back to Home</a>
      </div>";
exit;
