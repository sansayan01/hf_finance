<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\BelongsToOrganization;

class Loan extends Model
{
    use BelongsToOrganization, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::make()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'organization_id',
        'borrower_id',
        'loan_product_id',
        'loan_officer_id',
        'loan_number',
        'applied_amount',
        'approved_amount',
        'disbursed_amount',
        'interest_rate',
        'interest_type',
        'tenure_months',
        'repayment_frequency',
        'processing_fee',
        'total_interest',
        'total_payable',
        'purpose',
        'collateral_type',
        'collateral_value',
        'collateral_description',
        'status',
        'applied_at',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'disbursed_at',
        'closed_at',
        'approved_by',
        'rejected_by',
        'disbursed_by',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'applied_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'disbursed_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'collateral_value' => 'decimal:2',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function loanOfficer()
    {
        return $this->belongsTo(User::class, 'loan_officer_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function disbursedBy()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function repaymentSchedules()
    {
        return $this->hasMany(RepaymentSchedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }
}
