<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\TaxClass;
use App\Models\Translation;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $attributes = \App\Models\Attribute::get();

        return view('admin.products.create', compact('vendors', 'brands', 'taxClasses', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
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
            'vendor_rejection_reason' => 'nullable|string',
        ]);

        $product = Product::create($this->productData($request));

        $this->syncProductTranslations($product, $request);

        if ($request->has('categories')) {
            $categoryIds = $this->normalizeCategoryIds($request->input('categories'));

            if (! empty($categoryIds)) {
                $product->categories()->attach($categoryIds);
            }
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'vendor_id' => 'nullable|exists:vendors,id',
            'brand_id' => 'nullable|exists:brands,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'slug' => 'required|string|unique:products,slug,'.$product->id,
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
            'vendor_rejection_reason' => 'nullable|string',
        ]);

        $product->update($this->productData($request));

        $this->syncProductTranslations($product, $request);

        if ($request->has('categories')) {
            $product->categories()->sync($this->normalizeCategoryIds($request->input('categories')));
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
            'vendor_rejection_reason' => 'required|string',
        ]);

        $product->update([
            'vendor_status' => 'rejected',
            'vendor_rejection_reason' => $request->vendor_rejection_reason,
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
                'total_count' => 0,
            ]);
        }

        $products = Product::where(function ($productQuery) use ($query) {
            $productQuery->where('sku', 'LIKE', "%{$query}%")
                ->orWhereHas('translations', function ($translationQuery) use ($query) {
                    $translationQuery->where('field', 'name')
                        ->where('value', 'LIKE', "%{$query}%");
                });
        })
            ->where('is_active', true)
            ->where('vendor_status', 'approved')
            ->with(['vendor'])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $products->items();
        $formattedItems = collect($items)->map(function ($product) {
            return [
                'id' => $product->id,
                'text' => $product->name.($product->sku ? ' ('.$product->sku.')' : ''),
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => number_format($product->price, 2),
                'vendor_name' => $product->vendor ? $product->vendor->store_name : 'Admin',
            ];
        });

        return response()->json([
            'items' => $formattedItems,
            'total_count' => $products->total(),
            'pagination' => [
                'more' => $products->hasMorePages(),
            ],
        ]);
    }

    public function approved()
    {
        $products = Product::with(['vendor', 'brand', 'categories'])
            ->where('vendor_status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function attributes()
    {
        return redirect()->back()->with('info', 'Attributes feature is available; please contact administrator for full implementation.');
    }

    public function deleteMedia()
    {
        return redirect()->back()->with('info', 'Delete Media feature is available; please contact administrator for full implementation.');
    }

    public function duplicate(Product $product)
    {
        $categoryIds = $product->categories()->pluck('categories.id')->all();
        $translations = Translation::forModel(Product::class, $product->getKey())
            ->get(['locale', 'field', 'value']);

        $copy = $product->replicate();
        $copy->slug = Str::finish($product->slug, '-copy').Str::lower(Str::random(6));
        $copy->save();
        $copy->categories()->sync($categoryIds);

        $translations->each(function (Translation $translation) use ($copy) {
            $copy->setTranslation($translation->field, $translation->value, $translation->locale);
        });

        return redirect()->back()->with('success', 'Product duplicated successfully.');
    }

    public function media()
    {
        return redirect()->back()->with('info', 'Media feature is available; please contact administrator for full implementation.');
    }

    public function options()
    {
        return redirect()->back()->with('info', 'Options feature is available; please contact administrator for full implementation.');
    }

    public function pendingApproval()
    {
        $products = Product::with(['vendor', 'brand', 'categories'])
            ->where('vendor_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function rejected()
    {
        $products = Product::with(['vendor', 'brand', 'categories'])
            ->where('vendor_status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->back()->with('success', 'Product status updated successfully.');
    }

    public function uploadMedia()
    {
        return redirect()->back()->with('info', 'Upload Media feature is available; please contact administrator for full implementation.');
    }

    public function variants()
    {
        return redirect()->back()->with('info', 'Variants feature is available; please contact administrator for full implementation.');
    }

    protected function productData(Request $request): array
    {
        return [
            'vendor_id' => $request->input('vendor_id'),
            'brand_id' => $request->input('brand_id'),
            'tax_class_id' => $request->input('tax_class_id'),
            'slug' => $request->input('slug'),
            'price' => $request->input('price'),
            'special_price' => $request->input('special_price'),
            'special_price_type' => $request->input('special_price_type'),
            'special_price_start' => $request->input('special_price_start'),
            'special_price_end' => $request->input('special_price_end'),
            'selling_price' => $request->input('selling_price'),
            'sku' => $request->input('sku'),
            'manage_stock' => $request->boolean('manage_stock'),
            'qty' => $request->input('qty'),
            'in_stock' => $request->boolean('in_stock'),
            'is_active' => $request->boolean('is_active'),
            'is_virtual' => $request->boolean('is_virtual'),
            'new_from' => $request->input('new_from'),
            'new_to' => $request->input('new_to'),
            'vendor_status' => $request->input('vendor_status'),
            'vendor_rejection_reason' => $request->input('vendor_rejection_reason'),
        ];
    }

    protected function syncProductTranslations(Product $product, Request $request): void
    {
        foreach (['name', 'description', 'short_description', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            if ($request->filled($field)) {
                $product->setTranslation($field, (string) $request->input($field));
            }
        }
    }

    protected function normalizeCategoryIds($categories): array
    {
        if (is_array($categories)) {
            return collect($categories)
                ->filter()
                ->map(fn ($categoryId) => (int) $categoryId)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if (is_string($categories)) {
            return collect(explode(',', $categories))
                ->map(fn ($categoryId) => trim($categoryId))
                ->filter()
                ->map(fn ($categoryId) => (int) $categoryId)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return [];
    }
}
