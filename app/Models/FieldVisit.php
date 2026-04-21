<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id', 'borrower_id', 'organization_id', 'collector_id', 'visit_date',
        'visited_at', 'latitude', 'longitude', 'address_verified', 'borrower_status_notes',
        'contact_person', 'contact_number', 'borrower_availability', 'amount_collected',
        'payment_method', 'promised_payment_date', 'promised_amount', 'collection_notes',
        'photos', 'next_action', 'next_visit_date', 'ai_priority_score', 'created_by'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visited_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'amount_collected' => 'decimal:2',
        'promised_payment_date' => 'date',
        'promised_amount' => 'decimal:2',
        'photos' => 'json',
        'next_visit_date' => 'date',
        'ai_priority_score' => 'decimal:2',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
