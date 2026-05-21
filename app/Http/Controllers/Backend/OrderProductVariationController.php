<?php

namespace App\Http\Controllers\Backend;

use App\Models\OrderProduct;
use App\Models\OrderProductVariation;
use App\Models\Variation;
use Illuminate\Http\Request;

class OrderProductVariationController extends BaseController
{
    protected string $resource = 'order_product_variation';

    protected array $additionalPermissions = ['order_product_variation_management_access'];

    public function index()
    {
        $orderProductVariations = OrderProductVariation::with(['orderProduct', 'variation'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.order-product-variations.index', compact('orderProductVariations'));
    }

    public function create()
    {
        $orderProducts = OrderProduct::all();
        $variations = Variation::all();

        return view('admin.order-product-variations.create', compact('orderProducts', 'variations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_product_id' => 'required|exists:order_products,id',
            'variation_id' => 'required|exists:variations,id',
            'type' => 'required|string',
            'value' => 'required|string',
        ]);

        OrderProductVariation::create($validated);

        return redirect()->route('admin.order-product-variations.index')->with('success', 'Order Product Variation created successfully.');
    }

    public function show(OrderProductVariation $orderProductVariation)
    {
        $orderProductVariation->load(['orderProduct', 'variation']);

        return view('admin.order-product-variations.show', compact('orderProductVariation'));
    }

    public function edit(OrderProductVariation $orderProductVariation)
    {
        $orderProducts = OrderProduct::all();
        $variations = Variation::all();

        return view('admin.order_product_variations.edit', compact('orderProductVariation', 'orderProducts', 'variations'));
    }

    public function update(Request $request, OrderProductVariation $orderProductVariation)
    {
        $validated = $request->validate([
            'order_product_id' => 'required|exists:order_products,id',
            'variation_id' => 'required|exists:variations,id',
            'type' => 'required|string',
            'value' => 'required|string',
        ]);

        $orderProductVariation->update($validated);

        return redirect()->route('admin.order-product-variations.index')->with('success', 'Order Product Variation updated successfully.');
    }

    public function destroy(OrderProductVariation $orderProductVariation)
    {
        $orderProductVariation->delete();

        return redirect()->route('admin.order-product-variations.index')->with('success', 'Order Product Variation deleted successfully.');
    }
}
