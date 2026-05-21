<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorOrder;
use Illuminate\Http\Request;

class VendorOrderController extends BaseController
{
    protected string $resource = 'vendor_order';

    protected array $additionalPermissions = ['vendor_order_management_access'];

    public function index()
    {
        $vendorOrders = VendorOrder::with(['vendor', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor_orders.index', compact('vendorOrders'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $orders = Order::all();

        return view('admin.vendor-orders.create', compact('vendors', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'order_id' => 'required|exists:orders,id',
            'sub_total' => 'required|decimal:0,4',
            'commission_amount' => 'required|decimal:0,4',
            'vendor_amount' => 'required|decimal:0,4',
            'status' => 'required|in:pending,processing,shipped,delivered,canceled,refunded',
            'note' => 'nullable|string',
        ]);

        VendorOrder::create($validated);

        return redirect()->route('admin.vendor-orders.index')->with('success', 'Vendor Order created successfully.');
    }

    public function show(VendorOrder $vendorOrder)
    {
        $vendorOrder->load(['vendor', 'order']);

        return view('admin.vendor_orders.show', compact('vendorOrder'));
    }

    public function edit(VendorOrder $vendorOrder)
    {
        $vendors = Vendor::all();
        $orders = Order::all();

        return view('admin.vendor-orders.edit', compact('vendorOrder', 'vendors', 'orders'));
    }

    public function update(Request $request, VendorOrder $vendorOrder)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'order_id' => 'required|exists:orders,id',
            'sub_total' => 'required|decimal:0,4',
            'commission_amount' => 'required|decimal:0,4',
            'vendor_amount' => 'required|decimal:0,4',
            'status' => 'required|in:pending,processing,shipped,delivered,canceled,refunded',
            'note' => 'nullable|string',
        ]);

        $vendorOrder->update($validated);

        return redirect()->route('admin.vendor-orders.index')->with('success', 'Vendor Order updated successfully.');
    }

    public function destroy(VendorOrder $vendorOrder)
    {
        $vendorOrder->delete();

        return redirect()->route('admin.vendor-orders.index')->with('success', 'Vendor Order deleted successfully.');
    }

    public function byStatus($status)
    {
        $vendorOrders = Order::where('status', $status)->paginate(15);

        return view('admin.vendor_orders.index', compact('vendorOrders'));
    }

    public function byVendor($vendor)
    {
        $vendorOrders = Order::where('vendor_id', $vendor)->paginate(15);

        return view('admin.vendor_orders.index', compact('vendorOrders'));
    }

    public function updateStatus()
    {
        return redirect()->back()->with('info', 'Update Status feature is available; please contact administrator for full implementation.');
    }
}
