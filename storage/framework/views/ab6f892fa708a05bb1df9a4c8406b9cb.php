

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Conversation History')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-7xl md:p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <?php echo e(__('Conversation with')); ?> <?php echo e(ucwords($customer->name)); ?>

            </h2>
            <nav>
                <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                    <li>
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="inline-flex items-center gap-1.5">
                            <?php echo e(__('Home')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.customers.index')); ?>" class="inline-flex items-center gap-1.5">
                            <?php echo e(__('Customers')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-gray-800 dark:text-white/90"><?php echo e(ucwords($customer->name)); ?></li>
                </ol>
            </nav>
        </div>


        <div class="border border-gray-200 dark:border-gray-700 rounded-2xl bg-white dark:bg-white/[0.03] max-h-[70vh] overflow-y-auto"
            style="background-image: url('<?php echo e(asset('images/' . ltrim('whatsapp_bg.jpg', '/'))); ?>'); background-position: center; background-size: 40%; background-attachment: fixed;
                                            background-color: #003a00;">
            <div class="flex items-center gap-4 p-4 bg-green-600 text-white rounded-t-2xl shadow-sm"
                style="background-color: #128c7e; color: #fff">
                <div class="flex-shrink-0">
                    <?php if(!empty($customer->name)): ?>
                        <div
                            class="w-12 h-12 rounded-full flex items-center justify-center font-semibold text-2xl text-white border-2 border-white">
                            <?php echo e(strtoupper(substr($customer->name, 0, 1))); ?>

                        </div>
                    <?php else: ?>
                        <div class="w-12 h-12 rounded-full border-2 border-white"></div>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="text-lg font-semibold leading-snug">
                        <?php echo e(!empty($customer->name) ? ucwords($customer->name) : $customer->whatsapp_number); ?>

                    </div>
                    <?php if(!empty($customer->name)): ?>
                        <div class="text-sm text-white/90">
                            <?php echo e($customer->whatsapp_number); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="chat-container">
                <?php
                    $lastMessageDate = null;
                ?>

                <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $messageDate = \Carbon\Carbon::createFromTimestamp($message->timestamp)->toDateString();
                        ?>

                        <?php if($lastMessageDate !== $messageDate): ?>
                            <div class="text-center my-4">
                                <span
                                    class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs px-3 py-1 rounded-full">
                                    <?php echo e(\Carbon\Carbon::parse($messageDate)->format('F j, Y')); ?>

                                </span>
                            </div>
                            <?php
                                $lastMessageDate = $messageDate;
                            ?>
                        <?php endif; ?>
                        <div class="p-4 w-full mb-4 flex <?php echo e($message->direction === 'inbound' ? 'justify-start' : 'justify-end'); ?>">
                            <div class="max-w-[75%] px-4 py-2 rounded-xl shadow-sm text-sm leading-relaxed whitespace-pre-line
                                                                                                                                                        <?php echo e($message->direction === 'inbound'
                    ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-bl-none'
                    : 'bg-green-100 text-gray-900 dark:bg-green-800 dark:text-white rounded-br-none'); ?>">
                                <?php
                                    $raw = json_decode($message->raw_data, true);
                                ?>

                                <?php if(isset($raw['type']) && $raw['type'] === 'interactive'): ?>
                                    <?php
                                        $interactive = $raw['interactive'];
                                    ?>

                                    
                                    <?php if(in_array($interactive['type'], ['button', 'list'])): ?>
                                        
                                        <?php if(isset($interactive['header']['text'])): ?>
                                            <div class="font-semibold mb-1 text-sm">
                                                <?php echo e($interactive['header']['text']); ?>

                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if(isset($interactive['body']['text'])): ?>
                                            <div class="mb-2">
                                                <?php echo e($interactive['body']['text']); ?>

                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($interactive['type'] === 'button' && isset($interactive['action']['buttons'])): ?>
                                            <div class="flex flex-wrap gap-2">
                                                <?php $__currentLoopData = $interactive['action']['buttons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">
                                                        <?php echo e($button['reply']['title']); ?>

                                                    </button>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>

                                            
                                        <?php elseif($interactive['type'] === 'list' && isset($interactive['action']['sections'])): ?>
                                            <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                                                <div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">
                                                    <?php echo e($interactive['action']['button'] ?? 'Options'); ?>

                                                </div>
                                                <ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">
                                                    <?php $__currentLoopData = $interactive['action']['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php $__currentLoopData = $section['rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li>
                                                                <?php echo e($row['title']); ?>

                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                        
                                    <?php elseif(in_array($interactive['type'], ['button_reply', 'list_reply'])): ?>
                                        <?php
                                            $reply = $interactive[$interactive['type']];
                                        ?>
                                        <div class="italic text-sm text-gray-800 dark:text-gray-100">
                                            Selected: <span class="font-semibold"><?php echo e($reply['title'] ?? $reply['id']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div><?php echo e($message->content); ?></div>
                                    <?php endif; ?>
                                <?php elseif(isset($raw['type']) && $raw['type'] === 'template' && isset($raw['template']['components'])): ?>
                                    <?php
                                        $mpmComponent = collect($raw['template']['components'])
                                            ->first(fn($comp) => $comp['type'] === 'button' && ($comp['sub_type'] ?? null) === 'mpm');
                                    ?>

                                    <?php if($mpmComponent && isset($mpmComponent['parameters'][0]['action']['sections'])): ?>
                                        <div class="mb-2 font-semibold text-sm text-gray-800 dark:text-gray-100">
                                            Multi-Product Message (MPM):
                                        </div>
                                        <?php $__currentLoopData = $mpmComponent['parameters'][0]['action']['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1">
                                                <strong><?php echo e($section['title'] ?? 'Section'); ?>:</strong>
                                            </div>
                                            <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">
                                                <ul class="text-sm text-gray-800 dark:text-gray-100 mb-3">
                                                    <?php $__currentLoopData = $section['product_items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li><?php echo e($product['product_retailer_id']); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div><?php echo e($message->content); ?></div>
                                <?php endif; ?>

                                <div class="text-end text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?php echo e(\Carbon\Carbon::createFromTimestamp($message->timestamp)->format('Y-m-d H:i')); ?>

                                </div>
                            </div>
                        </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400"><?php echo e(__('No messages found.')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<script>
    let lastTimestamp = <?php echo e($messages->last()?->timestamp ?? 0); ?>;
    const fetchUrl = "<?php echo e(route('admin.customers.messages', $customer->id)); ?>";

    function formatMessage(message) {
        const container = document.createElement('div');
        container.className = 'p-4 w-full mb-4 flex ' + (message.direction === 'inbound' ? 'justify-start' : 'justify-end');

        const bubble = document.createElement('div');
        bubble.className =
            'max-w-[75%] px-4 py-2 rounded-xl shadow-sm text-sm leading-relaxed whitespace-pre-line ' +
            (message.direction === 'inbound'
                ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100 rounded-bl-none'
                : 'bg-green-100 text-gray-900 dark:bg-green-800 dark:text-white rounded-br-none');

        let content = '';
        let raw = {};

        try {
            raw = typeof message.raw_data === 'string' ? JSON.parse(message.raw_data) : message.raw_data;
        } catch (e) {
            raw = {};
        }

        if (raw.type === 'interactive') {
            const interactive = raw.interactive;

            if (['button', 'list'].includes(interactive.type)) {
                if (interactive.header?.text) {
                    content += `<div class="font-semibold mb-1 text-sm">${interactive.header.text}</div>`;
                }
                if (interactive.body?.text) {
                    content += `<div class="mb-2">${interactive.body.text}</div>`;
                }

                if (interactive.type === 'button' && interactive.action?.buttons) {
                    content += '<div class="flex flex-wrap gap-2">';
                    interactive.action.buttons.forEach(button => {
                        content += `<button class="px-3 py-1 rounded bg-blue-600 text-white text-sm cursor-default">${button.reply.title}</button>`;
                    });
                    content += '</div>';
                }

                if (interactive.type === 'list' && interactive.action?.sections) {
                    content += '<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">';
                    content += `<div class="font-medium text-sm text-gray-700 dark:text-gray-200 mb-2">${interactive.action.button ?? 'Options'}</div>`;
                    content += '<ul class="space-y-1 text-sm text-gray-800 dark:text-gray-100">';
                    interactive.action.sections.forEach(section => {
                        section.rows.forEach(row => {
                            content += `<li>${row.title}</li>`;
                        });
                    });
                    content += '</ul></div>';
                }
            } else if (['button_reply', 'list_reply'].includes(interactive.type)) {
                const reply = interactive[interactive.type];
                content += `<div class="italic text-sm text-gray-800 dark:text-gray-100">Selected: <span class="font-semibold">${reply.title ?? reply.id}</span></div>`;
            } else {
                content += `<div>${message.content}</div>`;
            }
        } else if (raw.type === 'template' && Array.isArray(raw.template?.components)) {
            const mpmComponent = raw.template.components.find(comp => comp.type === 'button' && comp.sub_type === 'mpm');
            if (mpmComponent?.parameters?.[0]?.action?.sections) {
                content += '<div class="mb-2 font-semibold text-sm text-gray-800 dark:text-gray-100">Multi-Product Message (MPM):</div>';
                mpmComponent.parameters[0].action.sections.forEach(section => {
                    content += `<div class="text-sm text-gray-700 dark:text-gray-300 mb-1"><strong>${section.title ?? 'Section'}:</strong></div>`;
                    content += '<div class="border border-gray-300 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 dark:border-gray-600">';
                    content += '<ul class="text-sm text-gray-800 dark:text-gray-100 mb-3">';
                    section.product_items.forEach(product => {
                        content += `<li>${product.product_retailer_id}</li>`;
                    });
                    content += '</ul></div>';
                });
            } else {
                content += `<div>${message.content}</div>`;
            }
        } else {
            content += `<div>${message.content}</div>`;
        }

        content += `<div class="text-end text-xs text-gray-500 dark:text-gray-400 mt-1">${new Date(message.timestamp * 1000).toLocaleString()}</div>`;

        bubble.innerHTML = content;
        container.appendChild(bubble);
        return container;
    }


    function fetchMessages() {
        fetch(`${fetchUrl}?since=${lastTimestamp}`)
            .then(response => response.json())
            .then(data => {
                const newMessages = data.messages;
                if (newMessages.length > 0) {
                    lastTimestamp = newMessages.at(-1).timestamp;

                    const chatContainer = document.querySelector('#chat-container');

                    const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 50;

                    newMessages.forEach(msg => {
                        chatContainer.appendChild(formatMessage(msg));
                    });

                    // Only scroll to bottom if user was already near the bottom
                    if (isNearBottom) {
                        requestAnimationFrame(() => {
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                        });
                    }
                }
            })
            .catch(error => console.error("Message fetch failed:", error));
    }

    setInterval(fetchMessages, 5000); // poll every 5 seconds
</script>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/customers/chat.blade.php ENDPATH**/ ?>