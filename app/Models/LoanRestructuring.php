<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRestructuring extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id', 'requested_by', 'approved_by', 'restructure_type',
        'reason', 'old_tenure_months', 'new_tenure_months', 'old_interest_rate',
        'new_interest_rate', 'old_emi_amount', 'new_emi_amount',
        'outstanding_principal', 'outstanding_interest', 'moratorium_start_date',
        'moratorium_end_date', 'terms_conditions', 'status', 'approved_at', 'rejection_reason'
    ];

    protected $casts = [
        'old_tenure_months' => 'integer',
        'new_tenure_months' => 'integer',
        'old_interest_rate' => 'decimal:4',
        'new_interest_rate' => 'decimal:4',
        'old_emi_amount' => 'decimal:2',
        'new_emi_amount' => 'decimal:2',
        'outstanding_principal' => 'decimal:2',
        'outstanding_interest' => 'decimal:2',
        'moratorium_start_date' => 'date',
        'moratorium_end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
