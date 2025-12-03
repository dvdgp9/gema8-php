<?php
/**
 * Credits badge partial
 */
?>
<div class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-sm">
    <i data-lucide="coins" class="h-4 w-4 mr-1 text-amber-500"></i>
    <span id="creditsCount" class="font-medium"><?= $profile['credits'] ?? 0 ?></span>
    <span class="text-gray-500 ml-1">cr</span>
</div>
