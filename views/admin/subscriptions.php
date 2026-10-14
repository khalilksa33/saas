<?php
/**
 * Superadmin Subscription & User Management
 * Manage creator tiers (Free, Pro, Agency) and roles
 */
Auth::requireSuperAdmin();

$pageTitle = 'Subscription & User Management';
$currentRoute = 'admin_subscriptions';
$user = Auth::user();
$pdo = DB::getConnection();

// Handle User Plan or Role Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('error', 'Session expired. Please try again.');
        redirect('admin/subscriptions');
    }

    $targetUserId = (int) ($_POST['target_user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($targetUserId > 0) {
        if ($action === 'update_plan') {
            $newPlan = in_array($_POST['plan'] ?? '', ['free', 'pro', 'agency']) ? $_POST['plan'] : 'pro';
            $stmt = $pdo->prepare("UPDATE users SET plan = ? WHERE id = ?");
            $stmt->execute([$newPlan, $targetUserId]);
            set_flash('success', 'User plan successfully changed to ' . strtoupper($newPlan) . '!');
            redirect('admin/subscriptions');

        } elseif ($action === 'toggle_role') {
            // Prevent removing oneself as superadmin
            if ($targetUserId === Auth::id()) {
                set_flash('error', 'You cannot change your own superadmin role.');
                redirect('admin/subscriptions');
            }
            $targetUser = $pdo->query("SELECT role FROM users WHERE id = $targetUserId")->fetch();
            $newRole = ($targetUser['role'] === 'superadmin') ? 'user' : 'superadmin';
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $targetUserId]);
            set_flash('success', 'User role updated to ' . strtoupper($newRole) . '!');
            redirect('admin/subscriptions');

        } elseif ($action === 'delete_user') {
            if ($targetUserId === Auth::id()) {
                set_flash('error', 'You cannot delete your own active superadmin account.');
                redirect('admin/subscriptions');
            }
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$targetUserId]);
            set_flash('success', 'User account permanently deleted.');
            redirect('admin/subscriptions');
        }
    }
}

// Filters & Search
$planFilter = trim($_GET['plan'] ?? '');
$search = trim($_GET['q'] ?? '');

$sql = "SELECT id, name, username, email, plan, role, avatar_url, created_at FROM users WHERE 1=1";
$params = [];

if ($planFilter && in_array($planFilter, ['free', 'pro', 'agency'])) {
    $sql .= " AND plan = ?";
    $params[] = $planFilter;
}

