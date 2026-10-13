<?php

namespace App\Jobs;

use App\Models\TrackingLog;
use App\Models\TrackingPlatform;
use App\Services\Tracking\TrackingManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTrackingEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $timeout = 15;

    protected int $platformId;
    protected string $eventName;
    protected string $eventId;
    protected array $eventData;
    protected ?int $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $platformId, string $eventName, string $eventId, array $eventData, ?int $orderId = null)
    {
        $this->platformId = $platformId;
        $this->eventName = $eventName;
        $this->eventId = $eventId;
        $this->eventData = $eventData;
        $this->orderId = $orderId;

        // Set queue
        $this->onQueue('tracking');
    }

    /**
     * Execute the job.
     */
    public function handle(TrackingManager $trackingManager): void
    {
        $platform = TrackingPlatform::find($this->platformId);

        if (!$platform) {
            Log::error("Platform not found: {$this->platformId}");
            return;
        }

        // Create or update tracking log
        $log = TrackingLog::firstOrCreate(
            [
                'platform_id' => $this->platformId,
                'event_name' => $this->eventName,
                'event_id' => $this->eventId,
            ],
            [
                'order_id' => $this->orderId,
                'payload' => $this->eventData,
                'status' => 'pending',
                'attempts' => 0,
            ]
        );

        // Increment attempts
        $log->increment('attempts');
        $log->update(['sent_at' => now()]);

        try {
            // Get adapter and send event
            $adapter = $trackingManager->getAdapter($platform->platform);

            if (!$adapter) {
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'Adapter not found for platform: ' . $platform->platform,
                ]);
                return;
            }

            $result = $adapter->send($this->eventData, $platform);

            // Update log with result
            $log->update([
                'response' => $result->getData(),
                'http_status' => $result->getHttpStatus(),
                'status' => $result->success ? 'success' : 'failed',
                'error_message' => $result->getError(),
            ]);

            // If failed and not retryable, mark as failed permanently
            if (!$result->success && !$result->isRetryable()) {
                $this->fail(new \Exception('Permanent error: ' . $result->getError()));
            }

        } catch (\Exception $e) {
            Log::error('SendTrackingEventJob error: ' . $e->getMessage(), [
                'platform_id' => $this->platformId,
                'event_name' => $this->eventName,
                'event_id' => $this->eventId,
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Mark job as failed if max attempts reached
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
            }

            throw $e;
        }
    }

    /**
     * Calculate backoff time for retries
     */
    public function backoff(): array
    {
        return [10, 60, 300, 1800, 3600]; // 10s, 1m, 5m, 30m, 1h
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        $log = TrackingLog::where('platform_id', $this->platformId)
            ->where('event_name', $this->eventName)
            ->where('event_id', $this->eventId)
            ->first();

        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Job failed after ' . $this->attempts() . ' attempts: ' . $exception->getMessage(),
            ]);
        }

        Log::error('SendTrackingEventJob failed permanently', [
            'platform_id' => $this->platformId,
            'event_name' => $this->eventName,
            'event_id' => $this->eventId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
        ]);
    }
}
