<?php
$pageTitle = 'Sign In — ' . APP_NAME;
$hideNav = false;
$hideFooter = true;

// Handle Login POST or Demo Auto-login
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['demo'])) {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/helpers.php';
    require_once __DIR__ . '/../includes/auth.php';

    $isDemo = isset($_GET['demo']) || isset($_POST['demo_login']);
    $pdo = DB::getConnection();

    if ($isDemo) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['demo@creatorpulse.com']);
        $user = $stmt->fetch();
        if ($user) {
            Auth::login($user);
            set_flash('success', 'Logged in as Demo Creator (Alex Rivera)!');
            redirect('dashboard');
        }
    } else {
        if (!verify_csrf()) {
            set_flash('error', 'Session expired. Please try again.');
            redirect('login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            Auth::login($user);
            set_flash('success', 'Welcome back, ' . $user['name'] . '!');
            redirect('dashboard');
        } else {
            set_flash('error', 'Invalid email or password.');
            redirect('login');
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="card" style="width: 100%; max-width: 440px; padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" style="margin: 0 auto 1rem; width: 48px; height: 48px; filter: drop-shadow(0 0 16px rgba(99, 102, 241, 0.45));">
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">Welcome back</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Sign in to access your creator studio</p>
        </div>

        <!-- 1-Click Demo Button -->
        <a href="<?= url('login?demo=1') ?>" class="btn btn-secondary" style="width: 100%; margin-bottom: 1.5rem; border-color: rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.1); color: #c7d2fe;">
            <span>⚡ 1-Click Demo Login (Pre-Loaded Studio)</span>
        </a>

        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">or sign in with email</span>
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
        </div>

        <form method="POST" action="<?= url('login') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="creator@example.com" required>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label class="form-label" for="password">Password</label>
                    <a href="#" style="font-size: 0.8rem; color: var(--primary);">Forgot password?</a>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                Sign In to Studio
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.75rem; font-size: 0.88rem; color: var(--text-secondary);">
            Don't have an account? <a href="<?= url('register') ?>" style="color: var(--primary); font-weight: 600;">Sign up for free</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
