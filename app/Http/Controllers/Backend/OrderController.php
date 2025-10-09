<?php
namespace App\Http\Controllers\Backend;


use App\Enums\ActionType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Enum;


class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    protected function getCurrentBusiness()
    {
        if (app()->has('current_business')) {
            return app('current_business');
        }
        return auth()->user()->businesses()->first();
    }

    public function index(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['orders.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $search = $request->input('search');
        $perPage = config('settings.default_pagination', 10);

        $orders = Order::where('business_id', $business->id)
            ->when($search, function($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
            })
            ->with(['customer', 'items.product'])
            ->latest('created_on')
            ->paginate($perPage);

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'business' => $business,
        ]);
    }

    public function create()
    {
        return view('backend.pages.orders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['orders.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total' => 'required|numeric|min:0',
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order = new Order();
        $order->business_id = $business->id;
        $order->customer_id = $request->customer_id;
        $order->total = $request->total;
        $order->status = $request->status;
        $order->save();

        $this->storeActionLog(ActionType::CREATED, ['order' => $order]);

        return redirect()->route('admin.orders.index')->with('success', __('Order created successfully.'));
    }

    public function show($id)
    {
        $this->checkAuthorization(auth()->user(), ['orders.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $order = Order::where('business_id', $business->id)
            ->with(['customer', 'items.product'])
            ->findOrFail($id);

        return view('backend.pages.orders.show', [
            'order' => $order,
            'business' => $business,
        ]);
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['orders.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $order = Order::where('business_id', $business->id)->findOrFail($id);

        return view('backend.pages.orders.edit', [
            'order' => $order,
            'business' => $business,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['orders.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $order = Order::where('business_id', $business->id)->findOrFail($id);

        $request->validate([
            'total' => 'required|numeric|min:0',
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order->total = $request->input('total');
        $order->status = $request->input('status');

        $order = ld_apply_filters('order_update_before_save', $order, $request);
        $order->save();
        $order = ld_apply_filters('order_update_after_save', $order, $request);
        ld_do_action('order_update_after', $order);

        $this->storeActionLog(ActionType::UPDATED, ['order' => $order]);

        session()->flash('success', __('Order has been updated.'));

        return back();
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', new Enum(OrderStatus::class)],
        ]);

        $order->status = OrderStatus::from($request->input('status'));

        $order->save();
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }


    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['orders.delete']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $order = Order::where('business_id', $business->id)->findOrFail($id);

        $order = ld_apply_filters('order_delete_before', $order);
        $order->delete();
        $order = ld_apply_filters('order_delete_after', $order);

        $this->storeActionLog(ActionType::DELETED, ['order' => $order]);

        ld_do_action('order_delete_after', $order);

        return redirect()->route('admin.orders.index')->with('success', __('Order deleted successfully.'));
    }
}
