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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['products.view']);

        return view('backend.pages.products.index', [
            'products' => $this->productService->getProducts()
        ]);
    }

    public function create()
    {
        return view('backend.pages.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('backend.pages.products.show', compact('product'));
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['products.edit']);
        $product = Product::findOrFail($id);
        return view('backend.pages.products.edit', [
            'product' => $product
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['products.edit']);

        $product = Product::findOrFail($id);

        $product->name_en = $request->input('name_en');
        $product->name_ar = $request->input('name_ar');
        $product->description_en = $request->input('description_en');
        $product->description_ar = $request->input('description_ar');
        $product->sku = $request->input('sku');
        $product->brand = $request->input('brand');
        $product->price = $request->input('price');
        $product->stock = $request->input('stock');
        $product->status = $request->input('status');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $product->image = $path;
        }

        $product = ld_apply_filters('product_update_before_save', $product, $request);
        $product->save();
        $product = ld_apply_filters('product_update_after_save', $product, $request);
        ld_do_action('product_update_after', $product);

        $this->storeActionLog(ActionType::UPDATED, ['product' => $product]);

        session()->flash('success', 'Product has been updated.');

        return back();
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('backend.pages.products.index')->with('success', 'Product deleted successfully.');
    }
}
