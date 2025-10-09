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

    protected function getCurrentBusiness()
    {
        if (app()->has('current_business')) {
            return app('current_business');
        }
        return auth()->user()->businesses()->first();
    }

    protected function requireBusiness()
    {
        $business = $this->getCurrentBusiness();
        
        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }
        
        return $business;
    }

    public function index(Request $request): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        // Get all distinct attribute keys for this business's customers
        $attributes = CustomerAttribute::whereHas('customer', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->select('key')
        ->distinct()
        ->pluck('key');

        // Get all distinct values for this business's customers
        $values = CustomerAttribute::whereHas('customer', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->select('value')
        ->distinct()
        ->pluck('value');

        $search = $request->input('search');
        $perPage = config('settings.default_pagination', 10);

        $customers = Customer::where('business_id', $business->id)
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('whatsapp_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with('attributes')
            ->latest('created_on')
            ->paginate($perPage);

        return view('backend.pages.customers.index', [
            'customers' => $customers,
            'attributes' => $attributes,
            'values' => $values,
            'business' => $business,
        ]);
    }

    public function getAttributeValues(string $key)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return response()->json([]);
        }

        $values = CustomerAttribute::whereHas('customer', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->where('key', $key)
        ->select('value')
        ->distinct()
        ->pluck('value');

        return response()->json($values);
    }

    public function sendMessage(Request $request, int $customerId)
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return response()->json(['success' => false, 'message' => 'No business selected'], 400);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $customer = Customer::where('business_id', $business->id)->findOrFail($customerId);

        // Send via WhatsApp Cloud API
        $this->whatsAppService->sendText($customer->whatsapp_number, $business, $request->message);
        
        return response()->json(['success' => true]);
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        ld_do_action('customer_create_page_before');

        return view('backend.pages.customers.create', [
            'business' => $business,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|unique:customers,whatsapp_number,NULL,id,business_id,' . $business->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string',
        ]);

        $customer = new Customer();
        $customer->business_id = $business->id;
        $customer->name = $request->name;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->address = $request->address;
        $customer->email = $request->email;
        $customer->birthday = $request->birthday;
        $customer->gender = $request->gender;
        $customer->save();

        // Handle dynamic attributes
        $attributes = $request->input('attributes', []);
        if (!empty($attributes['key'])) {
            foreach ($attributes['key'] as $i => $key) {
                $value = $attributes['value'][$i] ?? null;
                if ($key && $value) {
                    $customer->attributes()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        session()->flash('success', __('Customer has been created.'));
        return redirect()->route('admin.customers.index');
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($id);

        ld_do_action('customer_edit_page_before');

        $customer = ld_apply_filters('customer_edit_page_before_with_user', $customer);

        return view('backend.pages.customers.edit', [
            'customer' => $customer,
            'business' => $business,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|unique:customers,whatsapp_number,' . $id . ',id,business_id,' . $business->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string',
        ]);

        $customer->name = $request->name;
        $customer->whatsapp_number = $request->whatsapp_number;
        $customer->address = $request->address;
        $customer->email = $request->email;
        $customer->birthday = $request->birthday;
        $customer->gender = $request->gender;
        $customer->save();

        // Refresh attributes
        $customer->attributes()->delete();

        $attributes = $request->input('attributes', []);
        if (!empty($attributes['key'])) {
            foreach ($attributes['key'] as $i => $key) {
                $value = $attributes['value'][$i] ?? null;
                if ($key && $value) {
                    $customer->attributes()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        session()->flash('success', 'Customer has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.delete']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($id);

        $customer = ld_apply_filters('customer_delete_before', $customer);
        $customer->delete();
        $customer = ld_apply_filters('customer_delete_after', $customer);
        
        session()->flash('success', 'Customer has been deleted.');

        $this->storeActionLog(ActionType::DELETED, ['customer' => $customer]);

        ld_do_action('customer_delete_after', $customer);

        return back();
    }

    public function orders(int $customerId): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['orders.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $customer = Customer::where('business_id', $business->id)->findOrFail($customerId);
        
        $orders = $customer->orders()
            ->where('business_id', $business->id)
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('backend.pages.customers.orders', [
            'customer' => $customer,
            'orders' => $orders,
            'business' => $business,
        ]);
    }

    public function chat(int $customerId): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $customer = Customer::where('business_id', $business->id)
            ->with([
                'whatsappConversation.messages' => function ($query) use ($business) {
                    $query->where('business_id', $business->id)
                          ->orderBy('timestamp', 'asc');
                }
            ])
            ->findOrFail($customerId);

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
            'business' => $business,
        ]);
    }

    public function fetchMessages(Request $request, int $customerId)
    {
        $business = $this->getCurrentBusiness();

        if (!$business) {
            return response()->json(['html' => '', 'lastTs' => 0, 'count' => 0]);
        }

        $customer = Customer::where('business_id', $business->id)
            ->with('whatsappConversation')
            ->findOrFail($customerId);
            
        $conversation = $customer->whatsappConversation;

        if (!$conversation) {
            return response()->json(['html' => '', 'lastTs' => 0, 'count' => 0]);
        }

        $since = $request->query('since');

        $messages = WhatsAppMessage::where('conversation_id', $conversation->id)
            ->where('business_id', $business->id)
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

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $request->validate([
            'customer_csv' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $result = $this->importService->importFromCsv(
            $request->file('customer_csv'),
            $business->id
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