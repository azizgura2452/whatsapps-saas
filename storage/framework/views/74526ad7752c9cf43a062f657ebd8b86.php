

<?php $__env->startSection('title', __('Broadcast Groups') . ' | ' . config('app.name')); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            <?php echo e(__('Broadcast Groups')); ?>

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
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Broadcast Groups')); ?></li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    <?php echo e(__('Groups List')); ?>

                </h3>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('admin.broadcast-groups.create')); ?>" class="btn-primary">
                        <i class="bi bi-plus-circle mr-2"></i>
                        <?php echo e(__('New Group')); ?>

                    </a>
                </div>
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="5%">
                                #
                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <?php echo e(__('Name')); ?>

                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <?php echo e(__('Description')); ?>

                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <?php echo e(__('Customers')); ?>

                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <?php echo e(__('Conditions')); ?>

                            </th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5" width="15%">
                                <?php echo e(__('Actions')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><?php echo e($groups->firstItem() + $loop->index); ?></td>
                                <td class="px-5 py-4 sm:px-6"><?php echo e($group->name); ?></td>
                                <td class="px-5 py-4 sm:px-6"><?php echo e($group->description ?? '-'); ?></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="bi bi-people mr-1"></i>
                                        <?php echo e($group->getCustomerCount()); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <?php $__empty_2 = true; $__currentLoopData = $group->conditions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cond): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <span class="inline-block text-xs bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 mr-1 mb-1">
                                            <?php echo e($cond->field); ?> <?php echo e($cond->operator); ?> <?php echo e($cond->value); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <span class="text-xs text-gray-400"><?php echo e(__('No conditions')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <a href="<?php echo e(route('admin.broadcast-groups.edit', $group->id)); ?>" 
                                       class="btn-default !p-2"
                                       title="<?php echo e(__('Edit')); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form action="<?php echo e(route('admin.broadcast-groups.destroy', $group->id)); ?>"
                                          method="POST"
                                          onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this group?')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-danger !p-2"
                                                title="<?php echo e(__('Delete')); ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No groups found')); ?></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    <?php echo e($groups->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/broadcast_groups/index.blade.php ENDPATH**/ ?>