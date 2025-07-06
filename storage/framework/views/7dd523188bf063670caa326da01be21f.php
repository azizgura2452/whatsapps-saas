

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Dashboard')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('before_vite_build'); ?>
    <script>
        var userGrowthData = <?php echo json_encode($user_growth_data['data'], 15, 512) ?>;
        var userGrowthLabels = <?php echo json_encode($user_growth_data['labels'], 15, 512) ?>;
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('Dashboard')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Dashboard')); ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 space-y-6">
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-4 md:gap-6">
                    <?php echo $__env->make('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-card-checklist',
                        'label' => __('Products'),
                        'value' => $total_products,
                        'bg' => '#635BFF',
                        'class' => 'bg-white',
                        'url' => route('admin.products.index'),
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('backend.pages.dashboard.partials.card', [
                        'icon_svg' => asset('images/icons/user.svg'),
                        'label' => __('Customers'),
                        'value' => $total_customers,
                        'bg' => '#00D7FF',
                        'class' => 'bg-white',
                        'url' => route('admin.customers.index'),
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-cash-coin',
                        'label' => __('Orders'),
                        'value' => $total_orders,
                        'bg' => '#FF4D96',
                        'class' => 'bg-white',
                        'url' => route('admin.orders.index'),
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/dashboard/index.blade.php ENDPATH**/ ?>