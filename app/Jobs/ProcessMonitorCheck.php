<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\MonitorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessMonitorCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Monitor $monitor;

    /**
     * Create a new job instance.
     *
     * @param  Monitor  $monitor
     * @return void
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $monitor_id = $this->monitor->id;
        $start = microtime(true);
        $response = Http::get($this->monitor->url_or_ip);
        $ttfb = microtime(true) - $start;

        if ($response->successful()) {
            $status = $response->status();
            $contentType = $response->header('Content-Type');
            $contentLength = strlen($response->body());
            $this->logMonitorStatus($monitor_id, true);
        } else {
            $status = $response->status();
            $contentType = null;
            $contentLength = null;
            $this->logMonitorStatus($monitor_id, false);
        }

        $this->logAction([
            'url' => $this->monitor->url_or_ip,
            'ttfb' => $ttfb,
            'status' => $status,
            'contentType' => $contentType,
            'contentLength' => $contentLength,
            'monitor_id' => $monitor_id,
        ]);
    }

    /**
     * Log the action in a data store.
     *
     * @param array $data
     * @return void
     */
    protected function logAction(array $data)
    {
        MonitorLog::create($data);
    }

    /**
     * Log the status in the monitor.
     *
     * @param int $id
     * @param bool $status
     * @return void
     */
    protected function logMonitorStatus(int $id, bool $status)
    {
        $monitor = Monitor::findOrFail($id);
        $ret = $monitor->update(['status' => $status]);
        error_log($ret);
    }
}
