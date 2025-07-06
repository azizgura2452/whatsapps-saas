

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Product Edit')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
    <div class="p-4 mx-auto max-w-7xl md:p-6">
        <div x-data="{ pageName: '<?php echo e(__('Edit Product')); ?>' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Edit Product')); ?></h2>
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
                                href="<?php echo e(route('admin.products.index')); ?>">
                                <?php echo e(__('Products')); ?>

                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            <?php echo e(__('Edit Product')); ?>

                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-5 py-2.5 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white"><?php echo e(__('Edit Product')); ?> -
                        <?php echo e($product->name_en); ?>

                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <form action="<?php echo e(route('admin.products.update', $product->id)); ?>" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        <?php echo method_field('PUT'); ?>
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name_en"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Name (English)')); ?></label>
                                <input type="text" name="name_en" id="name_en" required value="<?php echo e($product->name_en); ?>"
                                    placeholder="<?php echo e(__('Enter English Name')); ?>" class="input-text form-control">
                            </div>
                            <div>
                                <label for="name_ar"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Name (Arabic)')); ?></label>
                                <input type="text" name="name_ar" id="name_ar" required value="<?php echo e($product->name_ar); ?>"
                                    placeholder="<?php echo e(__('Enter Arabic Name')); ?>" class="input-text form-control">
                            </div>
                            <div>
                                <label for="description_en"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Description (English)')); ?></label>
                                <textarea name="description_en" id="description_en" rows="4"
                                    class="input-text form-control"><?php echo e($product->description_en); ?></textarea>
                            </div>
                            <div>
                                <label for="description_ar"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Description (Arabic)')); ?></label>
                                <textarea name="description_ar" id="description_ar" rows="4"
                                    class="input-text form-control"><?php echo e($product->description_ar); ?></textarea>
                            </div>
                            <div>
                                <label for="sku"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('SKU')); ?></label>
                                <input type="text" name="sku" id="sku" required value="<?php echo e($product->sku); ?>"
                                    class="input-text form-control">
                            </div>
                            <div>
                                <label for="brand"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Brand')); ?></label>
                                <input type="text" name="brand" id="brand" value="<?php echo e($product->brand); ?>"
                                    class="input-text form-control">
                            </div>
                            <div>
                                <label for="price"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Price')); ?></label>
                                <input type="number" step="0.01" name="price" id="price" required
                                    value="<?php echo e($product->price); ?>" class="input-text form-control">
                            </div>
                            <div>
                                <label for="stock"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Stock')); ?></label>
                                <input type="number" name="stock" id="stock" required value="<?php echo e($product->stock); ?>"
                                    class="input-text form-control">
                            </div>
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Status')); ?></label>
                                <select name="status" id="status" class="input-text form-control">
                                    <option value="1" <?php echo e($product->status == 1 ? 'selected' : ''); ?>><?php echo e(__('In Stock')); ?>

                                    </option>
                                    <option value="0" <?php echo e($product->status == 0 ? 'selected' : ''); ?>><?php echo e(__('Out of Stock')); ?>

                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="link"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Link')); ?></label>
                                <input type="text" name="link" id="link" required
                                    value="<?php echo e($product->link ? $product->link : 'https://varsityheadwear.com'); ?>"
                                    class="input-text form-control">
                            </div>
                            <div>
                                <label for="image"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Product Image')); ?></label>
                                <input type="file" name="image" id="image" class="input-text form-control">
                                <?php
                                    $imageSrc = Str::startsWith($product->image, ['http://', 'https://'])
                                        ? $product->image
                                        : asset('storage/' . ltrim($product->image, '/'));
                                ?>

                                <?php if(!empty($product->image)): ?>
                                    <img src="<?php echo e($imageSrc); ?>" alt="Product Image" class="mt-2 h-20">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-primary"><?php echo e(__('Save')); ?></button>
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn-default"><?php echo e(__('Cancel')); ?></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/products/edit.blade.php ENDPATH**/ ?>