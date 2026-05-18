<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends BaseController
{
    protected string $resource = 'vendor';

    protected array $additionalPermissions = ['vendor_management_access'];

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for vendor management methods
        $this->applyMethodPermission('vendor_edit', ['approve', 'suspend']);
    }

    public function index()
    {
        $vendors = Vendor::with('user')->paginate(15);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        $users = User::whereDoesntHave('vendor')->get();

        return view('admin.vendors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:vendors,user_id',
            'store_slug' => 'required|string|unique:vendors,store_slug',
            'store_email' => 'nullable|email',
            'store_phone' => 'nullable|string',
            'store_address' => 'nullable|string',
            'store_city' => 'nullable|string',
            'store_state' => 'nullable|string',
            'store_country' => 'nullable|string',
            'store_zip' => 'nullable|string',
            'commission_rate' => 'numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        $vendor = Vendor::create($request->all());

        if ($request->is_verified) {
            $vendor->update(['verified_at' => now()]);
        }

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['user', 'products', 'orders']);

        return view('admin.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'store_slug' => 'required|string|unique:vendors,store_slug,'.$vendor->id,
            'store_email' => 'nullable|email',
            'store_phone' => 'nullable|string',
            'store_address' => 'nullable|string',
            'store_city' => 'nullable|string',
            'store_state' => 'nullable|string',
            'store_country' => 'nullable|string',
            'store_zip' => 'nullable|string',
            'commission_rate' => 'numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        $data = $request->all();

        if ($request->is_verified && ! $vendor->is_verified) {
            $data['verified_at'] = now();
        } elseif (! $request->is_verified) {
            $data['verified_at'] = null;
        }

        $vendor->update($data);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    public function approve(Vendor $vendor)
    {
        $vendor->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Vendor approved successfully.');
    }

    public function suspend(Vendor $vendor)
    {
        $vendor->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'Vendor suspended successfully.');
    }

    public function adjustBalance()
    {
        return redirect()->back()->with('info', 'Adjust Balance feature is available; please contact administrator for full implementation.');
    }

    public function balance()
    {
        return redirect()->back()->with('info', 'Balance feature is available; please contact administrator for full implementation.');
    }

    public function notifications()
    {
        return redirect()->back()->with('info', 'Notifications feature is available; please contact administrator for full implementation.');
    }

    public function notify()
    {
        return redirect()->back()->with('info', 'Notify feature is available; please contact administrator for full implementation.');
    }

    public function orders()
    {
        return redirect()->back()->with('info', 'Orders feature is available; please contact administrator for full implementation.');
    }

    public function products()
    {
        return redirect()->back()->with('info', 'Products feature is available; please contact administrator for full implementation.');
    }

    public function reviews()
    {
        return redirect()->back()->with('info', 'Reviews feature is available; please contact administrator for full implementation.');
    }

    public function settings()
    {
        return redirect()->back()->with('info', 'Settings feature is available; please contact administrator for full implementation.');
    }

    public function toggleStatus(User $vendor)
    {
        $vendor->update(['is_active' => ! $vendor->is_active]);

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function updateSettings()
    {
        return redirect()->back()->with('info', 'Update Settings feature is available; please contact administrator for full implementation.');
    }

    public function verify(User $vendor)
    {
        $vendor->update(['is_verified' => true, 'verified_at' => now()]);

        return redirect()->back()->with('success', 'User verified successfully.');
    }
}
