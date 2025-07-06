

<?php $__env->startSection('title'); ?>
    <?php echo e(__('New WhatsApp Template')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('New WhatsApp Template')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('New WhatsApp Template')); ?></h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="<?php echo e(route('admin.dashboard')); ?>">
                                <?php echo e(__('Home')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="<?php echo e(route('admin.whatsapp-templates.index')); ?>">
                                <?php echo e(__('WhatsApp Templates')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            <?php echo e(__('New Template')); ?>

                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                        <?php echo e(__('Create New WhatsApp Template')); ?>

                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <form action="<?php echo e(route('admin.whatsapp-templates.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Template Title')); ?></label>
                                <input type="text" name="title" id="title" required value="<?php echo e(old('title')); ?>"
                                    placeholder="<?php echo e(__('Enter Template Title')); ?>" class="form-input form-control">
                            </div>

                            <div>
                                <label for="is_active"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Status')); ?></label>
                                <select name="is_active" id="is_active" class="form-input form-control">
                                    <option value="1" <?php echo e(old('is_active') == '1' ? 'selected' : ''); ?>><?php echo e(__('Active')); ?>

                                    </option>
                                    <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>><?php echo e(__('Inactive')); ?>

                                    </option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Message Body')); ?></label>
                                <textarea name="message" id="message" rows="6" class="form-input form-control"
                                    placeholder="<?php echo e(__('Enter Template Message')); ?>"><?php echo e(old('message')); ?></textarea>
                            </div>

                            <?php echo ld_apply_filters('after_whatsapp_template_fields', '', null); ?>

                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-primary"><?php echo e(__('Save')); ?></button>
                            <a href="<?php echo e(route('admin.whatsapp-templates.index')); ?>"
                                class="btn-default"><?php echo e(__('Cancel')); ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/whatsapp_templates/create.blade.php ENDPATH**/ ?>