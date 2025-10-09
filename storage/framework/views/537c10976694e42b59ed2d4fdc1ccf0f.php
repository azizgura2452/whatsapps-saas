

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Customer Edit')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('Edit Customer')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Edit Customer')); ?></h2>
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
                                href="<?php echo e(route('admin.customers.index')); ?>">
                                <?php echo e(__('Customers')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            <?php echo e(__('Edit Customer')); ?>

                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-5 py-2.5 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white"><?php echo e(__('Edit Customer')); ?> -
                        <?php echo e($customer->name); ?>

                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <form action="<?php echo e(route('admin.customers.update', $customer->id)); ?>" method="POST" class="space-y-6"
                        enctype="multipart/form-data">
                        <?php echo method_field('PUT'); ?>
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Full Name')); ?></label>
                                <input type="text" name="name" id="name" required value="<?php echo e($customer->name); ?>"
                                    placeholder="<?php echo e(__('Enter Full Name')); ?>"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="whatsapp_number"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('WhatsApp Number')); ?></label>
                                <input type="text" name="whatsapp_number" id="whatsapp_number" required
                                    value="<?php echo e($customer->whatsapp_number); ?>" placeholder="<?php echo e(__('Enter WhatsApp Number')); ?>"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="address"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Address')); ?></label>
                                <input type="text" name="address" id="address"
                                    value="<?php echo e($customer->address); ?>" placeholder="<?php echo e(__('Enter Address')); ?>"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Email')); ?></label>
                                <input type="text" name="email" id="email"
                                    value="<?php echo e($customer->email); ?>" placeholder="<?php echo e(__('Enter Email')); ?>"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="birthday"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Date of Birth')); ?></label>
                                <input type="date" name="birthday" id="birthday"
                                    value="<?php echo e($customer->birthday); ?>" placeholder="<?php echo e(__('Enter Date of Birth')); ?>"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>

                            <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
        <?php echo e(__('Gender')); ?>

    </label>
    <div class="mt-2 flex items-center gap-6">
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="male"
                <?php echo e($customer->gender === 'male' ? 'checked' : ''); ?>

                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Male</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="female"
                <?php echo e($customer->gender === 'female' ? 'checked' : ''); ?>

                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Female</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="gender" value="other"
                <?php echo e($customer->gender === 'other' ? 'checked' : ''); ?>

                class="text-brand-600 border-gray-300 focus:ring-brand-500">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Other</span>
        </label>
    </div>
</div>


                        </div>
                        <div class="space-y-4">
                            <h3 class="block font-medium text-gray-700 dark:text-gray-400">
                                <?php echo e(__('Custom Attributes')); ?>

                            </h3>
                            <br>

                            <div id="attributes-container" class="space-y-3">
                                <?php if(isset($customer)): ?>
                                    <?php $__currentLoopData = $customer->attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center gap-2">
                                            <label>Attribute:</label>
                                            <input type="text" name="attributes[key][]" value="<?php echo e($attr->key); ?>" placeholder="Key"
                                                class="w-1/3 border rounded px-2 py-1">
                                            <label>Value:</label>
                                            <input type="text" name="attributes[value][]" value="<?php echo e($attr->value); ?>"
                                                placeholder="Value" class="w-2/3 border rounded px-2 py-1">
                                            <button type="button" class="remove-attr text-red-500">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                            <br>
                            <button type="button" id="add-attr" class="btn-default">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add Attribute')); ?>

                            </button>
                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-primary"><?php echo e(__('Save')); ?></button>
                            <a href="<?php echo e(route('admin.customers.index')); ?>" class="btn-default"><?php echo e(__('Cancel')); ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.getElementById('add-attr').addEventListener('click', function () {
            let container = document.getElementById('attributes-container');
            let div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'gap-2');
            div.innerHTML = `
            <label>Attribute:</label>
            <input type="text" name="attributes[key][]" placeholder="Key" class="w-1/3 border rounded px-2 py-1">
            <label>Value:</label>
            <input type="text" name="attributes[value][]" placeholder="Value" class="w-2/3 border rounded px-2 py-1">
            <button type="button" class="remove-attr text-red-500">
                <i class="fa fa-trash"></i>
            </button>
        `;
            container.appendChild(div);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-attr')) {
                e.target.closest('.flex').remove();
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/customers/edit.blade.php ENDPATH**/ ?>