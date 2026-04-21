<?php

namespace App\Services;

use App\Models\Borrower;
use App\Models\Loan;

class AICreditScoringService
{
    public function calculateRiskScore(Borrower $borrower): array
    {
        $factors = [];
        $baseScore = 500;

        // Income stability factor (0-100)
        $incomeFactor = min($borrower->monthly_income / 10000, 100);
        $factors['income_stability'] = $incomeFactor;
        $baseScore += $incomeFactor * 0.2;

        // Employment stability
        $employmentScore = $this->getEmploymentScore($borrower->employment_type);
        $factors['employment_stability'] = $employmentScore;
        $baseScore += $employmentScore * 0.15;

        // Credit history
        $creditHistoryScore = $this->analyzeCreditHistory($borrower);
        $factors['credit_history'] = $creditHistoryScore;
        $baseScore += $creditHistoryScore * 0.25;

        // Geographic risk
        $geographicScore = $this->getGeographicScore($borrower->city);
        $factors['geographic_risk'] = $geographicScore;
        $baseScore += $geographicScore * 0.1;

        // Existing loans
        $existingLoansScore = $this->analyzeExistingLoans($borrower);
        $factors['existing_loans'] = $existingLoansScore;
        $baseScore += $existingLoansScore * 0.15;

        // KYC verification
        $kycScore = $borrower->kyc_status === 'verified' ? 100 : 50;
        $factors['kyc_verification'] = $kycScore;
        $baseScore += $kycScore * 0.15;

        // Normalize to 300-850 range
        $finalScore = min(max($baseScore, 300), 850);

        return [
            'score' => (int) $finalScore,
            'risk_category' => $this->getRiskCategory($finalScore),
            'factors' => $factors,
            'recommendation' => $this->getRecommendation($finalScore),
        ];
    }

    private function getEmploymentScore(?string $employmentType): float
    {
        return match ($employmentType) {
            'salaried' => 100,
            'self_employed' => 75,
            'business' => 60,
            'unemployed' => 0,
            default => 50,
        };
    }

    private function analyzeCreditHistory(Borrower $borrower): float
    {
        $completedLoans = $borrower->loans()->where('status', 'completed')->count();
        $defaultedLoans = $borrower->loans()->where('status', 'defaulted')->count();
        $totalLoans = $completedLoans + $defaultedLoans;

        if ($totalLoans === 0) {
            return 50;
        }

        $repaymentRate = $completedLoans / $totalLoans;
        return min($repaymentRate * 100, 100);
    }

    private function getGeographicScore(?string $city): float
    {
        $highRiskCities = [];
        return in_array($city, $highRiskCities) ? 60 : 90;
    }

    private function analyzeExistingLoans(Borrower $borrower): float
    {
        $activeLoans = $borrower->loans()->whereIn('status', ['active', 'disbursed'])->count();
        $totalLoanAmount = $borrower->loans()->whereIn('status', ['active', 'disbursed'])->sum('approved_amount');
        $monthlyIncome = $borrower->monthly_income;

        if ($monthlyIncome == 0) {
            return 0;
        }

        $debtToIncomeRatio = $totalLoanAmount / ($monthlyIncome * 12);

        if ($debtToIncomeRatio > 0.5) {
            return max(0, 100 - ($debtToIncomeRatio * 100));
        }

        return max(0, 100 - ($activeLoans * 10));
    }

    private function getRiskCategory(float $score): string
    {
        return match (true) {
            $score >= 750 => 'low',
            $score >= 650 => 'medium',
            $score >= 550 => 'high',
            default => 'very_high',
        };
    }

    private function getRecommendation(float $score): string
    {
        return match (true) {
            $score >= 750 => 'Approve with standard rates',
            $score >= 650 => 'Approve with slightly higher rates',
            $score >= 550 => 'Approve with higher rates and collateral',
            default => 'Reject or require guarantor',
        };
    }

    public function predictApprovalLikelihood(Borrower $borrower, float $loanAmount): array
    {
        $riskScore = $this->calculateRiskScore($borrower);
        $dti = $this->calculateDTI($borrower, $loanAmount);

        $approvalProbability = min(
            ($riskScore['score'] / 850) * 0.6 +
            (1 - $dti) * 0.4,
            0.99
        ) * 100;

        return [
            'probability' => round($approvalProbability, 2),
            'risk_score' => $riskScore['score'],
            'debt_to_income' => round($dti, 4),
            'recommended_amount' => $this->getRecommendedAmount($borrower, $riskScore['score']),
            'confidence' => $approvalProbability > 80 ? 'high' : ($approvalProbability > 50 ? 'medium' : 'low'),
        ];
    }

    private function calculateDTI(Borrower $borrower, float $newLoanAmount): float
    {
        $monthlyIncome = $borrower->monthly_income;
        $existingEMIs = $borrower->loans()
            ->whereIn('status', ['active', 'disbursed'])
            ->with('repaymentSchedules')
            ->get()
            ->sum(fn($loan) => $loan->repaymentSchedules->first()?->total_amount ?? 0);

        $newEMI = $newLoanAmount / 12;
        $totalMonthlyObligations = $existingEMIs + $newEMI;

        return $monthlyIncome > 0 ? $totalMonthlyObligations / $monthlyIncome : 1;
    }

    private function getRecommendedAmount(Borrower $borrower, float $score): float
    {
        $maxMultiplier = match (true) {
            $score >= 750 => 6,
            $score >= 650 => 4,
            $score >= 550 => 2,
            default => 0,
        };

        return $borrower->monthly_income * $maxMultiplier;
    }
}
