<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use Illuminate\Http\Request;

class UpSellProductController extends BaseController
{
    protected string $resource = 'up_sell_product';

    protected array $additionalPermissions = ['product_management_access'];

    public function index(Request $request)
    {
        $query = Product::with(['upSellProducts', 'vendor', 'brand']);

        // Filter by vendor if provided
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Only show products that have up-sell relationships
        $query->whereHas('upSellProducts');

        $products = $query->orderBy('name')->paginate(15);

        return view('admin.up_sell_products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'up_sell_product_id' => 'required|exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $upSellProduct = Product::findOrFail($request->up_sell_product_id);

        // Check if relationship already exists
        if ($product->upSellProducts()->where('up_sell_product_id', $upSellProduct->id)->exists()) {
            return redirect()->back()->with('error', 'Up-sell product relationship already exists.');
        }

        // Attach the up-sell product
        $product->upSellProducts()->attach($upSellProduct->id);

        return redirect()->route('admin.up-sell-products.index')
            ->with('success', 'Up-sell product relationship created successfully.');
    }

    public function destroy(Product $product, Product $upSellProduct)
    {
        // Detach the up-sell product relationship
        $product->upSellProducts()->detach($upSellProduct->id);

        return redirect()->route('admin.up-sell-products.index')
            ->with('success', 'Up-sell product relationship removed successfully.');
    }

    /**
     * Get up-sell products for a specific product
     */
    public function getProductUpSells(Product $product)
    {
        $upSellProducts = $product->upSellProducts()
            ->with(['vendor', 'brand'])
            ->orderBy('name')
            ->get();

        return response()->json($upSellProducts);
    }

    /**
     * Add multiple up-sell products to a product
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'up_sell_product_ids' => 'required|array',
            'up_sell_product_ids.*' => 'exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Get existing up-sell product IDs
        $existingIds = $product->upSellProducts()->pluck('up_sell_product_id')->toArray();

        // Filter out already existing relationships
        $newIds = array_diff($request->up_sell_product_ids, $existingIds);

        if (! empty($newIds)) {
            $product->upSellProducts()->attach($newIds);
            $count = count($newIds);

            return redirect()->route('admin.up-sell-products.index')
                ->with('success', "Added {$count} up-sell product relationships successfully.");
        }

        return redirect()->route('admin.up-sell-products.index')
            ->with('info', 'All selected up-sell relationships already exist.');
    }

    /**
     * Remove all up-sell products from a product
     */
    public function clearAll(Product $product)
    {
        $count = $product->upSellProducts()->count();
        $product->upSellProducts()->detach();

        return redirect()->route('admin.up-sell-products.index')
            ->with('success', "Removed {$count} up-sell product relationships successfully.");
    }

    /**
     * Create mutual up-sell relationships (both ways)
     */
    public function createMutual(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'up_sell_product_id' => 'required|exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $upSellProduct = Product::findOrFail($request->up_sell_product_id);

        $attached = 0;

        // Create first relationship
        if (! $product->upSellProducts()->where('up_sell_product_id', $upSellProduct->id)->exists()) {
            $product->upSellProducts()->attach($upSellProduct->id);
            $attached++;
        }

        // Create reverse relationship
        if (! $upSellProduct->upSellProducts()->where('up_sell_product_id', $product->id)->exists()) {
            $upSellProduct->upSellProducts()->attach($product->id);
            $attached++;
        }

        if ($attached > 0) {
            return redirect()->route('admin.up-sell-products.index')
                ->with('success', "Created {$attached} mutual up-sell product relationships successfully.");
        }

        return redirect()->route('admin.up-sell-products.index')
            ->with('info', 'Mutual up-sell product relationships already exist.');
    }

    /**
     * Get up-sell suggestions based on categories and price range
     */
    public function getSuggestions(Product $product)
    {
        $suggestions = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where(function ($query) use ($product) {
                // Same categories
                $query->whereHas('categories', function ($q) use ($product) {
                    $q->whereIn('category_id', $product->categories->pluck('id'));
                })
                // Or higher price range (for up-selling)
                    ->orWhere('price', '>', $product->price);
            })
            ->with(['vendor', 'brand', 'categories'])
            ->orderBy('price', 'desc')
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }

    /**
     * Auto-suggest up-sell products based on order history
     */
    public function autoSuggest(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $product = Product::findOrFail($request->product_id);
        $limit = $request->get('limit', 10);

        // Find products frequently bought by customers who also bought this product
        $suggestions = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->whereHas('orderProducts', function ($query) use ($product) {
                $query->whereHas('order', function ($q) use ($product) {
                    $q->whereHas('orderProducts', function ($orderQ) use ($product) {
                        $orderQ->where('product_id', $product->id);
                    });
                });
            })
            ->where('price', '>', $product->price) // Up-sell should be higher priced
            ->withCount(['orderProducts as frequency'])
            ->orderBy('frequency', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($suggestions);
    }
}
