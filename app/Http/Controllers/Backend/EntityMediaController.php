<?php

namespace App\Http\Controllers\Backend;

use App\Models\EntityMedia;
use App\Models\Media;
use Illuminate\Http\Request;

class EntityMediaController extends BaseController
{
    protected string $resource = 'entity_media';

    protected array $additionalPermissions = ['media_management_access'];

    public function index(string $entityType, int $entityId)
    {
        // Validate entity type
        $allowedEntityTypes = ['products', 'categories', 'brands', 'users', 'vendors', 'blog_posts', 'pages'];

        if (! in_array($entityType, $allowedEntityTypes)) {
            abort(404, 'Invalid entity type');
        }

        $entityMedia = EntityMedia::with('media')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.entity_media.index', compact('entityMedia', 'entityType', 'entityId'));
    }

    public function store(Request $request, string $entityType, int $entityId)
    {
        $request->validate([
            'media_id' => 'required|exists:media,id',
            'zone' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Check if the media is already attached to this entity
        $exists = EntityMedia::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('media_id', $request->media_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Media is already attached to this entity.');
        }

        // Get the next sort order if not provided
        $sortOrder = $request->sort_order;
        if (is_null($sortOrder)) {
            $sortOrder = EntityMedia::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->max('sort_order') + 1;
        }

        EntityMedia::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'media_id' => $request->media_id,
            'zone' => $request->zone,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('admin.entity_media.index', [$entityType, $entityId])
            ->with('success', 'Media attached successfully.');
    }

    public function destroy(EntityMedia $entityMedia)
    {
        $entityType = $entityMedia->entity_type;
        $entityId = $entityMedia->entity_id;

        $entityMedia->delete();

        return redirect()->route('admin.entity_media.index', [$entityType, $entityId])
            ->with('success', 'Media detached successfully.');
    }

    /**
     * Reorder entity media items
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:entity_media,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            EntityMedia::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Media order updated successfully.']);
    }

    /**
     * Update entity media zone
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
     * Get media by zone for a specific entity
     */
    public function byZone(string $entityType, int $entityId, string $zone)
    {
        $entityMedia = EntityMedia::with('media')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('zone', $zone)
            ->orderBy('sort_order')
            ->get();

        return response()->json($entityMedia);
    }

    /**
     * Bulk attach media to entity
     */
    public function bulkAttach(Request $request, string $entityType, int $entityId)
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id',
            'zone' => 'nullable|string|max:255',
        ]);

        $attachedCount = 0;
        $sortOrder = EntityMedia::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->max('sort_order') + 1;

        foreach ($request->media_ids as $mediaId) {
            // Check if already exists
            $exists = EntityMedia::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('media_id', $mediaId)
                ->exists();

            if (! $exists) {
                EntityMedia::create([
                    'entity_type' => $entityType,
                    'entity_id' => $entityId,
                    'media_id' => $mediaId,
                    'zone' => $request->zone,
                    'sort_order' => $sortOrder++,
                ]);
                $attachedCount++;
            }
        }

        return redirect()->route('admin.entity_media.index', [$entityType, $entityId])
            ->with('success', "Attached {$attachedCount} media files successfully.");
    }

    /**
     * Remove all media from entity
     */
    public function clearAll(string $entityType, int $entityId)
    {
        $count = EntityMedia::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->count();

        EntityMedia::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();

        return redirect()->route('admin.entity_media.index', [$entityType, $entityId])
            ->with('success', "Removed {$count} media files successfully.");
    }
}
