<?php

namespace App\Services;

use App\Models\Borrower;
use App\Models\ChatbotSession;
use App\Models\Loan;

class ChatbotService
{
    private array $intents = [
        'check_balance' => ['balance', 'outstanding', 'how much', 'remaining'],
        'check_emi' => ['emi', 'installment', 'due amount', 'next payment'],
        'payment_status' => ['payment', 'paid', 'last payment', 'recent payment'],
        'loan_status' => ['loan status', 'application', 'approved', 'rejected'],
        'payment_link' => ['pay now', 'payment link', 'make payment', 'online payment'],
        'download_statement' => ['statement', 'repayment schedule', 'download'],
        'contact_support' => ['help', 'support', 'agent', 'human'],
    ];

    public function processMessage(string $sessionId, string $message): array
    {
        $session = ChatbotSession::where('session_id', $sessionId)->first();

        if (! $session) {
            return $this->createErrorResponse('Session not found');
        }

        // Update activity
        $session->updateActivity();

        // Log incoming message
        $session->messages()->create([
            'direction' => 'inbound',
            'message' => $message,
            'message_type' => 'text',
            'sent_at' => now(),
        ]);

        $session->increment('message_count');

        // Detect intent
        $intent = $this->detectIntent($message);
        $response = $this->generateResponse($intent, $message, $session);

        // Log outgoing message
        $session->messages()->create([
            'direction' => 'outbound',
            'message' => $response['message'],
            'message_type' => $response['type'] ?? 'text',
            'metadata' => $response['metadata'] ?? null,
            'sent_at' => now(),
        ]);

        return $response;
    }

    public function createSession(string $phone, string $platform = 'whatsapp'): ChatbotSession
    {
        // Try to find existing borrower
        $borrower = Borrower::where('phone', $phone)->first();

        return ChatbotSession::create([
            'organization_id' => $borrower?->organization_id,
            'borrower_id' => $borrower?->id,
            'session_id' => uniqid('CHAT-', true),
            'phone' => $phone,
            'platform' => $platform,
            'status' => 'active',
            'started_at' => now(),
            'last_activity_at' => now(),
            'context' => [
                'authenticated' => false,
                'current_loan_id' => null,
                'awaiting_input' => null,
            ],
        ]);
    }

