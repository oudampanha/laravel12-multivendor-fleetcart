<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Models\Coupon;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CouponController extends BaseController
{
    protected string $resource = 'coupon';

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $coupons = Coupon::with('vendor')->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        
        return view('admin.coupons.create', compact('vendors', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'code' => 'required|string|unique:coupons,code',
            'value' => 'nullable|numeric|min:0',
            'is_percent' => 'boolean',
            'free_shipping' => 'boolean',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_spend' => 'nullable|numeric|min:0',
            'usage_limit_per_coupon' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);

        $coupon = Coupon::create($request->all());

        if ($request->has('categories')) {
            $coupon->categories()->attach($request->categories);
        }

        if ($request->has('products')) {
            $coupon->products()->attach($request->products);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['vendor', 'categories', 'products']);
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $vendors = Vendor::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        
        return view('admin.coupons.edit', compact('coupon', 'vendors', 'categories', 'products'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'value' => 'nullable|numeric|min:0',
            'is_percent' => 'boolean',
            'free_shipping' => 'boolean',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_spend' => 'nullable|numeric|min:0',
            'usage_limit_per_coupon' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);

        $coupon->update($request->all());

        if ($request->has('categories')) {
            $coupon->categories()->sync($request->categories);
        }

        if ($request->has('products')) {
            $coupon->products()->sync($request->products);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}