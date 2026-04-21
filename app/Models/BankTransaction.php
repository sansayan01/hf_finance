<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'bank_account_id', 'transaction_date', 'reference_number',
        'description', 'debit_amount', 'credit_amount', 'balance', 'transaction_type',
        'counterparty_name', 'counterparty_account', 'status', 'reconciled_payment_id',
        'reconciled_by', 'reconciled_at', 'statement_file'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function reconciledPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'reconciled_payment_id');
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function isReconciled(): bool
    {
        return $this->status === 'reconciled';
    }

    public function getAmount(): float
    {
        return $this->credit_amount > 0 ? $this->credit_amount : $this->debit_amount;
    }
}