if ($search) {
    $sql .= " AND (name LIKE ? OR username LIKE ? OR email LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$usersList = $stmt->fetchAll();

// Metrics
$countFree = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'free'")->fetchColumn();
$countPro = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'pro'")->fetchColumn();
$countAgency = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE plan = 'agency'")->fetchColumn();
$totalMrr = ($countPro * 15) + ($countAgency * 39);

require_once __DIR__ . '/../../includes/app_layout_start.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.85rem; font-weight: 800;">Subscription &amp; User Accounts</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">
            Directly modify subscription tiers, grant Pro/Agency features, or manage administrator access.
        </p>
    </div>
    <div>
        <a href="<?= url('admin') ?>" class="btn btn-secondary">
            ← Back to Operations Overview
        </a>
    </div>
</div>

<!-- Plan Aggregation Bar -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 2rem;">💰</span>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #10b981;">$<?= number_format($totalMrr) ?></div>
            <div style="font-size: 0.78rem; color: var(--text-muted);">Platform Monthly Recurring Revenue</div>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 2rem;">⚡</span>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);"><?= $countPro ?></div>
            <div style="font-size: 0.78rem; color: var(--text-muted);">Creator Pro Members ($15/mo)</div>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 2rem;">🏢</span>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #ec4899;"><?= $countAgency ?></div>
            <div style="font-size: 0.78rem; color: var(--text-muted);">Agency Studio Members ($39/mo)</div>
        </div>
    </div>
    <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 2rem;">🌱</span>
        <div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-secondary);"><?= $countFree ?></div>
            <div style="font-size: 0.78rem; color: var(--text-muted);">Starter Free Accounts</div>
        </div>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="<?= url('admin/subscriptions') ?>" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="<?= url('admin/subscriptions') ?>" class="btn btn-sm <?= empty($planFilter) ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                All Users (<?= $countFree + $countPro + $countAgency ?>)
            </a>
            <a href="<?= url('admin/subscriptions?plan=free') ?>" class="btn btn-sm <?= $planFilter === 'free' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                Free Tier (<?= $countFree ?>)
            </a>
            <a href="<?= url('admin/subscriptions?plan=pro') ?>" class="btn btn-sm <?= $planFilter === 'pro' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                Creator Pro (<?= $countPro ?>)
            </a>
            <a href="<?= url('admin/subscriptions?plan=agency') ?>" class="btn btn-sm <?= $planFilter === 'agency' ? 'btn-primary' : 'btn-secondary' ?>" style="border-radius: 999px;">
                Agency Studio (<?= $countAgency ?>)
            </a>
        </div>

        <div style="display: flex; gap: 0.5rem; flex: 1; max-width: 380px;">
            <input type="text" name="q" class="form-control" placeholder="Search name, username, email..." value="<?= e($search) ?>" style="padding: 0.5rem 1rem;">
            <button type="submit" class="btn btn-secondary">
                Search
            </button>
            <?php if ($search || $planFilter): ?>
                <a href="<?= url('admin/subscriptions') ?>" class="btn btn-outline" title="Reset Filters">✕</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Users & Subscriptions Table -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="background: rgba(255,255,255,0.03); border-bottom: 1px solid var(--border-glass); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <th style="padding: 1rem 1.25rem;">Creator</th>
                    <th style="padding: 1rem;">Username / Bio</th>
                    <th style="padding: 1rem;">Registered</th>
                    <th style="padding: 1rem;">Current Plan</th>
                    <th style="padding: 1rem;">Role</th>
                    <th style="padding: 1rem 1.25rem; text-align: right;">Subscription Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usersList)): ?>
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            No users matched the selected criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usersList as $u): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.15s ease;">
                            <td style="padding: 1rem 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="<?= e($u['avatar_url'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80') ?>" alt="<?= e($u['name']) ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary);"><?= e($u['name']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?= e($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <a href="<?= url('p/' . e($u['username'])) ?>" target="_blank" style="color: #6ee7b7; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    @<?= e($u['username']) ?> <span style="font-size: 0.75rem;">↗</span>
                                </a>
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                                <?= date('M j, Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($u['plan'] === 'agency'): ?>
                                    <span class="badge" style="background: rgba(236,72,153,0.2); color: #ec4899; font-weight: 700; border: 1px solid rgba(236,72,153,0.3);">
                                        🏢 AGENCY ($39)
                                    </span>
                                <?php elseif ($u['plan'] === 'pro'): ?>
                                    <span class="badge badge-pro">
                                        ⚡ PRO ($15)
                                    </span>
                                <?php else: ?>
                                    <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--text-secondary);">
                                        FREE
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($u['role'] === 'superadmin'): ?>
                                    <span class="badge" style="background: rgba(245,158,11,0.2); color: #f59e0b; font-weight: 700;">
                                        👑 SUPERADMIN
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">User</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <!-- Plan Switch Form -->
                                    <form method="POST" action="<?= url('admin/subscriptions') ?>" style="display: inline-block;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update_plan">
                                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                        <select name="plan" onchange="this.form.submit()" style="padding: 0.35rem 0.65rem; border-radius: 6px; background: rgba(255,255,255,0.06); color: var(--text-primary); border: 1px solid var(--border-glass); font-size: 0.82rem; cursor: pointer;">
                                            <option value="free" <?= $u['plan'] === 'free' ? 'selected' : '' ?>>Starter (Free)</option>
                                            <option value="pro" <?= $u['plan'] === 'pro' ? 'selected' : '' ?>>Creator Pro ($15)</option>
                                            <option value="agency" <?= $u['plan'] === 'agency' ? 'selected' : '' ?>>Agency Studio ($39)</option>
                                        </select>
                                    </form>

                                    <!-- Role Toggle Form -->
                                    <?php if ($u['id'] !== Auth::id()): ?>
                                        <form method="POST" action="<?= url('admin/subscriptions') ?>" style="display: inline-block;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="toggle_role">
                                            <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline" style="padding: 0.3rem 0.5rem; font-size: 0.75rem;" title="<?= $u['role'] === 'superadmin' ? 'Revoke Superadmin' : 'Promote to Superadmin' ?>">
                                                <?= $u['role'] === 'superadmin' ? 'Demote' : '👑 Make Admin' ?>
                                            </button>
                                        </form>

                                        <!-- Delete Account Form -->
                                        <form method="POST" action="<?= url('admin/subscriptions') ?>" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to permanently delete this user account?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-secondary" style="padding: 0.3rem 0.5rem; font-size: 0.75rem; color: #ef4444;" title="Delete User Account">
                                                🗑️
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/app_layout_end.php'; ?>
