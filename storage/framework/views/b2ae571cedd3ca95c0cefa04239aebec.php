<?php use App\Enums\OrderStatus; ?>



<?php $__env->startSection('title'); ?>
    <?php echo e(__('Order Details')); ?> | <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <?php echo e(__('Order Details')); ?>

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
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                            href="<?php echo e(route('admin.orders.index')); ?>">
                            <?php echo e(__('Orders')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Order #') . $order->id); ?></li>
                </ol>
            </nav>
        </div>

        <!-- Order Summary -->
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <?php if(session('success')): ?>
                <div class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 text-sm">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="text-red-500 text-sm"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">Order ID: <?php echo e($order->id); ?></h3>
            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                <li><strong><?php echo e(__('Customer Name')); ?>:</strong> <?php echo e($order->customer->name); ?></li>

                <li><strong><?php echo e(__('Order Source')); ?>:</strong> <?php echo e(ucfirst($order->source)); ?></li>
                <li><strong><?php echo e(__('Customer Address')); ?>:</strong> <?php echo e($order->customer->address); ?></li>
                <li><strong><?php echo e(__('Customer WhatsApp')); ?>:</strong> <?php echo e($order->customer->whatsapp_number); ?></li>


                <li><strong><?php echo e(__('Date Created')); ?>:</strong>
                    <?php echo e(\Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A')); ?></li>
                <li>
                    <form action="<?php echo e(route('admin.orders.updateStatus', $order->id)); ?>" method="POST"
                        class="flex items-center gap-2">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>

                        <label for="status" class="text-gray-700 dark:text-gray-300">
                            <strong><?php echo e(__('Order Status')); ?>:</strong>
                        </label>
                        <select name="status" id="status"
                            class="px-2 py-1 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm"
                            style="width: 120px">
                            <?php $__currentLoopData = OrderStatus::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->value); ?>" <?php if($order->status->value === $status->value): echo 'selected'; endif; ?>>
                                    <?php echo e(__($status->value)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            <?php echo e(__('Update')); ?>

                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- Order Items Table -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90"><?php echo e(__('Ordered Items')); ?></h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-3"><?php echo e(__('#')); ?></th>
                            <th class="px-6 py-3"><?php echo e(__('Product Name')); ?></th>
                            <th class="px-6 py-3"><?php echo e(__('SKU')); ?></th>
                            <th class="px-6 py-3"><?php echo e(__('Quantity')); ?></th>
                            <th class="px-6 py-3"><?php echo e(__('Price')); ?></th>
                            <th class="px-6 py-3"><?php echo e(__('Total')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-6 py-4"><?php echo e($index + 1); ?></td>
                                <td class="px-6 py-4"><?php echo e($item->product->name_en ?? __('N/A')); ?></td>
                                <td class="px-6 py-4"><?php echo e($item->product->sku ?? __('N/A')); ?></td>
                                <td class="px-6 py-4"><?php echo e($item->quantity); ?></td>
                                <td class="px-6 py-4"><?php echo e($order->currency); ?> <?php echo e(number_format($item->price, 3)); ?></td>
                                <td class="px-6 py-4"><?php echo e($order->currency); ?> <?php echo e(number_format($item->total, 3)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <div class="w-full max-w-xs space-y-2 text-sm text-gray-800 dark:text-gray-200">
                    <?php
                        $subtotal = $order->items->sum('total');
                        $deliveryCharge = $order->delivery_charge;
                        $grandTotal = $subtotal + $deliveryCharge;
                    ?>

                    <div class="flex justify-between">
                        <span><?php echo e(__('Subtotal')); ?></span>
                        <span><?php echo e($order->currency); ?> <?php echo e(number_format($subtotal, 3)); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span><?php echo e(__('Delivery Charge')); ?></span>
                        <span><?php echo e($order->currency); ?> <?php echo e(number_format($deliveryCharge, 3)); ?></span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-2 border-gray-300 dark:border-gray-600">
                        <span><?php echo e(__('Total')); ?></span>
                        <span><?php echo e($order->currency); ?> <?php echo e(number_format($grandTotal, 3)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end px-6 py-4">
            <button onclick="printInvoice()" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-700">
                <?php echo e(__('Print Invoice')); ?>

            </button>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<div id="print-area" style="display: none; font-size: 14px; color: black;">
    <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 10px;"><?php echo e(__('Invoice')); ?></h2>
    <p><strong><?php echo e(__('Order ID')); ?>:</strong> <?php echo e($order->id); ?></p>
    <p><strong><?php echo e(__('Date')); ?>:</strong> <?php echo e(\Carbon\Carbon::parse($order->created_on)->format('M d, Y h:i A')); ?></p>
    <p><strong><?php echo e(__('Customer Name')); ?>:</strong> <?php echo e($order->customer->name); ?></p>
    <p><strong><?php echo e(__('Customer Address')); ?>:</strong> <?php echo e($order->customer->address); ?></p>
    <p><strong><?php echo e(__('WhatsApp')); ?>:</strong> <?php echo e($order->customer->whatsapp_number); ?></p>
    <p><strong><?php echo e(__('Order Status')); ?>:</strong> <?php echo e(__($order->status->value)); ?></p>

    <table style="width: 100%; margin-top: 20px; border-collapse: collapse; border: 1px solid black;">
        <thead>
            <tr style="background-color: #e5e7eb;">
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('#')); ?></th>
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('Product')); ?></th>
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('SKU')); ?></th>
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('Qty')); ?></th>
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('Price')); ?></th>
                <th style="border: 1px solid black; padding: 6px;"><?php echo e(__('Total')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($index + 1); ?></td>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($item->product->name_en ?? 'N/A'); ?></td>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($item->product->sku ?? 'N/A'); ?></td>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($item->quantity); ?></td>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($order->currency); ?>

                        <?php echo e(number_format($item->price, 3)); ?></td>
                    <td style="border: 1px solid black; padding: 6px;"><?php echo e($order->currency); ?>

                        <?php echo e(number_format($item->total, 3)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div style="margin-top: 20px; width: 300px; float: right;">
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
            <span><?php echo e(__('Subtotal')); ?></span>
            <span><?php echo e($order->currency); ?> <?php echo e(number_format($subtotal, 3)); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
            <span><?php echo e(__('Delivery Charge')); ?></span>
            <span><?php echo e($order->currency); ?> <?php echo e(number_format($deliveryCharge, 3)); ?></span>
        </div>
        <div
            style="display: flex; justify-content: space-between; font-weight: bold; border-top: 2px solid black; padding: 8px 0 0;">
            <span><?php echo e(__('Total')); ?></span>
            <span><?php echo e($order->currency); ?> <?php echo e(number_format($grandTotal, 3)); ?></span>
        </div>
    </div>

    <div style="clear: both;"></div>
</div>



<script>
    function printInvoice() {
        const content = document.getElementById('print-area').innerHTML;
        const printWindow = window.open('', '', 'height=700,width=900');
        printWindow.document.write('<html><head><title>Invoice</title>');
        printWindow.document.write('<style>body{font-family:sans-serif;padding:20px;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid black;padding:5px;text-align:left;} </style>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 500);
    }
</script>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/orders/show.blade.php ENDPATH**/ ?>