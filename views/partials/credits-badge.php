<?php
/**
 * Credits badge partial
 */
?>
<div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/80 backdrop-blur rounded-full text-sm shadow-sm">
    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
        <i data-lucide="zap" class="h-3 w-3 text-white"></i>
    </div>
    <span id="creditsCount" class="font-bold text-slate-700"><?= $profile['credits'] ?? 0 ?></span>
    <span class="text-slate-400 text-xs font-medium">credits</span>
</div>
