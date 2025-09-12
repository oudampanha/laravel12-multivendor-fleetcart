<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Brand;
use App\Models\TaxClass;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    protected string $resource = 'product';
    
    protected array $additionalPermissions = ['product_management_access'];

    public function __construct()
    {
        parent::__construct();
        
        // Apply specific permissions for product management methods
        $this->applyMethodPermission('product_edit', ['approve', 'reject']);
    }
    public function index()
    {
        $products = Product::with(['vendor', 'brand', 'categories'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $taxClasses = TaxClass::all();
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.products.create', compact('vendors', 'brands', 'taxClasses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'slug' => 'required|string|unique:products,slug',
            'price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'special_price_type' => 'nullable|string|in:fixed,percent',
            'special_price_start' => 'nullable|date',
            'special_price_end' => 'nullable|date|after:special_price_start',
            'sku' => 'nullable|string',
            'manage_stock' => 'boolean',
            'qty' => 'nullable|integer|min:0',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_virtual' => 'boolean',
            'new_from' => 'nullable|date',
            'new_to' => 'nullable|date|after:new_from',
            'vendor_status' => 'nullable|in:pending,approved,rejected',
            'vendor_rejection_reason' => 'nullable|string'
        ]);

        $product = Product::create($request->all());

        if ($request->has('categories')) {
            $product->categories()->attach($request->categories);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['vendor', 'brand', 'taxClass', 'categories', 'variants', 'reviews']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $vendors = Vendor::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $taxClasses = TaxClass::all();
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.products.edit', compact('product', 'vendors', 'brands', 'taxClasses', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'special_price_type' => 'nullable|string|in:fixed,percent',
            'special_price_start' => 'nullable|date',
            'special_price_end' => 'nullable|date|after:special_price_start',
            'sku' => 'nullable|string',
            'manage_stock' => 'boolean',
            'qty' => 'nullable|integer|min:0',
            'in_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_virtual' => 'boolean',
            'new_from' => 'nullable|date',
            'new_to' => 'nullable|date|after:new_from',
            'vendor_status' => 'nullable|in:pending,approved,rejected',
            'vendor_rejection_reason' => 'nullable|string'
        ]);

        $product->update($request->all());

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function approve(Product $product)
    {
        $product->update(['vendor_status' => 'approved']);

        return redirect()->back()
            ->with('success', 'Product approved successfully.');
    }

    public function reject(Request $request, Product $product)
    {
        $request->validate([
            'vendor_rejection_reason' => 'required|string'
        ]);

        $product->update([
            'vendor_status' => 'rejected',
            'vendor_rejection_reason' => $request->vendor_rejection_reason
        ]);

        return redirect()->back()
            ->with('success', 'Product rejected successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = 30;

        if (empty($query)) {
            return response()->json([
                'items' => [],
                'total_count' => 0
            ]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->where('is_active', true)
            ->where('vendor_status', 'approved')
            ->with(['vendor'])
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $products->items();
        $formattedItems = collect($items)->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name . ($product->sku ? ' (' . $product->sku . ')' : ''),
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => number_format($product->price, 2),
                'vendor_name' => $product->vendor ? $product->vendor->store_name : 'Admin'
            ];
        });

        return response()->json([
            'items' => $formattedItems,
            'total_count' => $products->total(),
            'pagination' => [
                'more' => $products->hasMorePages()
            ]
        ]);
    }
}