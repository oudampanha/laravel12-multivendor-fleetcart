<?php

namespace App\Http\Controllers\Backend;

use App\Models\GoodsReceipt;
use App\Models\ProductStock;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Warehouse;

class InventoryDashboardController extends BaseController
{
    protected array $additionalPermissions = ['inventory_management_access'];

    public function index()
    {
        $totalWarehouses = Warehouse::count();
        $activeWarehouses = Warehouse::where('is_active', true)->count();

        $totalSkus = ProductStock::distinct('product_id')->count('product_id');
        $totalQuantity = (int) ProductStock::sum('quantity');
        $totalReserved = (int) ProductStock::sum('reserved_quantity');
        $stockValue = (float) ProductStock::sum(\DB::raw('quantity * average_cost'));

        $lowStock = ProductStock::with(['product', 'variant', 'warehouse'])
            ->lowStock()
            ->orderBy('quantity')
            ->limit(10)
            ->get();

        $outOfStock = ProductStock::with(['product', 'variant', 'warehouse'])
            ->outOfStock()
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $recentMovements = StockMovement::with(['product', 'warehouse', 'user'])
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get();

        $openPos = PurchaseOrder::whereIn('status', [
            PurchaseOrder::STATUS_DRAFT,
            PurchaseOrder::STATUS_SENT,
            PurchaseOrder::STATUS_CONFIRMED,
            PurchaseOrder::STATUS_PARTIAL,
        ])->count();

        $pendingReceipts = GoodsReceipt::where('status', GoodsReceipt::STATUS_DRAFT)->count();

        $pendingAdjustments = StockAdjustment::where('status', StockAdjustment::STATUS_DRAFT)->count();

        $inTransitTransfers = StockTransfer::where('status', StockTransfer::STATUS_IN_TRANSIT)->count();

        return view('admin.inventory.dashboard', compact(
            'totalWarehouses',
            'activeWarehouses',
            'totalSkus',
            'totalQuantity',
            'totalReserved',
            'stockValue',
            'lowStock',
            'outOfStock',
            'recentMovements',
            'openPos',
            'pendingReceipts',
            'pendingAdjustments',
            'inTransitTransfers'
        ));
    }
}
