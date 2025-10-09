<?php

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    protected function getCurrentBusiness()
    {
        if (app()->has('current_business')) {
            return app('current_business');
        }
        return auth()->user()->businesses()->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['products.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $search = $request->input('search');
        $perPage = config('settings.default_pagination', 10);

        $products = Product::where('business_id', $business->id)
            ->when($search, function($query) use ($search) {
                $query->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%");
            })
            ->latest('created_on')
            ->paginate($perPage);

        $totalStock = Product::where('business_id', $business->id)->sum('stock');

        return view('backend.pages.products.index', [
            'products' => $products,
            'totalStock' => $totalStock,
            'business' => $business,
        ]);
    }

    public function create()
    {
        $this->checkAuthorization(auth()->user(), ['products.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        return view('backend.pages.products.create', [
            'business' => $business,
        ]);
    }

    public function store(Request $request)
    {
        $this->checkAuthorization(auth()->user(), ['products.create']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,NULL,id,business_id,' . $business->id,
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
        ]);

        // Prepare data
        $data = $request->only([
            'name_en',
            'name_ar',
            'description_en',
            'description_ar',
            'sku',
            'brand',
            'price',
            'stock',
            'status',
            'link',
        ]);

        $data['business_id'] = $business->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        $this->storeActionLog(ActionType::CREATED, ['product' => $product]);

        return redirect()->route('admin.products.index')->with('success', __('Product created successfully.'));
    }

    public function show(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['products.view']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $product = Product::where('business_id', $business->id)->findOrFail($id);

        return view('backend.pages.products.show', [
            'product' => $product,
            'business' => $business,
        ]);
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['products.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            session()->flash('error', __('Please create a business first.'));
            return redirect()->route('admin.businesses.create');
        }

        $product = Product::where('business_id', $business->id)->findOrFail($id);

        return view('backend.pages.products.edit', [
            'product' => $product,
            'business' => $business,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['products.edit']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $product = Product::where('business_id', $business->id)->findOrFail($id);

        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $id . ',id,business_id,' . $business->id,
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|url',
        ]);

        $product->name_en = $request->input('name_en');
        $product->name_ar = $request->input('name_ar');
        $product->description_en = $request->input('description_en');
        $product->description_ar = $request->input('description_ar');
        $product->sku = $request->input('sku');
        $product->brand = $request->input('brand');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->status = $request->input('status');
        $product->link = $request->input('link');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }

            $path = $request->file('image')->store('uploads/products', 'public');
            $product->image = $path;
        }

        $product = ld_apply_filters('product_update_before_save', $product, $request);
        $product->save();
        $product = ld_apply_filters('product_update_after_save', $product, $request);
        ld_do_action('product_update_after', $product);

        $this->storeActionLog(ActionType::UPDATED, ['product' => $product]);

        $page = $request->input('page', 1);

        return redirect()->route('admin.products.index', ['page' => $page])
                         ->with('success', __('Product updated successfully.'));
    }

    public function destroy(int $id)
    {
        $this->checkAuthorization(auth()->user(), ['products.delete']);

        $business = $this->getCurrentBusiness();

        if (!$business) {
            return back()->with('error', __('Please create a business first.'));
        }

        $product = Product::where('business_id', $business->id)->findOrFail($id);

        // Delete image if exists
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $product = ld_apply_filters('product_delete_before', $product);
        $product->delete();
        $product = ld_apply_filters('product_delete_after', $product);

        $this->storeActionLog(ActionType::DELETED, ['product' => $product]);

        ld_do_action('product_delete_after', $product);

        return redirect()->route('admin.products.index')->with('success', __('Product deleted successfully.'));
    }
}