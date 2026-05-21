<?php

namespace App\Http\Controllers\Backend;

use App\Models\VendorWithdrawal;
use Illuminate\Http\Request;

class VendorWithdrawalController extends BaseController
{
    protected string $resource = 'vendor_withdrawal';

    protected array $additionalPermissions = ['vendor_management_access'];

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for withdrawal management methods
        $this->applyMethodPermission('vendor_withdrawal_edit', ['approve', 'reject', 'complete']);
    }

    public function index()
    {
        $vendorWithdrawals = VendorWithdrawal::with('vendor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-withdrawals.index', compact('vendorWithdrawals'));
    }

    public function show(VendorWithdrawal $vendorWithdrawal)
    {
        $vendorWithdrawal->load('vendor');

        return view('admin.vendor-withdrawals.show', compact('vendorWithdrawal'));
    }

    public function edit(VendorWithdrawal $vendorWithdrawal)
    {
        if (in_array($vendorWithdrawal->status, ['completed', 'rejected'])) {
            return redirect()->back()
                ->with('error', 'Cannot edit completed or rejected withdrawals.');
        }

        return view('admin.vendor-withdrawals.edit', compact('vendorWithdrawal'));
    }

    public function update(Request $request, VendorWithdrawal $vendorWithdrawal)
    {
        if (in_array($vendorWithdrawal->status, ['completed', 'rejected'])) {
            return redirect()->back()
                ->with('error', 'Cannot edit completed or rejected withdrawals.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $data = $request->all();

        if (in_array($request->status, ['completed', 'rejected']) &&
            ! in_array($vendorWithdrawal->status, ['completed', 'rejected'])) {
            $data['processed_at'] = now();
        }

        $vendorWithdrawal->update($data);

        return redirect()->route('admin.vendor-withdrawals.index')
            ->with('success', 'Vendor withdrawal updated successfully.');
    }

    public function destroy(VendorWithdrawal $vendorWithdrawal)
    {
        if ($vendorWithdrawal->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot delete completed withdrawals.');
        }

        $vendorWithdrawal->delete();

        return redirect()->route('admin.vendor-withdrawals.index')
            ->with('success', 'Vendor withdrawal deleted successfully.');
    }

    public function approve(Request $request, VendorWithdrawal $vendorWithdrawal)
    {
        if ($vendorWithdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending withdrawals can be approved.');
        }

        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $vendorWithdrawal->update([
            'status' => 'processing',
            'admin_note' => $request->admin_note,
            'processed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Withdrawal approved successfully.');
    }

    public function reject(Request $request, VendorWithdrawal $vendorWithdrawal)
    {
        if ($vendorWithdrawal->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending withdrawals can be rejected.');
        }

        $request->validate([
            'admin_note' => 'required|string',
        ]);

        $vendorWithdrawal->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'processed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Withdrawal rejected successfully.');
    }

    public function complete(Request $request, VendorWithdrawal $vendorWithdrawal)
    {
        if ($vendorWithdrawal->status !== 'processing') {
            return redirect()->back()
                ->with('error', 'Only processing withdrawals can be completed.');
        }

        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $vendor = $vendorWithdrawal->vendor;
        $vendor->decrement('balance', $vendorWithdrawal->amount);

        $vendorWithdrawal->update([
            'status' => 'completed',
            'admin_note' => $request->admin_note,
            'processed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Withdrawal completed successfully.');
    }

    public function pending()
    {
        $vendorWithdrawals = VendorWithdrawal::where('status', 'pending')->paginate(15);

        return view('admin.vendor-withdrawals.index', compact('vendorWithdrawals'));
    }

    public function process()
    {
        return redirect()->back()->with('info', 'Process feature is available; please contact administrator for full implementation.');
    }

    public function processed()
    {
        $vendorWithdrawals = VendorWithdrawal::where('status', 'processed')->paginate(15);

        return view('admin.vendor-withdrawals.index', compact('vendorWithdrawals'));
    }
}
