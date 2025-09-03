<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FlowerRequest;
use Carbon\Carbon;

class AutoRejectFlowerRequests extends Command
{
    // php artisan flower:auto-reject-requests
    protected $signature = 'flower:auto-reject-requests {--dry-run : Show what would be changed without saving}';
    protected $description = 'Auto-reject past-dated, unpaid flower requests still in Pending/Approved status.';

    public function handle()
    {
        $tz = 'Asia/Kolkata';
        $today = Carbon::today($tz);

        $this->info("Scanning for requests with date < {$today->toDateString()} (TZ: {$tz})");

        // Base query: request date is before "today" AND status is Pending/Approved
        $query = FlowerRequest::query()
            ->with('order')
            ->whereIn('status', ['Pending', 'Approved'])
            // If your DB column type is DATE/DATETIME (recommended):
            ->whereDate('date', '<', $today)
            // If your 'date' is stored as string in format d-m-Y, replace the line above with:
            // ->whereDate(DB::raw("STR_TO_DATE(`date`, '%d-%m-%Y')"), '<', $today)
            ->where(function ($q) {
                // Unpaid logic:
                // 1) No related order -> treat as unpaid
                // 2) order.payment_status is pending/unpaid/not paid
                // 3) OR explicit boolean flag not paid (is_paid = 0 / null)
                $q->whereDoesntHave('order')
                  ->orWhereHas('order', function ($oq) {
                      $oq->where(function ($qq) {
                          $qq->whereIn(DB::raw('LOWER(payment_status)'), ['pending', 'unpaid', 'not paid'])
                             ->orWhereNull('is_paid')
                             ->orWhere('is_paid', 0);
                      });
                  });
            });

        $count = (clone $query)->count();
        $this->line("Matches found: {$count}");

        if ($count === 0) {
            $this->info('Nothing to do.'); 
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $sample = (clone $query)->take(10)->get(['request_id', 'status', 'date']);
            $this->table(['request_id', 'status', 'date'], $sample->map(fn($r) => [$r->request_id, $r->status, (string)$r->date])->toArray());
            $this->warn('Dry run only; no changes saved.');
            return self::SUCCESS;
        }

        $updated = 0;
        $reason = 'Auto-rejected: not paid by the next day.';
        $by = 'system';

        try {
            DB::transaction(function () use ($query, $reason, $by, &$updated) {
                // Chunk to avoid loading everything at once
                $query->chunk(200, function ($chunk) use ($reason, $by, &$updated) {
                    foreach ($chunk as $req) {
                        $req->status = 'Rejected';
                        $req->cancel_by = $by;
                        $req->cancel_reason = $reason;
                        $req->save();

                        // OPTIONAL: Sync a related order status as well
                        if ($req->relationLoaded('order') && $req->order) {
                            $req->order->status = 'Cancelled';
                            $req->order->save();
                        }

                        $updated++;
                    }
                });
            });

            $this->info("Auto-rejected {$updated} request(s).");
            Log::info('AutoRejectFlowerRequests completed', ['updated' => $updated, 'run_at' => now()->toDateTimeString()]);
            return self::SUCCESS;

        } catch (\Throwable $e) {
            Log::error('AutoRejectFlowerRequests failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
