<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class NPAService
{
    public function classifyNPA(Loan $loan): array
    {
        $daysOverdue = $loan->days_overdue;
        $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;

        // RBI NPA Classification
        $classification = match (true) {
            $daysOverdue >= 90 && $daysOverdue < 120 => [
                'category' => 'substandard',
                'provision_rate' => 0.15,
                'days_range' => '90-119 days',
            ],
            $daysOverdue >= 120 && $daysOverdue < 180 => [
                'category' => 'doubtful',
                'provision_rate' => 0.25,
                'days_range' => '120-179 days',
                'sub_category' => 'doubtful_1',
            ],
            $daysOverdue >= 180 && $daysOverdue < 365 => [
                'category' => 'doubtful',
                'provision_rate' => 0.40,
                'days_range' => '180-364 days',
                'sub_category' => 'doubtful_2',
            ],
            $daysOverdue >= 365 => [
                'category' => 'doubtful',
                'provision_rate' => 0.50,
                'days_range' => '365+ days',
                'sub_category' => 'doubtful_3',
            ],
            $daysOverdue >= 1095 => [
                'category' => 'loss',
                'provision_rate' => 1.0,
                'days_range' => '3+ years',
            ],
            default => [
                'category' => 'standard',
                'provision_rate' => 0,
                'days_range' => '0-89 days',
            ],
        };

        $provisionAmount = $outstanding * $classification['provision_rate'];

        $result = [
            'loan_id' => $loan->id,
            'days_overdue' => $daysOverdue,
            'outstanding_amount' => $outstanding,
            'npa_category' => $classification['category'],
            'days_range' => $classification['days_range'],
            'provision_rate' => $classification['provision_rate'],
            'provision_amount' => round($provisionAmount, 2),
            'secured_recovery_estimate' => $this->estimateSecuredRecovery($loan, $classification['category']),
            'action_required' => $this->getRequiredAction($classification['category'], $daysOverdue),
        ];

        // Update loan NPA status
        $loan->update([
            'npa_status' => $classification['category'],
        ]);

        return $result;
    }

    public function calculateProvisioning(int $organizationId): array
    {
        $loans = Loan::where('organization_id', $organizationId)
            ->whereIn('status', ['active', 'defaulted'])
            ->get();

        $standard = 0;
        $substandard = 0;
        $doubtful = 0;
        $loss = 0;

        $details = [];

        foreach ($loans as $loan) {
            $classification = $this->classifyNPA($loan);

            $details[] = [
                'loan_number' => $loan->loan_number,
                'borrower' => $loan->borrower->full_name,
                'outstanding' => $classification['outstanding_amount'],
                'days_overdue' => $classification['days_overdue'],
                'category' => $classification['npa_category'],
                'provision_required' => $classification['provision_amount'],
            ];

            match ($classification['npa_category']) {
                'standard' => $standard += $classification['provision_amount'],
                'substandard' => $substandard += $classification['provision_amount'],
                'doubtful' => $doubtful += $classification['provision_amount'],
                'loss' => $loss += $classification['provision_amount'],
            };
        }

        $totalProvision = $standard + $substandard + $doubtful + $loss;

        return [
            'total_provision_required' => round($totalProvision, 2),
            'breakdown' => [
                'standard' => round($standard, 2),
                'substandard' => round($substandard, 2),
                'doubtful' => round($doubtful, 2),
                'loss' => round($loss, 2),
            ],
            'details' => $details,
            'total_outstanding' => $loans->sum(fn($l) => $l->principal_outstanding + $l->interest_outstanding),
            'provision_percentage' => $totalProvision > 0
                ? round(($totalProvision / $loans->sum(fn($l) => $l->principal_outstanding + $l->interest_outstanding)) * 100, 2)
                : 0,
        ];
    }

    private function estimateSecuredRecovery(Loan $loan, string $category): float
    {
        $collateralValue = $loan->collateral_value ?? 0;
        $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;

        if ($collateralValue === 0) {
            return match ($category) {
                'substandard' => $outstanding * 0.60,
                'doubtful' => $outstanding * 0.30,
                'loss' => $outstanding * 0.10,
                default => $outstanding,
            };
        }

        $recoveryRate = match ($category) {
            'substandard' => 0.80,
            'doubtful' => 0.50,
            'loss' => 0.25,
            default => 1.0,
        };

        return min($collateralValue * $recoveryRate, $outstanding);
    }

    private function getRequiredAction(string $category, int $daysOverdue): string
    {
        return match (true) {
            $category === 'loss' => 'Write-off recommendation',
            $daysOverdue >= 180 => 'Legal proceedings initiated',
            $daysOverdue >= 90 => 'Recovery team assignment',
            $daysOverdue >= 60 => 'Intensified follow-up',
            $daysOverdue >= 30 => 'Standard reminder process',
            default => 'Regular monitoring',
        };
    }

    public function getPortfolioAtRisk(int $organizationId): array
    {
        $totalOutstanding = Loan::where('organization_id', $organizationId)
            ->whereIn('status', ['active', 'defaulted'])
            ->sum(DB::raw('principal_outstanding + interest_outstanding'));

        $par30 = $this->calculatePAR($organizationId, 30);
        $par60 = $this->calculatePAR($organizationId, 60);
        $par90 = $this->calculatePAR($organizationId, 90);

        return [
            'total_outstanding' => $totalOutstanding,
            'par_30' => $par30,
            'par_60' => $par60,
            'par_90' => $par90,
            'par_30_percentage' => $totalOutstanding > 0 ? round(($par30 / $totalOutstanding) * 100, 2) : 0,
            'par_60_percentage' => $totalOutstanding > 0 ? round(($par60 / $totalOutstanding) * 100, 2) : 0,
            'par_90_percentage' => $totalOutstanding > 0 ? round(($par90 / $totalOutstanding) * 100, 2) : 0,
        ];
    }

    private function calculatePAR(int $organizationId, int $days): float
    {
        return Loan::where('organization_id', $organizationId)
            ->whereIn('status', ['active', 'defaulted'])
            ->where('days_overdue', '>=', $days)
            ->sum(DB::raw('principal_outstanding + interest_outstanding'));
    }
}
