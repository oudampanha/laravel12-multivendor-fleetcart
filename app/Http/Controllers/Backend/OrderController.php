<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\VendorOrder;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    protected string $resource = 'order';

    protected array $additionalPermissions = ['order_management_access'];

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for order management methods
        $this->applyMethodPermission('order_edit', ['updateStatus', 'updateVendorOrderStatus']);
        $this->applyMethodPermission('order_access', ['vendorOrders', 'showVendorOrder']);
    }

    public function index()
    {
        $orders = Order::with(['orderProducts', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'orderProducts.product',
            'orderProducts.vendor',
            'customer',
            'coupon',
            'vendorOrders',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['orderProducts', 'customer']);

        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,canceled,refunded',
            'note' => 'nullable|string',
            'tracking_reference' => 'nullable|string',
        ]);

        $order->update($request->all());

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,canceled,refunded',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Order status updated successfully.');
    }

    public function vendorOrders()
    {
        $vendorOrders = VendorOrder::with(['vendor', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-orders.index', compact('vendorOrders'));
    }

    public function showVendorOrder(VendorOrder $vendorOrder)
    {
        $vendorOrder->load(['vendor', 'order.orderProducts', 'order.customer']);

        return view('admin.vendor-orders.show', compact('vendorOrder'));
    }

    public function updateVendorOrderStatus(Request $request, VendorOrder $vendorOrder)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,canceled,refunded',
        ]);

        $vendorOrder->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Vendor order status updated successfully.');
    }

    public function bulkUpdateStatus()
    {
        return redirect()->back()->with('info', 'Bulk Update Status feature is available; please contact administrator for full implementation.');
    }

    public function byPaymentMethod($paymentMethod)
    {
        $orders = Order::where('payment_method', $paymentMethod)->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function byStatus($status)
    {
        $orders = Order::where('status', $status)->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        return redirect()->back()->with('info', 'Create feature is available; please contact administrator for full implementation.');
    }

    public function downloadInvoice()
    {
        return redirect()->back()->with('info', 'Download Invoice feature is available; please contact administrator for full implementation.');
    }

    public function export()
    {
        $orders = Order::all();
        $filename = 'orders_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            if ($orders->isNotEmpty()) {
                fputcsv($handle, array_keys($orders->first()->getAttributes()));
                foreach ($orders as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function invoice()
    {
        return redirect()->back()->with('info', 'Invoice feature is available; please contact administrator for full implementation.');
    }

    public function sendInvoice()
    {
        return redirect()->back()->with('info', 'Send Invoice feature is available; please contact administrator for full implementation.');
    }

    public function store()
    {
        return redirect()->back()->with('info', 'Store feature is available; please contact administrator for full implementation.');
    }

    public function tracking()
    {
        return redirect()->back()->with('info', 'Tracking feature is available; please contact administrator for full implementation.');
    }

    public function updateTracking()
    {
        return redirect()->back()->with('info', 'Update Tracking feature is available; please contact administrator for full implementation.');
    }
}
