<?php

namespace App\Services;

use App\Models\Borrower;
use App\Models\LoanProduct;

class DynamicPricingService
{
    public function calculateInterestRate(LoanProduct $product, Borrower $borrower): array
    {
        if (! $product->dynamic_pricing_enabled) {
            return [
                'base_rate' => $product->base_interest_rate,
                'final_rate' => $product->base_interest_rate,
                'adjustments' => [],
                'risk_tier' => 'standard',
            ];
        }

        $baseRate = $product->base_interest_rate;
        $adjustments = [];

        // AI Risk Score Adjustment
        $aiScore = $borrower->ai_risk_score ?? 500;
        $riskTier = $this->getRiskTier($aiScore);
        $riskAdjustment = $this->getRiskAdjustment($riskTier, $product);
        $adjustments[] = [
            'factor' => 'ai_risk_score',
            'value' => $aiScore,
            'adjustment' => $riskAdjustment,
        ];

        // Credit History Adjustment
        $creditHistoryAdjustment = $this->calculateCreditHistoryAdjustment($borrower);
        $adjustments[] = [
            'factor' => 'credit_history',
            'value' => $borrower->credit_score,
            'adjustment' => $creditHistoryAdjustment,
        ];

        // Income Stability Adjustment
        $incomeAdjustment = $this->calculateIncomeAdjustment($borrower);
        $adjustments[] = [
            'factor' => 'income_stability',
            'value' => $borrower->monthly_income,
            'adjustment' => $incomeAdjustment,
        ];

        // Loyalty Adjustment (for returning customers)
        $loyaltyAdjustment = $this->calculateLoyaltyAdjustment($borrower);
        $adjustments[] = [
            'factor' => 'customer_loyalty',
            'value' => $loyaltyAdjustment > 0 ? 'returning' : 'new',
            'adjustment' => $loyaltyAdjustment,
        ];

        $totalAdjustment = collect($adjustments)->sum('adjustment');
        $finalRate = $baseRate + $totalAdjustment;

        // Ensure within product bounds
        $finalRate = max($baseRate, min($finalRate, $product->max_interest_rate ?? $baseRate * 1.5));

        return [
            'base_rate' => $baseRate,
            'final_rate' => round($finalRate, 4),
            'adjustments' => $adjustments,
            'total_adjustment' => round($totalAdjustment, 4),
            'risk_tier' => $riskTier,
        ];
    }

    private function getRiskTier(float $score): string
    {
        return match (true) {
            $score >= 750 => 'excellent',
            $score >= 700 => 'good',
            $score >= 650 => 'fair',
            $score >= 550 => 'poor',
            default => 'bad',
        };
    }

    private function getRiskAdjustment(string $tier, LoanProduct $product): float
    {
        if (! empty($product->risk_tier_rates)) {
            foreach ($product->risk_tier_rates as $rate) {
                if ($rate['tier'] === $tier) {
                    return $rate['adjustment'] ?? 0;
                }
            }
        }

        // Default adjustments
        return match ($tier) {
            'excellent' => -1.0,
            'good' => -0.5,
            'fair' => 0,
            'poor' => 1.5,
            'bad' => 3.0,
            default => 0,
        };
    }

    private function calculateCreditHistoryAdjustment(Borrower $borrower): float
    {
        $completedLoans = $borrower->loans()->where('status', 'completed')->count();
        $defaultedLoans = $borrower->loans()->where('status', 'defaulted')->count();
        $totalLoans = $completedLoans + $defaultedLoans;

        if ($totalLoans === 0) {
            return 0.5;
        }

        $successRate = $completedLoans / $totalLoans;

        return match (true) {
            $successRate >= 0.95 => -0.75,
            $successRate >= 0.90 => -0.50,
            $successRate >= 0.80 => 0,
            $successRate >= 0.70 => 0.75,
            default => 1.50,
        };
    }

    private function calculateIncomeAdjustment(Borrower $borrower): float
    {
        $monthlyIncome = $borrower->monthly_income;

        return match (true) {
            $monthlyIncome >= 100000 => -0.50,
            $monthlyIncome >= 50000 => -0.25,
            $monthlyIncome >= 25000 => 0,
            $monthlyIncome >= 15000 => 0.50,
            default => 1.00,
        };
    }

    private function calculateLoyaltyAdjustment(Borrower $borrower): float
    {
        $previousLoans = $borrower->loans()->where('status', 'completed')->count();

        return match (true) {
            $previousLoans >= 3 => -0.50,
            $previousLoans >= 1 => -0.25,
            default => 0,
        };
    }

    public function calculateProcessingFee(LoanProduct $product, float $loanAmount, Borrower $borrower): float
    {
        if ($product->processing_fee_type === 'fixed') {
            return $product->processing_fee_value;
        }

        $baseFee = $loanAmount * ($product->processing_fee_value / 100);

        // Discount for good customers
        if ($borrower->ai_risk_score >= 700) {
            $baseFee *= 0.75;
        }

        return round($baseFee, 2);
    }
}
