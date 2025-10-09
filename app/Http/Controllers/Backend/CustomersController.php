<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Customer;
use App\Models\CustomerAttribute;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\CustomerService;
use App\Services\RolesService;
use App\Services\CustomerImportService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Services\WhatsApp\WhatsAppService;

class CustomersController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly RolesService $rolesService,
        private readonly WhatsAppService $whatsAppService,
        private readonly CustomerImportService $importService
    ) {
    }

    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['user.view']);

        // get all distinct attribute keys
        $attributes = CustomerAttribute::select('key')
            ->distinct()
            ->pluck('key');

        // get all distinct values
        $values = CustomerAttribute::select('value')
            ->distinct()
            ->pluck('value');

        return view('backend.pages.customers.index', [
            'customers' => $this->customerService->getCustomers(),
            'attributes' => $attributes,
            'values' => $values,
        ]);
    }

    public function getAttributeValues(string $key)
    {
        $values = \App\Models\CustomerAttribute::where('key', $key)
            ->select('value')
            ->distinct()
            ->pluck('value');

        return response()->json($values);
    }

    public function sendMessage(Request $request, int $customerId)
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $request->validate([
            'message' => 'required|string',
        ]);

        $customer = Customer::findOrFail($customerId);

        // Send via WhatsApp Cloud API (or your wrapper service)
        $this->whatsAppService->sendText($customer->whatsapp_number, $request->message);
        return response()->json(['success' => true]);
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        ld_do_action('customer_create_page_before');

        return view('backend.pages.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        $user = new Customer();
        $user->name = $request->name;
        $user->whatsapp_number = $request->whatsapp_number;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->save();

        // handle dynamic attributes
        $attributes = $request->input('attributes', []);
        if (!empty($attributes['key'])) {
            foreach ($attributes['key'] as $i => $key) {
                $value = $attributes['value'][$i] ?? null;
                if ($key && $value) {
                    $user->attributes()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        session()->flash('success', __('Customer has been created.'));
        return redirect()->route('admin.customers.index');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $user = Customer::findOrFail($id);
        $user->name = $request->name;
        $user->whatsapp_number = $request->whatsapp_number;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->save();

        // refresh attributes
        $user->attributes()->delete();

        $attributes = $request->input('attributes', []);
        if (!empty($attributes['key'])) {
            foreach ($attributes['key'] as $i => $key) {
                $value = $attributes['value'][$i] ?? null;
                if ($key && $value) {
                    $user->attributes()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        session()->flash('success', 'Customer has been updated.');
        return back();
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $user = Customer::findOrFail($id);

        ld_do_action('customer_edit_page_before');

        $user = ld_apply_filters('customer_edit_page_before_with_user', $user);

        return view('backend.pages.customers.edit', [
            'customer' => $user
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.delete']);
        $user = $this->customerService->getCustomerById($id);

        $user = ld_apply_filters('customer_delete_before', $user);
        $user->delete();
        $user = ld_apply_filters('customer_delete_after', $user);
        session()->flash('success', 'Customer has been deleted.');

        $this->storeActionLog(ActionType::DELETED, ['customer' => $user]);

        ld_do_action('customer_delete_after', $user);

        return back();
    }

    public function orders(int $customerId): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['orders.view']);

        $customer = $this->customerService->getCustomerById($customerId);
        $orders = $this->customerService->getCustomerOrders($customerId);

        return view('backend.pages.customers.orders', [
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    public function chat(int $customerId): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.view']);

        $customer = Customer::with([
            'whatsappConversation.messages' => function ($query) {
                $query->orderBy('timestamp', 'asc');
            }
        ])->findOrFail($customerId);

        if (!$customer) {
            \Log::warning("Customer not found", ['customerId' => $customerId]);
        } else {
            \Log::info("Customer loaded", ['customer' => $customer->toArray()]);
        }

        \Log::info('Customer WhatsApp Conversation:', ['conversation' => $customer->whatsappConversation]);

        $messages = $customer->whatsappConversation->messages ?? collect();

        \Log::info("Loaded messages count", ['count' => $messages->count()]);

        if ($messages->isNotEmpty()) {
            \Log::debug("First message", ['message' => $messages->first()->toArray()]);
        } else {
            \Log::info("No messages found for customer", ['customerId' => $customerId]);
        }

        return view('backend.pages.customers.chat', [
            'customer' => $customer,
            'messages' => $messages,
        ]);
    }

    public function fetchMessages(Request $request, int $customerId)
    {
        $customer = Customer::with('whatsappConversation')->findOrFail($customerId);
        $conversation = $customer->whatsappConversation;

        $since = $request->query('since');

        $messages = WhatsAppMessage::where('conversation_id', $conversation->id)
            ->when($since, fn($query) => $query->where('timestamp', '>', $since))
            ->orderBy('timestamp', 'asc')
            ->get();

        $html = view('backend.pages.chatbox._messages', [
            'messages' => $messages,
        ])->render();

        return response()->json([
            'html' => $html,
            'lastTs' => $messages->last()->timestamp ?? $since,
            'count' => $messages->count(),
        ]);
    }

    /**
     * Import customers from CSV file
     */
    public function importCustomers(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        $request->validate([
            'customer_csv' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $result = $this->importService->importFromCsv(
            $request->file('customer_csv')
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

    /**
     * Download CSV template for customer import
     */
    public function downloadTemplate()
    {
        $csvContent = "phone,name,email,city,age,gender,purchase_history\n";
        $csvContent .= "96597021234,John Doe,john@example.com,Kuwait,30,Male,Premium\n";
        $csvContent .= "96597021235,Jane Smith,jane@example.com,Kuwait,25,Female,Regular\n";
        $csvContent .= "96597021236,Bob Johnson,bob@example.com,Kuwait,35,Male,VIP\n";

        $fileName = 'customer_import_template_' . date('Y-m-d') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }
}