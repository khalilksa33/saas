<?php
$pageTitle = 'Account & Subscription Settings';
$currentRoute = 'profile';

$pdo = DB::getConnection();
$userId = Auth::id();

// Handle Profile Update or Plan Switch
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Session expired. Please try again.');
        redirect('profile');
    }

    if (isset($_POST['update_profile'])) {
        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $avatarUrl = filter_var($_POST['avatar_url'] ?? '', FILTER_SANITIZE_URL);

        $update = $pdo->prepare("UPDATE users SET name = ?, bio = ?, avatar_url = ? WHERE id = ?");
        $update->execute([$name, $bio, $avatarUrl, $userId]);
        set_flash('success', 'Profile updated successfully!');
        redirect('profile');

    } elseif (isset($_POST['switch_plan'])) {
        $newPlan = in_array($_POST['plan'], ['free', 'pro', 'agency']) ? $_POST['plan'] : 'pro';
        $update = $pdo->prepare("UPDATE users SET plan = ? WHERE id = ?");
        $update->execute([$newPlan, $userId]);
        $_SESSION['user_plan'] = $newPlan;
        set_flash('success', 'Subscription plan updated to ' . strtoupper($newPlan) . '!');
        redirect('profile');
    }
}

require_once __DIR__ . '/../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800;">Account & Subscription Plan</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Manage your creator credentials, subscription tier, and Hostinger deployment configuration
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 2rem; align-items: flex-start;">
    <!-- Profile Info Form -->
    <div class="card">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>👤</span> Profile Information
        </h3>

        <form method="POST" action="<?= url('profile') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="update_profile" value="1">

            <div class="form-group">
                <label class="form-label" for="profile_name">Full Name</label>
                <input type="text" id="profile_name" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Creator Handle (Username)</label>
                <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled style="opacity: 0.6;">
                <span style="font-size: 0.75rem; color: var(--text-muted);">Public bio URL: <?= e(url('p/' . $user['username'])) ?></span>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled style="opacity: 0.6;">
            </div>

            <div class="form-group">
                <label class="form-label" for="profile_avatar">Avatar Image URL</label>
                <input type="url" id="profile_avatar" name="avatar_url" class="form-control" value="<?= e($user['avatar_url']) ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label class="form-label" for="profile_bio">Bio / Creator Statement</label>
                <textarea id="profile_bio" name="bio" class="form-control" rows="3"><?= e($user['bio']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Subscription & Plan Management -->
    <div>
        <div class="card card-glow" style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 700;">Active Plan</h3>
                <span class="badge badge-pro"><?= strtoupper(e($user['plan'])) ?> TIER</span>
            </div>

            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">
                You currently have access to all <?= e(APP_NAME) ?> pro modules, unlimited bio links, 4K canvas exports, and sponsorship tools.
            </p>

            <form method="POST" action="<?= url('profile') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="switch_plan" value="1">

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="submit" name="plan" value="free" class="btn <?= $user['plan'] === 'free' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span>Starter Plan ($0/mo)</span>
                        <span><?= $user['plan'] === 'free' ? '✓ Current' : 'Select' ?></span>
                    </button>
                    <button type="submit" name="plan" value="pro" class="btn <?= $user['plan'] === 'pro' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span>Creator Pro ($15/mo)</span>
                        <span><?= $user['plan'] === 'pro' ? '✓ Current' : 'Select' ?></span>
                    </button>
                    <button type="submit" name="plan" value="agency" class="btn <?= $user['plan'] === 'agency' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span>Agency & Studio ($39/mo)</span>
                        <span><?= $user['plan'] === 'agency' ? '✓ Current' : 'Select' ?></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Hostinger CI/CD Integration Details -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🚀</span> Hostinger Shared Hosting CI/CD
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                Domain: <strong>ilovedigital.online</strong><br>
                GitHub Account: <strong>khalilksa33</strong><br>
                Deployment Method: <strong>GitHub Actions FTPS to Hostinger public_html</strong><br>
                Database Engine: <strong>SQLite / MySQL PDO (Zero-Config)</strong>
            </p>
            <a href="<?= url('DEPLOYMENT.md') ?>" class="btn btn-sm btn-outline" style="width: 100%;">
                View Hostinger Setup Guide ↗
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>
