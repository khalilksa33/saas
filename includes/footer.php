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
                        <?= __('footer_tagline') ?>
                    </p>
                </div>
                <div class="footer-col">
                    <h4><?= __('dash_creator_studio') ?></h4>
                    <ul>
                        <li><a href="<?= url('tools/bio-link') ?>"><?= __('tool_bio_title') ?></a></li>
                        <li><a href="<?= url('tools/thumbnail-studio') ?>"><?= __('tool_thumb_title') ?></a></li>
                        <li><a href="<?= url('tools/copywriter') ?>"><?= __('tool_copy_title') ?></a></li>
                        <li><a href="<?= url('tools/smart-links') ?>"><?= __('tool_links_title') ?></a></li>
                        <li><a href="<?= url('tools/rate-calculator') ?>"><?= __('tool_rates_title') ?></a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?= __('nav_tools') ?></h4>
                    <ul>
                        <li><a href="<?= url('blog') ?>"><?= __('nav_blog', 'Creator Blog & Insights') ?></a></li>
                        <li><a href="<?= url('p/alexcreator') ?>" target="_blank">Demo Creator Page</a></li>
                        <li><a href="<?= url('#pricing') ?>"><?= __('nav_pricing') ?></a></li>
                        <li><a href="<?= url('#faq') ?>"><?= __('nav_faq') ?></a></li>
                        <li><a href="<?= url('DEPLOYMENT.md') ?>"><?= __('footer_guide') ?></a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4><?= __('dash_settings') ?></h4>
                    <ul>
                        <li><a href="<?= url('login') ?>"><?= __('auth_signin') ?></a></li>
                        <li><a href="<?= url('register') ?>"><?= __('auth_signup') ?></a></li>
                        <li><a href="<?= url('dashboard') ?>"><?= __('dash_overview') ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. <?= __('footer_rights') ?></p>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="#"><?= __('footer_privacy') ?></a>
                    <a href="#"><?= __('footer_terms') ?></a>
                    <a href="#"><?= __('footer_security') ?></a>
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
