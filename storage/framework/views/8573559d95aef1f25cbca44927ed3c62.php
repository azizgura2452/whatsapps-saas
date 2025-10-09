<?php $__env->startPush('styles'); ?>
  <style>
    /* Force outgoing (your) bubble background like WhatsApp */
    :root {
      --wa-outgoing: #DCF8C6;
    }

    /* light green */
    .dark :root {
      --wa-outgoing: #075E54;
    }

    /* dark mode green */
    .wa-outgoing {
      background-color: var(--wa-outgoing) !important;
    }
  </style>
<?php $__env->stopPush(); ?>

<?php
  $lastMessageDate = null;
  $lastTs = (int) ($messages->last()->timestamp ?? 0);
  $appTimezone = config('app.timezone');
?>


<div class="flex flex-col" style="height:90vh;">

  
  <div
    class="flex items-center justify-between px-4 py-2 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 grid place-items-center font-semibold"
        style="justify-content:center;align-items:center;font-size:24px;">
        <i class="fa fa-user"></i>
      </div>
      <div class="leading-tight">
        <div class="font-semibold">
          <?php echo e($customer->name ? ucwords($customer->name) : $customer->whatsapp_number); ?>

        </div>
      </div>
    </div>
  </div>

  
  <div id="chat-container" class="p-4 md:p-5" 
    data-last-timestamp="<?php echo e($lastTs); ?>" style="
      flex: 1 1 auto;                      /* fill remaining height */
      overflow-y: auto;                    /* make this the scroller */
      background-color:#F0F2F5;
      background-image:url('<?php echo e(asset('images/' . ltrim('whatsapp_bg.jpg', '/'))); ?>'),
                       radial-gradient(rgba(0,0,0,0.03) 1px, transparent 1px);
      background-repeat:repeat;
      background-size:40%, 6px 6px;
      background-position:center, 0 0;
    ">
    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php
        $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)
            ->timezone($appTimezone)
            ->toDateString();
      ?>

      
      <?php if($lastMessageDate !== $messageDate): ?>
        <div class="text-center my-4">
          <span
            class="inline-block bg-gray-200/80 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs px-3 py-1 rounded-full">
            <?php echo e(\Carbon\Carbon::parse($messageDate)->timezone($appTimezone)->format('F j, Y')); ?>

          </span>
        </div>
        <?php $lastMessageDate = $messageDate; ?>
      <?php endif; ?>

      
      <div class="w-full mb-2 flex <?php echo e($message->direction === 'inbound' ? 'justify-start' : 'justify-end'); ?>">
        <div class="relative max-w-[75%] text-[15px] leading-snug px-3 py-2 rounded-lg shadow-sm
                          <?php echo e($message->direction === 'inbound'
      ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none'
      : 'wa-outgoing text-gray-900 dark:text-white rounded-br-none'); ?>">
          <?php $raw = json_decode($message->raw_data ?? '[]', true) ?: []; ?>

          
          <?php if(isset($raw['image']) || isset($raw['video']) || isset($raw['document']) || isset($raw['audio'])): ?>
            <?php echo $__env->make('backend.pages.chatbox.partials._media_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


            
          <?php elseif(isset($raw['type']) && $raw['type'] === 'interactive'): ?>
            <?php $interactive = $raw['interactive'] ?? []; ?>
            <?php if(in_array($interactive['type'] ?? '', ['button', 'list'], true)): ?>
              <?php if(data_get($interactive, 'header.text')): ?>
                <div class="font-semibold mb-1 text-sm"><?php echo e(data_get($interactive, 'header.text')); ?></div>
              <?php endif; ?>
              <?php if(data_get($interactive, 'body.text')): ?>
                <div class="mb-2"><?php echo e(data_get($interactive, 'body.text')); ?></div>
              <?php endif; ?>
              <?php if(($interactive['type'] ?? '') === 'button' && data_get($interactive, 'action.buttons')): ?>
                <div class="flex flex-wrap gap-2">
                  <?php $__currentLoopData = data_get($interactive, 'action.buttons', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                      <?php echo e(data_get($button, 'reply.title')); ?>

                    </button>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              <?php elseif(($interactive['type'] ?? '') === 'list' && data_get($interactive, 'action.sections')): ?>
                <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                  <div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">
                    <?php echo e(data_get($interactive, 'action.button', 'Options')); ?>

                  </div>
                  <ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">
                    <?php $__currentLoopData = data_get($interactive, 'action.sections', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php $__currentLoopData = data_get($section, 'rows', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e(data_get($row, 'title')); ?></li>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </div>
              <?php endif; ?>
            <?php elseif(in_array($interactive['type'] ?? '', ['button_reply', 'list_reply'], true)): ?>
              <?php echo $__env->make('backend.pages.chatbox.partials._interactive_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php else: ?>
              <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                <?php echo e($message->content); ?>

              </div>
            <?php endif; ?>

            
          <?php elseif(isset($raw['type']) && $raw['type'] === 'template'): ?>
            <?php echo $__env->make('backend.pages.chatbox.partials._template_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


            
          <?php else: ?>
            <div class="whitespace-pre-line"><?php echo e($message->content); ?></div>
          <?php endif; ?>

          
          <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-300 text-right">
            <?php echo e(\Carbon\Carbon::createFromTimestamp($message->timestamp)->timezone($appTimezone)->format('h:i A')); ?>

          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p class="text-center text-sm text-gray-500 dark:text-gray-400 p-6"><?php echo e(__('No messages found.')); ?></p>
    <?php endif; ?>
  </div>

  
  <div class="px-3 py-2 bg-[#F0F2F5] dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="flex items-center gap-2">
      <form class="flex-1 flex items-center gap-2" onsubmit="return false;">
        <input id="chat-message" type="text" placeholder="Type a message..."
          class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-2 text-sm"
          onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();sendMessage(<?php echo e($customer->id); ?>);}">
        <button type="button" id="sendBtn" class="btn-primary" onclick="sendMessage(<?php echo e($customer->id); ?>)">
          <i class="bi bi-send"></i>
        </button>
      </form>
    </div>
  </div>

</div><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/_chat.blade.php ENDPATH**/ ?>