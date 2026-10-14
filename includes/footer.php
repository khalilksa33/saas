<?php
/**
 * Global Footer Component
 */
?>
    <?php if (empty($hideFooter)): ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="brand-logo" style="margin-bottom: 1rem;">
                        <img src="<?= asset('images/logo-icon.svg') ?>" alt="<?= e(APP_NAME) ?>" class="brand-logo-img" width="34" height="34">
                        <span class="brand-text">iLove<span class="brand-highlight">Digital</span></span>
                    </div>
                    <p style="font-size: 0.9rem; line-height: 1.6; max-width: 320px; color: var(--text-secondary);">
                        The all-in-one growth suite for YouTube, Instagram, TikTok, and digital creators. Built for speed, high-conversion, and effortless monetization.
                    </p>
                </div>
                <div class="footer-col">
                    <h4>Creator Suite</h4>
                    <ul>
                        <li><a href="<?= url('tools/bio-link') ?>">Bio-Link Builder</a></li>
                        <li><a href="<?= url('tools/thumbnail-studio') ?>">Thumbnail Studio</a></li>
                        <li><a href="<?= url('tools/copywriter') ?>">Viral Hook Generator</a></li>
                        <li><a href="<?= url('tools/smart-links') ?>">Branded QR & Links</a></li>
                        <li><a href="<?= url('tools/rate-calculator') ?>">Sponsorship Calculator</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="<?= url('p/alexcreator') ?>" target="_blank">Demo Creator Page</a></li>
                        <li><a href="<?= url('#pricing') ?>">Subscription Plans</a></li>
                        <li><a href="<?= url('#faq') ?>">Creator Academy</a></li>
                        <li><a href="<?= url('DEPLOYMENT.md') ?>">Hostinger Setup Guide</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Account</h4>
                    <ul>
                        <li><a href="<?= url('login') ?>">Creator Login</a></li>
                        <li><a href="<?= url('register') ?>">Create Free Account</a></li>
                        <li><a href="<?= url('dashboard') ?>">Creator Dashboard</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved. Crafted for modern digital creators.</p>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Security</a>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Main JS -->
    <script src="<?= asset('js/main.js') ?>?v=<?= APP_VERSION ?>"></script>
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?= asset('js/' . $js) ?>?v=<?= APP_VERSION ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Flash message trigger -->
    <?php if ($successMsg = get_flash('success')): ?>
        <script>document.addEventListener('DOMContentLoaded', () => window.showToast("<?= addslashes($successMsg) ?>", 'success'));</script>
    <?php endif; ?>
    <?php if ($errorMsg = get_flash('error')): ?>
        <script>document.addEventListener('DOMContentLoaded', () => window.showToast("<?= addslashes($errorMsg) ?>", 'error'));</script>
    <?php endif; ?>
</body>
</html>
