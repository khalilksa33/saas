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

$sql = "SELECT id, name, username, email, google_id, plan, role, avatar_url, created_at FROM users WHERE 1=1";
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
                                        <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                            <span><?= e($u['email']) ?></span>
                                            <?php if (!empty($u['google_id'])): ?>
                                                <span title="Signed up with Google" style="display: inline-flex; align-items: center; background: rgba(66, 133, 244, 0.15); border: 1px solid rgba(66, 133, 244, 0.3); border-radius: 4px; padding: 1px 4px; font-size: 0.7rem; color: #93c5fd;">
                                                    <svg width="10" height="10" viewBox="0 0 48 48" style="margin-right: 2px;"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg> Google
                                                </span>
                                            <?php endif; ?>
                                        </div>
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
