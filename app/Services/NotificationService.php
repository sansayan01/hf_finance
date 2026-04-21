<?php

namespace App\Services;

use App\Models\Borrower;
use App\Models\Loan;
use App\Models\NotificationLog;
use App\Models\RepaymentSchedule;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendPaymentReminder(RepaymentSchedule $schedule, string $channel = 'all'): array
    {
        $borrower = $schedule->loan->borrower;
        $loan = $schedule->loan;

        $results = [];

        if ($channel === 'all' || $channel === 'sms') {
            $results['sms'] = $this->sendSMS(
                $borrower->phone,
                $this->getPaymentReminderSMSTemplate($schedule)
            );
        }

        if ($channel === 'all' || $channel === 'email') {
            $results['email'] = $this->sendEmail(
                $borrower->email,
                'Payment Reminder - Loan #' . $loan->loan_number,
                $this->getPaymentReminderEmailTemplate($schedule)
            );
        }

        if ($channel === 'all' || $channel === 'whatsapp') {
            $results['whatsapp'] = $this->sendWhatsApp(
                $borrower->phone,
                $this->getPaymentReminderWhatsAppTemplate($schedule)
            );
        }

        return $results;
    }

    public function scheduleReminders(RepaymentSchedule $schedule): void
    {
        $dueDate = $schedule->due_date;

        // 3 days before
        $this->scheduleNotification($schedule, $dueDate->copy()->subDays(3), 'gentle_reminder');

        // 1 day before
        $this->scheduleNotification($schedule, $dueDate->copy()->subDay(), 'urgent_reminder');

        // On due date
        $this->scheduleNotification($schedule, $dueDate, 'due_today');

        // 3 days after (if unpaid)
        $this->scheduleNotification($schedule, $dueDate->copy()->addDays(3), 'overdue_notice');

        // 7 days after (if unpaid)
        $this->scheduleNotification($schedule, $dueDate->copy()->addDays(7), 'final_notice');
    }

    private function scheduleNotification(RepaymentSchedule $schedule, $date, string $type): void
    {
        // This would typically dispatch a queued job
        // For now, we'll store in notification_schedules table (if implemented)
    }

    public function sendLoanApprovedNotification(Loan $loan): array
    {
        $borrower = $loan->borrower;

        return [
            'sms' => $this->sendSMS(
                $borrower->phone,
                "Dear {$borrower->first_name}, your loan application #{$loan->loan_number} for " .
                "Rs. " . number_format($loan->approved_amount, 2) . " has been approved. " .
                "EMI: Rs. " . number_format($loan->repaymentSchedules->first()->total_amount ?? 0, 2) . "/month. " .
                "Contact your branch for disbursement."
            ),
            'email' => $this->sendEmail(
                $borrower->email,
                'Loan Approved - ' . config('app.name'),
                view('emails.loan-approved', ['loan' => $loan])->render()
            ),
        ];
    }

    public function sendPaymentConfirmation(Payment $payment): array
    {
        $borrower = $payment->borrower;
        $loan = $payment->loan;

        return [
            'sms' => $this->sendSMS(
                $borrower->phone,
                "Payment received! Rs. " . number_format($payment->amount, 2) .
                " for loan #{$loan->loan_number}. Receipt: {$payment->receipt_number}. " .
                "Outstanding: Rs. " . number_format($loan->principal_outstanding + $loan->interest_outstanding, 2)
            ),
            'email' => $this->sendEmail(
                $borrower->email,
                'Payment Receipt - ' . config('app.name'),
                view('emails.payment-receipt', ['payment' => $payment])->render()
            ),
        ];
    }

    private function sendSMS(string $phone, string $message): array
    {
        // Integration with SMS provider (Twilio, MSG91, etc.)
        $log = NotificationLog::create([
            'organization_id' => auth()->user()?->organization_id,
            'channel' => 'sms',
            'recipient' => $phone,
            'content' => $message,
            'status' => 'pending',
        ]);

        // Mock successful sending
        $log->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return [
            'success' => true,
            'message_id' => $log->id,
        ];
    }

    private function sendEmail(string $email, string $subject, string $content): array
    {
        $log = NotificationLog::create([
            'organization_id' => auth()->user()?->organization_id,
            'channel' => 'email',
            'recipient' => $email,
            'subject' => $subject,
            'content' => $content,
            'status' => 'pending',
        ]);

        // Send via Laravel Mail
        try {
            // Mail::html($content, function ($message) use ($email, $subject) {
            //     $message->to($email)->subject($subject);
            // });

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return ['success' => true, 'message_id' => $log->id];
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sendWhatsApp(string $phone, string $message): array
    {
        // Integration with WhatsApp Business API
        $log = NotificationLog::create([
            'organization_id' => auth()->user()?->organization_id,
            'channel' => 'whatsapp',
            'recipient' => $phone,
            'content' => $message,
            'status' => 'pending',
        ]);

        // Mock
        $log->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return [
            'success' => true,
            'message_id' => $log->id,
        ];
    }

    private function getPaymentReminderSMSTemplate(RepaymentSchedule $schedule): string
    {
        $loan = $schedule->loan;
        $borrower = $loan->borrower;
        $daysRemaining = now()->diffInDays($schedule->due_date, false);

        if ($daysRemaining > 0) {
            return "Hi {$borrower->first_name}, your EMI of Rs. " .
                number_format($schedule->total_amount, 2) .
                " for loan #{$loan->loan_number} is due on " .
                $schedule->due_date->format('d M Y') .
                ". Please ensure sufficient balance.";
        }

        return "URGENT: Hi {$borrower->first_name}, your EMI of Rs. " .
            number_format($schedule->total_amount, 2) .
            " for loan #{$loan->loan_number} is OVERDUE by " .
            abs($daysRemaining) . " days. Please pay immediately to avoid penalties.";
    }

    private function getPaymentReminderEmailTemplate(RepaymentSchedule $schedule): string
    {
        return view('emails.payment-reminder', ['schedule' => $schedule])->render();
    }

    private function getPaymentReminderWhatsAppTemplate(RepaymentSchedule $schedule): string
    {
        return $this->getPaymentReminderSMSTemplate($schedule);
    }
}
