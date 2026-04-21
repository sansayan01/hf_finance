<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\RepaymentSchedule;
use Illuminate\Support\Collection;

class SmartCollectionService
{
    public function prioritizeCollection(Collection $overdueLoans): Collection
    {
        return $overdueLoans->map(function ($loan) {
            $priorityScore = $this->calculatePriorityScore($loan);
            $loan->ai_collection_priority = $priorityScore;
            $loan->save();

            return [
                'loan' => $loan,
                'priority_score' => $priorityScore,
                'priority_rank' => 0,
                'recommended_action' => $this->getRecommendedAction($loan, $priorityScore),
                'predicted_recovery_amount' => $this->predictRecoveryAmount($loan),
                'optimal_contact_time' => $this->getOptimalContactTime($loan->borrower),
            ];
        })->sortByDesc('priority_score')->values();
    }

    public function calculatePriorityScore(Loan $loan): float
    {
        $score = 50;

        // Days overdue factor
        $daysOverdue = $loan->days_overdue;
        if ($daysOverdue > 90) {
            $score += 30;
        } elseif ($daysOverdue > 60) {
            $score += 20;
        } elseif ($daysOverdue > 30) {
            $score += 10;
        }

        // Outstanding amount factor
        $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;
        $avgOutstanding = $loan->organization->loans()->avg('approved_amount') ?? 1;
        $amountScore = min(($outstanding / $avgOutstanding) * 10, 20);
        $score += $amountScore;

        // Borrower payment history
        $paymentHistory = $this->analyzePaymentHistory($loan);
        $score += $paymentHistory;

        // Borrower engagement
        $engagementScore = $this->analyzeEngagement($loan->borrower);
        $score += $engagementScore;

        // NPA status
        if ($loan->npa_status === 'loss') {
            $score -= 20;
        } elseif ($loan->npa_status === 'doubtful') {
            $score -= 10;
        }

        return min(max($score, 0), 100);
    }

    private function analyzePaymentHistory(Loan $loan): float
    {
        $schedules = $loan->repaymentSchedules;
        $total = $schedules->count();

        if ($total === 0) {
            return 0;
        }

        $paidOnTime = $schedules->where('status', 'paid')
            ->filter(fn($s) => $s->paid_at <= $s->due_date)->count();

        $paymentRate = $paidOnTime / $total;
        return (1 - $paymentRate) * 15;
    }

    private function analyzeEngagement($borrower): float
    {
        $responseRate = NotificationLog::where('borrower_id', $borrower->id)
            ->where('status', 'delivered')
            ->count() / max(NotificationLog::where('borrower_id', $borrower->id)->count(), 1);

        return (1 - $responseRate) * 10;
    }

    private function getRecommendedAction(Loan $loan, float $priorityScore): string
    {
        $daysOverdue = $loan->days_overdue;

        return match (true) {
            $daysOverdue > 90 && $priorityScore > 70 => 'legal_notice',
            $daysOverdue > 60 => 'field_visit',
            $daysOverdue > 30 && $priorityScore > 60 => 'daily_calls',
            $daysOverdue > 15 => 'reminder_calls',
            $priorityScore > 50 => 'gentle_reminder',
            default => 'automated_sms',
        };
    }

    private function predictRecoveryAmount(Loan $loan): float
    {
        $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;
        $daysOverdue = $loan->days_overdue;

        $recoveryProbability = match (true) {
            $daysOverdue <= 30 => 0.9,
            $daysOverdue <= 60 => 0.75,
            $daysOverdue <= 90 => 0.55,
            default => 0.35,
        };

        return $outstanding * $recoveryProbability;
    }

    private function getOptimalContactTime($borrower): string
    {
        return match ($borrower->employment_type) {
            'salaried' => '18:00-20:00',
            'business', 'self_employed' => '10:00-12:00',
            default => '11:00-13:00',
        };
    }

    public function generateCollectionStrategy(Loan $loan): array
    {
        $priorityScore = $this->calculatePriorityScore($loan);
        $borrower = $loan->borrower;

        return [
            'loan_id' => $loan->id,
            'priority_score' => $priorityScore,
            'segments' => [
                'days_overdue' => $loan->days_overdue,
                'amount_segment' => $this->getAmountSegment($loan->approved_amount),
                'risk_segment' => $this->getRiskSegment($priorityScore),
            ],
            'recommended_actions' => [
                'immediate' => $this->getRecommendedAction($loan, $priorityScore),
                'follow_up' => $loan->days_overdue > 30 ? 'field_visit' : 'phone_call',
                'escalation' => $loan->days_overdue > 90 ? 'legal' : 'manager_review',
            ],
            'contact_preferences' => [
                'best_channel' => $this->getBestContactChannel($borrower),
                'best_time' => $this->getOptimalContactTime($borrower),
                'language' => 'en',
            ],
            'recovery_prediction' => [
                'probability' => $this->getRecoveryProbability($loan),
                'estimated_days' => $this->estimateRecoveryDays($loan),
                'recommended_settlement' => $this->calculateSettlementOffer($loan),
            ],
        ];
    }

    private function getAmountSegment(float $amount): string
    {
        return match (true) {
            $amount > 500000 => 'high',
            $amount > 100000 => 'medium',
            default => 'low',
        };
    }

    private function getRiskSegment(float $score): string
    {
        return match (true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default => 'low',
        };
    }

    private function getBestContactChannel($borrower): string
    {
        return $borrower->phone ? 'whatsapp' : 'email';
    }

    private function getRecoveryProbability(Loan $loan): float
    {
        $daysOverdue = $loan->days_overdue;
        return match (true) {
            $daysOverdue <= 30 => 0.95,
            $daysOverdue <= 60 => 0.80,
            $daysOverdue <= 90 => 0.60,
            default => 0.40,
        };
    }

    private function estimateRecoveryDays(Loan $loan): int
    {
        return (int) ($loan->days_overdue * 1.5);
    }

    private function calculateSettlementOffer(Loan $loan): float
    {
        $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;
        $discount = match (true) {
            $loan->days_overdue > 180 => 0.30,
            $loan->days_overdue > 90 => 0.20,
            $loan->days_overdue > 60 => 0.10,
            default => 0,
        };

        return $outstanding * (1 - $discount);
    }
}
