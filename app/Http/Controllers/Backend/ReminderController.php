<?php

namespace App\Http\Controllers\Backend;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends BaseController
{
    protected string $resource = 'reminder';

    protected array $additionalPermissions = ['security_management_access'];

    public function index(Request $request)
    {
        $query = Reminder::with('user')->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('completed', false);
            } elseif ($request->status === 'completed') {
                $query->where('completed', true);
            }
        }

        $reminders = $query->paginate(15);

        return view('admin.reminders.index', compact('reminders'));
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();

        return redirect()->route('admin.reminders.index')->with('success', 'Password reset reminder deleted successfully.');
    }

    /**
     * Get pending reminders
     */
    public function pending()
    {
        $reminders = Reminder::with('user')
            ->where('completed', false)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reminders.index', compact('reminders'));
    }

    /**
     * Get completed reminders
     */
    public function completed()
    {
        $reminders = Reminder::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reminders.index', compact('reminders'));
    }

    /**
     * Cleanup old reminder records
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 7);
        $cutoffDate = now()->subDays($days);

        $deleted = Reminder::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->route('admin.reminders.index')
            ->with('success', "Cleaned up {$deleted} old password reset reminders.");
    }

    /**
     * Get reminder statistics
     */
    public function statistics()
    {
        $stats = [
            'total_reminders' => Reminder::count(),
            'pending_reminders' => Reminder::where('completed', false)->count(),
            'completed_reminders' => Reminder::where('completed', true)->count(),
            'reminders_today' => Reminder::whereDate('created_at', today())->count(),
            'reminders_this_week' => Reminder::where('created_at', '>=', now()->startOfWeek())->count(),
            'reminders_this_month' => Reminder::where('created_at', '>=', now()->startOfMonth())->count(),
            'expired_reminders' => Reminder::where('completed', false)
                ->where('created_at', '<', now()->subHours(24))
                ->count(),
        ];

        return view('admin.reminders.statistics', compact('stats'));
    }

    /**
     * Force expire old pending reminders
     */
    public function expireOld(Request $request)
    {
        $hours = $request->input('hours', 24);
        $cutoffDate = now()->subHours($hours);

        $expired = Reminder::where('completed', false)
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return redirect()->route('admin.reminders.index')
            ->with('success', "Expired {$expired} old password reset reminders.");
    }

    /**
     * Get reminders by user
     */
    public function byUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $reminders = Reminder::with('user')
            ->where('user_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reminders.index', compact('reminders'));
    }
}
