<aside
    :class="sidebarToggle ? 'translate-x-0 lg:w-[85px] app-sidebar-minified' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-10 flex h-screen w-[290px] flex-col overflow-y-hidden border-r transition-all duration-300 ease-in-out <?php echo e(config('settings.sidebar_bg_lite') ? '' : 'bg-gray-800'); ?> dark:border-gray-900 dark:bg-gray-900 lg:static lg:translate-x-0"
    id="appSidebar"
    x-data="{
        isHovered: false,
        init() {
            this.updateBg();
            const observer = new MutationObserver(() => this.updateBg());
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            
            // Check if sidebarToggle value is present in localStorage and use it
            if (localStorage.getItem('sidebarToggle')) {
                sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle'));
            }
        },
        updateBg() {
            const htmlHasDark = document.documentElement.classList.contains('dark');
            const liteBg = '<?php echo e(config('settings.sidebar_bg_lite')); ?>';
            const darkBg = '<?php echo e(config('settings.sidebar_bg_dark')); ?>';
            this.$el.style.backgroundColor = htmlHasDark ? darkBg : liteBg;
        }
    }"
    x-init="init()"
>
    <!-- Sidebar Header -->
    <div
        :class="sidebarToggle"
        class="justify-center flex items-center gap-2 sidebar-header py-5 px-5 h-[100px] transition-all duration-300"
    >
        <a href="<?php echo e(route('admin.dashboard')); ?>">
            <span class="logo transition-opacity duration-300" :class="sidebarToggle && !isHovered ? 'hidden opacity-0' : 'opacity-100'">
                <img
                    class="dark:hidden max-h-[80px]"
                    src="<?php echo e(config('settings.site_logo_lite') ?? asset('images/logo/lara-dashboard.png')); ?>"
                    alt="<?php echo e(config('app.name')); ?>"
                />
                <img
                    class="hidden dark:block max-h-[80px]"
                    src="<?php echo e(config('settings.site_logo_dark') ?? '/images/logo/lara-dashboard-dark.png'); ?>"
                    alt="<?php echo e(config('app.name')); ?>"
                />
            </span>
            <!-- <img
                class="logo-icon w-20 lg:w-12 transition-opacity duration-300"
                :class="sidebarToggle && !isHovered ? 'lg:block opacity-100' : 'hidden opacity-0'"
                src="<?php echo e(config('settings.site_icon') ?? '/images/logo/icon.png'); ?>"
                alt="<?php echo e(config('app.name')); ?>"
            /> -->
        </a>
    </div>
    <!-- End Sidebar Header -->

    <div
        class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
    >
        <?php echo $__env->make('backend.layouts.partials.sidebar-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</aside>
<!-- End Sidebar -->
<?php /**PATH C:\xampp\htdocs\varsity\resources\views/backend/layouts/partials/sidebar-logo.blade.php ENDPATH**/ ?>