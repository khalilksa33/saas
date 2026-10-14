<?php
$pageTitle = 'Create Free Creator Account — ' . APP_NAME;
$hideNav = false;
$hideFooter = true;

// Handle Register POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/helpers.php';
    require_once __DIR__ . '/../includes/auth.php';

    if (!verify_csrf()) {
        set_flash('error', 'Session expired. Please try again.');
        redirect('register');
    }

    $name = trim($_POST['name'] ?? '');
    $username = strtolower(slugify(trim($_POST['username'] ?? '')));
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $plan = in_array($_POST['plan'] ?? '', ['pro', 'agency']) ? $_POST['plan'] : 'free';

    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        set_flash('error', 'Please fill in all required fields.');
        redirect('register');
    }

    if (strlen($password) < 6) {
        set_flash('error', 'Password must be at least 6 characters.');
        redirect('register');
    }

    $pdo = DB::getConnection();

    // Check if email or username already taken
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->execute([$email, $username]);
    if ($check->fetch()) {
        set_flash('error', 'Email or username is already taken. Please choose another.');
        redirect('register');
    }

    try {
        $pdo->beginTransaction();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (name, username, email, password_hash, plan, avatar_url, bio)
                                 VALUES (?, ?, ?, ?, ?, ?, ?)");
        $avatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80';
        $bio = 'Digital Creator. Welcome to my creative corner!';
        $insert->execute([$name, $username, $email, $hash, $plan, $avatar, $bio]);
        $newUserId = $pdo->lastInsertId();

        // Initialize bio profile
        $insBio = $pdo->prepare("INSERT INTO bio_profiles (user_id, theme, display_name, bio_text, avatar_url, social_links)
                                 VALUES (?, ?, ?, ?, ?, ?)");
        $socials = json_encode(['youtube' => '', 'instagram' => '', 'tiktok' => '', 'twitter' => '']);
        $insBio->execute([$newUserId, 'midnight', $name, $bio, $avatar, $socials]);

        $pdo->commit();

        // Log user in
        Auth::login([
            'id' => $newUserId,
            'name' => $name,
            'username' => $username,
            'plan' => $plan
        ]);

        set_flash('success', 'Your creator account is ready! Welcome to ' . APP_NAME . '.');
        redirect('dashboard');

    } catch (Exception $e) {
        $pdo->rollBack();
        set_flash('error', 'Registration failed: ' . $e->getMessage());
        redirect('register');
    }
}

$chosenPlan = $_GET['plan'] ?? 'free';
require_once __DIR__ . '/../includes/header.php';
?>

<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="card" style="width: 100%; max-width: 460px; padding: 2.5rem 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" style="margin: 0 auto 1rem; width: 48px; height: 48px; filter: drop-shadow(0 0 16px rgba(99, 102, 241, 0.45));">
            <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;"><?= __('auth_create_title') ?></h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= __('auth_create_sub') ?></p>
        </div>

        <!-- Sign up with Google Button -->
        <a href="<?= url('auth/google?intent=register') ?>" id="btn-google-register" class="btn" style="width: 100%; margin-bottom: 1.5rem; background: #ffffff; color: #1f2937; font-weight: 600; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08); transition: transform 0.15s ease, box-shadow 0.15s ease;">
            <svg width="20" height="20" viewBox="0 0 48 48" style="flex-shrink: 0;">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            <span><?= __('auth_google_signup', 'Sign up with Google') ?></span>
        </a>

        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;"><?= __('auth_or_email') ?></span>
            <div style="flex: 1; height: 1px; background: var(--border-glass);"></div>
        </div>

        <form method="POST" action="<?= url('register') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="plan" value="<?= e($chosenPlan) ?>">

            <div class="form-group">
                <label class="form-label" for="name"><?= __('auth_name') ?></label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Sarah Jenkins" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="username"><?= __('auth_handle') ?></label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 1rem; color: var(--text-muted); font-size: 0.9rem;">/p/</span>
                    <input type="text" id="username" name="username" class="form-control" style="padding-left: 2.5rem;" placeholder="sarahcreates" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email"><?= __('auth_email') ?></label>
                <input type="email" id="email" name="email" class="form-control" placeholder="sarah@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password"><?= __('auth_password') ?> (<?= __('auth_password_hint') ?>)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.8rem;">
                <?= __('auth_submit_register') ?>
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.75rem; font-size: 0.88rem; color: var(--text-secondary);">
            <?= __('auth_has_account') ?> <a href="<?= url('login') ?>" style="color: var(--primary); font-weight: 600;"><?= __('auth_signin') ?></a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
