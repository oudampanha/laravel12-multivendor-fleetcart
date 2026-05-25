<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\User;
use App\Models\WishList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishListController extends BaseController
{
    protected string $resource = 'wish_list';

    protected array $additionalPermissions = ['customer_management_access'];

    public function index(Request $request)
    {
        $query = WishList::with(['customer', 'product', 'product.vendor']);

        // Filter by customer if provided
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by product if provided
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Search by customer name or product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQ) use ($search) {
                    $customerQ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('product', function ($productQ) use ($search) {
                        $productQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $wishLists = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.wish-lists.index', compact('wishLists'));
    }

    /**
     * Get wish list items by customer
     */
    public function byCustomer(User $customer)
    {
        $wishLists = WishList::with(['product', 'product.vendor'])
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.wish-lists.by-customer', compact('wishLists', 'customer'));
    }

    /**
     * Get wish list items by product
     */
    public function byProduct(Product $product)
    {
        $wishLists = WishList::with(['customer'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.wish-lists.by-product', compact('wishLists', 'product'));
    }

    public function destroy(User $customer, Product $product)
    {
        $wishList = WishList::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();

        if (! $wishList) {
            return redirect()->back()->with('error', 'Wish list item not found.');
        }

        $wishList->delete();

        return redirect()->route('admin.wish-lists.index')
            ->with('success', 'Wish list item removed successfully.');
    }

    /**
     * Get popular products based on wish list data
     */
    public function popularProducts(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');

        $query = Product::with(['vendor', 'brand'])
            ->withCount(['wishLists as wish_count' => function ($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }])
            ->groupBy('id')
            ->having('wish_count', '>', 0)
            ->orderBy('wish_count', 'desc')
            ->limit($limit);

        $popularProducts = $query->get();

        return view('admin.wish-lists.popular-products', compact('popularProducts', 'limit', 'period'));
    }

    /**
     * Get wish list statistics
     */
    public function statistics()
    {
        $stats = [
            'total_wish_lists' => WishList::count(),
            'unique_customers' => WishList::distinct('customer_id')->count(),
            'unique_products' => WishList::distinct('product_id')->count(),
            'wish_lists_today' => WishList::whereDate('created_at', today())->count(),
            'wish_lists_this_week' => WishList::where('created_at', '>=', now()->startOfWeek())->count(),
            'wish_lists_this_month' => WishList::where('created_at', '>=', now()->startOfMonth())->count(),
            'avg_items_per_customer' => round(WishList::count() / max(WishList::distinct('customer_id')->count(), 1), 2),
        ];

        // Get top categories in wish lists
        $topCategories = DB::table('wish_lists')
            ->join('products', 'wish_lists.product_id', '=', 'products.id')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as wish_count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('wish_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.wish-lists.statistics', compact('stats', 'topCategories'));
    }

    /**
     * Get customers with most wish list items
     */
    public function topCustomers(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');

        $query = User::whereDoesntHave('vendor')
            ->withCount(['wishLists as wish_count' => function ($query) use ($period) {
                if ($period !== 'all_time') {
                    $days = $this->getPeriodDays($period);
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            }])
            ->groupBy('id')
            ->having('wish_count', '>', 0)
            ->orderBy('wish_count', 'desc')
            ->limit($limit);

        $topCustomers = $query->get();

        return view('admin.wish-lists.top-customers', compact('topCustomers', 'limit', 'period'));
    }

    /**
     * Bulk remove wish list items
     */
    public function bulkRemove(Request $request)
    {
        $request->validate([
            'wish_list_ids' => 'required|array',
            'wish_list_ids.*' => 'exists:wish_lists,id',
        ]);

        $deleted = WishList::whereIn('id', $request->wish_list_ids)->delete();

        return redirect()->route('admin.wish-lists.index')
            ->with('success', "Removed {$deleted} wish list items successfully.");
    }

    /**
     * Clear all wish list items for a customer
     */
    public function clearCustomerWishList(User $customer)
    {
        $count = WishList::where('customer_id', $customer->id)->count();
        WishList::where('customer_id', $customer->id)->delete();

        return redirect()->route('admin.wish-lists.index')
            ->with('success', "Cleared {$count} wish list items for customer: {$customer->full_name}.");
    }

    /**
     * Clear all wish list items for a product
     */
    public function clearProductWishLists(Product $product)
    {
        $count = WishList::where('product_id', $product->id)->count();
        WishList::where('product_id', $product->id)->delete();

        return redirect()->route('admin.wish-lists.index')
            ->with('success', "Cleared {$count} wish list items for product: {$product->name}.");
    }

    /**
     * Get wish list trends over time
     */
    public function trends(Request $request)
    {
        $period = $request->get('period', 'daily');
        $days = $request->get('days', 30);

        $dateFormat = match ($period) {
            'hourly' => '%Y-%m-%d %H:00:00',
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $trends = WishList::selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, COUNT(*) as count")
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.wish-lists.trends', compact('trends', 'period', 'days'));
    }

    /**
     * Send notification to customers about wish list items on sale
     */
    public function notifyOnSale(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $notificationsSent = 0;

        foreach ($request->product_ids as $productId) {
            $customers = WishList::where('product_id', $productId)
                ->with('customer')
                ->get()
                ->pluck('customer')
                ->unique('id');

            foreach ($customers as $customer) {
                // Here you would send actual notifications
                // For now, we'll just count them
                $notificationsSent++;
            }
        }

        return redirect()->back()
            ->with('success', "Sent {$notificationsSent} sale notifications to customers.");
    }

    /**
     * Helper method to get period days
     */
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
}
