<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key_id', 'organization_id', 'method', 'endpoint', 'request_body',
        'response_status', 'response_body', 'duration_ms', 'ip_address', 'user_agent', 'requested_at'
    ];

    protected $casts = [
        'request_body' => 'json',
        'response_body' => 'json',
        'response_status' => 'integer',
        'duration_ms' => 'integer',
        'requested_at' => 'datetime',
    ];

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
