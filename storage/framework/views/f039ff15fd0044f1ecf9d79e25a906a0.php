

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Broadcasts')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <?php echo e(__('Broadcasts')); ?>

            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                           href="<?php echo e(route('admin.dashboard')); ?>">
                            <?php echo e(__('Home')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Broadcasts')); ?></li>
                </ol>
            </nav>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        <?php echo e(__('Broadcast List')); ?>

                    </h3>
                    <a href="<?php echo e(route('admin.broadcasts.create')); ?>" class="btn-primary">
                        <i class="bi bi-plus-circle mr-2"></i>
                        <?php echo e(__('New Broadcast')); ?>

                    </a>
                </div>

                <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <table class="w-full dark:text-gray-400">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="5%">
                                    <?php echo e(__('#')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Title')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Template')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Source')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Recipients')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Status')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Success Rate')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <?php echo e(__('Created')); ?>

                                </th>
                                <th class="p-3 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="12%">
                                    <?php echo e(__('Actions')); ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4"><?php echo e($broadcasts->firstItem() + $loop->index); ?></td>
                                    <td class="px-5 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            <?php echo e($broadcast->broadcast_title ?? '-'); ?>

                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                            <?php echo e($broadcast->whatsapp_template_name ?? '-'); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
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
                                    <td class="px-5 py-4">
                                        <?php if($broadcast->broadcast_group_id): ?>
                                            <button type="button"
                                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                                                onclick="showGroupInfo(<?php echo e($broadcast->broadcastGroup->id); ?>, '<?php echo e(addslashes($broadcast->broadcastGroup->name)); ?>', <?php echo e($broadcast->broadcastGroup->getCustomerCount()); ?>)">
                                                <?php echo e($broadcast->broadcastGroup->name); ?>

                                                <span class="text-gray-500">(<?php echo e($broadcast->broadcastGroup->getCustomerCount()); ?>)</span>
                                            </button>
                                        <?php elseif($broadcast->custom_recipients): ?>
                                            <button type="button"
                                                class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
                                                onclick="showRecipients(<?php echo e(json_encode(explode(',', $broadcast->custom_recipients))); ?>)">
                                                <?php echo e(__('View List')); ?>

                                                <span class="text-gray-500">(<?php echo e(count(explode(',', $broadcast->custom_recipients))); ?>)</span>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-600 dark:text-gray-400 text-sm">
                                                <?php echo e(__('All Customers')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4">
                                        <?php
                                            $statusColors = [
                                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                'scheduled' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'sending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'sent' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            ];
                                            $statusClass = $statusColors[$broadcast->status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusClass); ?>">
                                            <?php echo e(ucfirst($broadcast->status)); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <?php
                                            $rate = $broadcast->getSuccessRate();
                                            $rateColor = $rate >= 80 ? 'text-green-600 dark:text-green-400' : 
                                                        ($rate >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 
                                                        'text-red-600 dark:text-red-400');
                                        ?>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold <?php echo e($rateColor); ?>"><?php echo e($rate); ?>%</span>
                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all" 
                                                     style="width: <?php echo e($rate); ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <?php echo e(\Carbon\Carbon::parse($broadcast->created_at)->timezone(config('app.timezone'))->format('M d, Y')); ?>

                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <?php echo e(\Carbon\Carbon::parse($broadcast->created_at)->timezone(config('app.timezone'))->format('h:i A')); ?>

                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex gap-2">
                                            <a href="<?php echo e(route('admin.broadcasts.report', $broadcast->id)); ?>" 
                                               class="btn-default !p-2"
                                               data-tooltip-target="tooltip-report-<?php echo e($broadcast->id); ?>"
                                               title="<?php echo e(__('View Report')); ?>">
                                                <i class="bi bi-bar-chart text-sm"></i>
                                            </a>
                                            <div id="tooltip-report-<?php echo e($broadcast->id); ?>" role="tooltip"
                                                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                                <?php echo e(__('View Report')); ?>

                                                <div class="tooltip-arrow" data-popper-arrow></div>
                                            </div>

                                            <?php if(!in_array($broadcast->status, ['sending', 'sent'])): ?>
                                                <form action="<?php echo e(route('admin.broadcasts.destroy', $broadcast->id)); ?>" 
                                                      method="POST"
                                                      onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this broadcast?')); ?>')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" 
                                                            class="btn-danger !p-2"
                                                            data-tooltip-target="tooltip-delete-<?php echo e($broadcast->id); ?>"
                                                            title="<?php echo e(__('Delete')); ?>">
                                                        <i class="bi bi-trash text-sm"></i>
                                                    </button>
                                                    <div id="tooltip-delete-<?php echo e($broadcast->id); ?>" role="tooltip"
                                                        class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                                        <?php echo e(__('Delete')); ?>

                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-8">
                                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                            <i class="bi bi-inbox text-4xl mb-2"></i>
                                            <p class="text-lg font-medium"><?php echo e(__('No broadcasts found')); ?></p>
                                            <p class="text-sm"><?php echo e(__('Create your first broadcast to get started')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    
                    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
                        <?php echo e($broadcasts->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div id="recipientsModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 w-[95%] max-w-md rounded-xl shadow-xl p-6 relative">
            <button onclick="closeRecipients()"
                class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl">&times;</button>
            <h2 class="text-lg font-semibold mb-4 dark:text-white"><?php echo e(__('Custom Recipients')); ?></h2>
            <div class="overflow-x-auto max-h-[400px] overflow-y-auto border rounded dark:border-gray-700">
                <table class="min-w-full border text-sm dark:border-gray-700">
                    <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0">
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
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?php echo e(__('Group Name')); ?></p>
                    <p class="font-medium dark:text-white" id="groupName"></p>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1"><?php echo e(__('Total Customers')); ?></p>
                    <p class="font-medium dark:text-white" id="groupCount"></p>
                </div>
                <a href="#" id="viewGroupLink" class="btn-primary w-full text-center block">
                    <i class="bi bi-eye mr-2"></i>
                    <?php echo e(__('View Group Details')); ?>

                </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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

    // Close modals on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRecipients();
            closeGroupInfo();
        }
    });

    // Close modals on backdrop click
    document.getElementById('recipientsModal').addEventListener('click', function(e) {
        if (e.target === this) closeRecipients();
    });

    document.getElementById('groupInfoModal').addEventListener('click', function(e) {
        if (e.target === this) closeGroupInfo();
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/broadcasts/index.blade.php ENDPATH**/ ?>