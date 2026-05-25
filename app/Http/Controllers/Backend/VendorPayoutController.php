<?php

namespace App\Http\Controllers\Backend;

use App\Models\Vendor;
use App\Models\VendorPayout;
use Illuminate\Http\Request;

class VendorPayoutController extends BaseController
{
    protected string $resource = 'vendor_payout';

    protected array $additionalPermissions = ['vendor_management_access'];

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for payout management methods
        $this->applyMethodPermission('vendor_payout_edit', ['approve', 'complete']);
    }

    public function index()
    {
        $vendorPayouts = VendorPayout::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-payouts.index', compact('vendorPayouts'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)
            ->where('balance', '>', 0)
            ->get();

        return view('admin.vendor-payouts.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:bank_transfer,paypal,stripe,manual',
            'note' => 'nullable|string',
        ]);

        $vendor = Vendor::find($request->vendor_id);

        if ($request->amount > $vendor->balance) {
            return redirect()->back()
                ->with('error', 'Payout amount cannot exceed vendor balance.')
                ->withInput();
        }

        VendorPayout::create($request->all());

        return redirect()->route('admin.vendor-payouts.index')
            ->with('success', 'Vendor payout created successfully.');
    }

    public function show(VendorPayout $vendorPayout)
    {
        $vendorPayout->load('vendor');

        return view('admin.vendor-payouts.show', compact('vendorPayout'));
    }

    public function edit(VendorPayout $vendorPayout)
    {
        if (in_array($vendorPayout->status, ['completed', 'failed'])) {
            return redirect()->back()
                ->with('error', 'Cannot edit completed or failed payouts.');
        }

        return view('admin.vendor-payouts.edit', compact('vendorPayout'));
    }

    public function update(Request $request, VendorPayout $vendorPayout)
    {
        if (in_array($vendorPayout->status, ['completed', 'failed'])) {
            return redirect()->back()
                ->with('error', 'Cannot edit completed or failed payouts.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:bank_transfer,paypal,stripe,manual',
            'status' => 'required|in:pending,processing,completed,failed,canceled',
            'reference_number' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->status === 'completed' && $vendorPayout->status !== 'completed') {
            $data['paid_at'] = now();
        }

        $vendorPayout->update($data);

        return redirect()->route('admin.vendor-payouts.index')
            ->with('success', 'Vendor payout updated successfully.');
    }

    public function destroy(VendorPayout $vendorPayout)
    {
        if ($vendorPayout->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot delete completed payouts.');
        }

        $vendorPayout->delete();

        return redirect()->route('admin.vendor-payouts.index')
            ->with('success', 'Vendor payout deleted successfully.');
    }

    public function approve(VendorPayout $vendorPayout)
    {
        if ($vendorPayout->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending payouts can be approved.');
        }

        $vendorPayout->update([
            'status' => 'processing',
        ]);

        return redirect()->back()
            ->with('success', 'Payout approved successfully.');
    }

    public function complete(VendorPayout $vendorPayout)
    {
        if (! in_array($vendorPayout->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', 'Only pending or processing payouts can be completed.');
        }

        $vendor = $vendorPayout->vendor;
        $vendor->decrement('balance', $vendorPayout->amount);

        $vendorPayout->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Payout completed successfully.');
    }

    public function completed()
    {
        $vendorPayouts = VendorPayout::with('vendor')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-payouts.index', compact('vendorPayouts'));
    }

    public function markPaid(VendorPayout $vendorPayout)
    {
        if ($vendorPayout->status === 'completed') {
            return redirect()->back()->with('error', 'This payout is already completed.');
        }

        $vendorPayout->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Payout marked as paid successfully.');
    }

    public function pending()
    {
        $vendorPayouts = VendorPayout::with('vendor')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-payouts.index', compact('vendorPayouts'));
    }

    public function reject(VendorPayout $vendorPayout)
    {
        $vendorPayout->update(['status' => 'failed']);

        return redirect()->back()->with('success', 'Vendor payout rejected successfully.');
    }
}
