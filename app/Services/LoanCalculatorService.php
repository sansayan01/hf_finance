<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\RepaymentSchedule;
use Illuminate\Support\Collection;

class LoanCalculatorService
{
    public function calculateSchedule(Loan $loan): Collection
    {
        $schedules = collect();
        $principal = $loan->approved_amount;
        $rate = $loan->interest_rate / 100 / 12;
        $tenure = $loan->tenure_months;

        if ($loan->interest_type === 'flat') {
            $schedules = $this->calculateFlatSchedule($principal, $loan->interest_rate, $tenure);
        } elseif ($loan->interest_type === 'declining') {
            $schedules = $this->calculateDecliningSchedule($principal, $rate, $tenure);
        } elseif ($loan->interest_type === 'compound') {
            $schedules = $this->calculateCompoundSchedule($principal, $loan->interest_rate, $tenure);
        }

        return $schedules;
    }

    private function calculateFlatSchedule(float $principal, float $rate, int $tenure): Collection
    {
        $schedules = collect();
        $monthlyInterest = ($principal * $rate / 100) / 12;
        $totalInterest = $monthlyInterest * $tenure;
        $monthlyPrincipal = $principal / $tenure;
        $emi = $monthlyPrincipal + $monthlyInterest;
        $balance = $principal + $totalInterest;
        $dueDate = now()->addMonth();

        for ($i = 1; $i <= $tenure; $i++) {
            $balance -= $emi;
            $schedules->push([
                'installment_number' => $i,
                'due_date' => $dueDate->copy()->addMonths($i - 1),
                'principal_amount' => $monthlyPrincipal,
                'interest_amount' => $monthlyInterest,
                'total_amount' => $emi,
                'balance' => max(0, $balance),
            ]);
        }

        return $schedules;
    }

    private function calculateDecliningSchedule(float $principal, float $rate, int $tenure): Collection
    {
        $schedules = collect();
        $emi = $this->calculateEMI($principal, $rate, $tenure);
        $balance = $principal;

        for ($i = 1; $i <= $tenure; $i++) {
            $interest = $balance * $rate;
            $principalPortion = $emi - $interest;
            $balance -= $principalPortion;

            $schedules->push([
                'installment_number' => $i,
                'due_date' => now()->addMonths($i),
                'principal_amount' => $principalPortion,
                'interest_amount' => $interest,
                'total_amount' => $emi,
                'balance' => max(0, $balance),
            ]);
        }

        return $schedules;
    }

    private function calculateCompoundSchedule(float $principal, float $rate, int $tenure): Collection
    {
        $schedules = collect();
        $monthlyRate = $rate / 100 / 12;
        $emi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $tenure)) /
               (pow(1 + $monthlyRate, $tenure) - 1);

        $balance = $principal;

        for ($i = 1; $i <= $tenure; $i++) {
            $interest = $balance * $monthlyRate;
            $principalPortion = $emi - $interest;
            $balance -= $principalPortion;

            $schedules->push([
                'installment_number' => $i,
                'due_date' => now()->addMonths($i),
                'principal_amount' => $principalPortion,
                'interest_amount' => $interest,
                'total_amount' => $emi,
                'balance' => max(0, $balance),
            ]);
        }

        return $schedules;
    }

    public function calculateEMI(float $principal, float $rate, int $tenure): float
    {
        if ($rate == 0) {
            return $principal / $tenure;
        }
        return ($principal * $rate * pow(1 + $rate, $tenure)) /
               (pow(1 + $rate, $tenure) - 1);
    }

    public function recalculateOnPrepayment(Loan $loan, float $prepaymentAmount): Collection
    {
        $outstanding = $loan->principal_outstanding;
        $newPrincipal = $outstanding - $prepaymentAmount;
        $rate = $loan->interest_rate / 100 / 12;
        $remainingTenure = $loan->repaymentSchedules->where('status', 'pending')->count();

        if ($loan->interest_type === 'declining') {
            return $this->calculateDecliningSchedule($newPrincipal, $rate, $remainingTenure);
        }

        return $this->calculateFlatSchedule($newPrincipal, $loan->interest_rate, $remainingTenure);
    }

    public function calculatePartialPrepayment(RepaymentSchedule $schedule, float $amount): array
    {
        $remaining = $schedule->getRemainingAmount();
        $newRemaining = $remaining - $amount;

        $interestPortion = min($amount * 0.3, $schedule->interest_amount);
        $principalPortion = $amount - $interestPortion;

        return [
            'interest_portion' => $interestPortion,
            'principal_portion' => $principalPortion,
            'new_balance' => max(0, $newRemaining),
            'is_fully_paid' => $newRemaining <= 0,
        ];
    }
}
