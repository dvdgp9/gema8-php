<?php
/**
 * Toast notifications partial
 */

$successMessage = getFlash('success');
$errorMessage = getFlash('error');
$infoMessage = getFlash('info');
?>

<?php if ($successMessage): ?>
<div class="toast toast-success animate-fadeIn">
    <?= e($successMessage) ?>
</div>
<?php endif; ?>

<?php if ($errorMessage): ?>
<div class="toast toast-error animate-fadeIn">
    <?= e($errorMessage) ?>
</div>
<?php endif; ?>

<?php if ($infoMessage): ?>
<div class="toast toast-info animate-fadeIn">
    <?= e($infoMessage) ?>
</div>
<?php endif; ?>
