<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'borrower_id', 'session_id', 'phone', 'platform',
        'status', 'started_at', 'last_activity_at', 'ended_at', 'context', 'message_count'
    ];

    protected $casts = [
        'context' => 'json',
        'message_count' => 'integer',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function updateActivity(): void
    {
        $this->last_activity_at = now();
        $this->save();
    }

    public function close(): void
    {
        $this->status = 'closed';
        $this->ended_at = now();
        $this->save();
    }
}
