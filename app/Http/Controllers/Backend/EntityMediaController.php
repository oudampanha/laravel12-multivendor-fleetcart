<?php

namespace App\Http\Controllers\Backend;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EntityMedia;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class EntityMediaController extends BaseController
{
    protected string $resource = 'entity_media';

    protected array $additionalPermissions = ['media_management_access'];

    /**
     * Map URL slug -> fully qualified model class. The entity_media table
     * stores `entity_type` as the FQCN (matching what Eloquent's morphTo
     * writes by default), so we normalise the URL slug to that here.
     */
    private function entityTypeMap(): array
    {
        return [
            'products' => Product::class,
            'categories' => Category::class,
            'brands' => Brand::class,
            'users' => User::class,
            'vendors' => Vendor::class,
            'blog_posts' => BlogPost::class,
        ];
    }

    private function resolveEntityClass(string $entityType): string
    {
        $map = $this->entityTypeMap();

        if (! isset($map[$entityType])) {
            abort(404, 'Invalid entity type');
        }

        return $map[$entityType];
    }

    public function index(string $entityType, int $entityId)
    {
        $entityClass = $this->resolveEntityClass($entityType);

        $entityMedia = EntityMedia::with('file')
            ->where('entity_type', $entityClass)
            ->where('entity_id', $entityId)
            ->orderBy('id')
            ->paginate(15);

        return view('admin.entity-media.index', compact('entityMedia', 'entityType', 'entityId'));
    }

    public function store(Request $request, string $entityType, int $entityId)
    {
        $entityClass = $this->resolveEntityClass($entityType);

        $request->validate([
            'file_id' => 'required|exists:media,id',
            'zone' => 'nullable|string|max:255',
        ]);

        $exists = EntityMedia::where('entity_type', $entityClass)
            ->where('entity_id', $entityId)
            ->where('file_id', $request->file_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Media is already attached to this entity.');
        }

        EntityMedia::create([
            'entity_type' => $entityClass,
            'entity_id' => $entityId,
            'file_id' => $request->file_id,
            'zone' => $request->zone,
        ]);

        return redirect()->route('admin.entity-media.index', [$entityType, $entityId])
            ->with('success', 'Media attached successfully.');
    }

    public function destroy(EntityMedia $entityMedia)
    {
        $entityClass = $entityMedia->entity_type;
        $entityId = $entityMedia->entity_id;

        // Reverse-resolve FQCN back to URL slug so the redirect target is valid.
        $slug = array_search($entityClass, $this->entityTypeMap(), true);

        $entityMedia->delete();

        if ($slug === false) {
            return redirect()->back()->with('success', 'Media detached successfully.');
        }

        return redirect()->route('admin.entity-media.index', [$slug, $entityId])
            ->with('success', 'Media detached successfully.');
    }

    /**
     * Update entity media zone.
     */
    public function updateZone(Request $request, EntityMedia $entityMedia)
    {
        $request->validate([
            'zone' => 'nullable|string|max:255',
        ]);

        $entityMedia->update(['zone' => $request->zone]);

        return redirect()->back()->with('success', 'Media zone updated successfully.');
    }

    /**
     * Get media by zone for a specific entity.
     */
    public function byZone(string $entityType, int $entityId, string $zone)
    {
        $entityClass = $this->resolveEntityClass($entityType);

        $entityMedia = EntityMedia::with('file')
            ->where('entity_type', $entityClass)
            ->where('entity_id', $entityId)
            ->where('zone', $zone)
            ->orderBy('id')
            ->get();

        return response()->json($entityMedia);
    }

    /**
     * Bulk attach media to entity.
     */
    public function bulkAttach(Request $request, string $entityType, int $entityId)
    {
        $entityClass = $this->resolveEntityClass($entityType);

        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:media,id',
            'zone' => 'nullable|string|max:255',
        ]);

        $attachedCount = 0;

        foreach ($request->file_ids as $fileId) {
            $exists = EntityMedia::where('entity_type', $entityClass)
                ->where('entity_id', $entityId)
                ->where('file_id', $fileId)
                ->exists();

            if (! $exists) {
                EntityMedia::create([
                    'entity_type' => $entityClass,
                    'entity_id' => $entityId,
                    'file_id' => $fileId,
                    'zone' => $request->zone,
                ]);
                $attachedCount++;
            }
        }

        return redirect()->route('admin.entity-media.index', [$entityType, $entityId])
            ->with('success', "Attached {$attachedCount} media files successfully.");
    }

    /**
     * Remove all media from entity.
     */
    public function clearAll(string $entityType, int $entityId)
    {
        $entityClass = $this->resolveEntityClass($entityType);

        $count = EntityMedia::where('entity_type', $entityClass)
            ->where('entity_id', $entityId)
            ->count();

        EntityMedia::where('entity_type', $entityClass)
            ->where('entity_id', $entityId)
            ->delete();

        return redirect()->route('admin.entity-media.index', [$entityType, $entityId])
            ->with('success', "Removed {$count} media files successfully.");
    }
}
