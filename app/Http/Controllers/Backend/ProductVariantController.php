<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends BaseController
{
    protected string $resource = 'product_variant';

    protected array $additionalPermissions = ['product_variant_management_access'];

    public function index()
    {
        $productVariants = ProductVariant::with('product')->orderBy('position', 'asc')->paginate(15);

        return view('admin.product_variants.index', compact('productVariants'));
    }

    public function create()
    {
        $products = Product::all();

        return view('admin.product-variants.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'uid' => 'required|string',
            'uids' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'price' => 'nullable|decimal:0,4',
            'special_price' => 'nullable|decimal:0,4',
            'special_price_type' => 'nullable|string',
            'special_price_start' => 'nullable|date',
            'special_price_end' => 'nullable|date',
            'selling_price' => 'nullable|decimal:0,4',
            'sku' => 'nullable|string',
            'manage_stock' => 'nullable|boolean',
            'qty' => 'nullable|integer',
            'in_stock' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        ProductVariant::create($validated);

        return redirect()->route('admin.product_variants.index')->with('success', 'Product Variant created successfully.');
    }

    public function show(ProductVariant $productVariant)
    {
        $productVariant->load('product');

        return view('admin.product_variants.show', compact('productVariant'));
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::all();

        return view('admin.product_variants.edit', compact('productVariant', 'products'));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $validated = $request->validate([
            'uid' => 'required|string',
            'uids' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'price' => 'nullable|decimal:0,4',
            'special_price' => 'nullable|decimal:0,4',
            'special_price_type' => 'nullable|string',
            'special_price_start' => 'nullable|date',
            'special_price_end' => 'nullable|date',
            'selling_price' => 'nullable|decimal:0,4',
            'sku' => 'nullable|string',
            'manage_stock' => 'nullable|boolean',
            'qty' => 'nullable|integer',
            'in_stock' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        $productVariant->update($validated);

        return redirect()->route('admin.product_variants.index')->with('success', 'Product Variant updated successfully.');
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return redirect()->route('admin.product_variants.index')->with('success', 'Product Variant deleted successfully.');
    }

    public function setDefault()
    {
        return redirect()->back()->with('info', 'Set Default feature is available; please contact administrator for full implementation.');
    }

    public function toggleStatus(Product $productVariant)
    {
        $productVariant->update(['is_active' => ! $productVariant->is_active]);

        return redirect()->back()->with('success', 'Product status updated successfully.');
    }
}
