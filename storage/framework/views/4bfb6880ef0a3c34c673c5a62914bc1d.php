

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Flow Builder')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Flow Builder')); ?></h2>
            <?php if($business): ?>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                <?php echo e(__('Managing flow for')); ?>: <strong><?php echo e($business->name); ?></strong>
            </p>
            <?php endif; ?>
        </div>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                        <?php echo e(__('Home')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Flow Builder')); ?></li>
            </ol>
        </nav>
    </div>

    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- React Flow Builder Component -->
    <div id="flow-builder-root"></div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Pass flow steps data to React component
    window.flowStepsData = <?php echo json_encode($flowSteps->map(function($step) {
        return [
            'id' => $step->id,
            'name' => $step->name,
            'step_type' => $step->step_type,
            'order' => $step->order,
            'is_active' => $step->is_active,
            'messages' => $step->messages,
            'triggers' => $step->triggers,
        ];
    })->values()); ?>;
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/flow-builder/index.blade.php ENDPATH**/ ?>