<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'keyable_type', 'keyable_id', 'name', 'key', 'secret',
        'permissions', 'allowed_ips', 'rate_limit', 'last_used_at', 'expires_at', 'status'
    ];

    protected $casts = [
        'permissions' => 'json',
        'allowed_ips' => 'json',
        'rate_limit' => 'integer',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'secret',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function keyable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isValid(): bool
    {
        return ! $this->isExpired() && ! $this->isRevoked() && $this->status === 'active';
    }

    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return true;
        }
        return in_array($permission, $this->permissions) || in_array('*', $this->permissions);
    }

    public function recordUsage(): void
    {
        $this->last_used_at = now();
        $this->save();
    }
}
