<?php
namespace App\Services;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function createOrUpdate(array $data): Customer
    {
        return Customer::updateOrCreate(
            ['whatsapp_number' => $data['whatsapp_number']],
            [
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null
            ]
        );
    }

    public function getOrCreateCustomer(array $data): Customer
    {
        return Customer::firstOrCreate(
            ['whatsapp_number' => $data['whatsapp_number']],
            [
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }


    public function getCustomers(): LengthAwarePaginator
    {
        $query = Customer::query();
        $search = request()->input('search');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('whatsapp_number', 'like', "%{$search}%");
        }

        return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    }

    public function getCustomerById(int $id): ?Customer
    {
        return Customer::findOrFail($id);
    }

    public function getCustomerOrders(int $customerId): LengthAwarePaginator
    {
        return Order::where('customer_id', $customerId)
            ->latest()
            ->paginate(config('settings.default_pagination') ?? 10);
    }

    public function findByWhatsapp(string $whatsappNumber): ?Customer
    {
        return Customer::where('whatsapp_number', $whatsappNumber)->first();
    }

    // In CustomerService.php
    public function getAllPhoneNumbers(): array
    {
        return Customer::pluck('whatsapp_number')->filter()->unique()->toArray();
    }
}
