<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends BaseController
{
    use ImageUploadTrait;

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

            $logo = $brand->logo
                ? '<img src="'.asset('storage/'.$brand->logo->path).'" alt="'.($brand->name ?? 'Brand').'" class="img-thumbnail" style="width:50px; height:50px; object-fit:cover;">'
                : '<div class="text-center" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:5px;"><i class="fas fa-image text-muted"></i></div>';

            $actions = '
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-brand" data-id="'.$brand->id.'">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-brand" data-id="'.$brand->id.'">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-brand" data-id="'.$brand->id.'">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

            $data[] = [
                'id' => $brand->id,
                'logo' => $logo,
                'name' => '<strong>'.($brand->name ?? 'Unnamed Brand').'</strong><br><small class="text-muted">'.$brand->slug.'</small>',
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
            'slug' => 'required|string|unique:brands,slug',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['logo', 'logo_url', 'old_logo']);

        // Handle logo upload - Check both file upload and URL from media selector
        if ($request->hasFile('logo')) {
            // Direct file upload
            $data['logo'] = $this->uploadImage($request, 'logo', 'uploads/brands', 'brand_');
            Log::info('Brand logo uploaded from file: '.$data['logo']);
        } elseif ($request->filled('logo_url')) {
            // Media selector URL - convert full URL to relative path
            $logoUrl = $request->logo_url;
            if (str_contains($logoUrl, '/storage/')) {
                // Extract relative path from full URL
                $data['logo'] = str_replace(url('/storage/'), '', $logoUrl);
            } else {
                // Keep external URLs as-is
                $data['logo'] = $logoUrl;
            }
            Log::info('Brand logo set from URL: '.$data['logo']);
        }

        // Create the brand
        $brand = Brand::create($data);

        // Save translations
        if ($request->has('name')) {
            $brand->setTranslation('name', $request->name, app()->getLocale());
        }
        if ($request->has('description')) {
            $brand->setTranslation('description', $request->description, app()->getLocale());
        }

        if ($request->ajax()) {
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
            return response()->json([
                'success' => true,
                'brand' => $brand,
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
            return response()->json([
                'success' => true,
                'brand' => $brand,
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
            'slug' => 'required|string|unique:brands,slug,'.$brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['logo', 'logo_url', 'old_logo']);

        // Handle logo upload/update - Check both file upload and URL from media selector
        if ($request->hasFile('logo')) {
            // Direct file upload - delete old and upload new
            $data['logo'] = $this->updateImage($request, 'logo', 'uploads/brands', 'brand_', $brand->logo);
            Log::info('Brand logo updated from file: '.$data['logo']);
        } elseif ($request->filled('logo_url')) {
            // Media selector URL - convert full URL to relative path
            $logoUrl = $request->logo_url;
            $relativePath = $logoUrl;

            if (str_contains($logoUrl, '/storage/')) {
                // Extract relative path from full URL
                $relativePath = str_replace(url('/storage/'), '', $logoUrl);
            }

            // Only update if different from current
            if ($relativePath !== $brand->logo) {
                if ($brand->logo && ! str_starts_with($brand->logo, 'http')) {
                    // Delete old local file if switching to different file
                    $this->deleteImage($brand->logo);
                }
                $data['logo'] = $relativePath;
            }
        } elseif ($request->filled('old_logo') && empty($request->logo_url)) {
            // Media selector cleared - delete logo
            if ($brand->logo && ! str_starts_with($brand->logo, 'http')) {
                $this->deleteImage($brand->logo);
            }
            $data['logo'] = null;
        }

        $brand->update($data);

        // Update translations
        if ($request->has('name')) {
            $brand->setTranslation('name', $request->name, app()->getLocale());
        }
        if ($request->has('description')) {
            $brand->setTranslation('description', $request->description, app()->getLocale());
        }

        if ($request->ajax()) {
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
        // Delete logo if exists
        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

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
                $query->whereNotNull('logo');
                break;
            case 'without_logo':
                $query->whereNull('logo');
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
