

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Product Create')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div x-data="{ pageName: '<?php echo e(__('New Product')); ?>' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('New Product')); ?></h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                            <?php echo e(__('Home')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.products.index')); ?>">
                            <?php echo e(__('Products')); ?>

                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">
                        <?php echo e(__('New Product')); ?>

                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90"><?php echo e(__('Create New Product')); ?></h3>
            </div>
            <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name_en" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Product Name (EN)')); ?></label>
                            <input type="text" name="name_en" id="name_en" required value="<?php echo e(old('name_en')); ?>" placeholder="<?php echo e(__('Enter Product Name in English')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="name_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Product Name (AR)')); ?></label>
                            <input type="text" name="name_ar" id="name_ar" required value="<?php echo e(old('name_ar')); ?>" placeholder="<?php echo e(__('Enter Product Name in Arabic')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('SKU')); ?></label>
                            <input type="text" name="sku" id="sku" value="<?php echo e(old('sku')); ?>" placeholder="<?php echo e(__('Enter SKU')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Brand')); ?></label>
                            <input type="text" name="brand" id="brand" value="<?php echo e(old('brand')); ?>" placeholder="<?php echo e(__('Enter Brand')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Price')); ?></label>
                            <input type="number" step="0.01" name="price" id="price" required value="<?php echo e(old('price')); ?>" placeholder="<?php echo e(__('Enter Price')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Stock Quantity')); ?></label>
                            <input type="number" name="stock" id="stock" required value="<?php echo e(old('stock')); ?>" placeholder="<?php echo e(__('Enter Stock Quantity')); ?>" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Product Image')); ?></label>
                            <input type="file" name="image" id="image" accept="image/*" class="form-input  form-control">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Status')); ?></label>
                            <select name="status" id="status" class="form-input  form-control">
                                <option value="active" <?php echo e(old('status') === 'active' ? 'selected' : ''); ?>><?php echo e(__('In Stock')); ?></option>
                                <option value="inactive" <?php echo e(old('status') === 'inactive' ? 'selected' : ''); ?>><?php echo e(__('Out of Stock')); ?></option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description_en" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Description (EN)')); ?></label>
                            <textarea name="description_en" id="description_en" rows="3" class="form-input  form-control"><?php echo e(old('description_en')); ?></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-400"><?php echo e(__('Description (AR)')); ?></label>
                            <textarea name="description_ar" id="description_ar" rows="3" class="form-input  form-control"><?php echo e(old('description_ar')); ?></textarea>
                        </div>

                        <?php echo ld_apply_filters('after_product_fields', '', null); ?>

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

<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u829817072/domains/silver-quetzal-131368.hostingersite.com/varsity-laravel/resources/views/backend/pages/products/create.blade.php ENDPATH**/ ?>