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
