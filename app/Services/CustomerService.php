<?php
namespace App\Services;
use App\Models\Customer;
use App\Models\Order;
use App\Models\WhatsAppMessage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CustomerService
{
    public function createOrUpdate(array $data): Customer
    {
        $customer = Customer::updateOrCreate(
            ['whatsapp_number' => $data['whatsapp_number']],
            [
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'gender' => $data['gender'] ?? null,
            ]
        );

        if (!empty($data['attributes'])) {
            $customer->attributes()->delete(); // clear old
            foreach ($data['attributes'] as $key => $value) {
                if ($key && $value) {
                    $customer->attributes()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        return $customer;
    }

    public function getCustomers(): LengthAwarePaginator
    {
        $search = request()->input('search');
        $attribute = request()->input('attribute');
        $value = request()->input('value');

        $query = Customer::query()
            ->select('customers.*')
            ->addSelect([
                'latest_message_timestamp' => WhatsAppMessage::select('timestamp')
                    ->whereColumn('phone_number', 'customers.whatsapp_number')
                    ->orderByDesc('timestamp')
                    ->limit(1)
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'like', "%{$search}%")
                    ->orWhere('customers.whatsapp_number', 'like', "%{$search}%")
                    ->orWhereHas('attributes', function ($sub) use ($search) {
                        $sub->where('key', 'like', "%{$search}%")
                            ->orWhere('value', 'like', "%{$search}%");
                    });
            });
        }

        if ($attribute) {
            $query->whereHas('attributes', function ($q) use ($attribute, $value) {
                $q->where('key', $attribute);
                if ($value) {
                    $q->where('value', $value);
                }
            });
        }

        return $query->orderByDesc('latest_message_timestamp')
            ->paginate(config('settings.default_pagination') ?? 10);
    }




    // public function getCustomers(): LengthAwarePaginator
    // {
    //     $query = Customer::query();
    //     $search = request()->input('search');

    //     if ($search) {
    //         $query->where('name', 'like', "%{$search}%")
    //             ->orWhere('whatsapp_number', 'like', "%{$search}%");
    //     }

    //     return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    // }

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

        /**
     * Get or create customer by WhatsApp number
     */
    public function getOrCreateCustomer(array $data)
    {
        $businessId = $data['business_id'] ?? null;
        
        if (!$businessId) {
            throw new \Exception('Business ID is required');
        }

        return Customer::firstOrCreate(
            [
                'whatsapp_number' => $data['whatsapp_number'],
                'business_id' => $businessId
            ],
            [
                'name' => $data['name'] ?? null,
                'address' => $data['address'] ?? null,
                'email' => $data['email'] ?? null,
                'business_id' => $businessId,
            ]
        );
    }

    /**
     * Find customer by WhatsApp number and business
     */
    public function findByWhatsapp(string $whatsappNumber, ?int $businessId = null)
    {
        $query = Customer::where('whatsapp_number', $whatsappNumber);
        
        if ($businessId) {
            $query->where('business_id', $businessId);
        }
        
        return $query->first();
    }

    /**
     * Get all phone numbers for a business
     */
    public function getAllPhoneNumbers(?int $businessId = null): array
    {
        $query = Customer::query();
        
        if ($businessId) {
            $query->where('business_id', $businessId);
        }
        
        return $query->pluck('whatsapp_number')->toArray();
    }

    /**
     * Get customers for a business
     */
    public function getCustomersForBusiness(int $businessId)
    {
        return Customer::where('business_id', $businessId)->get();
    }
}
