

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Orders of')); ?> <?php echo e($customer->name); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: '<?php echo e(__('Orders of')); ?> <?php echo e($customer->name); ?>' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <?php echo e(__('Orders of')); ?> <?php echo e($customer->name); ?>

            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                            <?php echo e(__('Home')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.customers.index')); ?>">
                            <?php echo e(__('Customers')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Orders')); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Customer Orders Table -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    <?php echo e(__('Orders for')); ?> <?php echo e($customer->name); ?>

                </h3>
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left"><?php echo e(__('#')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Order ID')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Total')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Delivery Charge')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Source')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Status')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Created On')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Modified On')); ?></th>
                            <th class="px-5 py-3 text-left"><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4"><?php echo e($loop->iteration); ?></td>
                                <td class="px-5 py-4"><?php echo e($order->id); ?></td>
                                <td class="px-5 py-4"><?php echo e(number_format($order->total, 3)); ?></td>
                                <td class="px-5 py-4"><?php echo e(number_format($order->delivery_charge, 3)); ?></td>
                                <td class="px-5 py-4"><?php echo e(ucfirst($order->source)); ?></td>
                                <td class="px-5 py-4">
                                    <?php
                                        $statusValue = strtolower($order->status->value ?? 'pending');
                                        $statusClasses = [
                                            'pending' => 'bg-blue-100 text-blue-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusText = ucfirst($statusValue);
                                        $statusClass = $statusClasses[$statusValue] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold <?php echo e($statusClass); ?>">
                                        <?php echo e($statusText); ?>

                                    </span>
                                </td>
                                <td class="px-5 py-4"><?php echo e(\Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A')); ?></td>
                                <td class="px-5 py-4"><?php echo e(\Carbon\Carbon::parse($order->modified_on)->format('M d, Y h:i A')); ?></td>
                                <td class="px-5 py-4 flex gap-2">
                                    <?php if(auth()->user()->can('orders.view')): ?>
                                        <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn-default">
                                            <i class="bi bi-eye"></i>
                                            <span><?php echo e(__('View')); ?></span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->can('orders.delete')): ?>
                                        <form action="<?php echo e(route('admin.orders.destroy', $order->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Are you sure?')); ?>');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn-danger">
                                                <i class="bi bi-trash"></i>
                                                <span><?php echo e(__('Delete')); ?></span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    <?php echo e(__('No orders found for this customer.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    <?php echo e($orders->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/customers/orders.blade.php ENDPATH**/ ?>