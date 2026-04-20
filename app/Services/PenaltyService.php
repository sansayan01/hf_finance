<?php

namespace App\Services;

use App\Models\RepaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenaltyService
{
    /**
     * Calculate and apply penalties for overdue installments.
     */
    public function applyOverduePenalties(): int
    {
        $count = 0;
        
        DB::transaction(function () use (&$count) {
            $overdueSchedules = RepaymentSchedule::where('due_date', '<', Carbon::today())
                ->where('status', '!=', 'paid')
                ->with('loan')
                ->get();

            foreach ($overdueSchedules as $schedule) {
                $loan = $schedule->loan;
                
                if (!$loan->penalty_type || $loan->penalty_amount <= 0) {
                    continue;
                }

                // Check grace days (from loan product or config)
                $graceDays = $loan->loanProduct->penalty_grace_days ?? 0;
                if (Carbon::today()->diffInDays($schedule->due_date) < $graceDays) {
                    continue;
                }

                $penalty = 0;
                if ($loan->penalty_type === 'fixed') {
                    $penalty = $loan->penalty_amount;
                } elseif ($loan->penalty_type === 'percentage') {
                    // Penalty as percentage of overdue principal
                    $overdueAmount = $schedule->principal_amount - $schedule->paid_amount;
                    $penalty = ($overdueAmount * $loan->penalty_amount) / 100;
                }

                if ($penalty > $schedule->penalty_amount) {
                    $schedule->update([
                        'penalty_amount' => $penalty,
                        'status' => 'overdue'
                    ]);
                    $count++;
                }
            }
        });

        return $count;
    }
}
