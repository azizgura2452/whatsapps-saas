<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BroadcastGroup;
use App\Services\BroadcastGroupService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use App\Models\CustomerAttribute;

class BroadcastGroupController extends Controller
{
    public function __construct(private readonly BroadcastGroupService $service)
    {
    }

    public function index(): Renderable
    {
        $groups = $this->service->getGroups();
        return view('backend.pages.broadcast_groups.index', compact('groups'));
    }

    public function create(): Renderable
    {
        $attributes = CustomerAttribute::select('key')->distinct()->pluck('key');
        return view('backend.pages.broadcast_groups.create', compact('attributes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->all();
        $this->service->createOrUpdate($data);
        session()->flash('success', __('Broadcast group created successfully.'));
        return redirect()->route('admin.broadcast-groups.index');
    }

    public function edit(int $id): Renderable
    {
        $group = $this->service->getGroupById($id);
        $attributes = CustomerAttribute::select('key')->distinct()->pluck('key');
        return view('backend.pages.broadcast_groups.edit', compact('group', 'attributes'));
    }

    public function getAttributeValues(string $key)
    {
        $values = CustomerAttribute::where('key', $key)
            ->select('value')
            ->distinct()
            ->pluck('value');

        return response()->json($values);
    }


    public function update(Request $request, int $id): RedirectResponse
    {
        $group = $this->service->getGroupById($id);
        $this->service->createOrUpdate($request->all(), $group);

        session()->flash('success', __('Broadcast group updated successfully.'));
        return redirect()->route('admin.broadcast-groups.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $group = $this->service->getGroupById($id);
        $this->service->delete($group);

        session()->flash('success', __('Broadcast group deleted successfully.'));
        return back();
    }
}
