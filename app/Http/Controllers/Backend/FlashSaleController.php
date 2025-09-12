<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function index()
    {
        $flashSales = FlashSale::withCount('products')->paginate(15);
        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        return view('admin.flash-sales.create');
    }

    public function store(Request $request)
    {
        $flashSale = FlashSale::create($request->all());

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale created successfully.');
    }

    public function show(FlashSale $flashSale)
    {
        $flashSale->load('products.product');
        return view('admin.flash-sales.show', compact('flashSale'));
    }

    public function edit(FlashSale $flashSale)
    {
        return view('admin.flash-sales.edit', compact('flashSale'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $flashSale->update($request->all());

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale updated successfully.');
    }

    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash sale deleted successfully.');
    }

    public function addProduct(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'end_date' => 'required|date|after:today',
            'position' => 'required|integer'
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
}