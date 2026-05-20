<?php

namespace App\Http\Controllers\Backend;

use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ProductStockController extends BaseController
{
    protected string $resource = 'product_stock';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = ProductStock::with(['product', 'variant', 'warehouse']);

        if ($warehouseId = $request->input('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($search = $request->input('q')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->input('low_stock')) {
            $query->lowStock();
        }

        if ($request->input('out_of_stock')) {
            $query->outOfStock();
        }

        $stocks = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.product_stocks.index', compact('stocks', 'warehouses'));
    }

    public function show(ProductStock $productStock)
    {
        $productStock->load(['product', 'variant', 'warehouse']);

        return view('admin.product_stocks.show', compact('productStock'));
    }

    public function edit(ProductStock $productStock)
    {
        $productStock->load(['product', 'variant', 'warehouse']);

        return view('admin.product_stocks.edit', compact('productStock'));
    }

    public function update(Request $request, ProductStock $productStock)
    {
        $data = $request->validate([
            'reorder_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
        ]);

        $productStock->update($data);

        return redirect()->route('admin.product-stocks.index')->with('success', 'Reorder thresholds updated.');
    }

    public function lowStock(Request $request)
    {
        $stocks = ProductStock::with(['product', 'variant', 'warehouse'])
            ->lowStock()
            ->orderBy('quantity')
            ->paginate(25)
            ->withQueryString();
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.product_stocks.index', compact('stocks', 'warehouses'));
    }

    public function outOfStock(Request $request)
    {
        $stocks = ProductStock::with(['product', 'variant', 'warehouse'])
            ->outOfStock()
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();
        $warehouses = Warehouse::active()->orderBy('position')->get();

        return view('admin.product_stocks.index', compact('stocks', 'warehouses'));
    }
}
