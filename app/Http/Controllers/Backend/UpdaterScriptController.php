<?php

namespace App\Http\Controllers\Backend;

use App\Models\UpdaterScript;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdaterScriptController extends BaseController
{
    protected string $resource = 'updater_script';

    protected array $additionalPermissions = ['system_management_access'];

    public function index(Request $request)
    {
        $query = UpdaterScript::orderBy('version', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $updaterScripts = $query->paginate(15);

        return view('admin.updater-scripts.index', compact('updaterScripts'));
    }

    public function run(Request $request, UpdaterScript $updaterScript)
    {
        // Check if script is already running or completed
        if ($updaterScript->status === 'running') {
            return redirect()->back()->with('error', 'Script is already running.');
        }

        if ($updaterScript->status === 'completed') {
            return redirect()->back()->with('error', 'Script has already been completed.');
        }

        try {
            // Mark script as running
            $updaterScript->update([
                'status' => 'running',
                'started_at' => now(),
                'output' => 'Script execution started...',
            ]);

            $output = '';
            $exitCode = 0;

            // Execute the script based on its type
            switch ($updaterScript->type) {
                case 'migration':
                    $exitCode = Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'seeder':
                    $exitCode = Artisan::call('db:seed', [
                        '--class' => $updaterScript->script_class,
                        '--force' => true,
                    ]);
                    $output = Artisan::output();
                    break;

                case 'command':
                    $exitCode = Artisan::call($updaterScript->script_command);
                    $output = Artisan::output();
                    break;

                case 'php':
                    // Execute PHP script file
                    if ($updaterScript->script_file && file_exists($updaterScript->script_file)) {
                        ob_start();
                        include $updaterScript->script_file;
                        $output = ob_get_clean();
                    } else {
                        throw new \Exception('Script file not found: '.$updaterScript->script_file);
                    }
                    break;

                default:
                    throw new \Exception('Unknown script type: '.$updaterScript->type);
            }

            // Mark script as completed or failed
            $status = $exitCode === 0 ? 'completed' : 'failed';
            $updaterScript->update([
                'status' => $status,
                'completed_at' => now(),
                'output' => $output,
                'exit_code' => $exitCode,
            ]);

            // Log the execution
            Log::info('Updater script executed', [
                'script_id' => $updaterScript->id,
                'version' => $updaterScript->version,
                'status' => $status,
                'exit_code' => $exitCode,
            ]);

            if ($status === 'completed') {
                return redirect()->route('admin.updater-scripts.index')
                    ->with('success', 'Script executed successfully.');
            } else {
                return redirect()->route('admin.updater-scripts.index')
                    ->with('error', 'Script execution failed. Check the output for details.');
            }

        } catch (\Exception $e) {
            // Mark script as failed
            $updaterScript->update([
                'status' => 'failed',
                'completed_at' => now(),
                'output' => 'Error: '.$e->getMessage(),
                'exit_code' => 1,
            ]);

            Log::error('Updater script execution failed', [
                'script_id' => $updaterScript->id,
                'version' => $updaterScript->version,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.updater-scripts.index')
                ->with('error', 'Script execution failed: '.$e->getMessage());
        }
    }

    /**
     * Get execution logs
     */
    public function logs(Request $request)
    {
        $logs = UpdaterScript::whereNotNull('started_at')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('admin.updater_scripts.logs', compact('logs'));
    }

    /**
     * Cleanup old log entries
     */
    public function cleanupLogs(Request $request)
    {
        $days = $request->input('days', 30);
        $cutoffDate = now()->subDays($days);

        $deleted = UpdaterScript::where('status', 'completed')
            ->where('completed_at', '<', $cutoffDate)
            ->whereNotNull('output')
            ->update(['output' => null]);

        return redirect()->route('admin.updater_scripts.logs')
            ->with('success', "Cleaned up logs for {$deleted} completed scripts.");
    }

    /**
     * Get pending scripts
     */
    public function pending()
    {
        $pendingScripts = UpdaterScript::where('status', 'pending')
            ->orderBy('version', 'asc')
            ->paginate(15);

        return view('admin.updater_scripts.pending', compact('pendingScripts'));
    }

    /**
     * Get completed scripts
     */
    public function completed()
    {
        $completedScripts = UpdaterScript::where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('admin.updater_scripts.completed', compact('completedScripts'));
    }

    /**
     * Get failed scripts
     */
    public function failed()
    {
        $failedScripts = UpdaterScript::where('status', 'failed')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        return view('admin.updater_scripts.failed', compact('failedScripts'));
    }

    /**
     * Reset a failed script to pending
     */
    public function reset(UpdaterScript $updaterScript)
    {
        if ($updaterScript->status === 'running') {
            return redirect()->back()->with('error', 'Cannot reset a running script.');
        }

        $updaterScript->update([
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
            'output' => null,
            'exit_code' => null,
        ]);

        return redirect()->route('admin.updater-scripts.index')
            ->with('success', 'Script has been reset to pending status.');
    }

    /**
     * Download script output as a file
     */
    public function downloadOutput(UpdaterScript $updaterScript)
    {
        if (empty($updaterScript->output)) {
            return redirect()->back()->with('error', 'No output available for this script.');
        }

        $filename = "updater_script_{$updaterScript->version}_{$updaterScript->id}_output.txt";

        return response()->streamDownload(function () use ($updaterScript) {
            echo $updaterScript->output;
        }, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Run all pending scripts in sequence
     */
    public function runAll(Request $request)
    {
        $pendingScripts = UpdaterScript::where('status', 'pending')
            ->orderBy('version', 'asc')
            ->get();

        if ($pendingScripts->isEmpty()) {
            return redirect()->back()->with('info', 'No pending scripts to run.');
        }

        $executed = 0;
        $failed = 0;

        foreach ($pendingScripts as $script) {
            try {
                // Use a simple approach to run the script
                $result = $this->executeScript($script);
                if ($result) {
                    $executed++;
                } else {
                    $failed++;
                    break; // Stop on first failure to prevent cascading issues
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error('Batch script execution failed', [
                    'script_id' => $script->id,
                    'error' => $e->getMessage(),
                ]);
                break;
            }
        }

        $message = "Executed {$executed} scripts successfully.";
        if ($failed > 0) {
            $message .= " {$failed} scripts failed.";
        }

        return redirect()->route('admin.updater-scripts.index')
            ->with($failed > 0 ? 'warning' : 'success', $message);
    }

    /**
     * Helper method to execute a single script
     */
    private function executeScript(UpdaterScript $script)
    {
        $script->update(['status' => 'running', 'started_at' => now()]);

        try {
            $exitCode = 0;
            $output = '';

            switch ($script->type) {
                case 'migration':
                    $exitCode = Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    break;
                case 'command':
                    $exitCode = Artisan::call($script->script_command);
                    $output = Artisan::output();
                    break;
                    // Add other types as needed
            }

            $status = $exitCode === 0 ? 'completed' : 'failed';
            $script->update([
                'status' => $status,
                'completed_at' => now(),
                'output' => $output,
                'exit_code' => $exitCode,
            ]);

            return $status === 'completed';
        } catch (\Exception $e) {
            $script->update([
                'status' => 'failed',
                'completed_at' => now(),
                'output' => 'Error: '.$e->getMessage(),
                'exit_code' => 1,
            ]);

            return false;
        }
    }
}
