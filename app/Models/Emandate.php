<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emandate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'borrower_id', 'loan_id', 'mandate_id', 'reference_number',
        'account_holder_name', 'account_number', 'ifsc_code', 'bank_name', 'account_type',
        'phone', 'email', 'max_amount', 'frequency', 'start_date', 'end_date',
        'status', 'umrn', 'npci_response', 'activated_at', 'cancelled_at', 'cancel_reason'
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'npci_response' => 'json',
        'activated_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function activate(): void
    {
        $this->status = 'active';
        $this->activated_at = now();
        $this->save();
    }

    public function cancel(string $reason = null): void
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancel_reason = $reason;
        $this->save();
    }
}
