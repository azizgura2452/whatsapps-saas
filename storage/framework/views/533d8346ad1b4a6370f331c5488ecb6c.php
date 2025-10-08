<?php $lastMessageDate = null; ?>

<?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)->toDateString();
        $raw = json_decode($message->raw_data ?? '[]', true) ?: [];
    ?>

    
    <div class="w-full mb-2 flex <?php echo e($message->direction === 'inbound' ? 'justify-start' : 'justify-end'); ?>">
        <div class="relative max-w-[75%] text-[15px] leading-snug px-3 py-2 rounded-lg shadow-sm
                <?php echo e($message->direction === 'inbound'
            ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none'
            : 'wa-outgoing text-gray-900 dark:text-white rounded-br-none'); ?>">

            
            <?php if(isset($raw['image']) || isset($raw['video']) || isset($raw['document']) || isset($raw['audio'])): ?>
                <?php echo $__env->make('backend.pages.chatbox.partials._media_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
            <?php elseif(isset($raw['type']) && $raw['type'] === 'interactive'): ?>
                <?php echo $__env->make('backend.pages.chatbox.partials._interactive_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                

                
            <?php elseif(isset($raw['type']) && $raw['type'] === 'template'): ?>
                <?php echo $__env->make('backend.pages.chatbox.partials._template_message', ['raw' => $raw, 'message' => $message], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                
                

                
            <?php else: ?>
                <div class="whitespace-pre-line"><?php echo e($message->content); ?></div>
            <?php endif; ?>

            
            <div class="mt-1 text-[10px] text-gray-500 dark:text-gray-300 text-right">
                <?php echo e(\Carbon\Carbon::createFromTimestamp($message->timestamp)->format('h:i A')); ?>

            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/_messages.blade.php ENDPATH**/ ?>