<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class OrderService
{
    public function createOrderWithItems(array $data): Order
    {
        $total = 0;
        $orderItems = [];

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
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
            'customer_id' => $data['customer_id'],
            'total' => $total,
            'delivery_charge' => $data['delivery_charge'],
            'currency' => $data['currency'],
            'status' => $data['status'],
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
        return Order::with('items')->find($orderId);
    }

    public function updateOrderStatus(int $orderId, string $status): bool
    {
        $order = Order::find($orderId);
        if (!$order)
            return false;
        $order->status = $status;
        return $order->save();
    }

    public function cancelOrder(int $orderId): bool
    {
        $order = Order::with('items')->find($orderId);
        if (!$order)
            return false;
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
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
        return Order::where('customer_id', $customerId)
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
        $query = Order::query();
        $search = request()->input('search');

        if ($search) {
            $query->where('id', $search);
        }

        return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    }
}
