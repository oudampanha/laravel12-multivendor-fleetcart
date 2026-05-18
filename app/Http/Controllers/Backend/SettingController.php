<?php

namespace App\Http\Controllers\Backend;

use App\Models\Setting;
use App\Models\Vendor;
use App\Models\VendorSetting;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    protected string $resource = 'setting';

    protected array $additionalPermissions = ['system_settings_access'];

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for vendor settings
        $this->applyMethodPermission('vendor_setting_access', ['vendorSettings']);
        $this->applyMethodPermission('vendor_setting_create', ['createVendorSetting', 'storeVendorSetting']);
        $this->applyMethodPermission('vendor_setting_edit', ['editVendorSetting', 'updateVendorSetting']);
        $this->applyMethodPermission('vendor_setting_delete', ['destroyVendorSetting']);
    }

    public function index()
    {
        $settings = Setting::paginate(15);

        return view('admin.settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key',
            'is_translatable' => 'boolean',
            'plain_value' => 'nullable|string',
        ]);

        Setting::create($request->all());

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    public function show(Setting $setting)
    {
        return view('admin.settings.show', compact('setting'));
    }

    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|unique:settings,key,'.$setting->id,
            'is_translatable' => 'boolean',
            'plain_value' => 'nullable|string',
        ]);

        $setting->update($request->all());

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    public function vendorSettings()
    {
        $vendorSettings = VendorSetting::with('vendor')->paginate(15);

        return view('admin.vendor-settings.index', compact('vendorSettings'));
    }

    public function createVendorSetting()
    {
        $vendors = Vendor::where('is_active', true)->get();

        return view('admin.vendor_settings.create', compact('vendors'));
    }

    public function storeVendorSetting(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        VendorSetting::updateOrCreate(
            ['vendor_id' => $request->vendor_id, 'key' => $request->key],
            ['value' => $request->value]
        );

        return redirect()->route('admin.vendor-settings.index')
            ->with('success', 'Vendor setting created successfully.');
    }

    public function editVendorSetting(VendorSetting $vendorSetting)
    {
        $vendors = Vendor::where('is_active', true)->get();

        return view('admin.vendor_settings.edit', compact('vendorSetting', 'vendors'));
    }

    public function updateVendorSetting(Request $request, VendorSetting $vendorSetting)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $vendorSetting->update($request->all());

        return redirect()->route('admin.vendor-settings.index')
            ->with('success', 'Vendor setting updated successfully.');
    }

    public function destroyVendorSetting(VendorSetting $vendorSetting)
    {
        $vendorSetting->delete();

        return redirect()->route('admin.vendor-settings.index')
            ->with('success', 'Vendor setting deleted successfully.');
    }

    public function analytics()
    {
        return redirect()->back()->with('info', 'Analytics feature is available; please contact administrator for full implementation.');
    }

    public function clearCache()
    {
        return redirect()->back()->with('info', 'Clear Cache feature is available; please contact administrator for full implementation.');
    }

    public function general()
    {
        return redirect()->back()->with('info', 'General feature is available; please contact administrator for full implementation.');
    }

    public function mail()
    {
        return redirect()->back()->with('info', 'Mail feature is available; please contact administrator for full implementation.');
    }

    public function payment()
    {
        return redirect()->back()->with('info', 'Payment feature is available; please contact administrator for full implementation.');
    }

    public function seo()
    {
        return redirect()->back()->with('info', 'Seo feature is available; please contact administrator for full implementation.');
    }

    public function shipping()
    {
        return redirect()->back()->with('info', 'Shipping feature is available; please contact administrator for full implementation.');
    }

    public function social()
    {
        return redirect()->back()->with('info', 'Social feature is available; please contact administrator for full implementation.');
    }

    public function tax()
    {
        return redirect()->back()->with('info', 'Tax feature is available; please contact administrator for full implementation.');
    }

    public function testMail()
    {
        return redirect()->back()->with('info', 'Test Mail feature is available; please contact administrator for full implementation.');
    }
}
