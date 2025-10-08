

<?php $__env->startSection('title'); ?>
    Broadcasts - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold"><?php echo e(__('Broadcasts')); ?></h1>
            <a href="<?php echo e(route('admin.broadcasts.create')); ?>" class="btn-primary">
                <i class="bi bi-plus-circle mr-2"></i>
                <?php echo e(__('New Broadcast')); ?>

            </a>
        </div>

        <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-xl">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="p-3 text-left"><?php echo e(__('ID')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Title')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('WhatsApp Template')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Recipient Source')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Recipients')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Success Rate')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Created')); ?></th>
                        <th class="p-3 text-left"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t dark:border-gray-800">
                            <td class="p-3"><?php echo e($broadcast->id); ?></td>
                            <td class="p-3"><?php echo e($broadcast->broadcast_title ?? '-'); ?></td>
                            <td class="p-3"><?php echo e($broadcast->whatsapp_template_name ?? '-'); ?></td>
                            <td class="p-3">
                                <?php if($broadcast->broadcast_group_id): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="bi bi-people mr-1"></i>
                                        <?php echo e(__('Group')); ?>

                                    </span>
                                <?php elseif($broadcast->custom_recipients): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        <i class="bi bi-list-ul mr-1"></i>
                                        <?php echo e(__('Custom')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="bi bi-globe mr-1"></i>
                                        <?php echo e(__('All')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php if($broadcast->broadcast_group_id): ?>
                                    <span class="cursor-pointer underline decoration-dotted text-blue-600 dark:text-blue-400"
                                        onclick="showGroupInfo(<?php echo e($broadcast->broadcastGroup->id); ?>, '<?php echo e($broadcast->broadcastGroup->name); ?>', <?php echo e($broadcast->broadcastGroup->getCustomerCount()); ?>)">
                                        <?php echo e($broadcast->broadcastGroup->name); ?> (<?php echo e($broadcast->broadcastGroup->getCustomerCount()); ?>)
                                    </span>
                                <?php elseif($broadcast->custom_recipients): ?>
                                    <span class="cursor-pointer underline decoration-dotted text-blue-600 dark:text-blue-400"
                                        onclick="showRecipients(<?php echo e(json_encode(explode(',', $broadcast->custom_recipients))); ?>)">
                                        <?php echo e(__('View Recipients')); ?> (<?php echo e(count(explode(',', $broadcast->custom_recipients))); ?>)
                                    </span>
                                <?php else: ?>
                                    <?php echo e(__('All Customers')); ?>

                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php
                                    $rate = $broadcast->getSuccessRate();
                                    $rateColor = $rate >= 80 ? 'text-green-600' : ($rate >= 50 ? 'text-yellow-600' : 'text-red-600');
                                ?>
                                <span class="font-semibold <?php echo e($rateColor); ?>"><?php echo e($rate); ?>%</span>
                                <div class="w-20 bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($rate); ?>%"></div>
                                </div>
                            </td>
                            <td class="p-3"><?php echo e($broadcast->created_at->format('Y-m-d H:i')); ?></td>
                            <td class="p-3">
                                <a href="<?php echo e(route('admin.broadcasts.report', $broadcast->id)); ?>" 
                                   class="btn-default inline-flex items-center gap-2"
                                   title="<?php echo e(__('View Report')); ?>">
                                    <i class="bi bi-bar-chart"></i>
                                    <span><?php echo e(__('Report')); ?></span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4"><?php echo e($broadcasts->links()); ?></div>
    </div>

    
    <div id="recipientsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 w-[95%] max-w-md rounded-xl shadow-xl p-6 relative">
            <button onclick="closeRecipients()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            <h2 class="text-lg font-semibold mb-4 dark:text-white"><?php echo e(__('Custom Recipients')); ?></h2>
            <div class="overflow-x-auto max-h-[400px] overflow-y-auto border rounded dark:border-gray-700">
                <table class="min-w-full border text-sm dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-2 text-left border-b dark:border-gray-600 dark:text-gray-300">#</th>
                            <th class="p-2 text-left border-b dark:border-gray-600 dark:text-gray-300"><?php echo e(__('Phone Number')); ?></th>
                        </tr>
                    </thead>
                    <tbody id="recipientsTableBody" class="dark:text-gray-300"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="groupInfoModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 w-[95%] max-w-md rounded-xl shadow-xl p-6 relative">
            <button onclick="closeGroupInfo()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            <h2 class="text-lg font-semibold mb-4 dark:text-white"><?php echo e(__('Broadcast Group')); ?></h2>
            <div class="space-y-3">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e(__('Group Name')); ?></p>
                    <p class="font-medium dark:text-white" id="groupName"></p>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e(__('Total Customers')); ?></p>
                    <p class="font-medium dark:text-white" id="groupCount"></p>
                </div>
                <a href="#" id="viewGroupLink" class="btn-primary w-full text-center block">
                    <i class="bi bi-eye mr-2"></i>
                    <?php echo e(__('View Group Details')); ?>

                </a>
            </div>
        </div>
    </div>

    <script>
        function showRecipients(numbers) {
            const tableBody = document.getElementById('recipientsTableBody');
            tableBody.innerHTML = '';
            numbers.forEach((num, index) => {
                const row = `<tr class="border-t dark:border-gray-700">
                                <td class="p-2">${index + 1}</td>
                                <td class="p-2">${num.trim()}</td>
                            </tr>`;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
            document.getElementById('recipientsModal').classList.remove('hidden');
        }

        function closeRecipients() {
            document.getElementById('recipientsModal').classList.add('hidden');
        }

        function showGroupInfo(groupId, groupName, count) {
            document.getElementById('groupName').textContent = groupName;
            document.getElementById('groupCount').textContent = count;
            document.getElementById('viewGroupLink').href = `/admin/broadcast-groups/${groupId}/edit`;
            document.getElementById('groupInfoModal').classList.remove('hidden');
        }

        function closeGroupInfo() {
            document.getElementById('groupInfoModal').classList.add('hidden');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/broadcasts/index.blade.php ENDPATH**/ ?>