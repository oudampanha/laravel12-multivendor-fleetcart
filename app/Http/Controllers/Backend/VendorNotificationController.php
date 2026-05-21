<?php

namespace App\Http\Controllers\Backend;

use App\Models\Vendor;
use App\Models\VendorNotification;
use Illuminate\Http\Request;

class VendorNotificationController extends BaseController
{
    protected string $resource = 'vendor_notification';

    protected array $additionalPermissions = ['vendor_notification_management_access'];

    public function index()
    {
        $vendorNotifications = VendorNotification::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor_notifications.index', compact('vendorNotifications'));
    }

    public function create()
    {
        $vendors = Vendor::all();

        return view('admin.vendor-notifications.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'is_read' => 'boolean',
            'read_at' => 'nullable|date',
        ]);

        VendorNotification::create($validated);

        return redirect()->route('admin.vendor-notifications.index')->with('success', 'Vendor Notification created successfully.');
    }

    public function show(VendorNotification $vendorNotification)
    {
        $vendorNotification->load('vendor');

        return view('admin.vendor-notifications.show', compact('vendorNotification'));
    }

    public function edit(VendorNotification $vendorNotification)
    {
        $vendors = Vendor::all();

        return view('admin.vendor-notifications.edit', compact('vendorNotification', 'vendors'));
    }

    public function update(Request $request, VendorNotification $vendorNotification)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'nullable|array',
            'is_read' => 'boolean',
            'read_at' => 'nullable|date',
        ]);

        $vendorNotification->update($validated);

        return redirect()->route('admin.vendor-notifications.index')->with('success', 'Vendor Notification updated successfully.');
    }

    public function destroy(VendorNotification $vendorNotification)
    {
        $vendorNotification->delete();

        return redirect()->route('admin.vendor-notifications.index')->with('success', 'Vendor Notification deleted successfully.');
    }

    public function markAsRead(VendorNotification $vendorNotification)
    {
        $vendorNotification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->route('admin.vendor-notifications.index')->with('success', 'Vendor Notification marked as read.');
    }

    public function markAsUnread(VendorNotification $vendorNotification)
    {
        $vendorNotification->update([
            'is_read' => false,
            'read_at' => null,
        ]);

        return redirect()->route('admin.vendor-notifications.index')->with('success', 'Vendor Notification marked as unread.');
    }

    public function byVendor($vendor)
    {
        $vendorNotifications = Vendor::where('vendor_id', $vendor)->paginate(15);

        return view('admin.vendor_notifications.index', compact('vendorNotifications'));
    }

    public function markAllRead(Vendor $vendorNotification)
    {
        $vendorNotification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Marked successfully.');
    }

    public function markRead(Vendor $vendorNotification)
    {
        $vendorNotification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Marked successfully.');
    }
}
