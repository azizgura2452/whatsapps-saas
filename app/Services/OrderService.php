<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class OrderService
{
    protected function getCurrentBusinessId(): ?int
    {
        if (app()->has('current_business')) {
            return app('current_business')->id;
        }
        return auth()->user()?->businesses()?->first()?->id;
    }

    public function createOrderWithItems(array $data): Order
    {
        $total = 0;
        $orderItems = [];
        $businessId = $data['business_id'] ?? $this->getCurrentBusinessId();

        foreach ($data['items'] as $item) {
            $product = Product::where('business_id', $businessId)->findOrFail($item['product_id']);
            $lineTotal = $product->price * $item['quantity'];
            $total += $lineTotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'total' => $lineTotal,
            ];
        }

        $order = Order::create([
            'business_id' => $businessId,
            'customer_id' => $data['customer_id'],
            'total' => $total,
            'delivery_charge' => $data['delivery_charge'] ?? 0,
            'currency' => $data['currency'] ?? 'KWD',
            'status' => $data['status'] ?? 'Pending',
            'source' => $data['source'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($orderItems as $item) {
            $item['order_id'] = $order->id;
            OrderItem::create($item);
        }

        return $order;
    }

    public function getOrderById(int $orderId): ?Order
    {
        $businessId = $this->getCurrentBusinessId();
        return Order::where('business_id', $businessId)
            ->with('items')
            ->find($orderId);
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $businessId = $this->getCurrentBusinessId();
        $order = Order::where('business_id', $businessId)->find($orderId);
        if (!$order) {
            return false;
        }
        $order->status = $status;
        return $order->save();
    }

    public function cancelOrder(int $orderId): bool
    {
        $businessId = $this->getCurrentBusinessId();
        $order = Order::where('business_id', $businessId)->with('items')->find($orderId);
        if (!$order) {
            return false;
        }
        foreach ($order->items as $item) {
            $product = Product::where('business_id', $businessId)->find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }
        $order->status = 'Cancelled';
        return $order->save();
    }

    public function getOrdersByCustomer(int $customerId, int $perPage = 10)
    {
        $businessId = $this->getCurrentBusinessId();
        return Order::where('business_id', $businessId)
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function isStockAvailable(array $items): bool
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return false;
            }
        }
        return true;
    }

    public function getOrders(): LengthAwarePaginator
    {
        $businessId = $this->getCurrentBusinessId();
        $query = Order::where('business_id', $businessId);
        $search = request()->input('search');

        if ($search) {
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    }
}
