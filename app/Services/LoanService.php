<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\RepaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Generate repayment schedule for a loan.
     */
    public function generateRepaymentSchedule(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Delete existing schedule if any
            $loan->repaymentSchedules()->delete();

            $principal = $loan->approved_amount;
            $annualRate = $loan->interest_rate;
            $tenureMonths = $loan->tenure_months;
            $interestType = $loan->interest_type;
            $frequency = $loan->repayment_frequency;

            // Simple logic for monthly frequency. 
            // For production, this should handle weekly, daily, etc.
            $periods = $tenureMonths; 
            $monthlyRate = ($annualRate / 100) / 12;

            if ($interestType === 'flat') {
                $this->calculateFlatSchedule($loan, $principal, $annualRate, $periods);
            } elseif ($interestType === 'declining') {
                $this->calculateDecliningSchedule($loan, $principal, $monthlyRate, $periods);
            }
        });
    }

    protected function calculateFlatSchedule(Loan $loan, $principal, $annualRate, $periods): void
    {
        $graceMonths = $loan->grace_period_months ?? 0;
        $totalInterest = ($principal * $annualRate * ($periods / 12)) / 100;
        $interestPerPeriod = $totalInterest / $periods;
        
        $repaymentPeriods = $periods - $graceMonths;
        $principalPerPeriod = $repaymentPeriods > 0 ? $principal / $repaymentPeriods : 0;

        $totalPayable = $principal + $totalInterest;

        $loan->update([
            'total_interest' => $totalInterest,
            'total_payable' => $totalPayable,
        ]);

        $dueDate = Carbon::parse($loan->disbursed_at ?? now())->addMonth();
        $runningBalance = $totalPayable;

        for ($i = 1; $i <= $periods; $i++) {
            $isGrace = $i <= $graceMonths;
            $p = $isGrace ? 0 : $principalPerPeriod;
            $total = $p + $interestPerPeriod;
            $runningBalance -= $total;

            RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate->copy(),
                'principal_amount' => $p,
                'interest_amount' => $interestPerPeriod,
                'total_amount' => $total,
                'balance' => max(0, $runningBalance),
                'status' => 'pending',
            ]);
            $dueDate->addMonth();
        }
    }

    protected function calculateDecliningSchedule(Loan $loan, $principal, $monthlyRate, $periods): void
    {
        $graceMonths = $loan->grace_period_months ?? 0;
        $repaymentPeriods = $periods - $graceMonths;

        // Amortization Formula for the repayment part
        if ($monthlyRate > 0 && $repaymentPeriods > 0) {
            $repaymentInstallment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $repaymentPeriods)) / (pow(1 + $monthlyRate, $repaymentPeriods) - 1);
        } elseif ($repaymentPeriods > 0) {
            $repaymentInstallment = $principal / $repaymentPeriods;
        } else {
            $repaymentInstallment = 0;
        }

        $graceInterest = $principal * $monthlyRate;
        
        $totalInterest = ($graceInterest * $graceMonths) + (($repaymentInstallment * $repaymentPeriods) - $principal);
        $totalPayable = $principal + $totalInterest;

        $loan->update([
            'total_interest' => $totalInterest,
            'total_payable' => $totalPayable,
        ]);

        $currentPrincipalBalance = $principal;
        $dueDate = Carbon::parse($loan->disbursed_at ?? now())->addMonth();

        for ($i = 1; $i <= $periods; $i++) {
            $isGrace = $i <= $graceMonths;
            
            if ($isGrace) {
                $interestForPeriod = $principal * $monthlyRate;
                $principalForPeriod = 0;
                $installmentAmount = $interestForPeriod;
            } else {
                $interestForPeriod = $currentPrincipalBalance * $monthlyRate;
                $principalForPeriod = $repaymentInstallment - $interestForPeriod;
                $installmentAmount = $repaymentInstallment;
                $currentPrincipalBalance -= $principalForPeriod;
            }

            RepaymentSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate->copy(),
                'principal_amount' => $principalForPeriod,
                'interest_amount' => $interestForPeriod,
                'total_amount' => $installmentAmount,
                'balance' => max(0, $currentPrincipalBalance), // Note: using principal balance here or remaining payable?
                'status' => 'pending',
            ]);
            $dueDate->addMonth();
        }
    }/**
     * Apply a payment to the loan repayment schedule.
     */
    public function applyPayment(\App\Models\Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $loan = $payment->loan;
            $remainingAmount = $payment->amount;

            $schedules = $loan->repaymentSchedules()
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('due_date')
                ->get();

            foreach ($schedules as $installment) {
                if ($remainingAmount <= 0) break;

                $dueForInstallment = $installment->total_amount - $installment->paid_amount;
                
                if ($remainingAmount >= $dueForInstallment) {
                    // Fully pay this installment
                    $remainingAmount -= $dueForInstallment;
                    $installment->update([
                        'paid_amount' => $installment->total_amount,
                        'paid_at' => $payment->payment_date,
                        'status' => 'paid',
                        'balance' => 0,
                    ]);
                } else {
                    // Partially pay this installment
                    $installment->update([
                        'paid_amount' => $installment->paid_amount + $remainingAmount,
                        'paid_at' => $payment->payment_date,
                        'status' => 'partial',
                        'balance' => $installment->total_amount - ($installment->paid_amount + $remainingAmount),
                    ]);
                    $remainingAmount = 0;
                }
            }

            // check if all installments are paid
            if ($loan->repaymentSchedules()->where('status', '!=', 'paid')->count() === 0) {
                $loan->update([
                    'status' => 'completed',
                    'closed_at' => now(),
                ]);
            } elseif ($loan->status === 'disbursed') {
                $loan->update(['status' => 'active']);
            }
        });
    }
}
