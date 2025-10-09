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
    <?php endif; ?>

    
    <?php if(($interactive['type'] ?? '') === 'list' && data_get($interactive, 'action.sections')): ?>
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
    <?php $reply = $interactive[$interactive['type']] ?? []; ?>
    <div class="italic text-sm">
        Selected: <span class="font-semibold"><?php echo e(data_get($reply, 'title', data_get($reply, 'id'))); ?></span>
    </div>
<?php else: ?>
    <div><?php echo e($message->content); ?></div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/chatbox/partials/_interactive_message.blade.php ENDPATH**/ ?>