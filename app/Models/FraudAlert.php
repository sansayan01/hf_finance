<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FraudAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'loan_id', 'borrower_id', 'alert_type', 'severity',
        'description', 'detected_patterns', 'fraud_score', 'related_data',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes'
    ];

    protected $casts = [
        'detected_patterns' => 'json',
        'fraud_score' => 'decimal:2',
        'related_data' => 'json',
        'reviewed_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isHighRisk(): bool
    {
        return $this->severity === 'high' || $this->fraud_score >= 80;
    }
}
