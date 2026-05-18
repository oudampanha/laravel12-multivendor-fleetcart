<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends BaseController
{

  protected string $resource = 'brand';

  protected array $additionalPermissions = ['brand_management_access'];

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      return $this->getDataTableData($request);
    }

    return view('admin.brands.index');
  }

  /**
   * Get data for DataTables Ajax
   */
  private function getDataTableData(Request $request)
  {
    $query = Brand::query();

    // Handle global search
    if ($request->has('search') && $request->search['value']) {
      $search = $request->search['value'];
      $query->where(function ($q) use ($search) {
        $q->where('slug', 'like', "%{$search}%")
          ->orWhereHas('translations', function ($tq) use ($search) {
            $tq->where('value', 'like', "%{$search}%")
              ->where('key', 'name');
          });
      });
    }

    // Handle column-specific filters
    if ($request->has('columns')) {
      foreach ($request->columns as $index => $column) {
        if (! empty($column['search']['value'])) {
          $searchValue = $column['search']['value'];

          switch ($index) {
            case 3: // Status column
              if ($searchValue === 'Active') {
                $query->where('is_active', 1);
              } elseif ($searchValue === 'Inactive') {
                $query->where('is_active', 0);
              }
              break;
          }
        }
      }
    }

    // Handle column ordering
    if ($request->has('order')) {
      $columns = ['id', 'logo', 'name', 'slug', 'is_active', 'created_at'];
      $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
      $orderDirection = $request->order[0]['dir'] ?? 'desc';

      // Handle special columns that can't be ordered directly
      if (! in_array($orderColumn, ['logo'])) {
        $query->orderBy($orderColumn, $orderDirection);
      } else {
        $query->orderBy('created_at', 'desc'); // Default fallback
      }
    } else {
      $query->orderBy('created_at', 'desc');
    }

    $totalRecords = Brand::count();
    $filteredRecords = $query->count();

    // Handle pagination
    $start = $request->start ?? 0;
    $length = $request->length ?? 10;
    $brands = $query->skip($start)->take($length)->get();

    $data = [];
    foreach ($brands as $brand) {
      $status = $brand->is_active
        ? '<span class="badge badge-success">Active</span>'
        : '<span class="badge badge-danger">Inactive</span>';

      $logoMedia = $brand->getFirstMediaByZone('logo');
      $logo = $logoMedia
        ? '<img src="' . $logoMedia->full_url . '" alt="' . ($brand->getTranslation('name') ?? 'Brand') . '" class="img-thumbnail" style="width:50px; height:50px; object-fit:cover;">'
        : '<div class="text-center" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:5px;"><i class="fas fa-image text-muted"></i></div>';

      $actions = '
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-brand" data-id="' . $brand->id . '">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-brand" data-id="' . $brand->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-brand" data-id="' . $brand->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

      $brandName = $brand->getTranslation('name') ?? 'Unnamed Brand';

      $data[] = [
        'id' => $brand->id,
        'logo' => $logo,
        'name' => '<strong>' . $brandName . '</strong><br><small class="text-muted">' . $brand->slug . '</small>',
        'slug' => $brand->slug,
        'status' => $status,
        'created_at' => $brand->created_at ? $brand->created_at->format('Y-m-d H:i') : '-',
        'actions' => $actions,
      ];
    }

    return response()->json([
      'draw' => intval($request->draw),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $filteredRecords,
      'data' => $data,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('admin.brands.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'slug' => 'nullable|string|unique:brands,slug',
      'logo' => 'nullable|integer|exists:media,id',
      'banner' => 'nullable|integer|exists:media,id',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'banner_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'is_active' => 'boolean',
      'meta_title' => 'nullable|string|max:255',
      'meta_description' => 'nullable|string',
      'meta_keywords' => 'nullable|string',
    ]);

    // Generate slug if not provided
    $slug = $request->slug ?: \Illuminate\Support\Str::slug($request->name);
    $originalSlug = $slug;
    $counter = 1;
    while (Brand::where('slug', $slug)->exists()) {
      $slug = $originalSlug . '-' . $counter;
      $counter++;
    }

    $data = [
      'slug' => $slug,
      'is_active' => $request->has('is_active') ? 1 : 0,
    ];

    // Create the brand
    $brand = Brand::create($data);

    // Save translations
    if ($request->filled('name')) {
      $brand->setTranslation('name', $request->name);
    }
    if ($request->filled('description')) {
      $brand->setTranslation('description', $request->description);
    }

    // Save metadata
    if ($request->filled('meta_title')) {
      $brand->setMeta('meta_title', $request->meta_title);
    }
    if ($request->filled('meta_description')) {
      $brand->setMeta('meta_description', $request->meta_description);
    }
    if ($request->filled('meta_keywords')) {
      $brand->setMeta('meta_keywords', $request->meta_keywords);
    }

    // Handle logo media using HasMedia trait
    if ($request->filled('logo') && is_numeric($request->logo)) {
      $brand->setMediaForZone($request->logo, 'logo');
      Log::info('Brand logo attached via media ID: ' . $request->logo);
    }
    if ($request->filled('banner') && is_numeric($request->banner)) {
      $brand->setMediaForZone($request->banner, 'banner');
      Log::info('Brand banner attached via media ID: ' . $request->banner);
    }

    if ($request->ajax()) {
      $brand->name = $brand->getTranslation('name');

      return response()->json([
        'success' => true,
        'message' => '🎉 Brand created successfully!',
        'title' => 'Success',
        'type' => 'success',
        'brand' => $brand,
      ]);
    }

    sweetalert()->success('Brand created successfully!');

    return redirect()->route('admin.brands.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Brand $brand, Request $request)
  {
    $brand->load(['products']);

    if ($request->ajax()) {
      // Include logo, banner and translations for AJAX requests
      $logoMedia = $brand->getFirstMediaByZone('logo');
      $bannerMedia = $brand->getFirstMediaByZone('banner');
      $brandData = $brand->toArray();
      $brandData['name'] = $brand->getTranslation('name');
      $brandData['description'] = $brand->getTranslation('description');
      $brandData['logo'] = $logoMedia ? $logoMedia->full_url : null;
      $brandData['logo_id'] = $logoMedia ? $logoMedia->id : null;
      $brandData['banner'] = $bannerMedia ? $bannerMedia->full_url : null;
      $brandData['banner_id'] = $bannerMedia ? $bannerMedia->id : null;
      $brandData['meta_title'] = $brand->getMeta('meta_title');
      $brandData['meta_description'] = $brand->getMeta('meta_description');
      $brandData['meta_keywords'] = $brand->getMeta('meta_keywords');

      return response()->json([
        'success' => true,
        'brand' => $brandData,
      ]);
    }

    return view('admin.brands.show', compact('brand'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Brand $brand, Request $request)
  {
    if ($request->ajax()) {
      // Include logo media info and translations for editing
      $logoMedia = $brand->getFirstMediaByZone('logo');
      $bannerMedia = $brand->getFirstMediaByZone('banner');
      $brandData = $brand->toArray();
      $brandData['name'] = $brand->getTranslation('name');
      $brandData['description'] = $brand->getTranslation('description');
      $brandData['logo'] = $logoMedia ? $logoMedia->full_url : null;
      $brandData['logo_id'] = $logoMedia ? $logoMedia->id : null;
      $brandData['banner'] = $bannerMedia ? $bannerMedia->full_url : null;
      $brandData['banner_id'] = $bannerMedia ? $bannerMedia->id : null;
      $brandData['meta_title'] = $brand->getMeta('meta_title');
      $brandData['meta_description'] = $brand->getMeta('meta_description');
      $brandData['meta_keywords'] = $brand->getMeta('meta_keywords');

      return response()->json([
        'success' => true,
        'brand' => $brandData,
      ]);
    }

    return view('admin.brands.edit', compact('brand'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Brand $brand)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'slug' => 'nullable|string|unique:brands,slug,' . $brand->id,
      'logo' => 'nullable|integer|exists:media,id',
      'banner' => 'nullable|integer|exists:media,id',
      'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'banner_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      'is_active' => 'boolean',
      'meta_title' => 'nullable|string|max:255',
      'meta_description' => 'nullable|string',
      'meta_keywords' => 'nullable|string',
    ]);

    // Generate slug if not provided or if name changed
    $slug = $request->slug ?: \Illuminate\Support\Str::slug($request->name);
    $originalSlug = $slug;
    $counter = 1;
    while (Brand::where('slug', $slug)->where('id', '!=', $brand->id)->exists()) {
      $slug = $originalSlug . '-' . $counter;
      $counter++;
    }

    $data = [
      'slug' => $slug,
      'is_active' => $request->has('is_active') ? 1 : 0,
    ];

    $brand->update($data);

    // Update translations
    if ($request->filled('name')) {
      $brand->setTranslation('name', $request->name);
    }
    if ($request->filled('description')) {
      $brand->setTranslation('description', $request->description);
    }

    // Update metadata
    if ($request->filled('meta_title')) {
      $brand->setMeta('meta_title', $request->meta_title);
    }
    if ($request->filled('meta_description')) {
      $brand->setMeta('meta_description', $request->meta_description);
    }
    if ($request->filled('meta_keywords')) {
      $brand->setMeta('meta_keywords', $request->meta_keywords);
    }

    // Handle logo media using HasMedia trait
    if ($request->filled('logo') && is_numeric($request->logo)) {
      $brand->setMediaForZone($request->logo, 'logo');
      Log::info('Brand logo updated via media ID: ' . $request->logo);
    } elseif ($request->has('logo') && empty($request->logo)) {
      // Clear logo if empty string
      $brand->clearZone('logo');
      Log::info('Brand logo cleared');
    }
    if ($request->filled('banner') && is_numeric($request->banner)) {
      $brand->setMediaForZone($request->banner, 'banner');
      Log::info('Brand banner updated via media ID: ' . $request->banner);
    } elseif ($request->has('banner') && empty($request->banner)) {
      // Clear banner if empty string
      $brand->clearZone('banner');
      Log::info('Brand banner cleared');
    }

    if ($request->ajax()) {
      $brand->name = $brand->getTranslation('name');

      return response()->json([
        'success' => true,
        'message' => '✅ Brand updated successfully!',
        'title' => 'Updated',
        'type' => 'success',
        'brand' => $brand,
      ]);
    }

    sweetalert()->success('Brand updated successfully!');

    return redirect()->route('admin.brands.index');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Brand $brand, Request $request)
  {
    // Delete the brand - this triggers all trait cleanup hooks
    $brand->delete();

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'message' => '🗑️ Brand deleted successfully!',
        'title' => 'Deleted',
        'type' => 'success',
      ]);
    }

    sweetalert()->success('Brand deleted successfully!');

    return redirect()->route('admin.brands.index');
  }

  /**
   * Toggle brand status
   */
  public function toggleStatus(Brand $brand)
  {
    $brand->update([
      'is_active' => ! $brand->is_active,
    ]);

    $status = $brand->is_active ? 'activated' : 'deactivated';

    return redirect()->back()
      ->with('success', "Brand has been {$status} successfully.");
  }

  /**
   * Get brands by status
   */
  public function byStatus(Request $request)
  {
    $status = $request->get('status', 'all');

    $query = Brand::query();

    switch ($status) {
      case 'active':
        $query->where('is_active', true);
        break;
      case 'inactive':
        $query->where('is_active', false);
        break;
      case 'with_logo':
        $query->whereHas('entityMedia', function ($q) {
          $q->where('zone', 'logo');
        });
        break;
      case 'without_logo':
        $query->whereDoesntHave('entityMedia', function ($q) {
          $q->where('zone', 'logo');
        });
        break;
    }

    $brands = $query->orderBy('created_at', 'desc')->paginate(15);

    return view('admin.brands.index', compact('brands', 'status'));
  }

  /**
   * Search brands
   */
  public function search(Request $request)
  {
    $query = $request->get('q');

    $brands = Brand::where(function ($q) use ($query) {
      $q->where('slug', 'like', "%{$query}%")
        ->orWhereHas('translations', function ($tq) use ($query) {
          $tq->where('value', 'like', "%{$query}%")
            ->where('key', 'name');
        });
    })->paginate(15);

    return view('admin.brands.index', compact('brands', 'query'));
  }

  /**
   * Get products by brand
   */
  public function products(Brand $brand)
  {
    $products = $brand->products()->paginate(15);

    return view('admin.brands.products', compact('brand', 'products'));
  }
}
