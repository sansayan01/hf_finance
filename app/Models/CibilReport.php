<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CibilReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'borrower_id', 'bureau_name', 'report_number',
        'report_date', 'credit_score', 'score_category', 'total_accounts',
        'active_accounts', 'closed_accounts', 'overdue_accounts', 'current_balance',
        'total_credit_limit', 'enquiries_last_6_months', 'enquiries_last_12_months',
        'accounts_data', 'enquiries_data', 'report_pdf_path', 'status'
    ];

    protected $casts = [
        'report_date' => 'date',
        'credit_score' => 'integer',
        'total_accounts' => 'integer',
        'active_accounts' => 'integer',
        'closed_accounts' => 'integer',
        'overdue_accounts' => 'integer',
        'enquiries_last_6_months' => 'integer',
        'enquiries_last_12_months' => 'integer',
        'current_balance' => 'decimal:2',
        'total_credit_limit' => 'decimal:2',
        'accounts_data' => 'json',
        'enquiries_data' => 'json',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public static function getRiskScoreCategory(int $score): string
    {
        return match (true) {
            $score >= 750 => 'excellent',
            $score >= 700 => 'good',
            $score >= 650 => 'fair',
            $score >= 550 => 'poor',
            default => 'bad',
        };
    }
}
