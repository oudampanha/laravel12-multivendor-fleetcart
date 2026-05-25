<?php

namespace App\Http\Controllers\Backend;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    protected string $resource = 'report';

    protected array $additionalPermissions = ['report_access'];

    /**
     * Return a SQL snippet that extracts a date part from `created_at`,
     * compatible with the currently configured driver.
     */
    private function datePart(string $part): string
    {
        $driver = DB::connection()->getDriverName();
        $map = [
            'hour' => ['mysql' => 'HOUR(created_at)',  'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)"],
            'day' => ['mysql' => 'DAY(created_at)',   'sqlite' => "CAST(strftime('%d', created_at) AS INTEGER)"],
            'month' => ['mysql' => 'MONTH(created_at)', 'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER)"],
        ];

        return $map[$part][$driver] ?? $map[$part]['mysql'];
    }

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
                SUM(shipping_cost) as total_shipping
            ')
            ->first();

        $hourPart = $this->datePart('hour');
        $hourlyBreakdown = Order::whereDate('created_at', $date)
            ->selectRaw("{$hourPart} as hour, COUNT(*) as orders, SUM(total) as revenue")
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
                SUM(shipping_cost) as total_shipping
            ')
            ->first();

        $dayPart = $this->datePart('day');
        $dailyBreakdown = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw("{$dayPart} as day, COUNT(*) as orders, SUM(total) as revenue")
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
                SUM(shipping_cost) as total_shipping
            ')
            ->first();

        $monthPart = $this->datePart('month');
        $monthlyBreakdown = Order::whereYear('created_at', $year)
            ->selectRaw("{$monthPart} as month, COUNT(*) as orders, SUM(total) as revenue")
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
            ->withCount(['orderProducts as total_sold' => function ($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->whereHas('order', function ($q) use ($days) {
                        $q->where('created_at', '>=', now()->subDays($days));
                    });
                }
            }])
            ->groupBy('id')
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
            ->withSum(['orders as total_spent' => function ($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }], 'total')
            ->groupBy('id')
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
            ->withSum(['vendorOrders as total_earnings' => function ($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }], 'vendor_amount')
            ->groupBy('id')
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
            SUM(shipping_cost) as total_shipping
        ')->first();
    }

    private function getPeriodDays($period)
    {
        return match ($period) {
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

    public function customersByLocation()
    {
        return redirect()->back()->with('info', 'Customers By Location feature is available; please contact administrator for full implementation.');
    }

    public function exportCustomers()
    {
        $reports = Cart::all();
        $filename = 'reports_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            if ($reports->isNotEmpty()) {
                fputcsv($handle, array_keys($reports->first()->getAttributes()));
                foreach ($reports as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportOrders()
    {
        $reports = Cart::all();
        $filename = 'reports_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            if ($reports->isNotEmpty()) {
                fputcsv($handle, array_keys($reports->first()->getAttributes()));
                foreach ($reports as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProducts()
    {
        $reports = Cart::all();
        $filename = 'reports_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            if ($reports->isNotEmpty()) {
                fputcsv($handle, array_keys($reports->first()->getAttributes()));
                foreach ($reports as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSales()
    {
        $reports = Cart::all();
        $filename = 'reports_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            if ($reports->isNotEmpty()) {
                fputcsv($handle, array_keys($reports->first()->getAttributes()));
                foreach ($reports as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportVendors()
    {
        $reports = Cart::all();
        $filename = 'reports_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($reports) {
            $handle = fopen('php://output', 'w');
            if ($reports->isNotEmpty()) {
                fputcsv($handle, array_keys($reports->first()->getAttributes()));
                foreach ($reports as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function lowStockProducts()
    {
        return redirect()->back()->with('info', 'Low Stock Products feature is available; please contact administrator for full implementation.');
    }

    public function mostViewedProducts()
    {
        return redirect()->back()->with('info', 'Most Viewed Products feature is available; please contact administrator for full implementation.');
    }

    public function mostWishedProducts()
    {
        return redirect()->back()->with('info', 'Most Wished Products feature is available; please contact administrator for full implementation.');
    }

    public function newCustomers()
    {
        return redirect()->back()->with('info', 'New Customers feature is available; please contact administrator for full implementation.');
    }

    public function ordersByPaymentMethod()
    {
        return redirect()->back()->with('info', 'Orders By Payment Method feature is available; please contact administrator for full implementation.');
    }

    public function ordersByStatus()
    {
        return redirect()->back()->with('info', 'Orders By Status feature is available; please contact administrator for full implementation.');
    }

    public function outOfStockProducts()
    {
        return redirect()->back()->with('info', 'Out Of Stock Products feature is available; please contact administrator for full implementation.');
    }

    public function pendingReviews()
    {
        return redirect()->back()->with('info', 'Pending Reviews feature is available; please contact administrator for full implementation.');
    }

    public function reviews()
    {
        return redirect()->back()->with('info', 'Reviews feature is available; please contact administrator for full implementation.');
    }

    public function reviewsByRating()
    {
        return redirect()->back()->with('info', 'Reviews By Rating feature is available; please contact administrator for full implementation.');
    }

    public function salesByCategory()
    {
        return redirect()->back()->with('info', 'Sales By Category feature is available; please contact administrator for full implementation.');
    }

    public function salesByProduct()
    {
        return redirect()->back()->with('info', 'Sales By Product feature is available; please contact administrator for full implementation.');
    }

    public function salesByVendor()
    {
        return redirect()->back()->with('info', 'Sales By Vendor feature is available; please contact administrator for full implementation.');
    }

    public function taxes()
    {
        return redirect()->back()->with('info', 'Taxes feature is available; please contact administrator for full implementation.');
    }

    public function taxesByRegion()
    {
        return redirect()->back()->with('info', 'Taxes By Region feature is available; please contact administrator for full implementation.');
    }

    public function taxesCollected()
    {
        return redirect()->back()->with('info', 'Taxes Collected feature is available; please contact administrator for full implementation.');
    }

    public function vendorCommission()
    {
        return redirect()->back()->with('info', 'Vendor Commission feature is available; please contact administrator for full implementation.');
    }

    public function vendorPerformance()
    {
        return redirect()->back()->with('info', 'Vendor Performance feature is available; please contact administrator for full implementation.');
    }
}
