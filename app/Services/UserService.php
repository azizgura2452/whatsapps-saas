<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users, optionally scoped to a business.
     */
    public function getUsers(?int $businessId = null): LengthAwarePaginator
    {
        $query = User::query();

        // Business scoping
        if (!is_null($businessId)) {
            $query->where('business_id', $businessId);
        }

        // Text search
        if ($search = request()->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = request()->input('role')) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    }

    /**
     * Create a user and assign business_id (if provided).
     */
    public function createUser(array $data, ?int $businessId = null): User
    {
        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'username'    => $data['username'] ?? null,
            'password'    => Hash::make($data['password']),
            'business_id' => $businessId ?? ($data['business_id'] ?? null),
        ]);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Get a user by ID, optionally enforcing business scope.
     */
    public function getUserById(int $id, ?int $businessId = null): ?User
    {
        $query = User::query();

        if (!is_null($businessId)) {
            $query->where('business_id', $businessId);
        }

        return $query->findOrFail($id);
    }
}
