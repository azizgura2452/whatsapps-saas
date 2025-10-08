<?php
    /** @var \App\Services\MenuService\AdminMenuItem $item */
?>

<?php if(isset($item->htmlData)): ?>
    <?php echo $item->htmlData; ?>

    <?php if(isset($item->id)): ?>
        <?php echo ld_apply_filters('sidebar_menu_item_after_' . strtolower($item->id), ''); ?>

    <?php endif; ?>
<?php elseif(!empty($item->children)): ?>
    <?php
        $submenuId = $item->id ?? \Str::slug($item->label) . '-submenu';
        $isActive = $item->active ? 'menu-item-active' : 'menu-item-inactive';
        $showSubmenu = app(\App\Services\MenuService\AdminMenuService::class)->shouldExpandSubmenu($item);
        $rotateClass = $showSubmenu ? 'rotate-180' : '';
    ?>

    <li class="hover:menu-item-active">
        <button :style="`color: ${textColor}`" class="menu-item group w-full text-left <?php echo e($isActive); ?>" type="button" onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.menu-item-arrow').classList.toggle('rotate-180')">
            <?php if(!empty($item->icon)): ?>
                <img src="<?php echo e(asset('images/icons/' . $item->icon)); ?>" alt="<?php echo $item->label; ?>" class="menu-item-icon dark:invert">
            <?php elseif(!empty($item->iconClass)): ?>
                <i class="<?php echo e($item->iconClass); ?> menu-item-icon"></i>
            <?php endif; ?>
            <span class="menu-item-text"><?php echo $item->label; ?></span>
            <img src="<?php echo e(asset('images/icons/chevron-down.svg')); ?>" alt="Arrow" class="menu-item-arrow dark:invert transition-transform duration-300 <?php echo e($rotateClass); ?>">
        </button>
        <ul id="<?php echo e($submenuId); ?>" class="submenu pl-12 mt-2 space-y-2 overflow-hidden <?php echo e($showSubmenu ? '' : 'hidden'); ?>">
            <?php $__currentLoopData = $item->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('backend.layouts.partials.menu-item', ['item' => $child], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </li>
    <?php if(isset($item->id)): ?>
        <?php echo ld_apply_filters('sidebar_menu_item_after_' . strtolower($item->id), ''); ?>

    <?php endif; ?>
<?php else: ?>
    <?php
        $isActive = $item->active ? 'menu-item-active' : 'menu-item-inactive';
        $target = !empty($item->target) ? ' target="' . e($item->target) . '"' : '';
    ?>

    <li class="hover:menu-item-active">
        <a :style="`color: ${textColor}`" href="<?php echo e($item->route ?? '#'); ?>" class="menu-item group <?php echo e($isActive); ?>" <?php echo $target; ?>>
            <?php if(!empty($item->icon)): ?>
                <img src="<?php echo e(asset('images/icons/' . $item->icon)); ?>" alt="<?php echo $item->label; ?>" class="menu-item-icon dark:invert">
            <?php elseif(!empty($item->iconClass)): ?>
                <i class="<?php echo e($item->iconClass); ?> menu-item-icon"></i>
            <?php endif; ?>
            <span class="menu-item-text"><?php echo $item->label; ?></span>
        </a>
    </li>
    <?php if(isset($item->id)): ?>
        <?php echo ld_apply_filters('sidebar_menu_item_after_' . strtolower($item->id), ''); ?>

    <?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/layouts/partials/menu-item.blade.php ENDPATH**/ ?>