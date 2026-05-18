<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Http\Request;

class OrderProductController extends BaseController
{
    protected string $resource = 'order_product';

    protected array $additionalPermissions = ['order_product_management_access'];

    public function index()
    {
        $orderProducts = OrderProduct::with(['order', 'product', 'vendor', 'productVariant'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.order_products.index', compact('orderProducts'));
    }

    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        $vendors = Vendor::all();
        $productVariants = ProductVariant::all();

        return view('admin.order_products.create', compact('orders', 'products', 'vendors', 'productVariants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'unit_price' => 'required|decimal:0,4',
            'qty' => 'required|integer|min:1',
            'line_total' => 'required|decimal:0,4',
            'vendor_commission' => 'decimal:0,4',
            'vendor_status' => 'required|in:pending,processing,shipped,delivered,canceled,refunded',
        ]);

        OrderProduct::create($validated);

        return redirect()->route('admin.order_products.index')->with('success', 'Order Product created successfully.');
    }

    public function show(OrderProduct $orderProduct)
    {
        $orderProduct->load(['order', 'product', 'vendor', 'productVariant']);

        return view('admin.order_products.show', compact('orderProduct'));
    }

    public function edit(OrderProduct $orderProduct)
    {
        $orders = Order::all();
        $products = Product::all();
        $vendors = Vendor::all();
        $productVariants = ProductVariant::all();

        return view('admin.order_products.edit', compact('orderProduct', 'orders', 'products', 'vendors', 'productVariants'));
    }

    public function update(Request $request, OrderProduct $orderProduct)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'unit_price' => 'required|decimal:0,4',
            'qty' => 'required|integer|min:1',
            'line_total' => 'required|decimal:0,4',
            'vendor_commission' => 'decimal:0,4',
            'vendor_status' => 'required|in:pending,processing,shipped,delivered,canceled,refunded',
        ]);

        $orderProduct->update($validated);

        return redirect()->route('admin.order_products.index')->with('success', 'Order Product updated successfully.');
    }

    public function destroy(OrderProduct $orderProduct)
    {
        $orderProduct->delete();

        return redirect()->route('admin.order_products.index')->with('success', 'Order Product deleted successfully.');
    }

    public function byStatus($status)
    {
        $orderProducts = Order::where('status', $status)->paginate(15);

        return view('admin.order_products.index', compact('orderProducts'));
    }

    public function byVendor($vendor)
    {
        $orderProducts = Order::where('vendor_id', $vendor)->paginate(15);

        return view('admin.order_products.index', compact('orderProducts'));
    }

    public function updateVendorStatus()
    {
        return redirect()->back()->with('info', 'Update Vendor Status feature is available; please contact administrator for full implementation.');
    }
}
