<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    // Use Laravel timestamps (created_at and updated_at)
    // If you want to use your custom timestamps, adjust accordingly
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'invoice_status',
        'invoice_reference',
        'customer_reference',
        'created_date',
        'expiry_date',
        'expiry_time',
        'invoice_value',
        'comments',
        'customer_name',
        'customer_mobile',
        'customer_email',
        'user_defined_field',
        'invoice_display_value',
        'due_deposit',
        'deposit_status',
    ];

    // Relationship to Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
