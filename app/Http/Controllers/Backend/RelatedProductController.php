<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use Illuminate\Http\Request;

class RelatedProductController extends BaseController
{
    protected string $resource = 'related_product';

    protected array $additionalPermissions = ['product_management_access'];

    public function index(Request $request)
    {
        $query = Product::with(['relatedProducts', 'vendor', 'brand']);

        // Filter by vendor if provided
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Only show products that have related product relationships
        $query->whereHas('relatedProducts');

        $products = $query->orderBy('name')->paginate(15);

        return view('admin.related_products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'related_product_id' => 'required|exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $relatedProduct = Product::findOrFail($request->related_product_id);

        // Check if relationship already exists
        if ($product->relatedProducts()->where('related_product_id', $relatedProduct->id)->exists()) {
            return redirect()->back()->with('error', 'Related product relationship already exists.');
        }

        // Attach the related product
        $product->relatedProducts()->attach($relatedProduct->id);

        return redirect()->route('admin.related_products.index')
            ->with('success', 'Related product relationship created successfully.');
    }

    public function destroy(Product $product, Product $relatedProduct)
    {
        // Detach the related product relationship
        $product->relatedProducts()->detach($relatedProduct->id);

        return redirect()->route('admin.related_products.index')
            ->with('success', 'Related product relationship removed successfully.');
    }

    /**
     * Get related products for a specific product
     */
    public function getProductRelated(Product $product)
    {
        $relatedProducts = $product->relatedProducts()
            ->with(['vendor', 'brand'])
            ->orderBy('name')
            ->get();

        return response()->json($relatedProducts);
    }

    /**
     * Add multiple related products to a product
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'related_product_ids' => 'required|array',
            'related_product_ids.*' => 'exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Get existing related product IDs
        $existingIds = $product->relatedProducts()->pluck('related_product_id')->toArray();

        // Filter out already existing relationships
        $newIds = array_diff($request->related_product_ids, $existingIds);

        if (! empty($newIds)) {
            $product->relatedProducts()->attach($newIds);
            $count = count($newIds);

            return redirect()->route('admin.related_products.index')
                ->with('success', "Added {$count} related product relationships successfully.");
        }

        return redirect()->route('admin.related_products.index')
            ->with('info', 'All selected related product relationships already exist.');
    }

    /**
     * Remove all related products from a product
     */
    public function clearAll(Product $product)
    {
        $count = $product->relatedProducts()->count();
        $product->relatedProducts()->detach();

        return redirect()->route('admin.related_products.index')
            ->with('success', "Removed {$count} related product relationships successfully.");
    }

    /**
     * Create mutual relationships (both ways)
     */
    public function createMutual(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'related_product_id' => 'required|exists:products,id|different:product_id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $relatedProduct = Product::findOrFail($request->related_product_id);

        $attached = 0;

        // Create first relationship
        if (! $product->relatedProducts()->where('related_product_id', $relatedProduct->id)->exists()) {
            $product->relatedProducts()->attach($relatedProduct->id);
            $attached++;
        }

        // Create reverse relationship
        if (! $relatedProduct->relatedProducts()->where('related_product_id', $product->id)->exists()) {
            $relatedProduct->relatedProducts()->attach($product->id);
            $attached++;
        }

        if ($attached > 0) {
            return redirect()->route('admin.related_products.index')
                ->with('success', "Created {$attached} mutual related product relationships successfully.");
        }

        return redirect()->route('admin.related_products.index')
            ->with('info', 'Mutual related product relationships already exist.');
    }
}
