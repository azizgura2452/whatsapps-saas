<?php
namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
}
