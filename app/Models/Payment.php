<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'loan_id',
        'repayment_schedule_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'received_by',
        'notes',
        'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function repaymentSchedule()
    {
        return $this->belongsTo(RepaymentSchedule::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
