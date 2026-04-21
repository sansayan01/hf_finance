<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EscrowAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'account_name', 'account_number', 'bank_name',
        'branch_name', 'ifsc_code', 'current_balance', 'held_amount',
        'available_balance', 'escrow_type', 'description', 'status'
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'held_amount' => 'decimal:2',
        'available_balance' => 'decimal:2',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(EscrowTransaction::class);
    }

    public function credit(float $amount, $transactionable, string $reference = null, string $description = null): EscrowTransaction
    {
        $before = $this->current_balance;
        $this->current_balance += $amount;
        $this->available_balance = $this->current_balance - $this->held_amount;
        $this->save();

        return $this->transactions()->create([
            'organization_id' => $this->organization_id,
            'transactionable_type' => get_class($transactionable),
            'transactionable_id' => $transactionable->id,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $this->current_balance,
            'reference_number' => $reference,
            'description' => $description,
            'created_by' => auth()->id(),
            'processed_at' => now(),
        ]);
    }

    public function debit(float $amount, $transactionable, string $reference = null, string $description = null): EscrowTransaction
    {
        if ($this->available_balance < $amount) {
            throw new \Exception('Insufficient available balance');
        }

        $before = $this->current_balance;
        $this->current_balance -= $amount;
        $this->available_balance = $this->current_balance - $this->held_amount;
        $this->save();

        return $this->transactions()->create([
            'organization_id' => $this->organization_id,
            'transactionable_type' => get_class($transactionable),
            'transactionable_id' => $transactionable->id,
            'transaction_type' => 'debit',
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $this->current_balance,
            'reference_number' => $reference,
            'description' => $description,
            'created_by' => auth()->id(),
            'processed_at' => now(),
        ]);
    }
}
