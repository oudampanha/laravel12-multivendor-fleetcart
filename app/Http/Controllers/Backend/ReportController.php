<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\Cart;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    protected string $resource = 'report';
    
    protected array $additionalPermissions = ['report_access'];

    /**
     * Sales Reports
     */
    public function sales(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $salesData = $this->getSalesData($period, $startDate, $endDate);
        
        return view('admin.reports.sales', compact('salesData', 'period', 'startDate', 'endDate'));
    }

    public function dailySales(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $salesData = Order::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value,
                SUM(shipping_cost) as total_shipping,
                SUM(tax_amount) as total_tax
            ')
            ->first();
            
        $hourlyBreakdown = Order::whereDate('created_at', $date)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        return view('admin.reports.daily-sales', compact('salesData', 'hourlyBreakdown', 'date'));
    }

    public function monthlySales(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $salesData = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value,
                SUM(shipping_cost) as total_shipping,
                SUM(tax_amount) as total_tax
            ')
            ->first();
            
        $dailyBreakdown = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as day, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();
        
        return view('admin.reports.monthly-sales', compact('salesData', 'dailyBreakdown', 'year', 'month'));
    }

    public function yearlySales(Request $request)
    {
        $year = $request->get('year', now()->year);
        
        $salesData = Order::whereYear('created_at', $year)
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value,
                SUM(shipping_cost) as total_shipping,
                SUM(tax_amount) as total_tax
            ')
            ->first();
            
        $monthlyBreakdown = Order::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('admin.reports.yearly-sales', compact('salesData', 'monthlyBreakdown', 'year'));
    }

    /**
     * Product Reports
     */
    public function products()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'out_of_stock' => Product::where('manage_stock', true)->where('qty', 0)->count(),
            'low_stock' => Product::where('manage_stock', true)->where('qty', '>', 0)->where('qty', '<=', 5)->count(),
            'products_with_images' => Product::whereHas('media')->count(),
            'products_without_images' => Product::whereDoesntHave('media')->count(),
        ];
        
        return view('admin.reports.products', compact('stats'));
    }

    public function bestSellingProducts(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');
        
        $query = Product::with(['vendor', 'brand'])
            ->withCount(['orderProducts as total_sold' => function($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->whereHas('order', function($q) use ($days) {
                        $q->where('created_at', '>=', now()->subDays($days));
                    });
                }
            }])
            ->having('total_sold', '>', 0)
            ->orderBy('total_sold', 'desc')
            ->limit($limit);
            
        $products = $query->get();
        
        return view('admin.reports.best-selling-products', compact('products', 'limit', 'period'));
    }

    /**
     * Customer Reports
     */
    public function customers()
    {
        $stats = [
            'total_customers' => User::whereDoesntHave('vendor')->count(),
            'new_customers_today' => User::whereDoesntHave('vendor')->whereDate('created_at', today())->count(),
            'new_customers_week' => User::whereDoesntHave('vendor')->where('created_at', '>=', now()->startOfWeek())->count(),
            'new_customers_month' => User::whereDoesntHave('vendor')->where('created_at', '>=', now()->startOfMonth())->count(),
            'customers_with_orders' => User::whereDoesntHave('vendor')->whereHas('orders')->count(),
            'customers_without_orders' => User::whereDoesntHave('vendor')->whereDoesntHave('orders')->count(),
        ];
        
        return view('admin.reports.customers', compact('stats'));
    }

    public function topSpenders(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');
        
        $query = User::whereDoesntHave('vendor')
            ->withSum(['orders as total_spent' => function($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }], 'total')
            ->having('total_spent', '>', 0)
            ->orderBy('total_spent', 'desc')
            ->limit($limit);
            
        $customers = $query->get();
        
        return view('admin.reports.top-spenders', compact('customers', 'limit', 'period'));
    }

    /**
     * Vendor Reports
     */
    public function vendors()
    {
        $stats = [
            'total_vendors' => Vendor::count(),
            'active_vendors' => Vendor::where('is_active', true)->count(),
            'verified_vendors' => Vendor::where('is_verified', true)->count(),
            'vendors_with_products' => Vendor::whereHas('products')->count(),
            'vendors_with_orders' => Vendor::whereHas('vendorOrders')->count(),
        ];
        
        return view('admin.reports.vendors', compact('stats'));
    }

    public function topEarningVendors(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');
        
        $query = Vendor::with('user')
            ->withSum(['vendorOrders as total_earnings' => function($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }], 'vendor_amount')
            ->having('total_earnings', '>', 0)
            ->orderBy('total_earnings', 'desc')
            ->limit($limit);
            
        $vendors = $query->get();
        
        return view('admin.reports.top-earning-vendors', compact('vendors', 'limit', 'period'));
    }

    /**
     * Order Reports
     */
    public function orders()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'orders_this_week' => Order::where('created_at', '>=', now()->startOfWeek())->count(),
            'orders_this_month' => Order::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        
        return view('admin.reports.orders', compact('stats'));
    }

    public function abandonedCarts()
    {
        $abandonedCarts = Cart::with(['user', 'product'])
            ->where('is_active', true)
            ->where('updated_at', '<', now()->subDays(1))
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        $stats = [
            'total_abandoned' => Cart::where('is_active', true)->where('updated_at', '<', now()->subDays(1))->count(),
            'abandoned_value' => Cart::where('is_active', true)->where('updated_at', '<', now()->subDays(1))->sum('total'),
            'recovery_rate' => $this->calculateCartRecoveryRate(),
        ];
        
        return view('admin.reports.abandoned-carts', compact('abandonedCarts', 'stats'));
    }

    /**
     * Helper Methods
     */
    private function getSalesData($period, $startDate, $endDate)
    {
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        return $query->selectRaw('
            COUNT(*) as total_orders,
            SUM(total) as total_revenue,
            AVG(total) as average_order_value,
            SUM(shipping_cost) as total_shipping,
            SUM(tax_amount) as total_tax
        ')->first();
    }

    private function getPeriodDays($period)
    {
        return match($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 30
        };
    }

    private function calculateCartRecoveryRate()
    {
        $totalAbandoned = Cart::where('is_active', true)
            ->where('updated_at', '<', now()->subDays(1))
            ->count();
            
        if ($totalAbandoned === 0) {
            return 0;
        }
        
        // This is a simplified calculation - in practice you'd track actual recoveries
        $recovered = Order::where('created_at', '>=', now()->subDays(30))->count() * 0.1; // Estimated 10% recovery
        
        return round(($recovered / $totalAbandoned) * 100, 2);
    }
}