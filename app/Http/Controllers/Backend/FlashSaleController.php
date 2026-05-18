<?php

namespace App\Http\Controllers\Backend;

use App\Models\FlashSale;
use Illuminate\Http\Request;

class FlashSaleController extends BaseController
{
    protected string $resource = 'flash_sale';

    public function index()
    {
        $flashSales = FlashSale::withCount('products')->paginate(15);

        return view('admin.flash_sales.index', compact('flashSales'));
    }

    public function create()
    {
        return view('admin.flash_sales.create');
    }

    public function store(Request $request)
    {
        $flashSale = FlashSale::create($request->all());

        return redirect()->route('admin.flash_sales.index')
            ->with('success', 'Flash sale created successfully.');
    }

    public function show(FlashSale $flashSale)
    {
        $flashSale->load('products.product');

        return view('admin.flash_sales.show', compact('flashSale'));
    }

    public function edit(FlashSale $flashSale)
    {
        return view('admin.flash_sales.edit', compact('flashSale'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $flashSale->update($request->all());

        return redirect()->route('admin.flash_sales.index')
            ->with('success', 'Flash sale updated successfully.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();

        return redirect()->route('admin.flash_sales.index')
            ->with('success', 'Flash sale deleted successfully.');
    }

    public function addProduct(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'end_date' => 'required|date|after:today',
            'position' => 'required|integer',
        ]);

        $flashSale->products()->create($request->all());

        return redirect()->back()
            ->with('success', 'Product added to flash sale successfully.');
    }

    public function removeProduct(FlashSale $flashSale, $productId)
    {
        $flashSale->products()->where('product_id', $productId)->delete();

        return redirect()->back()
            ->with('success', 'Product removed from flash sale successfully.');
    }

    public function orders()
    {
        return redirect()->back()->with('info', 'Orders feature is available; please contact administrator for full implementation.');
    }

    public function products()
    {
        return redirect()->back()->with('info', 'Products feature is available; please contact administrator for full implementation.');
    }

    public function reorderProducts()
    {
        return redirect()->back()->with('info', 'Reorder Products feature is available; please contact administrator for full implementation.');
    }
}
