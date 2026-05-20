<?php

namespace App\Http\Controllers\Backend;

use App\Models\Vendor;
use App\Models\VendorSetting;
use Illuminate\Http\Request;

class VendorSettingController extends BaseController
{
    protected string $resource = 'vendor_setting';

    protected array $additionalPermissions = ['vendor_setting_management_access'];

    public function index()
    {
        $vendorSettings = VendorSetting::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor_settings.index', compact('vendorSettings'));
    }

    public function create()
    {
        $vendors = Vendor::all();

        return view('admin.vendor_settings.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        VendorSetting::create($validated);

        return redirect()->route('admin.vendor-settings.index')->with('success', 'Vendor Setting created successfully.');
    }

    public function show(VendorSetting $vendorSetting)
    {
        $vendorSetting->load('vendor');

        return view('admin.vendor_settings.show', compact('vendorSetting'));
    }

    public function edit(VendorSetting $vendorSetting)
    {
        $vendors = Vendor::all();

        return view('admin.vendor_settings.edit', compact('vendorSetting', 'vendors'));
    }

    public function update(Request $request, VendorSetting $vendorSetting)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $vendorSetting->update($validated);

        return redirect()->route('admin.vendor-settings.index')->with('success', 'Vendor Setting updated successfully.');
    }

    public function destroy(VendorSetting $vendorSetting)
    {
        $vendorSetting->delete();

        return redirect()->route('admin.vendor-settings.index')->with('success', 'Vendor Setting deleted successfully.');
    }
}
