

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Create Business')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-4xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('Create New Business')); ?></h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                        <?php echo e(__('Home')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.businesses.index')); ?>">
                        <?php echo e(__('Businesses')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Create')); ?></li>
            </ol>
        </nav>
    </div>

    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form action="<?php echo e(route('admin.businesses.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4"><?php echo e(__('Basic Information')); ?></h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Business Name')); ?> <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="<?php echo e(old('name')); ?>"
                                   placeholder="e.g., My E-commerce Store"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Email')); ?>

                            </label>
                            <input type="email" name="email" id="email"
                                   value="<?php echo e(old('email')); ?>"
                                   placeholder="business@example.com"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Phone')); ?>

                            </label>
                            <input type="text" name="phone" id="phone"
                                   value="<?php echo e(old('phone')); ?>"
                                   placeholder="+965 9999 9999"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Address')); ?>

                            </label>
                            <textarea name="address" id="address" rows="3"
                                      placeholder="Business address..."
                                      class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white"><?php echo e(old('address')); ?></textarea>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Configuration -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4"><?php echo e(__('WhatsApp Configuration')); ?></h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="whatsapp_phone_number_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('WhatsApp Phone Number ID')); ?> <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="whatsapp_phone_number_id" id="whatsapp_phone_number_id" required
                                   value="<?php echo e(old('whatsapp_phone_number_id')); ?>"
                                   placeholder="123456789012345"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            <p class="text-xs text-gray-500 mt-1"><?php echo e(__('From WhatsApp Business API Dashboard')); ?></p>
                            <?php $__errorArgs = ['whatsapp_phone_number_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="whatsapp_business_account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('WhatsApp Business Account ID')); ?> <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="whatsapp_business_account_id" id="whatsapp_business_account_id" required
                                   value="<?php echo e(old('whatsapp_business_account_id')); ?>"
                                   placeholder="123456789012345"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            <?php $__errorArgs = ['whatsapp_business_account_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="whatsapp_access_token" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('WhatsApp Access Token')); ?> <span class="text-red-500">*</span>
                            </label>
                            <textarea name="whatsapp_access_token" id="whatsapp_access_token" required rows="3"
                                      placeholder="EAAxxxxxxxxxx..."
                                      class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono text-sm"><?php echo e(old('whatsapp_access_token')); ?></textarea>
                            <?php $__errorArgs = ['whatsapp_access_token'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="whatsapp_catalog_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('WhatsApp Catalog ID')); ?> <span class="text-gray-400">(<?php echo e(__('Optional')); ?>)</span>
                            </label>
                            <input type="text" name="whatsapp_catalog_id" id="whatsapp_catalog_id"
                                   value="<?php echo e(old('whatsapp_catalog_id')); ?>"
                                   placeholder="123456789012345"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white font-mono">
                            <?php $__errorArgs = ['whatsapp_catalog_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Business Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4"><?php echo e(__('Business Settings')); ?></h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Currency')); ?> <span class="text-red-500">*</span>
                            </label>
                            <select name="currency" id="currency" required
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                                <option value="KWD" <?php echo e(old('currency') == 'KWD' ? 'selected' : ''); ?>>KWD - Kuwaiti Dinar</option>
                                <option value="USD" <?php echo e(old('currency') == 'USD' ? 'selected' : ''); ?>>USD - US Dollar</option>
                                <option value="EUR" <?php echo e(old('currency') == 'EUR' ? 'selected' : ''); ?>>EUR - Euro</option>
                                <option value="GBP" <?php echo e(old('currency') == 'GBP' ? 'selected' : ''); ?>>GBP - British Pound</option>
                                <option value="AED" <?php echo e(old('currency') == 'AED' ? 'selected' : ''); ?>>AED - UAE Dirham</option>
                                <option value="SAR" <?php echo e(old('currency') == 'SAR' ? 'selected' : ''); ?>>SAR - Saudi Riyal</option>
                            </select>
                            <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="delivery_charge" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-2">
                                <?php echo e(__('Delivery Charge')); ?> <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="delivery_charge" id="delivery_charge" required
                                   value="<?php echo e(old('delivery_charge', '2.000')); ?>"
                                   step="0.001"
                                   min="0"
                                   placeholder="2.000"
                                   class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-gray-800 dark:text-white">
                            <?php $__errorArgs = ['delivery_charge'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    <i class="bi bi-check-circle mr-1"></i>
                    <?php echo e(__('Create Business')); ?>

                </button>
                <a href="<?php echo e(route('admin.businesses.index')); ?>" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    <?php echo e(__('Cancel')); ?>

                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/businesses/create.blade.php ENDPATH**/ ?>