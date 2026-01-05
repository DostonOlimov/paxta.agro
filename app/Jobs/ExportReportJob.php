<?php

namespace App\Jobs;

use App\Models\ExportRequest;
use App\Exports\ReportExport;
use App\Notifications\ExportReadyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportRequest;
    protected $filters;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 3; // Retry 3 times on failure
    public $maxExceptions = 3;

    public function __construct(ExportRequest $exportRequest, array $filters)
    {
        $this->exportRequest = $exportRequest;
        $this->filters = $filters;
    }

    public function handle()
    {
        try {
            $this->exportRequest->update(['status' => 'processing']);

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

    protected function getReportQuery()
    {
        $reportController = app(\App\Http\Controllers\ReportController::class);
        
        // Create request object
        $request = new \Illuminate\Http\Request($this->filters);

        // Use reflection to access private method
        $reflection = new \ReflectionClass($reportController);
        $method = $reflection->getMethod('getReport');
        $method->setAccessible(true);

        // Get query builder and order
        $query = $method->invoke($reportController, $request);
        
        return $query->orderBy('id', 'desc');
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