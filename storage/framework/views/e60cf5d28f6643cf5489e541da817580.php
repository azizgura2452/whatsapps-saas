

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Permissions')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Permissions')); ?></h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                        <?php echo e(__('Home')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Permissions')); ?></li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90"><?php echo e(__('Permissions')); ?></h3>

                <?php echo $__env->make('backend.partials.search-form', [
                    'placeholder' => __('Search by name or group'),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <table id="dataTable" class="w-full dark:text-gray-400">
                    <thead class="bg-light text-capitalize">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th width="5%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Sl')); ?></th>
                            <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Name')); ?></th>
                            <th width="15%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Group')); ?></th>
                            <th width="45%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Roles')); ?></th>
                            <th width="10%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white"><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($loop->index + 1 != count($permissions) ?  'border-b border-gray-100 dark:border-gray-800' : ''); ?>">
                                <td class="px-5 py-4 sm:px-6"><?php echo e($loop->index + 1); ?></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <?php echo e(ucfirst($permission->name)); ?>

                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-white">
                                        <?php echo e(ucfirst($permission->group_name)); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6">
                                    <?php if($permission->role_count > 0): ?>
                                        <div class="flex items-center">
                                            <a href="<?php echo e(route('admin.permissions.show', $permission->id)); ?>" class="text-primary hover:underline">
                                                <span class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-white">
                                                    <?php echo e($permission->role_count); ?>

                                                </span>
                                                <?php echo e($permission->roles_list); ?>

                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400"><?php echo e(__('No roles assigned')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 sm:px-6 text-center">
                                    <a data-tooltip-target="tooltip-view-permission-<?php echo e($permission->id); ?>" class="btn-default !p-3" href="<?php echo e(route('admin.permissions.show', $permission->id)); ?>">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>
                                    <div id="tooltip-view-permission-<?php echo e($permission->id); ?>" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        <?php echo e(__('View Permission')); ?>

                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td colspan="5" class="px-5 py-4 sm:px-6 text-center">
                                    <span class="text-gray-500 dark:text-gray-400"><?php echo e(__('No permissions found')); ?></span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    <?php echo e($permissions->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/permissions/index.blade.php ENDPATH**/ ?>