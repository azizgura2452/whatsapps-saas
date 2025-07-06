

<?php $__env->startSection('title'); ?>
    <?php echo e(__('WhatsApp Templates')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: <?php echo e(__('WhatsApp Templates')); ?> }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <?php echo e(__('WhatsApp Templates')); ?>

            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                            <?php echo e(__('Home')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('WhatsApp Templates')); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90"><?php echo e(__('Template List')); ?></h3>

                <?php if(auth()->user()->can('templates.create')): ?>
                    <a href="<?php echo e(route('admin.whatsapp-templates.create')); ?>" class="btn-primary">
                        <i class="bi bi-plus-circle mr-2"></i>
                        <?php echo e(__('New Template')); ?>

                    </a>
                <?php endif; ?>
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <table id="dataTable" class="w-full dark:text-gray-400">
                    <thead class="bg-light text-capitalize">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">#</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Title')); ?></th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Message')); ?></th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Status')); ?></th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5"><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6"><?php echo e($loop->iteration); ?></td>
                                <td class="px-5 py-4 sm:px-6 font-semibold text-gray-800 dark:text-white"><?php echo e($template->title); ?></td>
                                <td class="px-5 py-4 sm:px-6"><?php echo e(Str::limit($template->message, 80)); ?></td>
                                <td class="px-5 py-4 sm:px-6">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full <?php echo e($template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo e($template->is_active ? __('Active') : __('Inactive')); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <?php if(auth()->user()->can('templates.edit')): ?>
                                        <a href="<?php echo e(route('admin.whatsapp-templates.edit', $template->id)); ?>" class="btn-default">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->can('templates.delete')): ?>
                                        <form action="<?php echo e(route('admin.whatsapp-templates.destroy', $template->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400"><?php echo e(__('No templates found')); ?></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    <?php echo e($templates->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/whatsapp_templates/index.blade.php ENDPATH**/ ?>