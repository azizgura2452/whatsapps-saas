

<?php $__env->startSection('title'); ?>
    <?php echo e(__('Businesses')); ?> - <?php echo e(config('app.name')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('admin-content'); ?>
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90"><?php echo e(__('My Businesses')); ?></h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="<?php echo e(route('admin.dashboard')); ?>">
                        <?php echo e(__('Home')); ?>

                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90"><?php echo e(__('Businesses')); ?></li>
            </ol>
        </nav>
    </div>

    <?php echo $__env->make('backend.layouts.partials.messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Current Business Card -->
    <?php if(isset($currentBusiness)): ?>
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center">
                    <i class="bi bi-building text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-200"><?php echo e(__('Current Business')); ?></p>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100"><?php echo e($currentBusiness->name); ?></h3>
                </div>
            </div>
            <span class="px-3 py-1 bg-blue-600 text-white text-sm rounded-full">
                <i class="bi bi-check-circle mr-1"></i><?php echo e(__('Active')); ?>

            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Businesses List -->
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center border-b border-gray-200 dark:border-gray-800">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90"><?php echo e(__('All Businesses')); ?></h3>
                
                <?php if(auth()->user()->can('business.create')): ?>
                <a href="<?php echo e(route('admin.businesses.create')); ?>" class="btn-primary">
                    <i class="bi bi-plus-circle mr-2"></i>
                    <?php echo e(__('Add Business')); ?>

                </a>
                <?php endif; ?>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Business Name')); ?></th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('WhatsApp Phone ID')); ?></th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Currency')); ?></th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Status')); ?></th>
                            <th class="p-4 text-left text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <?php $__empty_1 = true; $__currentLoopData = $businesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $business): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white"><?php echo e($business->name); ?></p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($business->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                                    <?php echo e(Str::limit($business->whatsapp_phone_number_id, 20)); ?>

                                </code>
                            </td>
                            <td class="p-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300"><?php echo e($business->currency); ?></span>
                            </td>
                            <td class="p-4">
                                <?php if($business->is_active): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="bi bi-check-circle mr-1"></i><?php echo e(__('Active')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <i class="bi bi-pause-circle mr-1"></i><?php echo e(__('Inactive')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <?php if(session('current_business_id') != $business->id): ?>
                                    <form action="<?php echo e(route('admin.businesses.switch', $business->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-success !p-2" title="<?php echo e(__('Switch to this business')); ?>">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <?php if(auth()->user()->can('business.edit')): ?>
                                    <a href="<?php echo e(route('admin.businesses.edit', $business->id)); ?>" class="btn-default !p-2" title="<?php echo e(__('Edit')); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if(auth()->user()->can('business.delete') && $businesses->count() > 1): ?>
                                    <button 
                                        data-modal-target="delete-modal-<?php echo e($business->id); ?>" 
                                        data-modal-toggle="delete-modal-<?php echo e($business->id); ?>"
                                        class="btn-danger !p-2" 
                                        title="<?php echo e(__('Delete')); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Delete Modal -->
                                    <div id="delete-modal-<?php echo e($business->id); ?>" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                        <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-<?php echo e($business->id); ?>">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"><?php echo e(__('Are you sure you want to delete this business?')); ?></h3>
                                                <form action="<?php echo e(route('admin.businesses.destroy', $business->id)); ?>" method="POST">
                                                    <?php echo method_field('DELETE'); ?>
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        <?php echo e(__('Yes, Delete')); ?>

                                                    </button>
                                                    <button data-modal-hide="delete-modal-<?php echo e($business->id); ?>" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                                        <?php echo e(__('Cancel')); ?>

                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="p-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-building text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4"><?php echo e(__('No businesses found')); ?></p>
                                    <?php if(auth()->user()->can('business.create')): ?>
                                    <a href="<?php echo e(route('admin.businesses.create')); ?>" class="btn-primary">
                                        <i class="bi bi-plus-circle mr-2"></i>
                                        <?php echo e(__('Create Your First Business')); ?>

                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($businesses->hasPages()): ?>
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800">
                <?php echo e($businesses->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/pages/businesses/index.blade.php ENDPATH**/ ?>