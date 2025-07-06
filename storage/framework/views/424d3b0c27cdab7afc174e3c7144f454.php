

<?php $__env->startSection('title'); ?>
    Broadcasts - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">Broadcasts</h1>
            <a href="<?php echo e(route('admin.broadcasts.create')); ?>" class="btn-primary">New Broadcast</a>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-xl">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Title</th>
                        <th class="p-3 text-left">Recipients</th>
                        <th class="p-3 text-left">Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t">
                            <td class="p-3"><?php echo e($broadcast->id); ?></td>
                            <td class="p-3"><?php echo e($broadcast->whatsapp_template_name ?? '-'); ?></td>
                            <td class="p-3">
                                <?php if($broadcast->custom_recipients): ?>
                                    <span class="cursor-pointer underline decoration-dotted text-blue-600"
                                        title="<?php echo e($broadcast->custom_recipients); ?>">
                                        Custom
                                    </span>
                                <?php else: ?>
                                    All Customers
                                <?php endif; ?>
                            </td>

                            <td class="p-3"><?php echo e($broadcast->created_at->format('Y-m-d H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4"><?php echo e($broadcasts->links()); ?></div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/broadcasts/index.blade.php ENDPATH**/ ?>