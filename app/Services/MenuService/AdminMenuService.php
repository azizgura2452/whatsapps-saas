<?php

namespace App\Services\MenuService;

use App\Services\MenuService\AdminMenuItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminMenuService
{
    /**
     * @var AdminMenuItem[][]
     */
    protected array $groups = [];

    /**
     * Add a menu item to the admin sidebar.
     *
     * @param AdminMenuItem|array $item The menu item or configuration array
     * @param string|null $group The group to add the item to
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addMenuItem(AdminMenuItem|array $item, ?string $group = null)
    {
        $group = $group ?: __('Main');
        $menuItem = $this->createAdminMenuItem($item);

        if (!isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }

        Log::info("Attempting to add menu item", ['label' => $menuItem->label]);

        if ($menuItem->userHasPermission()) {
            Log::info("User has permission for menu item", ['label' => $menuItem->label]);
            $this->groups[$group][] = $menuItem;
        } else {
            Log::warning("User does NOT have permission for menu item", ['label' => $menuItem->label, 'permissions' => $menuItem->permissions]);
        }
    }

    protected function createAdminMenuItem(AdminMenuItem|array $data): AdminMenuItem
    {
        if ($data instanceof AdminMenuItem) {
            return $data;
        }

        $menuItem = new AdminMenuItem();

        if (isset($data['children']) && is_array($data['children'])) {
            $data['children'] = array_map(
                fn($child) => auth()->user()->hasAnyPermission($child['permissions'] ?? [])
                ? $this->createAdminMenuItem($child)
                : null,
                $data['children']
            );

            // Filter out null values (items without permission).
            $data['children'] = array_filter($data['children']);
        }

        return $menuItem->setAttributes($data);
    }

    public function getMenu()
    {
        $this->addMenuItem([
            'label' => __('Dashboard'),
            'icon' => 'dashboard.svg',
            'route' => route('admin.dashboard'),
            'active' => Route::is('admin.dashboard'),
            'id' => 'dashboard',
            'priority' => 1,
            'permissions' => 'dashboard.view'
        ]);

        $this->addMenuItem([
            'label' => __('Products'),
            'iconClass' => 'bi bi-bag text-lg',
            'id' => 'products-submenu',
            'active' => Route::is('admin.products.*'),
            'priority' => 1,
            'permissions' => ['products.create', 'products.view', 'products.edit', 'products.delete'],
            'children' => [
                [
                    'label' => __('Products'),
                    'iconClass' => 'fa fa-list',
                    'route' => route('admin.products.index'),
                    'active' => Route::is('admin.products.index') || Route::is('admin.products.edit'),
                    'priority' => 20,
                    'permissions' => 'products.view'
                ],
                [
                    'label' => __('New Product'),
                    'iconClass' => 'fa fa-plus',
                    'route' => route('admin.products.create'),
                    'active' => Route::is('admin.products.create'),
                    'priority' => 10,
                    'permissions' => 'products.create'
                ]
            ]
        ]);

        $this->addMenuItem([
            'label' => __('Orders'),
            'iconClass' => 'bi bi-cart-check text-lg',
            'id' => 'orders-submenu',
            'active' => Route::is('admin.orders.*'),
            'priority' => 1,
            'permissions' => ['orders.create', 'orders.view', 'orders.edit', 'orders.delete'],
            'route' => route('admin.orders.index'),
        ]);

        // Add this after the Marketing menu item
$this->addMenuItem([
    'label' => __('Flow Builder'),
    'iconClass' => 'bi bi-diagram-3 text-lg',
    'id' => 'flow-builder-submenu',
    'active' => Route::is('admin.flow-builder.*'),
    'priority' => 15,
    'permissions' => ['flow.create', 'flow.view', 'flow.edit', 'flow.delete'],
    'children' => [
        [
            'label' => __('Flow Steps'),
            'iconClass' => 'fa fa-list',
            'route' => route('admin.flow-builder.index'),
            'active' => Route::is('admin.flow-builder.index') || Route::is('admin.flow-builder.edit'),
            'priority' => 20,
            'permissions' => 'flow.view'
        ],
        [
            'label' => __('New Flow Step'),
            'iconClass' => 'fa fa-plus',
            'route' => route('admin.flow-builder.create'),
            'active' => Route::is('admin.flow-builder.create'),
            'priority' => 10,
            'permissions' => 'flow.create'
        ]
    ]
]);

        // Add Business Management menu
        $this->addMenuItem([
            'label' => __('Businesses'),
            'iconClass' => 'bi bi-building text-lg',
            'id' => 'businesses-submenu',
            'active' => Route::is('admin.businesses.*'),
            'priority' => 5,
            'permissions' => ['business.create', 'business.view', 'business.edit', 'business.delete'],
            'children' => [
                [
                    'label' => __('All Businesses'),
                    'iconClass' => 'fa fa-list',
                    'route' => route('admin.businesses.index'),
                    'active' => Route::is('admin.businesses.index') || Route::is('admin.businesses.edit'),
                    'priority' => 20,
                    'permissions' => 'business.view'
                ],
                [
                    'label' => __('New Business'),
                    'iconClass' => 'fa fa-plus',
                    'route' => route('admin.businesses.create'),
                    'active' => Route::is('admin.businesses.create'),
                    'priority' => 10,
                    'permissions' => 'business.create'
                ]
            ]
        ]);

        $this->addMenuItem([
            'label' => __('WhatsApp Chats'),
            'iconClass' => 'fa fa-whatsapp',
            'id' => 'chatbox-submenu',
            'active' => Route::is('admin.whatsapp.chatbox'),
            'priority' => 19,
            'permissions' => ['chatbox.create', 'chatbox.view', 'chatbox.edit', 'chatbox.delete'],
            'route' => route('admin.whatsapp.chatbox'),
        ]);

        $this->addMenuItem([
            'label' => __('Customers'),
            'icon' => 'user.svg',
            'id' => 'customers-submenu',
            'active' => Route::is('admin.customers.*'),
            'priority' => 19,
            'permissions' => ['customers.create', 'customers.view', 'customers.edit', 'customers.delete'],
            'children' => [
                [
                    'label' => __('List Customers'),
                    'icon' => 'user.svg',
                    'route' => route('admin.customers.index'),
                    'active' => Route::is('admin.customers.index') || Route::is('admin.customers.edit'),
                    'priority' => 20,
                    'permissions' => 'customers.view'
                ],
                [
                    'label' => __('New Customer'),
                    'iconClass' => 'fa fa-plus',
                    'route' => route('admin.customers.create'),
                    'active' => Route::is('admin.customers.create'),
                    'priority' => 10,
                    'permissions' => 'customers.create'
                ]
            ]
        ]);

        $this->addMenuItem([
            'label' => __('Marketing'),
            'iconClass' => 'fa fa-bullseye',
            'id' => 'marketing-submenu',
            'active' => Route::is('admin.whatsapp-templates.*'),
            'priority' => 20,
            'permissions' => ['templates.create', 'templates.view', 'templates.edit', 'templates.delete', 'broadcasts.create', 'broadcasts.view', 'broadcasts.edit', 'broadcasts.delete'],
            'children' => [
                [
                    'label' => __('WhatsApp Templates'),
                    'route' => route('admin.whatsapp-templates.index'),
                    'active' => Route::is('admin.whatsapp-templates.index') || Route::is('admin.whatsapp-templates.edit'),
                    'priority' => 20,
                    'permissions' => 'templates.view'
                ],
                [
                    'label' => __('Broadcast Groups'),
                    'iconClass' => 'fa fa-users',
                    'route' => route('admin.broadcast-groups.index'),
                    'active' => Route::is('admin.broadcast-groups.index') || Route::is('admin.broadcast-groups.edit'),
                    'priority' => 10,
                    'permissions' => 'broadcasts.view'
                ],
                [
                    'label' => __('Broadcasts'),
                    'iconClass' => 'fa fa-bullhorn',
                    'route' => route('admin.broadcasts.index'),
                    'active' => Route::is('admin.broadcasts.index') || Route::is('admin.broadcasts.edit'),
                    'priority' => 12,
                    'permissions' => 'broadcasts.view'
                ]
            ]
        ]);

        $this->addMenuItem([
            'label' => __('User'),
            'icon' => 'user.svg',
            'id' => 'users-submenu',
            'active' => Route::is('admin.users.*'),
            'priority' => 1,
            'permissions' => ['user.create', 'user.view', 'user.edit', 'user.delete'],
            'children' => [
                [
                    'label' => __('Users'),
                    'iconClass' => 'fa fa-list',
                    'route' => route('admin.users.index'),
                    'active' => Route::is('admin.users.index') || Route::is('admin.users.edit'),
                    'priority' => 20,
                    'permissions' => 'user.view'
                ],
                [
                    'label' => __('New User'),
                    'iconClass' => 'fa fa-plus',
                    'route' => route('admin.users.create'),
                    'active' => Route::is('admin.users.create'),
                    'priority' => 10,
                    'permissions' => 'user.create'
                ]
            ]
        ], __('More'));

        // $this->addMenuItem([
        //     'label' => __('Modules'),
        //     'icon' => 'three-dice.svg',
        //     'route' => route('admin.modules.index'),
        //     'active' => Route::is('admin.modules.index'),
        //     'id' => 'modules',
        //     'priority' => 30,
        //     'permissions' => 'module.view'
        // ]);

        // $this->addMenuItem([
        //     'label' => __('Monitoring'),
        //     'icon' => 'tv.svg',
        //     'id' => 'monitoring-submenu',
        //     'active' => Route::is('admin.actionlog.*'),
        //     'priority' => 40,
        //     'permissions' => ['pulse.view', 'actionlog.view'],
        //     'children' => [
        //         [
        //             'label' => __('Action Logs'),
        //             'route' => route('admin.actionlog.index'),
        //             'active' => Route::is('admin.actionlog.index'),
        //             'priority' => 20,
        //             'permissions' => 'actionlog.view'
        //         ],
        //         [
        //             'label' => __('Laravel Pulse'),
        //             'route' => route('pulse'),
        //             'active' => false,
        //             'target' => '_blank',
        //             'priority' => 10,
        //             'permissions' => 'pulse.view'
        //         ]
        //     ]
        // ]);

        $this->addMenuItem([
            'label' => __('Roles & Permissions'),
            'icon' => 'key.svg',
            'id' => 'roles-submenu',
            'active' => Route::is('admin.roles.*'),
            'priority' => 2,
            'permissions' => ['role.create', 'role.view', 'role.edit', 'role.delete'],
            'children' => [
                [
                    'label' => __('Roles'),
                    'iconClass' => 'fa fa-list',
                    'route' => route('admin.roles.index'),
                    'active' => Route::is('admin.roles.index') || Route::is('admin.roles.edit'),
                    'priority' => 1,
                    'permissions' => 'role.view'
                ],
                [
                    'label' => __('New Role'),
                    'iconClass' => 'fa fa-plus',
                    'route' => route('admin.roles.create'),
                    'active' => Route::is('admin.roles.create'),
                    'priority' => 2,
                    'permissions' => 'role.create'
                ],
                [
                    'label' => __('Permissions'),
                    'iconClass' => 'fa fa-lock',
                    'route' => route('admin.permissions.index'),
                    'active' => Route::is('admin.permissions.index') || Route::is('admin.permissions.show'),
                    'priority' => 3,
                    'permissions' => 'role.view'
                ]
            ]
        ], __('More'));


        $this->addMenuItem([
            'label' => __('Settings'),
            'icon' => 'settings.svg',
            'id' => 'settings-submenu',
            'active' => Route::is('admin.settings.*') || Route::is('admin.translations.*'),
            'priority' => 3,
            'permissions' => ['settings.edit', 'translations.view'],
            'children' => [
                [
                    'label' => __('General Settings'),
                    'iconClass' => 'fa fa-list',
                    'route' => route('admin.settings.index'),
                    'active' => Route::is('admin.settings.index'),
                    'priority' => 20,
                    'permissions' => 'settings.edit'
                ],
                [
                    'label' => __('Translations'),
                    'route' => route('admin.translations.index'),
                    'active' => Route::is('admin.translations.*'),
                    'priority' => 10,
                    'permissions' => ['translations.view', 'translations.edit']
                ]
            ]
        ], __('More'));

        $this->addMenuItem([
            'label' => __('Logout'),
            'icon' => 'logout.svg',
            'route' => route('admin.dashboard'),
            'active' => false,
            'id' => 'logout',
            'priority' => 4,
            'html' => '
                <li class="hover:menu-item-active">
                    <form method="POST" action="' . route('logout') . '">
                        ' . csrf_field() . '
                        <button type="submit" class="menu-item group w-full text-left menu-item-inactive text-black dark:text-white hover:text-black">
                            <img src="' . asset('images/icons/logout.svg') . '" alt="Logout" class="menu-item-icon dark:invert">
                            <span class="menu-item-text">' . __('Logout') . '</span>
                        </button>
                    </form>
                </li>
            '
        ], __('More'));

        $this->sortMenuItemsByPriority();
        return $this->applyFiltersToMenuItems();
    }

    protected function sortMenuItemsByPriority(): void
    {
        foreach ($this->groups as &$groupItems) {
            usort($groupItems, function ($a, $b) {
                return $a->priority <=> $b->priority;
            });
        }
    }

    protected function applyFiltersToMenuItems(): array
    {
        $result = [];
        foreach ($this->groups as $group => $items) {
            // Filter items by permission.
            $filteredItems = array_filter($items, function (AdminMenuItem $item) {
                return $item->userHasPermission();
            });

            // Apply filters that might add/modify menu items.
            $filteredItems = ld_apply_filters('sidebar_menu_' . strtolower($group), $filteredItems);

            // Only add the group if it has items after filtering.
            if (!empty($filteredItems)) {
                $result[$group] = $filteredItems;
            }
        }

        return $result;
    }

    public function shouldExpandSubmenu(AdminMenuItem $menuItem): bool
    {
        // If the parent menu item is active, expand the submenu.
        if ($menuItem->active) {
            return true;
        }

        // Check if any child menu item is active.
        foreach ($menuItem->children as $child) {
            if ($child->active) {
                return true;
            }
        }

        return false;
    }

    public function render(array $groupItems): string
    {
        $html = '';
        foreach ($groupItems as $menuItem) {
            $filterKey = $menuItem->id ?? Str::slug($menuItem->label) ?? '';
            $html .= ld_apply_filters('sidebar_menu_before_' . $filterKey, '');

            $html .= view('backend.layouts.partials.menu-item', [
                'item' => $menuItem,
            ])->render();

            $html .= ld_apply_filters('sidebar_menu_after_' . $filterKey, '');
        }

        return $html;
    }
}
