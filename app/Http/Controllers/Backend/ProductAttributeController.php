<?php

namespace App\Http\Controllers\Backend;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends BaseController
{
    protected string $resource = 'product_attribute';

    protected array $additionalPermissions = ['product_attribute_management_access'];

    public function index()
    {
        $productAttributes = ProductAttribute::with(['product', 'attribute', 'attributeValues'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.product_attributes.index', compact('productAttributes'));
    }

    public function create()
    {
        $products = Product::orderBy('id', 'desc')->get();
        $attributes = Attribute::orderBy('id', 'desc')->get();
        $attributeValues = AttributeValue::orderBy('position')->get();

        return view('admin.product_attributes.create', compact('products', 'attributes', 'attributeValues'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'attribute_id' => 'required|exists:attributes,id',
            'attribute_value_ids' => 'nullable|array',
            'attribute_value_ids.*' => 'exists:attribute_values,id',
        ]);

        $productAttribute = ProductAttribute::create([
            'product_id' => $validated['product_id'],
            'attribute_id' => $validated['attribute_id'],
        ]);

        if (! empty($validated['attribute_value_ids'])) {
            $productAttribute->attributeValues()->sync($validated['attribute_value_ids']);
        }

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute created successfully.');
    }

    public function show(ProductAttribute $productAttribute)
    {
        $productAttribute->load(['product', 'attribute', 'attributeValues']);

        return view('admin.product_attributes.show', compact('productAttribute'));
    }

    public function edit(ProductAttribute $productAttribute)
    {
        $productAttribute->load('attributeValues');
        $products = Product::orderBy('id', 'desc')->get();
        $attributes = Attribute::orderBy('id', 'desc')->get();
        $attributeValues = AttributeValue::orderBy('position')->get();

        return view('admin.product_attributes.edit', compact('productAttribute', 'products', 'attributes', 'attributeValues'));
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'attribute_id' => 'required|exists:attributes,id',
            'attribute_value_ids' => 'nullable|array',
            'attribute_value_ids.*' => 'exists:attribute_values,id',
        ]);

        $productAttribute->update([
            'product_id' => $validated['product_id'],
            'attribute_id' => $validated['attribute_id'],
        ]);

        $productAttribute->attributeValues()->sync($validated['attribute_value_ids'] ?? []);

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute updated successfully.');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $productAttribute->delete();

        return redirect()->route('admin.product-attributes.index')
            ->with('success', 'Product attribute deleted successfully.');
    }

    public function byProduct(Product $product)
    {
        $productAttributes = ProductAttribute::with(['attribute', 'attributeValues'])
            ->where('product_id', $product->id)
            ->paginate(15);

        return view('admin.product_attributes.index', compact('productAttributes', 'product'));
    }

    public function byAttribute(Attribute $attribute)
    {
        $productAttributes = ProductAttribute::with(['product', 'attributeValues'])
            ->where('attribute_id', $attribute->id)
            ->paginate(15);

        return view('admin.product_attributes.index', compact('productAttributes', 'attribute'));
    }
}
