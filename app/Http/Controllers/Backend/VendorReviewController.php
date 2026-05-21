<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorReview;
use Illuminate\Http\Request;

class VendorReviewController extends BaseController
{
    protected string $resource = 'vendor_review';

    protected array $additionalPermissions = ['vendor_review_management_access'];

    public function index()
    {
        $vendorReviews = VendorReview::with(['vendor', 'customer', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor_reviews.index', compact('vendorReviews'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $customers = User::all();
        $orders = Order::all();

        return view('admin.vendor-reviews.create', compact('vendors', 'customers', 'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'customer_id' => 'nullable|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        VendorReview::create($validated);

        return redirect()->route('admin.vendor-reviews.index')->with('success', 'Vendor Review created successfully.');
    }

    public function show(VendorReview $vendorReview)
    {
        $vendorReview->load(['vendor', 'customer', 'order']);

        return view('admin.vendor-reviews.show', compact('vendorReview'));
    }

    public function edit(VendorReview $vendorReview)
    {
        $vendors = Vendor::all();
        $customers = User::all();
        $orders = Order::all();

        return view('admin.vendor-reviews.edit', compact('vendorReview', 'vendors', 'customers', 'orders'));
    }

    public function update(Request $request, VendorReview $vendorReview)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'customer_id' => 'nullable|exists:users,id',
            'order_id' => 'nullable|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $vendorReview->update($validated);

        return redirect()->route('admin.vendor-reviews.index')->with('success', 'Vendor Review updated successfully.');
    }

    public function destroy(VendorReview $vendorReview)
    {
        $vendorReview->delete();

        return redirect()->route('admin.vendor-reviews.index')->with('success', 'Vendor Review deleted successfully.');
    }

    public function approve(VendorReview $vendorReview)
    {
        $vendorReview->update(['is_approved' => true]);

        return redirect()->route('admin.vendor-reviews.index')->with('success', 'Vendor Review approved successfully.');
    }

    public function reject(VendorReview $vendorReview)
    {
        $vendorReview->update(['is_approved' => false]);

        return redirect()->route('admin.vendor-reviews.index')->with('success', 'Vendor Review rejected successfully.');
    }

    public function approved()
    {
        $vendorReviews = Order::where('status', 'approved')->paginate(15);

        return view('admin.vendor_reviews.index', compact('vendorReviews'));
    }

    public function byVendor($vendor)
    {
        $vendorReviews = Order::where('vendor_id', $vendor)->paginate(15);

        return view('admin.vendor_reviews.index', compact('vendorReviews'));
    }

    public function pending()
    {
        $vendorReviews = Order::where('status', 'pending')->paginate(15);

        return view('admin.vendor_reviews.index', compact('vendorReviews'));
    }
}
