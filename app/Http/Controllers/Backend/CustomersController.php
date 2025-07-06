<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Customer;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\CustomerService;
use App\Services\RolesService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly RolesService $rolesService
    ) {
    }

    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['user.view']);

        return view('backend.pages.customers.index', [
            'customers' => $this->customerService->getCustomers(),
        ]);
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        ld_do_action('customer_create_page_before');

        return view('backend.pages.customers.create');
    }

    public function store(FormRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.create']);

        $user = new Customer();
        $user->name = $request->name;
        $user->whatsapp_number = $request->whatsapp_number;

        $user = ld_apply_filters('customer_store_before_save', $user, $request);
        $user->save();
        $user = ld_apply_filters('customer_store_after_save', $user, $request);

        $this->storeActionLog(ActionType::CREATED, ['customer' => $user]);

        session()->flash('success', __('Customer has been created.'));

        ld_do_action('customer_store_after', $user);

        return redirect()->route('admin.customers.index');
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

    public function update(FormRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['customers.edit']);
        $user = Customer::findOrFail($id);

        $user->name = $request->name;
        $user->whatsapp_number = $request->whatsapp_number;

        $user = ld_apply_filters('customer_update_before_save', $user, $request);
        $user->save();
        $user = ld_apply_filters('customer_update_after_save', $user, $request);
        ld_do_action('customer_update_after', $user);

        $this->storeActionLog(ActionType::UPDATED, ['customer' => $user]);

        session()->flash('success', 'Customer has been updated.');

        return back();
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
        $this->checkAuthorization(auth()->user(), ['customers.view']); // Adjust permission if needed

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

        // Optional: log first message to check data format
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

    // In CustomerController or a new ChatController

    public function fetchMessages(Request $request, int $customerId)
    {
        $customer = Customer::with('whatsappConversation')->findOrFail($customerId);
        $conversation = $customer->whatsappConversation;

        $since = $request->query('since');

        $messages = WhatsAppMessage::where('conversation_id', $conversation->id)
            ->when($since, fn($query) => $query->where('timestamp', '>', $since))
            ->orderBy('timestamp', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }
}
