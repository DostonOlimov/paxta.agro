<?php

namespace App\Jobs;

use App\Models\ExportRequest;
use App\Exports\ReportExport;
use App\Notifications\ExportReadyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportRequest;
    protected $filters;
    protected $year;
    protected $cropType;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 3; // Retry 3 times on failure
    public $maxExceptions = 3;

    public function __construct(ExportRequest $exportRequest, array $filters, $year = null, $cropType = null)
    {
        $this->exportRequest = $exportRequest;
        $this->filters = $filters;
        $this->year = $year;
        $this->cropType = $cropType;
    }

    public function handle()
    {
        try {
            $this->exportRequest->update(['status' => 'processing']);

            // A worker has neither the requesting user nor their session, which the report
            // query and FinalResult's global scopes both read. Restore them first.
            $this->restoreRequestContext();

            // Get the query builder (not the results)
            $query = $this->getReportQuery();

            // Build file path
            $finalFilePath = $this->buildFilePath();

            // Ensure directory exists
            $this->ensureDirectoryExists($finalFilePath);

            // Export with query builder for memory efficiency
            $export = new ReportExport($query);
            Excel::store($export, $finalFilePath, 'local');

            // Update export request
            $this->markAsCompleted($finalFilePath);

            // Notify user
            $this->notifyUser();

            Log::info('Export completed successfully', [
                'export_request_id' => $this->exportRequest->id,
                'user_id' => $this->exportRequest->user_id,
                'file_path' => $finalFilePath,
            ]);

        } catch (\Exception $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    /**
     * Put the worker into the same state the original request was in.
     *
     * ReportController::getReport() reads Auth::user(), and FinalResult's global scopes read
     * both auth()->user() and the session-scoped year/crop. None of that exists in a queue
     * worker, which is why the job died on "Attempt to read property branch_id on null".
     */
    protected function restoreRequestContext(): void
    {
        $user = $this->exportRequest->user;

        if (!$user) {
            // Without the user we cannot scope the report to their region. Exporting
            // everything instead would hand a regional user the whole country's data.
            throw new \RuntimeException(
                "Export user #{$this->exportRequest->user_id} no longer exists; refusing to build an unscoped report."
            );
        }

        // setUser() rather than login(): this guard never needs to be persisted anywhere.
        Auth::setUser($user);

        if ($this->year !== null) {
            session(['year' => $this->year]);
        }

        if ($this->cropType !== null) {
            session(['crop' => $this->cropType]);
        }

        // Global scopes are captured once per process in Model::boot(). In a long-running
        // worker that means job #2 would silently reuse job #1's user, year and crop, so
        // drop the booted state and let the scopes rebuild from the context set above.
        Model::clearBootedModels();
    }

    protected function getReportQuery()
    {
        $reportController = app(\App\Http\Controllers\ReportController::class);

        // Build the same query the /full-report page shows, from the filters it sent
        $request = new \Illuminate\Http\Request($this->filters);

        return $reportController->getReport($request)
            // ReportExport walks these on every row; without them each chunk is an N+1 storm
            ->with([
                'dalolatnoma' => fn ($query) => $query->withSum('akt_amount', 'amount'),
                'dalolatnoma.test_program.application.crops.name',
            ])
            ->orderBy('id', 'desc');
    }

    protected function buildFilePath(): string
    {
        return "exports/{$this->exportRequest->user_id}/{$this->exportRequest->filename}";
    }

    protected function ensureDirectoryExists(string $filePath): void
    {
        $directory = dirname(storage_path("app/{$filePath}"));
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function markAsCompleted(string $filePath): void
    {
        $this->exportRequest->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'completed_at' => now(),
        ]);
    }

    protected function notifyUser(): void
    {
        $user = $this->exportRequest->user;
        
        if ($user) {
            $user->notify(new ExportReadyNotification(
                $this->exportRequest->filename,
                $this->exportRequest->id
            ));
        }
    }

    protected function handleFailure(\Exception $e): void
    {
        $this->exportRequest->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);

        Log::error('Export job failed', [
            'export_request_id' => $this->exportRequest->id,
            'user_id' => $this->exportRequest->user_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    public function failed(\Exception $exception): void
    {
        // This method is called when the job has exhausted all retry attempts
        $this->handleFailure($exception);
        
        // Optionally notify user of failure
        $user = $this->exportRequest->user;
        if ($user) {
            // You can create a separate notification for failures
            // $user->notify(new ExportFailedNotification($this->exportRequest));
        }
    }
}