<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use Illuminate\Http\Request;

class CrossSellProductController extends BaseController
{
    protected string $resource = 'cross_sell_product';

    protected array $additionalPermissions = ['product_management_access'];

    public function index(Request $request)
    {
        $query = Product::with(['crossSellProducts', 'vendor', 'brand']);

        // Filter by vendor if provided
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Only show products that have cross-sell relationships
        $query->whereHas('crossSellProducts');

        $products = $query->orderBy('name')->paginate(15);

        return view('admin.cross_sell_products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'cross_sell_product_id' => 'required|exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $crossSellProduct = Product::findOrFail($request->cross_sell_product_id);

        // Check if relationship already exists
        if ($product->crossSellProducts()->where('cross_sell_product_id', $crossSellProduct->id)->exists()) {
            return redirect()->back()->with('error', 'Cross-sell relationship already exists.');
        }

        // Attach the cross-sell product
        $product->crossSellProducts()->attach($crossSellProduct->id);

        return redirect()->route('admin.cross-sell-products.index')
            ->with('success', 'Cross-sell product relationship created successfully.');
    }

    public function destroy(Product $product, Product $crossSellProduct)
    {
        // Detach the cross-sell product relationship
        $product->crossSellProducts()->detach($crossSellProduct->id);

        return redirect()->route('admin.cross-sell-products.index')
            ->with('success', 'Cross-sell product relationship removed successfully.');
    }

    /**
     * Get cross-sell products for a specific product
     */
    public function getProductCrossSells(Product $product)
    {
        $crossSellProducts = $product->crossSellProducts()
            ->with(['vendor', 'brand'])
            ->orderBy('name')
            ->get();

        return response()->json($crossSellProducts);
    }

    /**
     * Add multiple cross-sell products to a product
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'cross_sell_product_ids' => 'required|array',
            'cross_sell_product_ids.*' => 'exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Get existing cross-sell product IDs
        $existingIds = $product->crossSellProducts()->pluck('cross_sell_product_id')->toArray();

        // Filter out already existing relationships
        $newIds = array_diff($request->cross_sell_product_ids, $existingIds);

        if (! empty($newIds)) {
            $product->crossSellProducts()->attach($newIds);
            $count = count($newIds);

            return redirect()->route('admin.cross-sell-products.index')
                ->with('success', "Added {$count} cross-sell product relationships successfully.");
        }

        return redirect()->route('admin.cross-sell-products.index')
            ->with('info', 'All selected cross-sell relationships already exist.');
    }

    /**
     * Remove all cross-sell products from a product
     */
    public function clearAll(Product $product)
    {
        $count = $product->crossSellProducts()->count();
        $product->crossSellProducts()->detach();

        return redirect()->route('admin.cross-sell-products.index')
            ->with('success', "Removed {$count} cross-sell product relationships successfully.");
    }
}
