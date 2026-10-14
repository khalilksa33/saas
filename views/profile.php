<?php
$pageTitle = __('profile_title');
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
        <h1 style="font-size: 1.75rem; font-weight: 800;"><?= __('profile_title') ?></h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            <?= __('profile_subtitle') ?>
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1.25fr 1fr; gap: 2rem; align-items: flex-start;">
    <!-- Profile Info Form -->
    <div class="card">
        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>👤</span> <?= __('profile_info') ?>
        </h3>

        <form method="POST" action="<?= url('profile') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="update_profile" value="1">

            <div class="form-group">
                <label class="form-label" for="profile_name"><?= __('profile_name') ?></label>
                <input type="text" id="profile_name" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('profile_handle') ?></label>
                <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled style="opacity: 0.6;">
                <span style="font-size: 0.75rem; color: var(--text-muted);"><?= __('profile_public_bio_url') ?> <?= e(url('p/' . $user['username'])) ?></span>
            </div>

            <div class="form-group">
                <label class="form-label"><?= __('profile_email') ?></label>
                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled style="opacity: 0.6;">
                <?php if (!empty($user['google_id'])): ?>
                    <div style="margin-top: 0.5rem; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.65rem; background: rgba(66, 133, 244, 0.1); border: 1px solid rgba(66, 133, 244, 0.3); border-radius: 6px; font-size: 0.78rem; color: #93c5fd;">
                        <svg width="14" height="14" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        <span><?= __('auth_google_connected', 'Google Account Connected') ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="profile_avatar"><?= __('profile_avatar') ?></label>
                <input type="url" id="profile_avatar" name="avatar_url" class="form-control" value="<?= e($user['avatar_url']) ?>" placeholder="https://...">
            </div>

            <div class="form-group">
                <label class="form-label" for="profile_bio"><?= __('profile_bio') ?></label>
                <textarea id="profile_bio" name="bio" class="form-control" rows="3"><?= e($user['bio']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                <?= __('profile_save') ?>
            </button>
        </form>
    </div>

    <!-- Subscription & Plan Management -->
    <div>
        <div class="card card-glow" style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.2rem; font-weight: 700;"><?= __('profile_active_plan') ?></h3>
                <span class="badge badge-pro"><?= strtoupper(e($user['plan'])) ?> <?= __('profile_plan_tier') ?></span>
            </div>

            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;">
                <?= str_replace(':appName', e(APP_NAME), __('profile_plan_desc')) ?>
            </p>

            <form method="POST" action="<?= url('profile') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="switch_plan" value="1">

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="submit" name="plan" value="free" class="btn <?= $user['plan'] === 'free' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span><?= __('profile_plan_starter') ?></span>
                        <span><?= $user['plan'] === 'free' ? __('profile_plan_current') : __('profile_plan_select') ?></span>
                    </button>
                    <button type="submit" name="plan" value="pro" class="btn <?= $user['plan'] === 'pro' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span><?= __('profile_plan_pro') ?></span>
                        <span><?= $user['plan'] === 'pro' ? __('profile_plan_current') : __('profile_plan_select') ?></span>
                    </button>
                    <button type="submit" name="plan" value="agency" class="btn <?= $user['plan'] === 'agency' ? 'btn-primary' : 'btn-secondary' ?>" style="justify-content: space-between;">
                        <span><?= __('profile_plan_agency') ?></span>
                        <span><?= $user['plan'] === 'agency' ? __('profile_plan_current') : __('profile_plan_select') ?></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Hostinger CI/CD Integration Details -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🚀</span> <?= __('profile_deployment_card') ?>
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                Domain: <strong>ilovedigital.online</strong><br>
                GitHub Account: <strong>khalilksa33</strong><br>
                Deployment Method: <strong>GitHub Actions FTPS to Hostinger public_html</strong><br>
                Database Engine: <strong>SQLite / MySQL PDO (Zero-Config)</strong>
            </p>
            <a href="<?= url('DEPLOYMENT.md') ?>" class="btn btn-sm btn-outline" style="width: 100%;">
                <?= __('profile_guide_btn') ?>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/app_layout_end.php'; ?>

