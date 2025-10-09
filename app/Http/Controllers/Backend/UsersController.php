<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Services\RolesService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly RolesService $rolesService
    ) {
    }

    /**
     * Get the current business (same approach as DashboardController).
     */
    protected function getCurrentBusiness()
    {
        if (app()->has('current_business')) {
            return app('current_business');
        }

        return auth()->user()?->businesses()->first();
    }

    public function index(): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.view']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $users = $this->userService->getUsers($business->id);

        return view('backend.pages.users.index', [
            'users' => $users,
            'roles' => $this->rolesService->getRolesDropdown(),
        ]);
    }

    public function create(): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.create']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        ld_do_action('user_create_page_before');

        return view('backend.pages.users.create', [
            'roles' => $this->rolesService->getRolesDropdown(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.create']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $user = $this->userService->createUser($request->all(), $business->id);

        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        $this->storeActionLog(ActionType::CREATED, ['user' => $user]);

        session()->flash('success', __('User has been created.'));
        ld_do_action('user_store_after', $user);

        return redirect()->route('admin.users.index');
    }

    public function edit(int $id): Renderable|RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.edit']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $user = $this->userService->getUserById($id, $business->id);

        ld_do_action('user_edit_page_before');
        $user = ld_apply_filters('user_edit_page_before_with_user', $user);

        return view('backend.pages.users.edit', [
            'user' => $user,
            'roles' => $this->rolesService->getRolesDropdown(),
        ]);
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.edit']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $user = $this->userService->getUserById($id, $business->id);

        $this->preventSuperAdminModification($user);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->business_id = $business->id;

        $user = ld_apply_filters('user_update_before_save', $user, $request);
        $user->save();
        $user = ld_apply_filters('user_update_after_save', $user, $request);
        ld_do_action('user_update_after', $user);

        $user->roles()->sync($request->roles ?? []);

        $this->storeActionLog(ActionType::UPDATED, ['user' => $user]);

        session()->flash('success', 'User has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['user.delete']);

        $business = $this->getCurrentBusiness();
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $user = $this->userService->getUserById($id, $business->id);

        $this->preventSuperAdminModification($user);

        $user = ld_apply_filters('user_delete_before', $user);
        $user->delete();
        $user = ld_apply_filters('user_delete_after', $user);

        $this->storeActionLog(ActionType::DELETED, ['user' => $user]);
        ld_do_action('user_delete_after', $user);

        session()->flash('success', 'User has been deleted.');
        return back();
    }
}
