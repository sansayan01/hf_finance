<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;

class LoanProduct extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'code',
        'description',
        'min_amount',
        'max_amount',
        'interest_rate',
        'interest_type',
        'min_tenure_months',
        'max_tenure_months',
        'repayment_frequency',
        'processing_fee_type',
        'processing_fee_value',
        'late_penalty_type',
        'late_penalty_value',
        'grace_period_days',
        'requires_guarantor',
        'requires_collateral',
        'status',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'processing_fee_value' => 'decimal:2',
        'late_penalty_value' => 'decimal:2',
        'requires_guarantor' => 'boolean',
        'requires_collateral' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
