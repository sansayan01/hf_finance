<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'escrow_account_id', 'organization_id', 'transactionable_type', 'transactionable_id',
        'transaction_type', 'amount', 'balance_before', 'balance_after',
        'reference_number', 'description', 'created_by', 'processed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function escrowAccount(): BelongsTo
    {
        return $this->belongsTo(EscrowAccount::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
