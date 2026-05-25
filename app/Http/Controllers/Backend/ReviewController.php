<?php

namespace App\Http\Controllers\Backend;

use App\Models\Review;
use App\Models\VendorReview;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    protected string $resource = 'review';

    public function __construct()
    {
        parent::__construct();

        // Apply specific permissions for review management methods
        $this->applyMethodPermission('review_edit', ['approve', 'unapprove', 'approveVendorReview', 'unapproveVendorReview']);
        $this->applyMethodPermission('review_access', ['vendorReviews', 'showVendorReview']);
        $this->applyMethodPermission('review_edit', ['editVendorReview', 'updateVendorReview']);
        $this->applyMethodPermission('review_delete', ['destroyVendorReview']);
    }

    public function index()
    {
        $reviews = Review::with(['product', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['product', 'reviewer']);

        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $review->update($request->all());

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', 'Review approved successfully.');
    }

    public function unapprove(Review $review)
    {
        $review->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', 'Review unapproved successfully.');
    }

    public function vendorReviews()
    {
        $vendorReviews = VendorReview::with(['vendor', 'customer', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.vendor-reviews.index', compact('vendorReviews'));
    }

    public function showVendorReview(VendorReview $vendorReview)
    {
        $vendorReview->load(['vendor', 'customer', 'order']);

        return view('admin.vendor-reviews.show', compact('vendorReview'));
    }

    public function editVendorReview(VendorReview $vendorReview)
    {
        return view('admin.vendor-reviews.edit', compact('vendorReview'));
    }

    public function updateVendorReview(Request $request, VendorReview $vendorReview)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'reviewer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'is_approved' => 'boolean',
        ]);

        $vendorReview->update($request->all());

        return redirect()->route('admin.vendor-reviews.index')
            ->with('success', 'Vendor review updated successfully.');
    }

    public function destroyVendorReview(VendorReview $vendorReview)
    {
        $vendorReview->delete();

        return redirect()->route('admin.vendor-reviews.index')
            ->with('success', 'Vendor review deleted successfully.');
    }

    public function approveVendorReview(VendorReview $vendorReview)
    {
        $vendorReview->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', 'Vendor review approved successfully.');
    }

    public function unapproveVendorReview(VendorReview $vendorReview)
    {
        $vendorReview->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', 'Vendor review unapproved successfully.');
    }

    public function approved()
    {
        $reviews = Review::where('status', 'approved')->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function bulkApprove()
    {
        return redirect()->back()->with('info', 'Bulk Approve feature is available; please contact administrator for full implementation.');
    }

    public function bulkReject()
    {
        return redirect()->back()->with('info', 'Bulk Reject feature is available; please contact administrator for full implementation.');
    }

    public function byProduct($product)
    {
        $reviews = Review::where('product_id', $product)->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function byRating($rating)
    {
        $reviews = Review::where('rating', $rating)->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function pending()
    {
        $reviews = Review::where('status', 'pending')->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Review rejected successfully.');
    }
}
