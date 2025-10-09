

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Dashboard')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('before_vite_build'); ?>
    <?php if(isset($user_growth_data['data']) && isset($user_growth_data['labels'])): ?>
        <script>
            var userGrowthData = <?php echo json_encode($user_growth_data['data'], 15, 512) ?>;
            var userGrowthLabels = <?php echo json_encode($user_growth_data['labels'], 15, 512) ?>;
        </script>
    <?php else: ?>
        <script>
            var userGrowthData = [];
            var userGrowthLabels = [];
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('Dashboard')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Dashboard')); ?></h2>
            </div>
        </div>

        <?php
            $business = app()->has('current_business') ? app('current_business') : auth()->user()->businesses()->first();
        ?>

        <?php if($business): ?>
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
        <?php else: ?>
            <div class="text-center py-16">
                <h3 class="text-2xl font-semibold text-gray-700 dark:text-white/80">
                    <?php echo e(__('No business associated.')); ?>

                </h3>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    <?php echo e(__('Please create or select a business to view your dashboard data.')); ?>

                </p>
                <a href="<?php echo e(route('admin.businesses.index')); ?>"
                   class="inline-block mt-4 px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <?php echo e(__('Go to Businesses')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/dashboard/index.blade.php ENDPATH**/ ?>