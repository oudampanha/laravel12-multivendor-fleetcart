<?php

namespace App\Http\Controllers\Backend;

use App\Models\Attribute;
use App\Models\AttributeSet;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeController extends BaseController
{
  protected string $resource = 'attribute';

  protected array $additionalPermissions = ['attribute_management_access'];

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

    return view('admin.attributes.index');
  }

  /**
   * Get categories for Select2 dropdown
   */
  public function getCategories(Request $request)
  {
    $categories = Category::where('is_active', true)
      ->orderBy('position')
      ->orderBy('created_at')
      ->get();

    $formattedCategories = [];
    foreach ($categories as $category) {
      $name = $category->getTranslation('name') ?? 'Unnamed Category';
      $formattedCategories[] = [
        'id' => $category->id,
        'text' => $name,
        'parent_id' => $category->parent_id,
      ];
    }

    return response()->json($formattedCategories);
  }

  /**
   * Get data for DataTables Ajax
   */
  private function getDataTableData(Request $request)
  {
    $query = Attribute::with(['attributeSet', 'attributeValues']);

    // Handle global search using translations
    if ($request->has('search') && $request->search['value']) {
      $search = $request->search['value'];
      $query->where(function ($q) use ($search) {
        $q->whereHas('translations', function ($tq) use ($search) {
          $tq->where('field', 'name')
            ->where('value', 'like', "%{$search}%");
        })->orWhereHas('attributeSet.translations', function ($tq) use ($search) {
          $tq->where('field', 'name')
            ->where('value', 'like', "%{$search}%");
        })->orWhere('slug', 'like', "%{$search}%");
      });
    }

    // Handle column-specific filters
    if ($request->has('columns')) {
      foreach ($request->columns as $index => $column) {
        if (! empty($column['search']['value'])) {
          $searchValue = $column['search']['value'];

          switch ($index) {
            case 3: // Filterable column
              if ($searchValue === 'Yes') {
                $query->where('is_filterable', 1);
              } elseif ($searchValue === 'No') {
                $query->where('is_filterable', 0);
              }
              break;
          }
        }
      }
    }

    // Handle column ordering
    if ($request->has('order')) {
      $columns = ['id', 'name', 'attribute_set', 'is_filterable', 'created_at'];
      $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
      $orderDirection = $request->order[0]['dir'] ?? 'desc';

      // Handle special columns that can't be ordered directly
      if (! in_array($orderColumn, ['name', 'attribute_set'])) {
        $query->orderBy($orderColumn, $orderDirection);
      } else {
        $query->orderBy('created_at', 'desc'); // Default fallback
      }
    } else {
      $query->orderBy('created_at', 'desc');
    }

    $totalRecords = Attribute::count();
    $filteredRecords = $query->count();

    // Handle pagination
    $start = $request->start ?? 0;
    $length = $request->length ?? 10;
    $attributes = $query->skip($start)->take($length)->get();

    $data = [];
    foreach ($attributes as $attribute) {
      $attributeSetName = $attribute->attributeSet ? $attribute->attributeSet->getTranslation('name') : 'N/A';
      $filterable = $attribute->is_filterable
        ? '<span class="badge badge-success">Yes</span>'
        : '<span class="badge badge-secondary">No</span>';

      $actions = '
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-attribute" data-id="' . $attribute->id . '">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-attribute" data-id="' . $attribute->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-attribute" data-id="' . $attribute->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

      $data[] = [
        'id' => $attribute->id,
        'name' => '<strong>' . ($attribute->getTranslation('name') ?? 'Unnamed Attribute') . '</strong>'
          . '<br><small class="text-muted">' . ($attribute->slug ?? 'No slug') . '</small>',
        'attribute_set' => '<span class="badge badge-primary">' . $attributeSetName . '</span>',
        'values_count' => '<span class="badge badge-info">' . $attribute->attributeValues->count() . ' values</span>',
        'is_filterable' => $filterable,
        'created_at' => $attribute->created_at ? $attribute->created_at->format('Y-m-d H:i') : '-',
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
    $attributeSets = AttributeSet::all();
    $categories = Category::all();

    return view('admin.attributes.create', compact('attributeSets', 'categories'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'attribute_set_id' => 'required|exists:attribute_sets,id',
      'slug' => 'nullable|string|unique:attributes,slug',
      'is_filterable' => 'boolean',
      'categories' => 'nullable|array',
      'categories.*' => 'exists:categories,id',
      'values' => 'nullable|array',
      'values.*' => 'required|string|max:255',
    ]);

    DB::beginTransaction();

    try {
      $data = $request->only(['attribute_set_id', 'slug', 'is_filterable']);

      // Create the attribute
      $attribute = Attribute::create($data);

      // Set the translation
      $attribute->setTranslation('name', $request->name);

      // Sync categories (sync empty array if no categories selected)
      $categories = $request->input('categories', []);
      $attribute->categories()->sync($categories);

      // Create attribute values if provided
      $values = $request->input('values', []);
      if (! empty($values)) {
        foreach ($values as $index => $value) {
          if (! empty(trim($value))) {
            $attributeValue = AttributeValue::create([
              'attribute_id' => $attribute->id,
              'position' => $index + 1,
            ]);

            // Set the translation for the value
            $attributeValue->setTranslation('value', trim($value));
          }
        }
      }

      DB::commit();

      if ($request->ajax()) {
        return response()->json([
          'success' => true,
          'message' => '🎉 Attribute created successfully!',
          'title' => 'Success',
          'type' => 'success',
          'attribute' => $attribute->load(['attributeSet', 'attributeValues']),
        ]);
      }

      sweetalert()->success('Attribute created successfully!');

      return redirect()->route('admin.attributes.index');
    } catch (\Exception $e) {
      DB::rollback();

      Log::error('Error creating attribute: ' . $e->getMessage());

      if ($request->ajax()) {
        return response()->json([
          'success' => false,
          'message' => '❌ Error creating attribute: ' . $e->getMessage(),
          'title' => 'Error',
          'type' => 'error',
        ]);
      }

      sweetalert()->error('Error creating attribute. Please try again.');

      return redirect()->back()->withInput();
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Attribute $attribute, Request $request)
  {
    $attribute->load(['attributeSet', 'attributeValues', 'categories']);

    if ($request->ajax()) {
      // Include translated values and relationships
      $attributeData = $attribute->toArray();
      $attributeData['name'] = $attribute->getTranslation('name');
      $attributeData['attribute_set_name'] = $attribute->attributeSet ? $attribute->attributeSet->getTranslation('name') : null;

      return response()->json([
        'success' => true,
        'attribute' => $attributeData,
      ]);
    }

    return view('admin.attributes.show', compact('attribute'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Attribute $attribute, Request $request)
  {
    $attribute->load(['categories', 'attributeValues']);
    $attributeSets = AttributeSet::all();
    $categories = Category::all();

    if ($request->ajax()) {
      // Include translated values and relationships
      $attributeData = $attribute->toArray();
      $attributeData['name'] = $attribute->getTranslation('name');
      $attributeData['category_ids'] = $attribute->categories->pluck('id')->toArray();

      // Include attribute values with translations
      $attributeData['attribute_values'] = $attribute->attributeValues->map(function ($value) {
        return [
          'id' => $value->id,
          'value' => $value->getTranslation('value'),
          'position' => $value->position,
        ];
      })->sortBy('position')->values()->toArray();

      return response()->json([
        'success' => true,
        'attribute' => $attributeData,
        'attribute_sets' => $attributeSets,
        'categories' => $categories,
      ]);
    }

    return view('admin.attributes.edit', compact('attribute', 'attributeSets', 'categories'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Attribute $attribute)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'attribute_set_id' => 'required|exists:attribute_sets,id',
      'slug' => 'nullable|string|unique:attributes,slug,' . $attribute->id,
      'is_filterable' => 'boolean',
      'categories' => 'nullable|array',
      'categories.*' => 'exists:categories,id',
      'values' => 'nullable|array',
      'values.*' => 'required|string|max:255',
    ]);

    DB::beginTransaction();

    try {
      $data = $request->only(['attribute_set_id', 'slug', 'is_filterable']);

      // Update the attribute
      $attribute->update($data);

      // Update the translation
      $attribute->setTranslation('name', $request->name);

      // Sync categories (sync empty array if no categories selected)
      $categories = $request->input('categories', []);
      $attribute->categories()->sync($categories);

      // Update attribute values
      // First, delete all existing values
      $attribute->attributeValues()->delete();

      // Create new values if provided
      $values = $request->input('values', []);
      if (! empty($values)) {
        foreach ($values as $index => $value) {
          if (! empty(trim($value))) {
            $attributeValue = AttributeValue::create([
              'attribute_id' => $attribute->id,
              'position' => $index + 1,
            ]);

            // Set the translation for the value
            $attributeValue->setTranslation('value', trim($value));
          }
        }
      }

      DB::commit();

      if ($request->ajax()) {
        return response()->json([
          'success' => true,
          'message' => '✅ Attribute updated successfully!',
          'title' => 'Updated',
          'type' => 'success',
          'attribute' => $attribute->load(['attributeSet', 'attributeValues']),
        ]);
      }

      sweetalert()->success('Attribute updated successfully!');

      return redirect()->route('admin.attributes.index');
    } catch (\Exception $e) {
      DB::rollback();

      Log::error('Error updating attribute: ' . $e->getMessage());

      if ($request->ajax()) {
        return response()->json([
          'success' => false,
          'message' => '❌ Error updating attribute: ' . $e->getMessage(),
          'title' => 'Error',
          'type' => 'error',
        ]);
      }

      sweetalert()->error('Error updating attribute. Please try again.');

      return redirect()->back()->withInput();
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Attribute $attribute, Request $request)
  {
    if ($attribute->attributeValues()->count() > 0) {
      if ($request->ajax()) {
        return response()->json([
          'success' => false,
          'message' => '❌ Cannot delete attribute with values.',
          'title' => 'Error',
          'type' => 'error',
        ]);
      }

      sweetalert()->error('Cannot delete attribute with values.');

      return redirect()->back();
    }

    $attribute->delete();

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'message' => '🗑️ Attribute deleted successfully!',
        'title' => 'Deleted',
        'type' => 'success',
      ]);
    }

    sweetalert()->success('Attribute deleted successfully!');

    return redirect()->route('admin.attributes.index');
  }
}
