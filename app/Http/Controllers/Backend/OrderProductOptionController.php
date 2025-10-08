<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use Illuminate\Http\Request;

class OrderProductOptionController extends BaseController
{
    protected string $resource = 'order_product_option';

    protected array $additionalPermissions = ['order_product_option_management_access'];

    public function index()
    {
        $orderProductOptions = OrderProductOption::with(['orderProduct', 'option'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.order_product_options.index', compact('orderProductOptions'));
    }

    public function create()
    {
        $orderProducts = OrderProduct::all();
        $options = Option::all();

        return view('admin.order_product_options.create', compact('orderProducts', 'options'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_product_id' => 'required|exists:order_products,id',
            'option_id' => 'required|exists:options,id',
            'value' => 'nullable|string',
        ]);

        OrderProductOption::create($validated);

        return redirect()->route('admin.order_product_options.index')->with('success', 'Order Product Option created successfully.');
    }

    public function show(OrderProductOption $orderProductOption)
    {
        $orderProductOption->load(['orderProduct', 'option']);

        return view('admin.order_product_options.show', compact('orderProductOption'));
    }

    public function edit(OrderProductOption $orderProductOption)
    {
        $orderProducts = OrderProduct::all();
        $options = Option::all();

        return view('admin.order_product_options.edit', compact('orderProductOption', 'orderProducts', 'options'));
    }

    public function update(Request $request, OrderProductOption $orderProductOption)
    {
        $validated = $request->validate([
            'order_product_id' => 'required|exists:order_products,id',
            'option_id' => 'required|exists:options,id',
            'value' => 'nullable|string',
        ]);

        $orderProductOption->update($validated);

        return redirect()->route('admin.order_product_options.index')->with('success', 'Order Product Option updated successfully.');
    }

    public function destroy(OrderProductOption $orderProductOption)
    {
        $orderProductOption->delete();

        return redirect()->route('admin.order_product_options.index')->with('success', 'Order Product Option deleted successfully.');
    }
}
