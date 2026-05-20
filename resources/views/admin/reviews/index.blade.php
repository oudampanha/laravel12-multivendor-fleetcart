@extends('admin.layouts.master_layout')

@section('pageTitle', 'Reviews Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Reviews Management</h4>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-2">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="approved">Approved</option>
              <option value="pending">Pending</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" id="ratingFilter">
              <option value="">All Ratings</option>
              <option value="5">5 Stars</option>
              <option value="4">4 Stars</option>
              <option value="3">3 Stars</option>
              <option value="2">2 Stars</option>
              <option value="1">1 Star</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" id="typeFilter">
              <option value="">All Types</option>
              <option value="product">Product Reviews</option>
              <option value="vendor">Vendor Reviews</option>
            </select>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="reviewsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Subject</th>
                <th>Reviewer</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($reviews as $review)
              <tr>
                <td>{{ $review->id }}</td>
                <td>
                  @if(isset($review->product_id))
                    <span class="badge badge-primary">Product</span>
                  @elseif(isset($review->vendor_id))
                    <span class="badge badge-info">Vendor</span>
                  @else
                    <span class="badge badge-secondary">Other</span>
                  @endif
                </td>
                <td>
                  @if(isset($review->product))
                    <a href="{{ route('admin.products.show', $review->product->id) }}">
                      {{ $review->product->name ?? 'Product' }}
                    </a>
                  @elseif(isset($review->vendor))
                    <a href="{{ route('admin.vendors.show', $review->vendor->id) }}">
                      {{ $review->vendor->store_slug }}
                    </a>
                  @else
                    N/A
                  @endif
                </td>
                <td>
                  <strong>{{ $review->reviewer_name }}</strong>
                  @if($review->reviewer)
                    <br><small class="text-muted">{{ $review->reviewer->email }}</small>
                  @endif
                </td>
                <td>
                  <div class="rating">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= $review->rating)
                        <i class="fas fa-star text-warning"></i>
                      @else
                        <i class="fas fa-star text-muted"></i>
                      @endif
                    @endfor
                    <span class="ml-1">({{ $review->rating }}/5)</span>
                  </div>
                </td>
                <td>
                  <div class="review-comment" style="max-width: 200px;">
                    {{ Str::limit($review->comment, 100) }}
                    @if(strlen($review->comment) > 100)
                      <br><a href="#" onclick="showFullComment({{ $review->id }})">Read more...</a>
                    @endif
                  </div>
                </td>
                <td>
                  @if($review->is_approved)
                    <span class="badge badge-success">Approved</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  {{ $review->created_at->format('M d, Y') }}<br>
                  <small class="text-muted">{{ $review->created_at->format('h:i A') }}</small>
                </td>
                <td>
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-info" onclick="showReview({{ $review->id }})">
                      <i class="fas fa-eye"></i>
                    </button>
                    @if(!$review->is_approved)
                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this review?')">
                        <i class="fas fa-check"></i>
                      </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($reviews, 'links'))
          <div class="d-flex justify-content-center">
            {{ $reviews->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Review Details Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Review Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="reviewContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
  const table = $('#reviewsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Status filter
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    if (status === 'approved') {
      table.column(6).search('Approved').draw();
    } else if (status === 'pending') {
      table.column(6).search('Pending').draw();
    } else {
      table.column(6).search('').draw();
    }
  });
  
  // Rating filter
  $('#ratingFilter').on('change', function() {
    const rating = $(this).val();
    table.column(4).search(rating).draw();
  });
  
  // Type filter
  $('#typeFilter').on('change', function() {
    const type = $(this).val();
    if (type === 'product') {
      table.column(1).search('Product').draw();
    } else if (type === 'vendor') {
      table.column(1).search('Vendor').draw();
    } else {
      table.column(1).search('').draw();
    }
  });
});

function showReview(reviewId) {
  $('#reviewModal').modal('show');
  $('#reviewContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
  
  // Load review details via AJAX
  $.get(`{{ route('admin.reviews.show', '') }}/${reviewId}`, function(data) {
    $('#reviewContent').html(data);
  }).fail(function() {
    $('#reviewContent').html('<div class="alert alert-danger">Failed to load review details.</div>');
  });
}

function showFullComment(reviewId) {
  // Implementation for showing full comment
  alert('Show full comment functionality to be implemented');
}
</script>
@endpush