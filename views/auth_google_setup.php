<?php
/**
 * Google OAuth 2.0 Direct Onboarding & Setup Guide
 * iLoveDigital SaaS Platform
 */

$pageTitle = 'Sign in with Google — ' . APP_NAME;
$hideNav = false;
$hideFooter = true;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/google_auth.php';
require_once __DIR__ . '/../includes/header.php';

$redirectUri = GOOGLE_REDIRECT_URI;
$originUri = APP_URL;
$isConfigured = GoogleAuth::isConfigured();
$intent = $_GET['intent'] ?? 'login';
?>

<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 2.5rem 1rem;">
    <div class="card" style="width: 100%; max-width: 580px; padding: 2.5rem 2rem; position: relative;">

        <!-- Brand & Google Header -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 16px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-glass); margin-bottom: 1rem; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);">
                <svg width="32" height="32" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
            </div>
            <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 0.5rem;">
                <?= $intent === 'register' ? __('auth_google_signup', 'Sign up with Google') : __('auth_google_continue', 'Continue with Google') ?>
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Fast, secure 1-click registration &amp; login for all digital creators
            </p>
        </div>

        <?php if ($isConfigured): ?>
            <!-- Live OAuth Redirect Button -->
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <p style="margin-bottom: 1rem; color: #10b981; font-weight: 600; font-size: 0.95rem;">
                    ✓ Google OAuth 2.0 Credentials Configured &amp; Active
                </p>
                <a href="<?= e(GoogleAuth::getAuthUrl($intent)) ?>" class="btn" style="width: 100%; background: #ffffff; color: #1f2937; font-weight: 600; padding: 0.85rem; border: 1px solid #e5e7eb; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
                    <svg width="20" height="20" viewBox="0 0 48 48">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.79l7.97-6.2z"/>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    </svg>
                    <span>Proceed to Official Google Accounts</span>
                </a>
            </div>
        <?php else: ?>
            <!-- Instant 1-Click Simulation / Testing Suite -->
            <div style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="font-weight: 700; font-size: 0.92rem; color: #c7d2fe;">⚡ Instant Gmail Onboarding Test</span>
                    <span class="badge badge-pro">READY NOW</span>
                </div>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5;">
                    Test the complete Google registration, auto-username generator, and creator bio seeding immediately:
                </p>

                <!-- Primary Khalil Gmail 1-Click Button -->
                <form method="POST" action="<?= url('auth/google/sim') ?>" style="margin-bottom: 0.75rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="khalilksa33@gmail.com">
                    <input type="hidden" name="name" value="Khalil Al-Saeed">
                    <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem;">
                        <span>Continue as</span>
                        <strong>khalilksa33@gmail.com</strong>
                        <span style="font-size: 0.75rem; background: rgba(0,0,0,0.25); padding: 2px 6px; border-radius: 4px;">Superadmin</span>
                    </button>
                </form>

                <!-- Custom Gmail Input for New User Testing -->
                <form method="POST" action="<?= url('auth/google/sim') ?>" style="display: flex; gap: 0.5rem;">
                    <?= csrf_field() ?>
                    <input type="email" name="email" class="form-control" placeholder="testcreator@gmail.com" required style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <button type="submit" class="btn btn-secondary" style="white-space: nowrap; font-size: 0.85rem; padding: 0.5rem 1rem;">
                        Test Any Gmail →
                    </button>
                </form>
            </div>

            <!-- Credentials Setup Guide -->
            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-glass); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>⚙️</span> Connecting Official Google Cloud OAuth
                </h4>
                <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 0.75rem; line-height: 1.5;">
                    To enable live Google redirection for public visitors, create credentials in Google Cloud Console:
                </p>

                <div style="font-size: 0.8rem; margin-bottom: 0.6rem;">
                    <div style="color: var(--text-muted); margin-bottom: 0.25rem;">Authorized JavaScript Origin:</div>
                    <code style="display: block; padding: 0.5rem 0.75rem; background: rgba(0,0,0,0.3); border-radius: 6px; color: #a5b4fc; word-break: break-all;"><?= e($originUri) ?></code>
                </div>

                <div style="font-size: 0.8rem; margin-bottom: 0.75rem;">
                    <div style="color: var(--text-muted); margin-bottom: 0.25rem;">Authorized Redirect URI:</div>
                    <code style="display: block; padding: 0.5rem 0.75rem; background: rgba(0,0,0,0.3); border-radius: 6px; color: #34d399; word-break: break-all;"><?= e($redirectUri) ?></code>
                </div>

                <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                    Then add <strong style="color: #fff;">GOOGLE_CLIENT_ID</strong> and <strong style="color: #fff;">GOOGLE_CLIENT_SECRET</strong> to your <code style="color: #a5b4fc;">.env</code> file.
                </p>
            </div>
        <?php endif; ?>

        <!-- Footer link back to standard login -->
        <div style="text-align: center; font-size: 0.88rem; color: var(--text-secondary);">
            <a href="<?= url('login') ?>" style="color: var(--primary); font-weight: 600; text-decoration: none;">
                ← <?= __('auth_or_email', 'Return to Email & Password Sign In') ?>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
