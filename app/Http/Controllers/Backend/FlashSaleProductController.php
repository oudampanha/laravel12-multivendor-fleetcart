<?php

namespace App\Http\Controllers\Backend;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleProductController extends BaseController
{
    protected string $resource = 'flash_sale_product';

    protected array $additionalPermissions = ['flash_sale_product_management_access'];

    public function index()
    {
        $flashSaleProducts = FlashSaleProduct::with(['flashSale', 'product'])
            ->orderBy('position', 'asc')
            ->paginate(15);

        return view('admin.flash_sale_products.index', compact('flashSaleProducts'));
    }

    public function create()
    {
        $flashSales = FlashSale::all();
        $products = Product::all();

        return view('admin.flash_sale_products.create', compact('flashSales', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flash_sale_id' => 'required|exists:flash_sales,id',
            'product_id' => 'required|exists:products,id',
            'end_date' => 'required|date',
            'price' => 'required|decimal:0,4',
            'qty' => 'required|integer|min:0',
            'position' => 'required|integer|min:0',
        ]);

        FlashSaleProduct::create($validated);

        return redirect()->route('admin.flash-sale-products.index')->with('success', 'Flash Sale Product created successfully.');
    }

    public function show(FlashSaleProduct $flashSaleProduct)
    {
        $flashSaleProduct->load(['flashSale', 'product']);

        return view('admin.flash_sale_products.show', compact('flashSaleProduct'));
    }

    public function edit(FlashSaleProduct $flashSaleProduct)
    {
        $flashSales = FlashSale::all();
        $products = Product::all();

        return view('admin.flash_sale_products.edit', compact('flashSaleProduct', 'flashSales', 'products'));
    }

    public function update(Request $request, FlashSaleProduct $flashSaleProduct)
    {
        $validated = $request->validate([
            'flash_sale_id' => 'required|exists:flash_sales,id',
            'product_id' => 'required|exists:products,id',
            'end_date' => 'required|date',
            'price' => 'required|decimal:0,4',
            'qty' => 'required|integer|min:0',
            'position' => 'required|integer|min:0',
        ]);

        $flashSaleProduct->update($validated);

        return redirect()->route('admin.flash-sale-products.index')->with('success', 'Flash Sale Product updated successfully.');
    }

    public function destroy(FlashSaleProduct $flashSaleProduct)
    {
        $flashSaleProduct->delete();

        return redirect()->route('admin.flash-sale-products.index')->with('success', 'Flash Sale Product deleted successfully.');
    }
}
