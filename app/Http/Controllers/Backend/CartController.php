<?php

namespace App\Http\Controllers\Backend;

use App\Models\Cart;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    protected string $resource = 'cart';
    
    protected array $additionalPermissions = ['cart_management_access'];

    public function index(Request $request)
    {
        $query = Cart::with(['user', 'product'])->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'abandoned') {
                $query->where('updated_at', '<', now()->subDays(7))
                      ->where('is_active', true);
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $carts = $query->paginate(15);
        return view('admin.carts.index', compact('carts'));
    }

    public function show(Cart $cart)
    {
        $cart->load(['user', 'product']);
        return view('admin.carts.show', compact('cart'));
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return redirect()->route('admin.carts.index')->with('success', 'Cart deleted successfully.');
    }

    /**
     * Get abandoned carts
     */
    public function abandoned()
    {
        $carts = Cart::with(['user', 'product'])
            ->where('updated_at', '<', now()->subDays(7))
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        return view('admin.carts.index', compact('carts'));
    }

    /**
     * Cleanup abandoned carts
     */
    public function cleanupAbandoned(Request $request)
    {
        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);
        
        $deleted = Cart::where('updated_at', '<', $cutoffDate)
            ->where('is_active', true)
            ->delete();
            
        return redirect()->route('admin.carts.index')
            ->with('success', "Cleaned up {$deleted} abandoned carts.");
    }

    /**
     * Get cart statistics
     */
    public function statistics()
    {
        $stats = [
            'total_carts' => Cart::count(),
            'active_carts' => Cart::where('is_active', true)->count(),
            'abandoned_carts' => Cart::where('updated_at', '<', now()->subDays(7))
                                     ->where('is_active', true)->count(),
            'total_items' => Cart::sum('qty'),
            'total_value' => Cart::sum('total'),
            'average_cart_value' => Cart::avg('total'),
            'carts_today' => Cart::whereDate('created_at', today())->count(),
            'carts_this_week' => Cart::where('created_at', '>=', now()->startOfWeek())->count(),
            'carts_this_month' => Cart::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
        
        return view('admin.carts.statistics', compact('stats'));
    }
}