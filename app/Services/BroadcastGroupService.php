<?php

namespace App\Services;

use App\Models\BroadcastGroup;
use App\Models\Customer;

class BroadcastGroupService
{
    public function getGroups()
    {
        return BroadcastGroup::with('conditions')
            ->where('business_id', app('current_business')->id)
            ->latest()
            ->paginate(config('settings.default_pagination') ?? 10);
    }

    public function getGroupById(int $id): BroadcastGroup
    {
        return BroadcastGroup::with('conditions')
            ->where('business_id', app('current_business')->id)
            ->findOrFail($id);
    }

    public function createOrUpdate(array $data, ?BroadcastGroup $group = null): BroadcastGroup
    {
        $group = $group ?? new BroadcastGroup();
        $group->name = $data['name'];
        $group->description = $data['description'] ?? null;
        if (!$group->exists) {
            $group->business_id = app('current_business')->id;
        }
        $group->save();

        // reset conditions
        $group->conditions()->delete();

        if (!empty($data['conditions']['field'])) {
            foreach ($data['conditions']['field'] as $i => $field) {
                $operator = $data['conditions']['operator'][$i] ?? '=';
                $value    = $data['conditions']['value'][$i] ?? null;

                if ($field && $value !== null) {
                    $group->conditions()->create([
                        'field'    => $field,
                        'operator' => $operator,
                        'value'    => $value,
                    ]);
                }
            }
        }

        return $group;
    }

    public function delete(BroadcastGroup $group): void
    {
        $group->delete();
    }

    /**
     * Fetch customers matching the group conditions
     */
    public function getCustomersForGroup(BroadcastGroup $group)
    {
        $query = Customer::query();

        foreach ($group->conditions as $condition) {
            if (in_array($condition->field, ['name', 'address', 'birthday', 'gender'])) {
                $query->where($condition->field, $condition->operator, $condition->value);
            } else {
                $query->whereHas('attributes', function ($q) use ($condition) {
                    if ($condition->operator === 'IN') {
                        $q->where('key', $condition->field)
                          ->whereIn('value', explode(',', $condition->value));
                    } else {
                        $q->where('key', $condition->field)
                          ->where('value', $condition->operator, $condition->value);
                    }
                });
            }
        }

        return $query->get();
    }
}
