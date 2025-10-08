<?php

namespace App\Http\Controllers\Backend;

use App\Models\Persistence;
use App\Models\User;
use Illuminate\Http\Request;

class PersistenceController extends BaseController
{
    protected string $resource = 'persistence';

    protected array $additionalPermissions = ['session_management_access'];

    public function index(Request $request)
    {
        $query = Persistence::with('user')->orderBy('created_at', 'desc');

        // Filter by user if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $persistences = $query->paginate(15);

        return view('admin.persistences.index', compact('persistences'));
    }

    public function destroy(Persistence $persistence)
    {
        $persistence->delete();

        return redirect()->route('admin.persistences.index')->with('success', 'Session record deleted successfully.');
    }

    /**
     * Get persistence records for a specific user
     */
    public function byUser(User $user)
    {
        $persistences = Persistence::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.persistences.by-user', compact('persistences', 'user'));
    }

    /**
     * Cleanup old persistence records
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);

        $deleted = Persistence::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('admin.persistences.index')
            ->with('success', "Cleaned up {$deleted} old session records.");
    }

    /**
     * Revoke all sessions for a user
     */
    public function revokeAll(User $user)
    {
        $count = Persistence::where('user_id', $user->id)->count();
        Persistence::where('user_id', $user->id)->delete();

        return redirect()->route('admin.persistences.index')
            ->with('success', "Revoked {$count} sessions for user: {$user->full_name}.");
    }

    /**
     * Get session statistics
     */
    public function statistics()
    {
        $stats = [
            'total_sessions' => Persistence::count(),
            'active_sessions_today' => Persistence::whereDate('created_at', today())->count(),
            'active_sessions_week' => Persistence::where('created_at', '>=', now()->startOfWeek())->count(),
            'active_sessions_month' => Persistence::where('created_at', '>=', now()->startOfMonth())->count(),
            'unique_users_today' => Persistence::whereDate('created_at', today())->distinct('user_id')->count(),
            'unique_users_week' => Persistence::where('created_at', '>=', now()->startOfWeek())->distinct('user_id')->count(),
            'unique_users_month' => Persistence::where('created_at', '>=', now()->startOfMonth())->distinct('user_id')->count(),
        ];

        return view('admin.persistences.statistics', compact('stats'));
    }
}
