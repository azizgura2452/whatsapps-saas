<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getProducts(?int $businessId = null, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::query();

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        return $query->latest('created_on')->paginate($perPage);
    }

    public function getProductById(int $id, ?int $businessId = null): ?Product
    {
        $query = Product::where('id', $id);

        if ($businessId) {
            $query->where('business_id', $businessId);
        }

        return $query->first();
    }

    public function getProductBySku(string $sku, int $businessId): ?Product
    {
        return Product::where('sku', $sku)
            ->where('business_id', $businessId)
            ->first();
    }

    public function getTotalStock(int $businessId): int
    {
        return Product::where('business_id', $businessId)->sum('stock');
    }
}