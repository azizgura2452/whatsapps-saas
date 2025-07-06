<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;

class Order extends Model
{
    protected $table = 'orders';

    protected $casts = [
        'status' => OrderStatus::class, // auto-casts from DB string to enum
    ];

    public $timestamps = false;
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'modified_on';

    protected $fillable = [
        'customer_id',
        'total',
        'delivery_charge',
        'currency',
        'status',
        'source',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
