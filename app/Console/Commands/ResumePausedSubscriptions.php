<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPauseResumeLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResumePausedSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:resume-paused';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically resume paused subscriptions when the resume date is reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('subscription:resume-paused command started.');

        // Get today's date
        $today = Carbon::now()->format('Y-m-d');

        // Fetch logs with resume date matching today
        $logs = SubscriptionPauseResumeLog::whereDate('resume_date', $today)
            ->whereHas('subscription', function ($query) {
                $query->where('status', 'paused');
            })
            ->with('subscription')
            ->get();

        if ($logs->isEmpty()) {
            Log::info('No paused subscriptions to resume today.');
            return Command::SUCCESS;
        }

        foreach ($logs as $log) {
            $subscription = $log->subscription;

            if (!$subscription || $subscription->status !== 'paused') {
                Log::warning('Invalid subscription state for resume', ['log_id' => $log->id]);
                continue;
            }

            try {
                DB::beginTransaction(); // Start transaction

                // Parse dates
                $pauseStartDate = Carbon::parse($subscription->pause_start_date);
                $resumeDate = Carbon::parse($log->resume_date);
                $pauseEndDate = Carbon::parse($subscription->pause_end_date);
                $currentEndDate = $subscription->new_date
                    ? Carbon::parse($subscription->new_date)
                    : Carbon::parse($subscription->end_date);

                // Calculate paused days and remaining paused days
                $actualPausedDays = $resumeDate->diffInDays($pauseStartDate);
                $totalPausedDays = $pauseEndDate->diffInDays($pauseStartDate) + 1;
                $remainingPausedDays = $totalPausedDays - $actualPausedDays;

                // Adjust end date
                $newEndDate = $remainingPausedDays > 0
                    ? $currentEndDate->subDays($actualPausedDays)
                    : $currentEndDate;

                // Update subscription
                $subscription->update([
                    'status' => 'active',
                    'pause_start_date' => null,
                    'pause_end_date' => null,
                    'new_date' => $newEndDate,
                ]);

                // Log success
                Log::info('Subscription resumed successfully', [
                    'subscription_id' => $subscription->subscription_id,
                    'order_id' => $subscription->order_id,
                    'new_end_date' => $newEndDate->toDateString(),
                ]);

                DB::commit(); // Commit transaction
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback transaction on error

                Log::error('Error processing subscription resume', [
                    'log_id' => $log->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('subscription:resume-paused command completed.');
        $this->info('Paused subscriptions processed successfully.');
        return Command::SUCCESS;
    }
}
