<?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    // Basic fields
    $title   = $c->name ? ucwords($c->name) : $c->whatsapp_number;

    // Find latest message content (first non-empty candidate)
    $rawPreview = '';
    $lastMsgCollection = data_get($c, 'whatsappConversation.messages');
    $lastMsgObject = $lastMsgCollection ? $lastMsgCollection->last() : null;

    $previewCandidates = [
        $c->last_message ?? null,
        data_get($c, 'lastMessage.content'),
        data_get($c, 'last_message_content'),
        $lastMsgObject ? ($lastMsgObject->content ?? null) : null,
    ];

    foreach ($previewCandidates as $cand) {
        $candStr = trim((string)($cand ?? ''));
        if ($candStr !== '') { $rawPreview = $candStr; break; }
    }

    // Clean + limit preview
    $preview = trim(\Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', strip_tags((string)$rawPreview)), 70));
    if ($preview === '') {
        $preview = __('No messages yet');
    }

    // Resolve time (prefer provided, else from latest message object)
    $lastAt = $c->last_at;
    if (!$lastAt) { $lastAt = data_get($c, 'lastMessage.created_at'); }
    if (!$lastAt && $lastMsgObject) { $lastAt = $lastMsgObject->created_at ?? $lastMsgObject->timestamp ?? null; }

    try {
        if ($lastAt) {
            if (is_numeric($lastAt)) {
                $time = \Carbon\Carbon::createFromTimestamp((int)$lastAt)->format('h:i A');
            } else {
                $time = \Carbon\Carbon::parse($lastAt)->format('h:i A');
            }
        } else {
            $time = '';
        }
    } catch (\Throwable $e) {
        $time = '';
    }

    $unread = (int)($c->unread_count ?? 0);
?>

<button
  data-customer-id="<?php echo e($c->id); ?>"
  class="chatbox-customer-btn w-full text-left px-3 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800
         <?php echo e($c->id == $selectedId ? 'bg-gray-50 dark:bg-gray-800' : ''); ?>

         min-h-[68px] py-3">

  
  <div class="flex-shrink-0">
    <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 grid place-items-center
                text-gray-700 dark:text-gray-200" style="justify-content: center; align-items: center;font-size: 24px;">
        <i class="fa fa-user"></i>
    </div>
  </div>

  
  <div class="min-w-0 flex-1">
    <div class="flex items-center gap-2">
      <div class="font-medium text-gray-900 dark:text-white truncate"><?php echo e($title); ?></div>
      <div class="ml-auto text-[11px] text-gray-500 dark:text-gray-400 flex-shrink-0" style="font-size: 11px"><?php echo e($time); ?></div>
    </div>
    <div class="flex items-center gap-2">
      <div class="text-xs text-gray-500 dark:text-gray-400 truncate min-w-0">
        <?php echo e($preview); ?>

      </div>
      <?php if($unread > 0): ?>
        <span class="ml-auto inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full
                     text-[10px] bg-[var(--wa-teal)] text-white flex-shrink-0"><?php echo e($unread); ?></span>
      <?php endif; ?>
    </div>
  </div>
</button>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/_customers.blade.php ENDPATH**/ ?>