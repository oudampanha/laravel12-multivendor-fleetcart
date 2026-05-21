<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockMovementController extends BaseController
{
    protected string $resource = 'stock_movement';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = StockMovement::with(['warehouse', 'product', 'variant', 'user']);

        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($productId = $request->input('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $movements = $query->orderBy('id', 'desc')->paginate(30)->withQueryString();
        $warehouses = Warehouse::active()->orderBy('position')->get();
        $products = Product::orderBy('id', 'desc')->limit(200)->get();

        return view('admin.stock_movements.index', compact('movements', 'warehouses', 'products'));
    }

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['warehouse', 'product', 'variant', 'user']);

        return view('admin.stock_movements.show', compact('stockMovement'));
    }
}
