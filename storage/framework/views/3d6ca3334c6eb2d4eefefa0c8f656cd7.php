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
            <?php endif; ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/customers/partials/chat-messages.blade.php ENDPATH**/ ?>