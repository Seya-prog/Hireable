<?php
/**
 * Toast / Flash Message Component
 * Include on any page after session.php is loaded.
 * Displays once and auto-dismisses after 4 seconds.
 */
$flash = getFlash();
if ($flash):
    $type = $flash['type'] === 'success' ? 'success' : 'error';
    $icon = $type === 'success' ? 'check_circle' : 'error';
?>
<div class="toast toast--<?= $type ?>" id="toast-message">
    <span class="material-symbols-outlined toast-icon"><?= $icon ?></span>
    <span class="toast-text"><?= htmlspecialchars($flash['message']) ?></span>
    <button class="toast-close">
        <span class="material-symbols-outlined">close</span>
    </button>
</div>
<script src="../../public/assets/js/toast.js"></script>
<?php endif; ?>
