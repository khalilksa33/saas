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
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;"><?= __('auth_welcome') ?></h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= __('auth_welcome_sub') ?></p>
        </div>

        <!-- Continue with Google Button -->
        <a href="<?= url('auth/google') ?>" id="btn-google-login" class="btn" style="width: 100%; margin-bottom: 0.85rem; background: #ffffff; color: #1f2937; font-weight: 600; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); transition: transform 0.15s ease, box-shadow 0.15s ease;">
            <svg width="20" height="20" viewBox="0 0 48 48" style="flex-shrink: 0;">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <span><?= __('auth_google_continue', 'Continue with Google') ?></span>
        </a>

        <!-- 1-Click Demo Button -->
        <a href="<?= url('login?demo=1') ?>" class="btn btn-secondary" style="width: 100%; margin-bottom: 1.5rem; border-color: rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.1); color: #c7d2fe;">
            <span><?= __('auth_demo_button') ?></span>
        </a>

        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;"><?= __('auth_or_email') ?></span>
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
        </div>

        <form method="POST" action="<?= url('login') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email"><?= __('auth_email') ?></label>
                <input type="email" id="email" name="email" class="form-control" placeholder="creator@example.com" required>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <label class="form-label" for="password"><?= __('auth_password') ?></label>
                    <a href="#" style="font-size: 0.8rem; color: var(--primary);"><?= __('auth_forgot') ?></a>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                <?= __('auth_submit_login') ?>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.75rem; font-size: 0.88rem; color: var(--text-secondary);">
            <?= __('auth_no_account') ?> <a href="<?= url('register') ?>" style="color: var(--primary); font-weight: 600;"><?= __('auth_signup') ?></a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
