<?php

namespace App\Http\Controllers\Backend;

use App\Models\Throttle;
use App\Models\User;
use Illuminate\Http\Request;

class ThrottleController extends BaseController
{
    protected string $resource = 'throttle';

    protected array $additionalPermissions = ['security_management_access'];

    public function index(Request $request)
    {
        $query = Throttle::with('user')->orderBy('created_at', 'desc');

        // Filter by IP if provided
        if ($request->filled('ip')) {
            $query->where('ip', 'like', '%'.$request->ip.'%');
        }

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $throttles = $query->paginate(15);

        return view('admin.throttle.index', compact('throttles'));
    }

    public function destroy(Throttle $throttle)
    {
        $throttle->delete();

        return redirect()->route('admin.throttle.index')->with('success', 'Throttle record deleted successfully.');
    }

    /**
     * Get throttle records by IP address
     */
    public function byIp(string $ip)
    {
        $throttles = Throttle::with('user')
            ->where('ip', $ip)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.throttles.by-ip', compact('throttles', 'ip'));
    }

    /**
     * Get throttle records by user
     */
    public function byUser(User $user)
    {
        $throttles = Throttle::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.throttles.by-user', compact('throttles', 'user'));
    }

    /**
     * Cleanup old throttle records
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);

        $deleted = Throttle::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('admin.throttle.index')
            ->with('success', "Cleaned up {$deleted} old throttle records.");
    }

    /**
     * Reset throttle attempts for a user
     */
    public function reset(User $user)
    {
        $count = Throttle::where('user_id', $user->id)->count();
        Throttle::where('user_id', $user->id)->delete();

        return redirect()->route('admin.throttle.index')
            ->with('success', "Reset {$count} throttle attempts for user: {$user->full_name}.");
    }

    /**
     * Reset throttle attempts for an IP address
     */
    public function resetIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ]);

        $count = Throttle::where('ip', $request->ip)->count();
        Throttle::where('ip', $request->ip)->delete();

        return redirect()->route('admin.throttle.index')
            ->with('success', "Reset {$count} throttle attempts for IP: {$request->ip}.");
    }

    /**
     * Get throttle statistics
     */
    public function statistics()
    {
        $stats = [
            'total_throttles' => Throttle::count(),
            'throttles_today' => Throttle::whereDate('created_at', today())->count(),
            'throttles_this_week' => Throttle::where('created_at', '>=', now()->startOfWeek())->count(),
            'throttles_this_month' => Throttle::where('created_at', '>=', now()->startOfMonth())->count(),
            'unique_ips_today' => Throttle::whereDate('created_at', today())->distinct('ip')->count(),
            'unique_ips_week' => Throttle::where('created_at', '>=', now()->startOfWeek())->distinct('ip')->count(),
            'unique_users_affected' => Throttle::whereNotNull('user_id')->distinct('user_id')->count(),
            'login_throttles' => Throttle::where('type', 'login')->count(),
            'global_throttles' => Throttle::where('type', 'global')->count(),
        ];

        return view('admin.throttles.statistics', compact('stats'));
    }

    /**
     * Get most throttled IPs
     */
    public function topIps(Request $request)
    {
        $limit = $request->get('limit', 20);
        $period = $request->get('period', 'all_time');

        $query = Throttle::selectRaw('ip, COUNT(*) as throttle_count')
            ->groupBy('ip')
            ->orderBy('throttle_count', 'desc')
            ->limit($limit);

        if ($period !== 'all_time') {
            $days = $this->getPeriodDays($period);
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $topIps = $query->get();

        return view('admin.throttles.top-ips', compact('topIps', 'limit', 'period'));
    }

    /**
     * Block IP address (add to blacklist)
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'reason' => 'nullable|string|max:255',
        ]);

        // This would typically involve adding to a blacklist table
        // For now, we'll just add a special throttle record
        Throttle::create([
            'ip' => $request->ip,
            'type' => 'blocked',
            'reason' => $request->reason ?? 'Manually blocked by admin',
        ]);

        return redirect()->route('admin.throttle.index')
            ->with('success', "IP address {$request->ip} has been blocked.");
    }

    /**
     * Get current active throttles (recent attempts)
     */
    public function active()
    {
        $activeThrottles = Throttle::with('user')
            ->where('created_at', '>=', now()->subHours(1))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.throttles.active', compact('activeThrottles'));
    }

    /**
     * Helper method to get period days
     */
    private function getPeriodDays($period)
    {
        return match ($period) {
            'today' => 1,
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 30
        };
    }
}
