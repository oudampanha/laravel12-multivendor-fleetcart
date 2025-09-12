<?php

namespace App\Http\Controllers\Backend;

use App\Models\Activation;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class ActivationController extends BaseController
{
    protected string $resource = 'activation';
    
    protected array $additionalPermissions = ['activation_management_access'];

    public function index(Request $request)
    {
        $query = Activation::with('user')->orderBy('created_at', 'desc');
        
        // Filter by status if provided
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('completed', false);
            } elseif ($request->status === 'completed') {
                $query->where('completed', true);
            }
        }
        
        $activations = $query->paginate(15);
        return view('admin.activations.index', compact('activations'));
    }

    public function show(Activation $activation)
    {
        $activation->load('user');
        return view('admin.activations.show', compact('activation'));
    }

    public function destroy(Activation $activation)
    {
        $activation->delete();
        return redirect()->route('admin.activations.index')->with('success', 'Activation record deleted successfully.');
    }

    /**
     * Get pending activations
     */
    public function pending()
    {
        $activations = Activation::with('user')
            ->where('completed', false)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.activations.index', compact('activations'));
    }

    /**
     * Get completed activations
     */
    public function completed()
    {
        $activations = Activation::with('user')
            ->where('completed', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.activations.index', compact('activations'));
    }

    /**
     * Cleanup old activation records
     */
    public function cleanup(Request $request)
    {
        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);
        
        $deleted = Activation::where('created_at', '<', $cutoffDate)
            ->where('completed', true)
            ->delete();
            
        return redirect()->route('admin.activations.index')
            ->with('success', "Cleaned up {$deleted} old activation records.");
    }
}