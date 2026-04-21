<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'borrower_id', 'loan_id', 'notification_type',
        'channel', 'recipient', 'subject', 'content', 'template_data',
        'template_id', 'status', 'provider_response', 'sent_at', 'delivered_at',
        'opened_at', 'error_message', 'retry_count'
    ];

    protected $casts = [
        'template_data' => 'json',
        'provider_response' => 'json',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    public function markAsOpened(): void
    {
        if (! $this->opened_at) {
            $this->opened_at = now();
            $this->save();
        }
    }

    public function markAsFailed(string $error): void
    {
        $this->status = 'failed';
        $this->error_message = $error;
        $this->retry_count++;
        $this->save();
    }
}
