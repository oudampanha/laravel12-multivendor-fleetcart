<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorOrder;

class DashboardController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // Dashboard requires dashboard_access permission (already handled in BaseController)
        // Additional permissions for analytics and reports
        $this->applyMethodPermission('dashboard_access', ['analytics', 'reports']);
    }

    public function index()
    {
        // Test Flasher functionality
        sweetalert()->success('Welcome to the Admin Dashboard!');

        $stats = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('is_active', true)->count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'pending_products' => Product::where('vendor_status', 'pending')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'pending_revenue' => VendorOrder::where('status', '!=', 'delivered')->sum('vendor_amount'),
        ];

        $recent_orders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_vendors = Vendor::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $top_products = Product::withCount('orderProducts')
            ->orderBy('order_products_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_vendors', 'top_products'));
    }

    public function analytics()
    {
        $monthly_revenue = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as revenue')
            ->where('status', 'delivered')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $monthly_orders = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $vendor_performance = Vendor::with(['orders' => function ($query) {
            $query->selectRaw('vendor_id, COUNT(*) as order_count, SUM(vendor_amount) as total_amount')
                ->groupBy('vendor_id');
        }])
            ->withCount('products')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.analytics', compact('monthly_revenue', 'monthly_orders', 'vendor_performance'));
    }

    public function reports()
    {
        $sales_report = [
            'daily' => Order::whereDate('created_at', today())->sum('total'),
            'weekly' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total'),
            'monthly' => Order::whereMonth('created_at', now()->month)->sum('total'),
            'yearly' => Order::whereYear('created_at', now()->year)->sum('total'),
        ];

        $vendor_stats = Vendor::selectRaw('
            COUNT(*) as total_vendors,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_vendors,
            SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified_vendors,
            SUM(balance) as total_balance
        ')->first();

        $product_stats = Product::selectRaw('
            COUNT(*) as total_products,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products,
            SUM(CASE WHEN vendor_status = "pending" THEN 1 ELSE 0 END) as pending_products,
            SUM(CASE WHEN vendor_status = "approved" THEN 1 ELSE 0 END) as approved_products,
            SUM(CASE WHEN vendor_status = "rejected" THEN 1 ELSE 0 END) as rejected_products
        ')->first();

        return view('admin.reports', compact('sales_report', 'vendor_stats', 'product_stats'));
    }
}
