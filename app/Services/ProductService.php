<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function getProducts(): LengthAwarePaginator
    {
        $query = Product::query();
        $search = request()->input('search');

        if ($search) {
            $query->where('name_en', 'like', "%{$search}%")
                ->orWhere('description_en', 'like', "%{$search}%");
        }

        $category = request()->input('category');
        if ($category) {
            $query->where('category_id', $category);
        }

        return $query->latest()->paginate(config('settings.default_pagination') ?? 10);
    }
}