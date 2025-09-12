<?php

namespace App\Http\Controllers\Backend;

use App\Models\VendorShippingZone;
use App\Models\Vendor;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class VendorShippingZoneController extends BaseController
{
    protected string $resource = 'vendor_shipping_zone';
    
    protected array $additionalPermissions = ['vendor_shipping_zone_management_access'];

    public function index()
    {
        $vendorShippingZones = VendorShippingZone::with('vendor')
                                                ->orderBy('created_at', 'desc')
                                                ->paginate(15);
        return view('admin.vendor_shipping_zones.index', compact('vendorShippingZones'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        return view('admin.vendor_shipping_zones.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'countries' => 'required|array',
            'states' => 'nullable|array',
            'zip_codes' => 'nullable|array',
            'shipping_method' => 'required|in:flat_rate,free_shipping,local_pickup,by_weight,by_price',
            'rate' => 'nullable|decimal:0,4',
            'minimum_order' => 'nullable|decimal:0,4',
            'is_active' => 'boolean'
        ]);

        VendorShippingZone::create($validated);

        return redirect()->route('admin.vendor_shipping_zones.index')->with('success', 'Vendor Shipping Zone created successfully.');
    }

    public function show(VendorShippingZone $vendorShippingZone)
    {
        $vendorShippingZone->load('vendor');
        return view('admin.vendor_shipping_zones.show', compact('vendorShippingZone'));
    }

    public function edit(VendorShippingZone $vendorShippingZone)
    {
        $vendors = Vendor::all();
        return view('admin.vendor_shipping_zones.edit', compact('vendorShippingZone', 'vendors'));
    }

    public function update(Request $request, VendorShippingZone $vendorShippingZone)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'name' => 'required|string|max:255',
            'countries' => 'required|array',
            'states' => 'nullable|array',
            'zip_codes' => 'nullable|array',
            'shipping_method' => 'required|in:flat_rate,free_shipping,local_pickup,by_weight,by_price',
            'rate' => 'nullable|decimal:0,4',
            'minimum_order' => 'nullable|decimal:0,4',
            'is_active' => 'boolean'
        ]);

        $vendorShippingZone->update($validated);

        return redirect()->route('admin.vendor_shipping_zones.index')->with('success', 'Vendor Shipping Zone updated successfully.');
    }

    public function destroy(VendorShippingZone $vendorShippingZone)
    {
        $vendorShippingZone->delete();

        return redirect()->route('admin.vendor_shipping_zones.index')->with('success', 'Vendor Shipping Zone deleted successfully.');
    }
}