    private function detectIntent(string $message): string
    {
        $message = strtolower($message);

        foreach ($this->intents as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($message, $keyword)) {
                    return $intent;
                }
            }
        }

        return 'unknown';
    }

    private function generateResponse(string $intent, string $message, ChatbotSession $session): array
    {
        $borrower = $session->borrower;

        if (! $borrower) {
            return [
                'message' => "Hello! I'm your virtual assistant. I notice you don't have a registered account with us. " .
                    "Please visit your nearest branch or call our helpline to register.",
                'type' => 'text',
                'metadata' => ['action' => 'registration_required'],
            ];
        }

        return match ($intent) {
            'check_balance' => $this->handleBalanceCheck($borrower),
            'check_emi' => $this->handleEmiCheck($borrower),
            'payment_status' => $this->handlePaymentStatus($borrower),
            'loan_status' => $this->handleLoanStatus($borrower),
            'payment_link' => $this->handlePaymentLink($borrower),
            'download_statement' => $this->handleStatementRequest($borrower),
            'contact_support' => $this->handleSupportRequest(),
            default => $this->handleUnknownIntent(),
        };
    }

    private function handleBalanceCheck(Borrower $borrower): array
    {
        $activeLoans = $borrower->loans()->whereIn('status', ['active', 'disbursed'])->get();

        if ($activeLoans->isEmpty()) {
            return [
                'message' => 'You currently have no active loans with us.',
                'type' => 'text',
            ];
        }

        $message = "Here are your active loan balances:\n\n";
        foreach ($activeLoans as $loan) {
            $outstanding = $loan->principal_outstanding + $loan->interest_outstanding;
            $message .= "Loan #{$loan->loan_number}:\n";
            $message .= "Outstanding: ₹" . number_format($outstanding, 2) . "\n";
            $message .= "Next EMI: ₹" . number_format($loan->currentEmi?->total_amount ?? 0, 2) . "\n";
            $message .= "Due Date: " . ($loan->currentEmi?->due_date?->format('d M Y') ?? 'N/A') . "\n\n";
        }

        return [
            'message' => $message,
            'type' => 'text',
        ];
    }

    private function handleEmiCheck(Borrower $borrower): array
    {
        $nextEmi = RepaymentSchedule::whereHas('loan', fn($q) => $q->where('borrower_id', $borrower->id))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        if (! $nextEmi) {
            return [
                'message' => 'You have no pending EMIs at this time.',
                'type' => 'text',
            ];
        }

        return [
            'message' => "Your next EMI details:\n\n" .
                "Loan: #{$nextEmi->loan->loan_number}\n" .
                "Amount: ₹" . number_format($nextEmi->total_amount, 2) . "\n" .
                "Due Date: " . $nextEmi->due_date->format('d M Y') . "\n" .
                "Principal: ₹" . number_format($nextEmi->principal_amount, 2) . "\n" .
                "Interest: ₹" . number_format($nextEmi->interest_amount, 2),
            'type' => 'text',
            'metadata' => [
                'action' => 'payment_reminder',
                'loan_id' => $nextEmi->loan_id,
                'amount' => $nextEmi->total_amount,
            ],
        ];
    }

    private function handlePaymentStatus(Borrower $borrower): array
    {
        $recentPayments = Payment::where('borrower_id', $borrower->id)
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();

        if ($recentPayments->isEmpty()) {
            return [
                'message' => 'No payment records found.',
                'type' => 'text',
            ];
        }

        $message = "Your recent payments:\n\n";
        foreach ($recentPayments as $payment) {
            $message .= "₹" . number_format($payment->amount, 2) . " - " .
                $payment->payment_date->format('d M Y') . "\n";
            $message .= "Receipt: {$payment->receipt_number}\n\n";
        }

        return [
            'message' => $message,
            'type' => 'text',
        ];
    }

    private function handleLoanStatus(Borrower $borrower): array
    {
        $loans = $borrower->loans()->orderBy('created_at', 'desc')->take(3)->get();

        if ($loans->isEmpty()) {
            return [
                'message' => 'You have no loan applications with us.',
                'type' => 'text',
            ];
        }

        $message = "Your loan applications:\n\n";
        foreach ($loans as $loan) {
            $message .= "Loan #{$loan->loan_number}:\n";
            $message .= "Amount: ₹" . number_format($loan->applied_amount, 2) . "\n";
            $message .= "Status: " . ucfirst($loan->status) . "\n";
            if ($loan->status === 'rejected') {
                $message .= "Reason: {$loan->rejection_reason}\n";
            }
            $message .= "\n";
        }

        return [
            'message' => $message,
            'type' => 'text',
        ];
    }

    private function handlePaymentLink(Borrower $borrower): array
    {
        $nextEmi = RepaymentSchedule::whereHas('loan', fn($q) => $q->where('borrower_id', $borrower->id))
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        if (! $nextEmi) {
            return [
                'message' => 'You have no pending payments at this time.',
                'type' => 'text',
            ];
        }

        // Generate payment link
        $paymentLink = url("/pay/{$nextEmi->loan_id}?token=" . encrypt($borrower->id));

        return [
            'message' => "Click the link below to make your payment:\n\n" .
                "Amount: ₹" . number_format($nextEmi->total_amount, 2) . "\n" .
                "Due Date: " . $nextEmi->due_date->format('d M Y') . "\n\n" .
                $paymentLink . "\n\n" .
                "This link is valid for 24 hours.",
            'type' => 'text',
            'metadata' => [
                'action' => 'payment_link',
                'amount' => $nextEmi->total_amount,
                'url' => $paymentLink,
            ],
        ];
    }

    private function handleStatementRequest(Borrower $borrower): array
    {
        $loan = $borrower->loans()->whereIn('status', ['active', 'completed'])->first();

        if (! $loan) {
            return [
                'message' => 'No statement available for download.',
                'type' => 'text',
            ];
        }

        $pdfUrl = url("/statements/{$loan->id}.pdf?token=" . encrypt($borrower->id));

        return [
            'message' => "Download your loan statement here:\n\n{$pdfUrl}",
            'type' => 'text',
            'metadata' => [
                'action' => 'download_statement',
                'url' => $pdfUrl,
            ],
        ];
    }

    private function handleSupportRequest(): array
    {
        return [
            'message' => "I'm connecting you to a customer service representative. " .
                "Please hold while we find an available agent.\n\n" .
                "Meanwhile, you can also call us at: 1800-XXX-XXXX",
            'type' => 'text',
            'metadata' => [
                'action' => 'escalate_to_human',
                'priority' => 'normal',
            ],
        ];
    }

    private function handleUnknownIntent(): array
    {
        return [
            'message' => "I'm not sure I understood that. Here are some things I can help with:\n\n" .
                "- Check loan balance\n" .
                "- View next EMI details\n" .
                "- Check payment history\n" .
                "- Download statement\n" .
                "- Make a payment\n\n" .
                "Or type 'help' to speak with a representative.",
            'type' => 'text',
            'metadata' => ['action' => 'show_menu'],
        ];
    }

    private function createErrorResponse(string $error): array
    {
        return [
            'message' => 'Error: ' . $error,
            'type' => 'text',
            'error' => true,
        ];
    }
}
