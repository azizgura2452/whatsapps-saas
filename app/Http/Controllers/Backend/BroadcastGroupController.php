<?php
// app/Http/Controllers/Backend/BroadcastGroupController.php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BroadcastGroup;
use App\Services\BroadcastGroupService;
use App\Services\CustomerImportService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use App\Models\CustomerAttribute;

class BroadcastGroupController extends Controller
{
    public function __construct(
        private readonly BroadcastGroupService $service,
        private readonly CustomerImportService $importService
    ) {
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
        $group = $this->service->createOrUpdate($data);

        // Handle CSV import if file is uploaded
        if ($request->hasFile('customer_csv')) {
            $request->validate([
                'customer_csv' => 'required|file|mimes:csv,txt|max:10240',
            ]);

            $result = $this->importService->importFromCsv(
                $request->file('customer_csv'),
                $group->id
            );

            if ($result['success']) {
                session()->flash('success', __('Broadcast group created with :count customers imported.', [
                    'count' => $result['imported']
                ]));
            } else {
                session()->flash('warning', __('Broadcast group created but import had errors: :errors', [
                    'errors' => implode(', ', $result['errors'])
                ]));
            }
        } else {
            session()->flash('success', __('Broadcast group created successfully.'));
        }

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

        // Handle CSV import if file is uploaded
        if ($request->hasFile('customer_csv')) {
            $request->validate([
                'customer_csv' => 'required|file|mimes:csv,txt|max:10240',
            ]);

            $result = $this->importService->importFromCsv(
                $request->file('customer_csv'),
                $group->id
            );

            if ($result['success']) {
                session()->flash('success', __('Broadcast group updated with :count customers imported.', [
                    'count' => $result['imported']
                ]));
            } else {
                session()->flash('warning', __('Broadcast group updated but import had errors.'));
            }
        } else {
            session()->flash('success', __('Broadcast group updated successfully.'));
        }

        return redirect()->route('admin.broadcast-groups.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $group = $this->service->getGroupById($id);
        $this->service->delete($group);

        session()->flash('success', __('Broadcast group deleted successfully.'));
        return back();
    }

    public function importCustomers(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'customer_csv' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $result = $this->importService->importFromCsv(
            $request->file('customer_csv'),
            $id
        );

        if ($result['success']) {
            session()->flash('success', __(':count customers imported successfully. Failed: :failed', [
                'count' => $result['imported'],
                'failed' => $result['failed']
            ]));
        } else {
            session()->flash('error', __('Import failed: :error', [
                'error' => implode(', ', $result['errors'])
            ]));
        }

        return back();
    }

    public function downloadTemplate()
    {
        $csvContent = "phone,name,email,city,age,gender,purchase_history\n";
        $csvContent .= "+1234567890,John Doe,john@example.com,New York,30,Male,Premium\n";
        $csvContent .= "+0987654321,Jane Smith,jane@example.com,Los Angeles,25,Female,Regular\n";
        $csvContent .= "+1122334455,Bob Johnson,bob@example.com,Chicago,35,Male,VIP\n";

        $fileName = 'customer_import_template_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }
}