<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepaymentSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'paid_amount',
        'paid_at',
        'balance',
        'status',
        'late_fee_charged',
        'days_overdue',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'late_fee_charged' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
