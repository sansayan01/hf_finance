<?php

namespace App\Services;

use App\Models\Borrower;
use App\Models\FraudAlert;
use App\Models\Loan;

class FraudDetectionService
{
    public function scanLoanApplication(Loan $loan): array
    {
        $alerts = [];
        $borrower = $loan->borrower;

        // Pattern 1: Multiple applications in short time
        $recentApplications = Loan::where('borrower_id', $borrower->id)
            ->where('created_at', '>', now()->subDays(30))
            ->where('id', '!=', $loan->id)
            ->count();

        if ($recentApplications > 2) {
            $alerts[] = $this->createAlert($loan, 'multiple_applications', 'medium', [
                'count' => $recentApplications,
                'timeframe' => '30 days',
            ]);
        }

        // Pattern 2: Unusual loan amount
        $avgIncome = Borrower::where('organization_id', $loan->organization_id)
            ->avg('monthly_income') ?? 1;

        if ($loan->applied_amount > ($borrower->monthly_income * 10)) {
            $alerts[] = $this->createAlert($loan, 'high_loan_to_income', 'high', [
                'loan_amount' => $loan->applied_amount,
                'monthly_income' => $borrower->monthly_income,
                'ratio' => $loan->applied_amount / max($borrower->monthly_income, 1),
            ]);
        }

        // Pattern 3: Blacklist check
        if ($borrower->isBlacklisted()) {
            $alerts[] = $this->createAlert($loan, 'blacklisted_borrower', 'critical', [
                'blacklist_reason' => $borrower->blacklist_reason,
            ]);
        }

        // Pattern 4: Document tampering indicators
        $unverifiedDocs = $loan->documents()->where('verified', false)->count();
        if ($unverifiedDocs > 0) {
            $alerts[] = $this->createAlert($loan, 'unverified_documents', 'low', [
                'unverified_count' => $unverifiedDocs,
            ]);
        }

        // Pattern 5: Velocity check
        $orgLoansLast24h = Loan::where('organization_id', $loan->organization_id)
            ->where('created_at', '>', now()->subDay())
            ->count();

        if ($orgLoansLast24h > 50) {
            $alerts[] = $this->createAlert($loan, 'high_velocity_applications', 'medium', [
                'count_24h' => $orgLoansLast24h,
            ]);
        }

        // Pattern 6: Suspicious contact patterns
        $samePhoneDifferentName = Borrower::where('phone', $borrower->phone)
            ->where('id', '!=', $borrower->id)
            ->exists();

        if ($samePhoneDifferentName) {
            $alerts[] = $this->createAlert($loan, 'duplicate_phone_number', 'high', [
                'phone' => $borrower->phone,
            ]);
        }

        // Calculate fraud score
        $fraudScore = $this->calculateFraudScore($alerts);

        return [
            'alerts' => $alerts,
            'fraud_score' => $fraudScore,
            'risk_level' => $this->getRiskLevel($fraudScore),
            'recommendation' => $this->getFraudRecommendation($fraudScore, $alerts),
        ];
    }

    private function createAlert(Loan $loan, string $type, string $severity, array $data): FraudAlert
    {
        return FraudAlert::create([
            'organization_id' => $loan->organization_id,
            'loan_id' => $loan->id,
            'borrower_id' => $loan->borrower_id,
            'alert_type' => $type,
            'severity' => $severity,
            'description' => $this->getAlertDescription($type, $data),
            'detected_patterns' => $data,
            'fraud_score' => match ($severity) {
                'critical' => 100,
                'high' => 75,
                'medium' => 50,
                default => 25,
            },
            'status' => 'new',
        ]);
    }

    private function getAlertDescription(string $type, array $data): string
    {
        return match ($type) {
            'multiple_applications' => "Borrower has submitted {$data['count']} applications in the last {$data['timeframe']}",
            'high_loan_to_income' => 'Loan amount significantly exceeds borrower income capacity',
            'blacklisted_borrower' => 'Borrower is on the blacklist: ' . ($data['blacklist_reason'] ?? 'No reason provided'),
            'unverified_documents' => "{$data['unverified_count']} documents require verification",
            'high_velocity_applications' => "Unusual volume: {$data['count_24h']} applications in last 24 hours",
            'duplicate_phone_number' => 'Phone number associated with multiple borrowers',
            default => 'Suspicious activity detected',
        };
    }

    private function calculateFraudScore(array $alerts): float
    {
        if (empty($alerts)) {
            return 0;
        }

        $totalScore = collect($alerts)->sum('fraud_score');
        $maxPossibleScore = count($alerts) * 100;

        return min(($totalScore / $maxPossibleScore) * 100, 100);
    }

    private function getRiskLevel(float $score): string
    {
        return match (true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            $score >= 20 => 'low',
            default => 'minimal',
        };
    }

    private function getFraudRecommendation(float $score, array $alerts): string
    {
        if ($score >= 80) {
            return 'Reject application immediately and flag for investigation';
        }
        if ($score >= 60) {
            return 'Require additional verification and manager approval';
        }
        if ($score >= 40) {
            return 'Enhanced due diligence recommended';
        }
        if (! empty($alerts)) {
            return 'Standard verification with alert monitoring';
        }
        return 'No fraud indicators detected';
    }

    public function checkForDocumentForgery(Loan $loan): array
    {
        $checks = [];

        foreach ($loan->documents as $document) {
            // Check metadata consistency
            if ($document->ocr_data) {
                $nameMatch = $this->checkNameMatch(
                    $document->ocr_data['parsed_data']['name'] ?? '',
                    $loan->borrower->full_name
                );

                $checks[] = [
                    'document_id' => $document->id,
                    'type' => $document->document_type,
                    'name_match' => $nameMatch,
                    'confidence' => $document->ocr_confidence,
                    'risk_flag' => ! $nameMatch || $document->ocr_confidence < 70,
                ];
            }
        }

        return [
            'checks' => $checks,
            'forgery_risk' => collect($checks)->where('risk_flag', true)->count() > 0,
        ];
    }

    private function checkNameMatch(string $docName, string $borrowerName): bool
    {
        $docName = strtolower(trim($docName));
        $borrowerName = strtolower(trim($borrowerName));

        // Simple fuzzy matching
        similar_text($docName, $borrowerName, $percent);

        return $percent > 80;
    }
}
