<?php
/**
 * Application Layout End
 */
?>
        </main>
    </div>
</div>

<!-- Main JS -->
<script src="<?= asset('js/main.js') ?>?v=<?= APP_VERSION ?>"></script>
<?php if (isset($extraJs)): ?>
    <?php foreach ($extraJs as $js): ?>
        <script src="<?= asset('js/' . $js) ?>?v=<?= APP_VERSION ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Flash Toast Notification Handler -->
<?php if ($successMsg = get_flash('success')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => window.showToast("<?= addslashes($successMsg) ?>", 'success'));</script>
<?php endif; ?>
<?php if ($errorMsg = get_flash('error')): ?>
    <script>document.addEventListener('DOMContentLoaded', () => window.showToast("<?= addslashes($errorMsg) ?>", 'error'));</script>
<?php endif; ?>

</body>
</html>
