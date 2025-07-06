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
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['orders.view']);

        return view('backend.pages.orders.index', [
            'orders' => $this->orderService->getOrders()
        ]);
    }

    public function create()
    {
        return view('backend.pages.orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'sku' => 'required|unique:orders,sku',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Order::create($request->all());

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function show($id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($id);
        return view('backend.pages.orders.show', compact('order'));
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['orders.edit']);
        $order = Order::findOrFail($id);
        return view('backend.pages.orders.edit', [
            'order' => $order
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['orders.edit']);

        $order = Order::findOrFail($id);

        $order->name_en = $request->input('name_en');
        $order->name_ar = $request->input('name_ar');
        $order->description_en = $request->input('description_en');
        $order->description_ar = $request->input('description_ar');
        $order->sku = $request->input('sku');
        $order->brand = $request->input('brand');
        $order->price = $request->input('price');
        $order->stock = $request->input('stock');
        $order->status = $request->input('status');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/orders', 'public');
            $order->image = $path;
        }

        $order = ld_apply_filters('order_update_before_save', $order, $request);
        $order->save();
        $order = ld_apply_filters('order_update_after_save', $order, $request);
        ld_do_action('order_update_after', $order);

        $this->storeActionLog(ActionType::UPDATED, ['order' => $order]);

        session()->flash('success', 'Order has been updated.');

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


    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
